<?php
include("include/db-connect.php");
include("include/auth-check.php");
require_once('include/a-header.php');

$status_filter = trim($_GET['status'] ?? '');

$total = 0;
$pending = 0;
$completed = 0;
$revenue = 0;

$stats_result = mysqli_query($conn, "SELECT status, COUNT(*) AS cnt, SUM(price) AS sum_price FROM leads GROUP BY status");
if ($stats_result) {
    while ($row = mysqli_fetch_assoc($stats_result)) {
        $total += (int) $row['cnt'];
        if ($row['status'] === 'pending') {
            $pending = (int) $row['cnt'];
        }
        if ($row['status'] === 'completed') {
            $completed = (int) $row['cnt'];
            $revenue += (float) $row['sum_price'];
        } 
    }
}

$sql = "SELECT * FROM leads";
$params = [];
$types = '';
if ($status_filter !== '') {
    $sql .= " WHERE status = ?";
    $params[] = $status_filter;
    $types = 's';
}
$sql .= " ORDER BY created_at DESC LIMIT 5";

if (!empty($params)) {
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $recent_result = mysqli_stmt_get_result($stmt);
} else {
    $recent_result = mysqli_query($conn, $sql);
}

$recent_leads = [];
if ($recent_result) {
    while ($row = mysqli_fetch_assoc($recent_result)) {
        $recent_leads[] = $row;
    }
}
?>
<?php require_once('section/sidebar.php'); ?>
<div class="main-content">
    <div class="content-header">
        <div>
            <h1 class="main-h1">Welcome, <?php echo $_SESSION['admin_username']; ?></h1>
            <p class="current-date"><?php echo date("l , F d , Y"); ?></p>
        </div>
        <div class="admin-avatar">AD</div>
    </div>

    <div class="stat-cards">
        <div class="stat-card highlight">
            <p class="label"><i class="fa-solid fa-users"></i> Total leads</p>
            <p class="value"><?php echo $total; ?></p>
        </div>
        <div class="stat-card">
            <p class="label"><i class="fa-solid fa-clock"></i> Pending</p>
            <p class="value warning"><?php echo $pending; ?></p>
        </div>
        <div class="stat-card">
            <p class="label"><i class="fa-solid fa-check"></i> Completed</p>
            <p class="value success"><?php echo $completed; ?></p>
        </div>
        <div class="stat-card" style="background-color:#ffe5a4">
            <p class="label"><i class="fa-solid fa-sack-dollar"></i> Est. payout</p>
            <p class="value">AED <?php echo number_format($revenue, 0); ?></p>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <p class="leads-heading mb-0">Recent leads</p>
        <form method="GET" action="dashboard.php" class="d-flex align-items-center gap-2">
            <select name="status" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                <option value="">All status</option>
                <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="contacted" <?php echo $status_filter === 'contacted' ? 'selected' : ''; ?>>Contacted</option>
                <option value="completed" <?php echo $status_filter === 'completed' ? 'selected' : ''; ?>>Completed</option>
                <option value="cancelled" <?php echo $status_filter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
            </select>
            <?php if ($status_filter !== ''): ?>
                <a href="dashboard.php" class="btn btn-sm btn-secondary">Clear</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="leads-table-wrap">
        <table class="leads-table">
            <tr>
                <th>Name</th>
                <th>Model</th>
                <th>Price</th>
                <th>Status</th>
            </tr>
            <?php if (empty($recent_leads)): ?>
                <tr>
                    <td colspan="4" style="padding:16px; text-align:center; color:#797979c5;">No leads yet</td>
                </tr>
            <?php else: ?>
                <?php foreach ($recent_leads as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['name'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['model'] ?? ''); ?></td>
                        <td>AED <?php echo number_format((float) ($row['price'] ?? 0), 0); ?></td>
                        <td>
                            <select class="form-select-value form-select-sm status-select"
                                data-status="<?php echo htmlspecialchars($row['status'] ?? 'pending'); ?>"
                                data-id="<?php echo (int) $row['id']; ?>">
                                <option value="pending" <?php echo ($row['status'] ?? '') === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="contacted" <?php echo ($row['status'] ?? '') === 'contacted' ? 'selected' : ''; ?>>Contacted</option>
                                <option value="completed" <?php echo ($row['status'] ?? '') === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                <option value="cancelled" <?php echo ($row['status'] ?? '') === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </table>
    </div>
        <?php
    // Published Blogs
    $blogs_total = 0;
    $recent_blogs = [];
    $blogs_count_res = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM blogs WHERE status = 'published'");
    if ($blogs_count_res) { $blogs_total = (int) mysqli_fetch_assoc($blogs_count_res)['cnt']; }
    $recent_blogs_res = mysqli_query($conn, "SELECT title, created_at FROM blogs WHERE status = 'published' ORDER BY created_at DESC LIMIT 3");
    if ($recent_blogs_res) { while ($row = mysqli_fetch_assoc($recent_blogs_res)) { $recent_blogs[] = $row; } }

    // Contact Messages
    $contact_total = 0;
    $recent_contacts = [];
    $contact_count_res = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM contact_messages WHERE status = 'unread'");
    if ($contact_count_res) { $contact_total = (int) mysqli_fetch_assoc($contact_count_res)['cnt']; }
    $recent_contacts_res = mysqli_query($conn, "SELECT name, subject, created_at FROM contact_messages ORDER BY created_at DESC LIMIT 3");
    if ($recent_contacts_res) { while ($row = mysqli_fetch_assoc($recent_contacts_res)) { $recent_contacts[] = $row; } }
    ?>

    <div class="mini-widgets">
        <div class="mini-widget-card">
            <div class="mini-widget-top">
                <p class="mini-widget-title"><span class="mini-widget-icon blue"><i class="fa-solid fa-newspaper"></i></span> Published Blogs</p>
                <a href="blogs.php" class="view-all-link">View All</a>
            </div>
            <p class="mini-widget-count"><?php echo $blogs_total; ?></p>
            <p class="mini-widget-caption">Total published</p>
            <div class="mini-widget-list">
                <?php if (empty($recent_blogs)): ?>
                    <div class="mini-widget-empty">No blogs yet</div>
                <?php else: ?>
                    <?php foreach ($recent_blogs as $b): ?>
                        <div class="mini-widget-item">
                            <div class="mini-widget-item-title"><?php echo htmlspecialchars($b['title']); ?></div>
                            <div class="mini-widget-item-sub"><?php echo date("M d, Y", strtotime($b['created_at'])); ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="mini-widget-card">
            <div class="mini-widget-top">
                <p class="mini-widget-title"><span class="mini-widget-icon green"><i class="fa-solid fa-envelope"></i></span> Contact Messages</p>
                <a href="contact.php" class="view-all-link">View All</a>
            </div>
            <p class="mini-widget-count"><?php echo $contact_total; ?></p>
            <p class="mini-widget-caption">Unread messages</p>
            <div class="mini-widget-list">
                <?php if (empty($recent_contacts)): ?>
                    <div class="mini-widget-empty">No messages yet</div>
                <?php else: ?>
                    <?php foreach ($recent_contacts as $c): ?>
                        <div class="mini-widget-item">
                            <div class="mini-widget-item-title"><?php echo htmlspecialchars($c['name']); ?></div>
                            <div class="mini-widget-item-sub"><?php echo htmlspecialchars($c['subject']); ?> &bull; <?php echo date("M d, Y", strtotime($c['created_at'])); ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</body>

</html>