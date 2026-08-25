<?php
include("include/db-connect.php");
include("include/auth-check.php");

$success_msg = "";
$error_msg = "";

$section_labels = [
    'about_story'      => ['label' => 'Our Story',        'icon' => 'fa-solid fa-book-open'],
    'about_mission'    => ['label' => 'Mission',          'icon' => 'fa-solid fa-bullseye'],
    'about_method'     => ['label' => 'Our Method',       'icon' => 'fa-solid fa-diagram-project'],
    'about_speciality' => ['label' => 'Speciality',       'icon' => 'fa-solid fa-star'],
];
$editable_sections = ['about_story', 'about_mission'];

// ---------- Update section content ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_section'])) {
    $id = (int) $_POST['section_id'];
    $kicker = trim($_POST['kicker'] ?? '');
    $heading = trim($_POST['heading'] ?? '');
    $heading_highlight = trim($_POST['heading_highlight'] ?? '');
    $description = trim($_POST['description'] ?? '');

    $stmt = mysqli_prepare($conn, "UPDATE home_sections SET kicker=?, heading=?, heading_highlight=?, description=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "ssssi", $kicker, $heading, $heading_highlight, $description, $id);

    if (mysqli_stmt_execute($stmt)) {
        $success_msg = "Section updated successfully.";
    } else {
        $error_msg = "Could not update section: " . mysqli_error($conn);
    }
}

// ---------- Add a stat card ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_item'])) {
    $section_id = (int) $_POST['section_id'];
    $icon = trim($_POST['icon'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $subtitle = trim($_POST['subtitle'] ?? '');
    $content = trim($_POST['content'] ?? '');

    $order_result = mysqli_query($conn, "SELECT COALESCE(MAX(sort_order),0)+1 AS next_order FROM home_section_items WHERE section_id = " . $section_id);
    $next_order = mysqli_fetch_assoc($order_result)['next_order'];

    $stmt = mysqli_prepare($conn, "INSERT INTO home_section_items (section_id, icon, title, subtitle, content, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "issssi", $section_id, $icon, $title, $subtitle, $content, $next_order);
    if (mysqli_stmt_execute($stmt)) {
        $success_msg = "Item added successfully.";
    } else {
        $error_msg = "Could not add item: " . mysqli_error($conn);
    }
}

// ---------- Edit a stat card ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_item'])) {
    $item_id = (int) $_POST['item_id'];
    $icon = trim($_POST['icon'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $subtitle = trim($_POST['subtitle'] ?? '');
    $content = trim($_POST['content'] ?? '');

    $stmt = mysqli_prepare($conn, "UPDATE home_section_items SET icon=?, title=?, subtitle=?, content=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "ssssi", $icon, $title, $subtitle, $content, $item_id);
    if (mysqli_stmt_execute($stmt)) {
        $success_msg = "Item updated successfully.";
    } else {
        $error_msg = "Could not update item: " . mysqli_error($conn);
    }
}

// ---------- Delete a stat card ----------
if (isset($_GET['delete_item'])) {
    $item_id = (int) $_GET['delete_item'];
    $redirect_section = $_GET['section'] ?? 'about_story';
    $stmt = mysqli_prepare($conn, "DELETE FROM home_section_items WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $item_id);
    mysqli_stmt_execute($stmt);
    header("Location: about-sections.php?section=" . urlencode($redirect_section) . "&deleted=1");
    exit;
}
require_once('include/a-header.php');
require_once('section/sidebar.php');

$sections_result = mysqli_query($conn, "SELECT * FROM home_sections");
$sections_by_key = [];
while ($row = mysqli_fetch_assoc($sections_result)) {
    $sections_by_key[$row['section_key']] = $row;
}

$active_key = $_GET['section'] ?? 'about_story';
if (!in_array($active_key, $editable_sections)) {
    $active_key = 'about_story';
}
$active_section = $sections_by_key[$active_key] ?? null;

$active_items = [];
if ($active_section) {
    $items_stmt = mysqli_prepare($conn, "SELECT * FROM home_section_items WHERE section_id = ? ORDER BY sort_order ASC");
    mysqli_stmt_bind_param($items_stmt, "i", $active_section['id']);
    mysqli_stmt_execute($items_stmt);
    $items_result = mysqli_stmt_get_result($items_stmt);
    while ($row = mysqli_fetch_assoc($items_result)) {
        $active_items[] = $row;
    }
}
?>

<div class="main-content">
    <div class="content-header">
        <div>
            <h1 class="main-h1">About Page</h1>
            <p class="current-date">Control the content shown on your website's About page</p>
        </div>
        <div class="admin-avatar">AD</div>
    </div>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success" style="border-radius:10px;">Item deleted successfully.</div>
    <?php endif; ?>
    <?php if ($success_msg): ?>
        <div class="alert alert-success" style="border-radius:10px;"><?php echo $success_msg; ?></div>
    <?php endif; ?>
    <?php if ($error_msg): ?>
        <div class="alert alert-danger" style="border-radius:10px;"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <!-- Section picker -->
    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 g-3 mb-4">
        <?php foreach ($section_labels as $key => $meta): ?>
            <?php $is_editable = in_array($key, $editable_sections); ?>
            <div class="col">
                <?php if ($is_editable): ?>
                    <a href="about-sections.php?section=<?php echo $key; ?>"
                       class="card h-100 text-decoration-none"
                       style="border-radius:14px; padding:16px; text-align:center; border:2px solid <?php echo $active_key === $key ? '#0B1E3F' : '#eee'; ?>;">
                        <i class="<?php echo $meta['icon']; ?>" style="font-size:22px; color:#0B1E3F;"></i>
                        <p style="margin:8px 0 0; font-weight:600; color:#0B1E3F; font-size:14px;"><?php echo $meta['label']; ?></p>
                    </a>
                <?php else: ?>
                    <div class="card h-100" style="border-radius:14px; padding:16px; text-align:center; opacity:0.55; border:2px dashed #ccc;">
                        <i class="<?php echo $meta['icon']; ?>" style="font-size:22px; color:#797979;"></i>
                        <p style="margin:8px 0 0; font-weight:600; color:#797979; font-size:14px;"><?php echo $meta['label']; ?></p>
                        <small style="color:#aaa;">Coming soon</small>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($active_key === 'about_story' && $active_section): ?>

        <div class="card" style="border-radius:14px; padding:24px; margin-bottom:24px;">
            <h5 style="margin-bottom:16px;"><i class="fa-solid fa-book-open me-2"></i>Our Story Section Content</h5>

            <form method="POST" action="about-sections.php?section=about_story">
                <input type="hidden" name="section_id" value="<?php echo (int) $active_section['id']; ?>">

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label" style="font-size:12.5px; color:#797979c5;">Kicker (small tag above heading)</label>
                        <input type="text" name="kicker" class="form-control" value="<?php echo htmlspecialchars($active_section['kicker']); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" style="font-size:12.5px; color:#797979c5;">Heading</label>
                        <input type="text" name="heading" class="form-control" value="<?php echo htmlspecialchars($active_section['heading']); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" style="font-size:12.5px; color:#797979c5;">Heading highlight (colored part)</label>
                        <input type="text" name="heading_highlight" class="form-control" value="<?php echo htmlspecialchars($active_section['heading_highlight']); ?>">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label" style="font-size:12.5px; color:#797979c5;">Description</label>
                        <textarea name="description" class="form-control" rows="2"><?php echo htmlspecialchars($active_section['description']); ?></textarea>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" name="update_section" class="btn" style="background:#0B1E3F; color:#fff;">Save Section Content</button>
                </div>
            </form>
        </div>

        <div class="card" style="border-radius:14px; padding:24px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 style="margin:0;"><i class="fa-solid fa-chart-simple me-2"></i>Stat Cards</h5>
                <button type="button" class="btn btn-sm" style="background:#0B1E3F; color:#fff;" data-bs-toggle="modal" data-bs-target="#addItemModal">
                    <i class="fa-solid fa-plus me-1"></i> Add Stat
                </button>
            </div>

            <div class="row row-cols-1 row-cols-sm-3 g-3">
                <?php foreach ($active_items as $item): ?>
                    <div class="col">
                        <div class="card h-100" style="border-radius:12px; padding:14px; text-align:center;">
                            <h6 style="margin:0; font-size:20px; color:#0B1E3F;"><?php echo htmlspecialchars($item['title']); ?></h6>
                            <small style="color:#797979;"><?php echo htmlspecialchars($item['subtitle']); ?></small>
                            <div class="d-flex justify-content-center gap-2 mt-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                    data-bs-toggle="modal" data-bs-target="#editItemModal"
                                    data-id="<?php echo (int) $item['id']; ?>"
                                    data-title="<?php echo htmlspecialchars($item['title']); ?>"
                                    data-subtitle="<?php echo htmlspecialchars($item['subtitle']); ?>">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <a href="about-sections.php?delete_item=<?php echo (int) $item['id']; ?>"
                                   class="btn btn-sm delete-btn" style="background:#fdeaea; color:#c0392b; border:none;"
                                   onclick="return confirm('Delete this stat card?');">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Add Item Modal -->
        <div class="modal fade" id="addItemModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius:14px;">
                    <form method="POST" action="about-sections.php?section=about_story">
                        <input type="hidden" name="section_id" value="<?php echo (int) $active_section['id']; ?>">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5"><i class="fa-solid fa-plus me-2"></i>Add stat card</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Number (e.g. 15,000+)</label>
                                    <input type="text" name="title" class="form-control" placeholder="e.g. 15,000+" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Label</label>
                                    <input type="text" name="subtitle" class="form-control" placeholder="e.g. Phones Purchased." required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="add_item" class="btn" style="background:#0B1E3F; color:#fff;">Add</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Item Modal -->
        <div class="modal fade" id="editItemModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius:14px;">
                    <form method="POST" action="about-sections.php?section=about_story">
                        <input type="hidden" name="item_id" id="editItemId" value="">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5"><i class="fa-solid fa-pen me-2"></i>Edit stat card</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Number</label>
                                    <input type="text" name="title" id="editItemTitle" class="form-control" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Label</label>
                                    <input type="text" name="subtitle" id="editItemSubtitle" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="edit_item" class="btn" style="background:#0B1E3F; color:#fff;">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var editModal = document.getElementById('editItemModal');
                editModal.addEventListener('show.bs.modal', function (event) {
                    var btn = event.relatedTarget;
                    document.getElementById('editItemId').value = btn.getAttribute('data-id');
                    document.getElementById('editItemTitle').value = btn.getAttribute('data-title');
                    document.getElementById('editItemSubtitle').value = btn.getAttribute('data-subtitle');
                });
            });
        </script>
    <?php elseif ($active_key === 'about_mission' && $active_section): ?>

        <div class="card" style="border-radius:14px; padding:24px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 style="margin:0;"><i class="fa-solid fa-bullseye me-2"></i>Mission / Vision / Values Cards</h5>
                <button type="button" class="btn btn-sm" style="background:#0B1E3F; color:#fff;" data-bs-toggle="modal" data-bs-target="#addItemModal">
                    <i class="fa-solid fa-plus me-1"></i> Add Card
                </button>
            </div>

            <div class="row row-cols-1 row-cols-sm-3 g-3">
                <?php foreach ($active_items as $item): ?>
                    <div class="col">
                        <div class="card h-100" style="border-radius:12px; padding:14px;">
                            <i class="<?php echo htmlspecialchars($item['icon']); ?>" style="font-size:20px; color:#0B1E3F;"></i>
                            <h6 style="margin:8px 0 4px;"><?php echo htmlspecialchars($item['title']); ?></h6>
                            <small style="color:#797979;"><?php echo htmlspecialchars($item['content']); ?></small>
                            <div class="d-flex gap-2 mt-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                    data-bs-toggle="modal" data-bs-target="#editItemModal"
                                    data-id="<?php echo (int) $item['id']; ?>"
                                    data-icon="<?php echo htmlspecialchars($item['icon']); ?>"
                                    data-title="<?php echo htmlspecialchars($item['title']); ?>"
                                    data-content="<?php echo htmlspecialchars($item['content']); ?>">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <a href="about-sections.php?section=about_mission&delete_item=<?php echo (int) $item['id']; ?>"
                                   class="btn btn-sm delete-btn" style="background:#fdeaea; color:#c0392b; border:none;"
                                   onclick="return confirm('Delete this card?');">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Add Item Modal -->
        <div class="modal fade" id="addItemModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius:14px;">
                    <form method="POST" action="about-sections.php?section=about_mission">
                        <input type="hidden" name="section_id" value="<?php echo (int) $active_section['id']; ?>">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5"><i class="fa-solid fa-plus me-2"></i>Add card</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Icon (Font Awesome class)</label>
                                    <input type="text" name="icon" class="form-control" placeholder="e.g. fa-solid fa-eye" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Title</label>
                                    <input type="text" name="title" class="form-control" placeholder="e.g. Our Vision" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Description</label>
                                    <textarea name="content" class="form-control" rows="3" required></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="add_item" class="btn" style="background:#0B1E3F; color:#fff;">Add</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Item Modal -->
        <div class="modal fade" id="editItemModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius:14px;">
                    <form method="POST" action="about-sections.php?section=about_mission">
                        <input type="hidden" name="item_id" id="editItemId" value="">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5"><i class="fa-solid fa-pen me-2"></i>Edit card</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Icon (Font Awesome class)</label>
                                    <input type="text" name="icon" id="editItemIcon" class="form-control" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Title</label>
                                    <input type="text" name="title" id="editItemTitle" class="form-control" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Description</label>
                                    <textarea name="content" id="editItemContent" class="form-control" rows="3" required></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="edit_item" class="btn" style="background:#0B1E3F; color:#fff;">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var editModal = document.getElementById('editItemModal');
                editModal.addEventListener('show.bs.modal', function (event) {
                    var btn = event.relatedTarget;
                    document.getElementById('editItemId').value = btn.getAttribute('data-id');
                    document.getElementById('editItemIcon').value = btn.getAttribute('data-icon');
                    document.getElementById('editItemTitle').value = btn.getAttribute('data-title');
                    document.getElementById('editItemContent').value = btn.getAttribute('data-content');
                });
            });
        </script>
    <?php endif; ?>

</div>
</body>
</html>