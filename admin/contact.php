<?php
include("include/db-connect.php");
include("include/auth-check.php");

$success_msg = "";
$error_msg = "";

// ---------- Ensure a contact_info row exists (id = 1) ----------
$check = mysqli_query($conn, "SELECT id FROM contact_info WHERE id = 1 LIMIT 1");
if ($check && mysqli_num_rows($check) === 0) {
    mysqli_query($conn, "INSERT INTO contact_info (id, phone, email, address, hours_weekday, hours_weekend, facebook, instagram, twitter, linkedin, whatsapp) VALUES (1, '', '', '', '', '', '#', '#', '#', '#', '#')");
}

// ---------- Update contact info ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_info'])) {
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $hours_weekday = trim($_POST['hours_weekday'] ?? '');
    $hours_weekend = trim($_POST['hours_weekend'] ?? '');
    $facebook = trim($_POST['facebook'] ?? '') ?: '#';
    $instagram = trim($_POST['instagram'] ?? '') ?: '#';
    $twitter = trim($_POST['twitter'] ?? '') ?: '#';
    $linkedin = trim($_POST['linkedin'] ?? '') ?: '#';
    $whatsapp = trim($_POST['whatsapp'] ?? '') ?: '#';

    $stmt = mysqli_prepare($conn, "UPDATE contact_info SET phone=?, email=?, address=?, hours_weekday=?, hours_weekend=?, facebook=?, instagram=?, twitter=?, linkedin=?, whatsapp=? WHERE id=1");
    mysqli_stmt_bind_param($stmt, "ssssssssss", $phone, $email, $address, $hours_weekday, $hours_weekend, $facebook, $instagram, $twitter, $linkedin, $whatsapp);

    if (mysqli_stmt_execute($stmt)) {
        $success_msg = "Contact info updated successfully.";
    } else {
        $error_msg = "Could not update contact info: " . mysqli_error($conn);
    }
}

// ---------- Update a message's status ----------
if (isset($_GET['mark'])) {
    $msg_id = (int) $_GET['mark'];
    $new_status = trim($_GET['status'] ?? '');
    if (in_array($new_status, ['unread', 'read', 'replied'], true)) {
        $stmt = mysqli_prepare($conn, "UPDATE contact_messages SET status=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "si", $new_status, $msg_id);
        mysqli_stmt_execute($stmt);
    }
    header("Location: contact.php?tab=messages");
    exit;
}

// ---------- Delete a message ----------
if (isset($_GET['delete_message'])) {
    $msg_id = (int) $_GET['delete_message'];
    $stmt = mysqli_prepare($conn, "DELETE FROM contact_messages WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $msg_id);
    mysqli_stmt_execute($stmt);
    header("Location: contact.php?tab=messages&deleted=1");
    exit;
}

require_once('include/a-header.php');
require_once('section/sidebar.php');

// ---------- Load contact info ----------
$info_result = mysqli_query($conn, "SELECT * FROM contact_info WHERE id = 1 LIMIT 1");
$info = mysqli_fetch_assoc($info_result) ?: [
    'phone' => '', 'email' => '', 'address' => '', 'hours_weekday' => '', 'hours_weekend' => '',
    'facebook' => '#', 'instagram' => '#', 'twitter' => '#', 'linkedin' => '#', 'whatsapp' => '#',
];

// ---------- Load messages (with status filter) ----------
$status_filter = trim($_GET['status_filter'] ?? '');
$where = "";
if (in_array($status_filter, ['unread', 'read', 'replied'], true)) {
    $where = " WHERE status = '" . mysqli_real_escape_string($conn, $status_filter) . "'";
}
$messages = [];
$msg_result = mysqli_query($conn, "SELECT * FROM contact_messages" . $where . " ORDER BY created_at DESC");
if ($msg_result) {
    while ($row = mysqli_fetch_assoc($msg_result)) {
        $messages[] = $row;
    }
}

$stats = ['total' => 0, 'unread' => 0, 'replied' => 0];
$stats_result = mysqli_query($conn, "SELECT status, COUNT(*) AS cnt FROM contact_messages GROUP BY status");
if ($stats_result) {
    while ($row = mysqli_fetch_assoc($stats_result)) {
        $stats['total'] += (int) $row['cnt'];
        if ($row['status'] === 'unread') $stats['unread'] = (int) $row['cnt'];
        if ($row['status'] === 'replied') $stats['replied'] = (int) $row['cnt'];
    }
}

$active_tab = ($_GET['tab'] ?? 'info') === 'messages' ? 'messages' : 'info';
?>
<style>
    .status-badge { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 500; display: inline-block; white-space: nowrap; }
    .status-unread  { background: #fdeaea; color: #c0392b; }
    .status-read    { background: #d1ecf1; color: #0c5460; }
    .status-replied { background: #d4edda; color: #155724; }
    .contact-tab-link { padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px; color: #797979; }
    .contact-tab-link.active { background: #0B1E3F; color: #fff; }
    .msg-table-wrap { overflow-x: auto; }
    .msg-table { width: 100%; min-width: 900px; }
    .msg-table th, .msg-table td { white-space: nowrap; padding: 10px 8px; }
    .msg-subject-cell { white-space: normal; max-width: 260px; }
</style>

<div class="main-content">
    <div class="content-header">
        <div>
            <h1 class="main-h1">Contact Page</h1>
            <p class="current-date">Manage contact info and messages received from the Contact Us form</p>
        </div>
        <div class="admin-avatar">AD</div>
    </div>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success" style="border-radius:10px;">Message deleted successfully.</div>
    <?php endif; ?>
    <?php if ($success_msg): ?>
        <div class="alert alert-success" style="border-radius:10px;"><?php echo $success_msg; ?></div>
    <?php endif; ?>
    <?php if ($error_msg): ?>
        <div class="alert alert-danger" style="border-radius:10px;"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <div class="stat-cards mb-4">
        <div class="stat-card highlight">
            <p class="label"><i class="fa-solid fa-envelope"></i> Total messages</p>
            <p class="value"><?php echo $stats['total']; ?></p>
        </div>
        <div class="stat-card">
            <p class="label"><i class="fa-solid fa-envelope-open-text"></i> Unread</p>
            <p class="value warning"><?php echo $stats['unread']; ?></p>
        </div>
        <div class="stat-card">
            <p class="label"><i class="fa-solid fa-check-double"></i> Replied</p>
            <p class="value success"><?php echo $stats['replied']; ?></p>
        </div>
    </div>

    <div class="d-flex gap-2 mb-4">
        <a href="contact.php?tab=info" class="contact-tab-link <?php echo $active_tab === 'info' ? 'active' : ''; ?>">
            <i class="fa-solid fa-address-card me-1"></i> Contact Info
        </a>
        <a href="contact.php?tab=messages" class="contact-tab-link <?php echo $active_tab === 'messages' ? 'active' : ''; ?>">
            <i class="fa-solid fa-inbox me-1"></i> Messages
        </a>
    </div>

    <?php if ($active_tab === 'info'): ?>

        <div class="card" style="border-radius:14px; padding:24px;">
            <h5 style="margin-bottom:16px;"><i class="fa-solid fa-address-card me-2"></i>Contact Info Content</h5>
            <p style="font-size:13px; color:#797979c5; margin-top:-10px; margin-bottom:20px;">
                This is exactly what shows on the "Get In Touch With Us" box on your public Contact page.
            </p>

            <form method="POST" action="contact.php?tab=info">
                <input type="hidden" name="update_info" value="1">

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" style="font-size:12.5px; color:#797979c5;">Phone Number</label>
                        <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($info['phone']); ?>" placeholder="+971 50 555 6779">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" style="font-size:12.5px; color:#797979c5;">Email Address</label>
                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($info['email']); ?>" placeholder="info@sellphonedubai.com">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label" style="font-size:12.5px; color:#797979c5;">Address</label>
                        <textarea name="address" class="form-control" rows="2" placeholder="Street, area, city"><?php echo htmlspecialchars($info['address']); ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" style="font-size:12.5px; color:#797979c5;">Working Hours (weekdays)</label>
                        <input type="text" name="hours_weekday" class="form-control" value="<?php echo htmlspecialchars($info['hours_weekday']); ?>" placeholder="Sun - Thu: 9AM - 10PM">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" style="font-size:12.5px; color:#797979c5;">Working Hours (weekend)</label>
                        <input type="text" name="hours_weekend" class="form-control" value="<?php echo htmlspecialchars($info['hours_weekend']); ?>" placeholder="Fri - Sat: 10AM - 8PM">
                    </div>

                    <div class="col-12"><hr></div>

                    <div class="col-md-6">
                        <label class="form-label" style="font-size:12.5px; color:#797979c5;"><i class="fab fa-facebook-f me-1"></i>Facebook URL</label>
                        <input type="text" name="facebook" class="form-control" value="<?php echo htmlspecialchars($info['facebook']); ?>" placeholder="https://facebook.com/yourpage">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" style="font-size:12.5px; color:#797979c5;"><i class="fab fa-instagram me-1"></i>Instagram URL</label>
                        <input type="text" name="instagram" class="form-control" value="<?php echo htmlspecialchars($info['instagram']); ?>" placeholder="https://instagram.com/yourpage">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" style="font-size:12.5px; color:#797979c5;"><i class="fab fa-twitter me-1"></i>Twitter / X URL</label>
                        <input type="text" name="twitter" class="form-control" value="<?php echo htmlspecialchars($info['twitter']); ?>" placeholder="https://twitter.com/yourpage">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" style="font-size:12.5px; color:#797979c5;"><i class="fab fa-linkedin-in me-1"></i>LinkedIn URL</label>
                        <input type="text" name="linkedin" class="form-control" value="<?php echo htmlspecialchars($info['linkedin']); ?>" placeholder="https://linkedin.com/company/yourpage">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" style="font-size:12.5px; color:#797979c5;"><i class="fab fa-whatsapp me-1"></i>WhatsApp URL</label>
                        <input type="text" name="whatsapp" class="form-control" value="<?php echo htmlspecialchars($info['whatsapp']); ?>" placeholder="https://wa.me/971505556779">
                    </div>

                    <div class="col-12 mt-3">
                        <button type="submit" class="btn" style="background:#0B1E3F; color:#fff; padding:10px 28px; border-radius:8px;">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>

    <?php else: ?>

        <form method="GET" action="contact.php" class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <input type="hidden" name="tab" value="messages">
            <select name="status_filter" class="form-select form-select-sm" style="max-width:180px;" onchange="this.form.submit()">
                <option value="">All status</option>
                <option value="unread" <?php echo $status_filter === 'unread' ? 'selected' : ''; ?>>Unread</option>
                <option value="read" <?php echo $status_filter === 'read' ? 'selected' : ''; ?>>Read</option>
                <option value="replied" <?php echo $status_filter === 'replied' ? 'selected' : ''; ?>>Replied</option>
            </select>
            <?php if ($status_filter !== ''): ?>
                <a href="contact.php?tab=messages" class="btn btn-sm btn-secondary">Clear</a>
            <?php endif; ?>
        </form>

        <!-- Message detail modals -->
        <?php foreach ($messages as $m): ?>
            <div class="modal fade" id="msgModal<?php echo (int) $m['id']; ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content" style="border-radius:14px;">
                        <div class="modal-header" style="border-bottom:1px solid #eef1f5;">
                            <div>
                                <h1 class="modal-title fs-5" style="color:#0B1E3F; margin-bottom:2px;"><?php echo htmlspecialchars($m['name']); ?></h1>
                                <span style="font-size:12px; color:#797979c5;"><?php echo htmlspecialchars($m['email']); ?></span>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p style="font-size:12.5px; color:#797979c5; margin-bottom:4px;">
                                <?php echo !empty($m['phone']) ? htmlspecialchars($m['phone']) . ' &middot; ' : ''; ?>
                                <?php echo htmlspecialchars(date("M j, Y g:i A", strtotime($m['created_at']))); ?>
                            </p>
                            <p style="font-weight:600; color:#0B1E3F; margin-bottom:8px;">
                                <?php echo htmlspecialchars($m['subject'] ?: '(No subject)'); ?>
                            </p>
                            <p style="white-space:pre-wrap; color:#333;"><?php echo htmlspecialchars($m['message']); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="msg-table-wrap">
            <table class="msg-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th class="msg-subject-cell">Subject</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($messages)): ?>
                        <tr><td colspan="7" style="text-align:center; padding:24px; color:#797979c5;">No messages yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($messages as $m): ?>
                            <tr>
                                <td>
                                    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#msgModal<?php echo (int) $m['id']; ?>" style="color:#0B1E3F; font-weight:600; text-decoration:none;">
                                        <?php echo htmlspecialchars($m['name']); ?>
                                    </a>
                                </td>
                                <td><?php echo htmlspecialchars($m['email']); ?></td>
                                <td><?php echo htmlspecialchars($m['phone'] ?: '—'); ?></td>
                                <td class="msg-subject-cell"><?php echo htmlspecialchars($m['subject'] ?: '—'); ?></td>
                                <td><?php echo htmlspecialchars(date("M j", strtotime($m['created_at']))); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo htmlspecialchars($m['status']); ?>">
                                        <?php echo htmlspecialchars(ucfirst($m['status'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($m['status'] !== 'read'): ?>
                                        <a href="contact.php?mark=<?php echo (int) $m['id']; ?>&status=read" title="Mark as read" style="color:#0c5460; margin-right:8px;"><i class="fa-solid fa-envelope-open"></i></a>
                                    <?php endif; ?>
                                    <?php if ($m['status'] !== 'replied'): ?>
                                        <a href="contact.php?mark=<?php echo (int) $m['id']; ?>&status=replied" title="Mark as replied" style="color:#155724; margin-right:8px;"><i class="fa-solid fa-check-double"></i></a>
                                    <?php endif; ?>
                                    <a href="contact.php?delete_message=<?php echo (int) $m['id']; ?>" title="Delete" style="color:#c0392b;" onclick="return confirm('Delete this message?');"><i class="fa-solid fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    <?php endif; ?>

</div>
</body>
</html>