<?php
include("include/db-connect.php");
include("include/auth-check.php");
require_once('include/a-header.php');

$brand_filter = trim($_GET['brand'] ?? '');
$status_filter = trim($_GET['status'] ?? '');
$search = trim($_GET['search'] ?? '');

$where = [];
$params = [];
$types = '';

if ($brand_filter !== '') {
    $where[] = "brand = ?";
    $params[] = $brand_filter;
    $types .= 's';
}
if ($status_filter !== '') {
    $where[] = "status = ?";
    $params[] = $status_filter;
    $types .= 's';
}
if ($search !== '') {
    $where[] = "(name LIKE ? OR phone LIKE ? OR model LIKE ? OR address LIKE ?)";
    $like = "%$search%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $types .= 'ssss';
}

$sql = "SELECT * FROM leads";
if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

// Count total matching leads first (for the pagination links)
$count_sql = "SELECT COUNT(*) as total FROM leads";
if (!empty($where)) {
    $count_sql .= " WHERE " . implode(" AND ", $where);
}
$total_leads = 0;
if (!empty($params)) {
    $count_stmt = mysqli_prepare($conn, $count_sql);
    mysqli_stmt_bind_param($count_stmt, $types, ...$params);
    mysqli_stmt_execute($count_stmt);
    $count_row = mysqli_fetch_assoc(mysqli_stmt_get_result($count_stmt));
} else {
    $count_row = mysqli_fetch_assoc(mysqli_query($conn, $count_sql));
}
$total_leads = (int) ($count_row['total'] ?? 0);

$per_page = 20;
$total_pages = max(1, (int) ceil($total_leads / $per_page));
$current_page = max(1, min($total_pages, (int) ($_GET['page'] ?? 1)));
$offset = ($current_page - 1) * $per_page;

$sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
$params[] = $per_page;
$params[] = $offset;
$types .= 'ii';

$leads = [];
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $leads[] = $row;
    }
}

// Build a lead_id -> [photo, ...] map from lead_images, which holds the
// photos the client actually uploaded of their own device.
$lead_photos = [];
if (!empty($leads)) {
    $lead_ids = array_column($leads, 'id');
    $placeholders = implode(',', array_fill(0, count($lead_ids), '?'));
    $id_types = str_repeat('i', count($lead_ids));

    $photo_stmt = mysqli_prepare($conn, "SELECT lead_id, image_path FROM lead_images WHERE lead_id IN ($placeholders) ORDER BY id ASC");
    mysqli_stmt_bind_param($photo_stmt, $id_types, ...$lead_ids);
    mysqli_stmt_execute($photo_stmt);
    $photo_result = mysqli_stmt_get_result($photo_stmt);
    while ($p = mysqli_fetch_assoc($photo_result)) {
        $lead_photos[$p['lead_id']][] = $p['image_path'];
    }
}

// Fall back to the model's catalog photo only if the client didn't upload
// any photos of their own for that lead.
foreach ($leads as $l) {
    if (empty($lead_photos[$l['id']]) && !empty($l['image'])) {
        $lead_photos[$l['id']] = [$l['image']];
    }
}

$brands = [];
$brand_result = mysqli_query($conn, "SELECT DISTINCT brand FROM leads WHERE brand IS NOT NULL AND brand <> '' ORDER BY brand");
if ($brand_result) {
    while ($b = mysqli_fetch_assoc($brand_result)) {
        $brands[] = $b['brand'];
    }
}
?>
<style>
    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        display: inline-block;
        white-space: nowrap;
    }
    .status-pending   { background: #fff3cd; color: #856404; }
    .status-contacted { background: #d1ecf1; color: #0c5460; }
    .status-completed { background: #d4edda; color: #155724; }
    .status-cancelled { background: #f8d7da; color: #a15055; opacity: 0.7; }

    /* ---- Bulk actions toolbar ---- */
    .bulk-toolbar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 14px;
        background: #f7f9fc;
        border: 1px solid #eef1f5;
        border-radius: 10px;
        padding: 10px 14px;
        margin-bottom: 16px;
    }

    .btn-select-toggle {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 16px;
        font-size: 13px;
        font-weight: 500;
        border-radius: 6px;
        border: 1px solid #0B1E3F;
        background: #fff;
        color: #0B1E3F;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .btn-select-toggle:hover { background: #eef1f5; }
    .btn-select-toggle.active { background: #0B1E3F; color: #fff; }

    .bulk-controls {
        display: none;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
        padding-left: 14px;
        border-left: 1px solid #dfe4ea;
    }
    .bulk-toolbar.select-mode .bulk-controls { display: flex; }

    .select-all-label {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        color: #4a4a4a;
        cursor: pointer;
        user-select: none;
        white-space: nowrap;
    }
    .select-all-label input { width: 15px; height: 15px; cursor: pointer; accent-color: #0B1E3F; }

    .bulk-status-select {
        font-size: 13px;
        padding: 6px 10px;
        border-radius: 6px;
        border: 1px solid #d6dbe3;
        color: #0B1E3F;
        background: #fff;
        min-width: 150px;
    }

    .btn-apply {
        padding: 7px 18px;
        font-size: 13px;
        font-weight: 500;
        border-radius: 6px;
        border: none;
        background: #0B1E3F;
        color: #fff;
        cursor: pointer;
        transition: opacity 0.15s ease;
    }
    .btn-apply:hover { opacity: 0.9; }

    .selected-count {
        font-size: 12.5px;
        color: #797979c5;
        font-weight: 500;
        white-space: nowrap;
    }

    /* ---- Table checkbox column, hidden until select mode is on ---- */
    .col-checkbox { display: none; width: 36px; text-align: center; }
    .col-checkbox.show { display: table-cell; }
    .row-checkbox { width: 15px; height: 15px; cursor: pointer; accent-color: #0B1E3F; }

    .leads-tables-wrap { overflow-x: auto; }
    .leads-tables { width: 100%; min-width: 1250px; }
    .leads-tables th, .leads-tables td { white-space: nowrap; }
    .col-order { min-width: 130px; }
</style>

<?php require_once('section/sidebar.php'); ?>
<div class="main-content">
    <div class="content-header">
        <div>
            <h1 class="main-h1">Leads</h1>
            <p class="current-date">Manage all sell-phone submissions</p>
        </div>
        <div class="admin-avatar">AD</div>
    </div>

    <form method="GET" action="leads.php" class="d-flex flex-wrap gap-2 align-items-center mb-4">
        <div class="input-group flex-nowrap" style="max-width:260px; height:30px; border-radius:2px;">
            <input type="text" name="search" class="form-control" placeholder="Search by name, phone, model, or address..."
                aria-label="Search" value="<?php echo htmlspecialchars($search); ?>">
        </div>

        <select name="brand" class="form-select form-select-sm" style="max-width:180px; margin-bottom:5px;" onchange="this.form.submit()">
            <option value="">All brands</option>
            <?php foreach ($brands as $b): ?>
                <option value="<?php echo htmlspecialchars($b); ?>" <?php echo ($brand_filter === $b) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($b); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="status" class="form-select form-select-sm" style="max-width:180px;" onchange="this.form.submit()">
            <option value="">All status</option>
            <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
            <option value="contacted" <?php echo $status_filter === 'contacted' ? 'selected' : ''; ?>>Contacted</option>
            <option value="completed" <?php echo $status_filter === 'completed' ? 'selected' : ''; ?>>Completed</option>
            <option value="cancelled" <?php echo $status_filter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
        </select>

        <button type="submit" class="btn btn-sm" style="background:#0B1E3F; color:#fff;">Filter</button>
        <?php if ($brand_filter !== '' || $status_filter !== '' || $search !== ''): ?>
            <a href="leads.php" class="btn btn-sm btn-secondary">Clear</a>
        <?php endif; ?>
    </form>

    <!-- Bulk select + status update bar -->
    <div class="bulk-toolbar" id="bulkToolbar">
        <button type="button" id="toggleSelectBtn" class="btn-select-toggle" onclick="toggleSelectMode()">
            <i class="fa-solid fa-square-check"></i> Select
        </button>

        <div id="bulkControls" class="bulk-controls">
            <label class="select-all-label">
                <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this)"> Select All
            </label>

            <select id="bulkStatusSelect" class="bulk-status-select">
                <option value="pending">Pending</option>
                <option value="contacted">Contacted</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>

            <button type="button" class="btn-apply" onclick="applyBulkStatus()">Apply</button>

            <span id="selectedCount" class="selected-count"></span>
        </div>
    </div>

    <!-- Details modals (one per lead: photos only) -->
    <?php foreach ($leads as $lead): ?>
        <?php $photos = $lead_photos[$lead['id']] ?? []; ?>
        <div class="modal fade" id="detailsModal<?php echo (int) $lead['id']; ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius:14px;">
                    <div class="modal-header" style="border-bottom:1px solid #eef1f5;">
                        <div>
                            <h1 class="modal-title fs-5" style="color:#0B1E3F; margin-bottom:2px;">
                                <?php echo htmlspecialchars($lead['name'] ?? ''); ?>
                            </h1>
                            <span style="font-size:12px; color:#797979c5;">Order #<?php echo htmlspecialchars($lead['order_number'] ?? '—'); ?></span>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <?php if (!empty($photos)): ?>
                            <p style="color:#797979c5; font-size:12.5px; margin-bottom:8px;">
                                Device Photo<?php echo count($photos) > 1 ? 's' : ''; ?>
                            </p>
                            <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(110px, 1fr)); gap:8px;">
                                <?php foreach ($photos as $photo): ?>
                                    <img src="../<?php echo htmlspecialchars($photo); ?>"
                                        style="width:100%; height:110px; object-fit:cover; border-radius:8px; background:#f7f9fc; cursor:pointer;"
                                        onclick="window.open(this.src, '_blank')">
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p style="color:#797979c5; font-size:13px; text-align:center; margin:12px 0;">No photos uploaded.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <div class="leads-tables-wrap">
        <table class="leads-tables">
            <thead>
                <tr>
                    <th class="col-checkbox"></th>
                    <th class="col-order">Order #</th>
                    <th>Name</th>
                    <th>Model</th>
                    <th>Storage</th>
                    <th>Price</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($leads)): ?>
                    <tr>
                        <td colspan="11" style="text-align:center; padding:24px; color:#797979c5;">No leads found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($leads as $lead): ?>
                        <?php $photos = $lead_photos[$lead['id']] ?? []; ?>
                        <tr>
                            <td class="col-checkbox">
                                <input type="checkbox" class="row-checkbox" value="<?php echo (int) $lead['id']; ?>" onchange="updateSelectedCount()">
                            </td>
                            <td class="col-order"><?php echo htmlspecialchars($lead['order_number'] ?? '—'); ?></td>
                            <td class="col-name">
                                <?php echo htmlspecialchars($lead['name'] ?? ''); ?>
                                <div>
                                    <a href="javascript:void(0)" class="view-details-link"
                                        data-bs-toggle="modal" data-bs-target="#detailsModal<?php echo (int) $lead['id']; ?>">
                                        View Details
                                        <?php if (!empty($photos)): ?>
                                            <span class="photo-badge"><i class="fa-solid fa-images"></i> <?php echo count($photos); ?></span>
                                        <?php endif; ?>
                                    </a>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($lead['model'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($lead['storage'] ?? ''); ?></td>
                            <td class="col-price">AED <?php echo number_format((float) ($lead['price'] ?? 0), 0); ?></td>
                            <td class="col-phone"><?php echo htmlspecialchars($lead['phone'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($lead['email'] ?? '—'); ?></td>
                            <td><?php echo htmlspecialchars($lead['address'] ?? '—'); ?></td>
                            <td><?php echo !empty($lead['created_at']) ? htmlspecialchars(date("M j", strtotime($lead['created_at']))) : ''; ?></td>
                            <td>
                                <?php $st = $lead['status'] ?? 'pending'; ?>
                                <span class="status-badge status-<?php echo htmlspecialchars($st); ?>" data-id="<?php echo (int) $lead['id']; ?>">
                                    <?php echo htmlspecialchars(ucfirst($st)); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($total_pages > 1): ?>
        <?php
        $query_params = array_filter([
            'search' => $search,
            'brand' => $brand_filter,
            'status' => $status_filter,
        ]);
        ?>
        <ul class="pagination pagination-sm justify-content-center mt-4">
            <?php for ($p = 1; $p <= $total_pages; $p++): ?>
                <?php $query_params['page'] = $p; ?>
                <li class="page-item <?php echo $p === $current_page ? 'active' : ''; ?>">
                    <a class="page-link"
                        style="<?php echo $p === $current_page ? 'background-color:#0B1E3F; border-color:#0B1E3F; color:white;' : 'color:#0B1E3F;'; ?>"
                        href="leads.php?<?php echo http_build_query($query_params); ?>">
                        <?php echo $p; ?>
                    </a>
                </li>
            <?php endfor; ?>
        </ul>
    <?php endif; ?>

</div>
</body>

</html>