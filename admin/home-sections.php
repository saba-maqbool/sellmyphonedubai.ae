<?php
include("include/db-connect.php");
include("include/auth-check.php");

$upload_dir = "../imgs/";
$allowed_ext = ["jpg", "jpeg", "png", "webp"];
$success_msg = "";
$error_msg = "";
$section_labels = [
    'hero'         => ['label' => 'Hero Section',            'icon' => 'fa-solid fa-house-flag'],
    'brand'        => ['label' => 'Brand / Get Quote',        'icon' => 'fa-solid fa-mobile-screen-button'],
    'process'      => ['label' => 'How It Works',             'icon' => 'fa-solid fa-diagram-project'],
    'chooseus'     => ['label' => 'Why Choose Us',            'icon' => 'fa-solid fa-star'],
    'testimonials' => ['label' => 'Testimonials',             'icon' => 'fa-solid fa-comment-dots'],
    'faq'          => ['label' => 'FAQ',                      'icon' => 'fa-solid fa-circle-question'],
    'about_con'    => ['label' => 'About / CTA',              'icon' => 'fa-solid fa-circle-info'],
];
$editable_sections = ['hero', 'brand', 'process', 'chooseus', 'testimonials' ,'faq', 'about_con'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_section'])) {
    $id = (int) $_POST['section_id'];
    $kicker = trim($_POST['kicker'] ?? '');
    $heading = trim($_POST['heading'] ?? '');
    $heading_highlight = trim($_POST['heading_highlight'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $extra_1 = trim($_POST['extra_1'] ?? '');
    $extra_2 = trim($_POST['extra_2'] ?? '');

    $image_sql = "";
    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed_ext)) {
            $filename = uniqid('hero_') . '.' . $ext;
            $target_path = $upload_dir . $filename;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                $image_path = "imgs/" . $filename;
            }
        } else {
            $error_msg = "Only jpg, jpeg, png, webp images are allowed.";
        }
    }

    if ($error_msg === "") {
        if ($image_path) {
            $stmt = mysqli_prepare($conn, "UPDATE home_sections SET kicker=?, heading=?, heading_highlight=?, description=?, extra_1=?, extra_2=?, image=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, "sssssssi", $kicker, $heading, $heading_highlight, $description, $extra_1, $extra_2, $image_path, $id);
        } else {
            $stmt = mysqli_prepare($conn, "UPDATE home_sections SET kicker=?, heading=?, heading_highlight=?, description=?, extra_1=?, extra_2=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, "ssssssi", $kicker, $heading, $heading_highlight, $description, $extra_1, $extra_2, $id);
        }

        if (mysqli_stmt_execute($stmt)) {
            $success_msg = "Section updated successfully.";
        } else {
            $error_msg = "Could not update section: " . mysqli_error($conn);
        }
    }
}

// ---------- Add a repeatable item (e.g. hero feature box, brand card) ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_item'])) {
    $section_id = (int) $_POST['section_id'];
    $icon = trim($_POST['icon'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $subtitle = trim($_POST['subtitle'] ?? '');
    $image_path = null;

    // Card image (used by Brand cards, not needed for hero feature boxes)
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed_ext)) {
            $filename = uniqid('item_') . '.' . $ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $filename)) {
                $image_path = "imgs/" . $filename;
            }
        }
    }
    // Icon image upload (Brand cards use a small logo image instead of a
    // Font Awesome class) -- overrides whatever was typed in the icon field.
    if (isset($_FILES['icon_image']) && $_FILES['icon_image']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['icon_image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed_ext)) {
            $filename = uniqid('icon_') . '.' . $ext;
            if (move_uploaded_file($_FILES['icon_image']['tmp_name'], $upload_dir . $filename)) {
                $icon = "imgs/" . $filename;
            }
        }
    }
    $content = trim($_POST['content'] ?? '');

    $order_result = mysqli_query($conn, "SELECT COALESCE(MAX(sort_order),0)+1 AS next_order FROM home_section_items WHERE section_id = " . $section_id);
    $next_order = mysqli_fetch_assoc($order_result)['next_order'];

    $stmt = mysqli_prepare($conn, "INSERT INTO home_section_items (section_id, icon, image, title, subtitle, content, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "isssssi", $section_id, $icon, $image_path, $title, $subtitle, $content, $next_order);
    if (mysqli_stmt_execute($stmt)) {
        $success_msg = "Item added successfully.";
    } else {
        $error_msg = "Could not add item: " . mysqli_error($conn);
    }
}

// ---------- Edit a repeatable item ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_item'])) {
    $item_id = (int) $_POST['item_id'];
    $icon = trim($_POST['icon'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $subtitle = trim($_POST['subtitle'] ?? '');
    $image_path = null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed_ext)) {
            $filename = uniqid('item_') . '.' . $ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $filename)) {
                $image_path = "imgs/" . $filename;
            }
        }
    }
    if (isset($_FILES['icon_image']) && $_FILES['icon_image']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['icon_image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed_ext)) {
            $filename = uniqid('icon_') . '.' . $ext;
            if (move_uploaded_file($_FILES['icon_image']['tmp_name'], $upload_dir . $filename)) {
                $icon = "imgs/" . $filename;
            }
        }
    }

       $content = trim($_POST['content'] ?? '');

    if ($image_path) {
        $stmt = mysqli_prepare($conn, "UPDATE home_section_items SET icon=?, image=?, title=?, subtitle=?, content=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "sssssi", $icon, $image_path, $title, $subtitle, $content, $item_id);
    } else {
        $stmt = mysqli_prepare($conn, "UPDATE home_section_items SET icon=?, title=?, subtitle=?, content=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "ssssi", $icon, $title, $subtitle, $content, $item_id);
    }
    if (mysqli_stmt_execute($stmt)) {
        $success_msg = "Item updated successfully.";
    } else {
        $error_msg = "Could not update item: " . mysqli_error($conn);
    }
}

// ---------- Delete a repeatable item ----------
if (isset($_GET['delete_item'])) {
    $item_id = (int) $_GET['delete_item'];

    // Look up which section this item belongs to first, so we can
    // redirect back to the right tab (not always hero).
    $lookup = mysqli_prepare($conn, "SELECT hs.section_key FROM home_section_items hsi JOIN home_sections hs ON hs.id = hsi.section_id WHERE hsi.id = ?");
    mysqli_stmt_bind_param($lookup, "i", $item_id);
    mysqli_stmt_execute($lookup);
    $lookup_row = mysqli_fetch_assoc(mysqli_stmt_get_result($lookup));
    $redirect_key = $lookup_row['section_key'] ?? 'hero';

    $stmt = mysqli_prepare($conn, "DELETE FROM home_section_items WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $item_id);
    mysqli_stmt_execute($stmt);
    header("Location: home-sections.php?section=" . urlencode($redirect_key) . "&deleted=1");
    exit;
}

require_once('include/a-header.php');
require_once('section/sidebar.php');

// Load all sections
$sections_result = mysqli_query($conn, "SELECT * FROM home_sections");
$sections_by_key = [];
while ($row = mysqli_fetch_assoc($sections_result)) {
    $sections_by_key[$row['section_key']] = $row;
}

// Which section is open for editing (default hero)
$active_key = $_GET['section'] ?? 'hero';
if (!in_array($active_key, $editable_sections)) {
    $active_key = 'hero';
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
            <h1 class="main-h1">Home Page</h1>
            <p class="current-date">Control the content shown on your website's home page</p>
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

    <!-- Section picker: 7 home page sections -->
    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 g-3 mb-4">
        <?php foreach ($section_labels as $key => $meta): ?>
            <?php $is_editable = in_array($key, $editable_sections); ?>
            <div class="col">
                <?php if ($is_editable): ?>
                    <a href="home-sections.php?section=<?php echo $key; ?>"
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

    <?php if ($active_key === 'hero' && $active_section): ?>

        <!-- Hero content form -->
        <div class="card" style="border-radius:14px; padding:24px; margin-bottom:24px;">
            <h5 style="margin-bottom:16px;"><i class="fa-solid fa-house-flag me-2"></i>Hero Section Content</h5>

            <form method="POST" action="home-sections.php?section=hero" enctype="multipart/form-data">
                <input type="hidden" name="section_id" value="<?php echo (int) $active_section['id']; ?>">

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" style="font-size:12.5px; color:#797979c5;">Kicker (small line above heading)</label>
                        <input type="text" name="kicker" class="form-control" value="<?php echo htmlspecialchars($active_section['kicker']); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" style="font-size:12.5px; color:#797979c5;">Heading</label>
                        <input type="text" name="heading" class="form-control" value="<?php echo htmlspecialchars($active_section['heading']); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" style="font-size:12.5px; color:#797979c5;">Heading highlight (colored word)</label>
                        <input type="text" name="heading_highlight" class="form-control" value="<?php echo htmlspecialchars($active_section['heading_highlight']); ?>">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label" style="font-size:12.5px; color:#797979c5;">Description</label>
                        <textarea name="description" class="form-control" rows="2"><?php echo htmlspecialchars($active_section['description']); ?></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" style="font-size:12.5px; color:#797979c5;">Google rating score</label>
                        <input type="text" name="extra_1" class="form-control" value="<?php echo htmlspecialchars($active_section['extra_1']); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" style="font-size:12.5px; color:#797979c5;">Rating caption</label>
                        <input type="text" name="extra_2" class="form-control" value="<?php echo htmlspecialchars($active_section['extra_2']); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" style="font-size:12.5px; color:#797979c5;">Hero image (leave empty to keep current)</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                </div>

                <?php if (!empty($active_section['image'])): ?>
                    <img src="../<?php echo htmlspecialchars($active_section['image']); ?>" alt="Current hero image" style="max-height:80px; margin-top:14px; border-radius:8px;">
                <?php endif; ?>

                <div class="mt-4">
                    <button type="submit" name="update_section" class="btn" style="background:#0B1E3F; color:#fff;">Save Hero Content</button>
                </div>
            </form>
        </div>

        <!-- Hero feature items -->
        <div class="card" style="border-radius:14px; padding:24px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 style="margin:0;"><i class="fa-solid fa-list-check me-2"></i>Hero Feature Boxes</h5>
                <button type="button" class="btn btn-sm" style="background:#0B1E3F; color:#fff;" data-bs-toggle="modal" data-bs-target="#addItemModal">
                    <i class="fa-solid fa-plus me-1"></i> Add Feature
                </button>
            </div>

            <div class="row row-cols-1 row-cols-sm-3 g-3">
                <?php foreach ($active_items as $item): ?>
                    <div class="col">
                        <div class="card h-100" style="border-radius:12px; padding:14px; text-align:center;">
                            <i class="<?php echo htmlspecialchars($item['icon']); ?>" style="font-size:20px; color:#0B1E3F;"></i>
                            <h6 style="margin:8px 0 0;"><?php echo htmlspecialchars($item['title']); ?></h6>
                            <small style="color:#797979;"><?php echo htmlspecialchars($item['subtitle']); ?></small>
                            <div class="d-flex justify-content-center gap-2 mt-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                    data-bs-toggle="modal" data-bs-target="#editItemModal"
                                    data-id="<?php echo (int) $item['id']; ?>"
                                    data-icon="<?php echo htmlspecialchars($item['icon']); ?>"
                                    data-title="<?php echo htmlspecialchars($item['title']); ?>"
                                    data-subtitle="<?php echo htmlspecialchars($item['subtitle']); ?>">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <a href="home-sections.php?delete_item=<?php echo (int) $item['id']; ?>"
                                   class="btn btn-sm delete-btn" style="background:#fdeaea; color:#c0392b; border:none;"
                                   onclick="return confirm('Delete this feature box?');">
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
                    <form method="POST" action="home-sections.php?section=hero">
                        <input type="hidden" name="section_id" value="<?php echo (int) $active_section['id']; ?>">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5"><i class="fa-solid fa-plus me-2"></i>Add feature box</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Icon (Font Awesome class)</label>
                                    <input type="text" name="icon" class="form-control" placeholder="e.g. fa-solid fa-hand-holding-dollar" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Title</label>
                                    <input type="text" name="title" class="form-control" placeholder="e.g. Best Prices" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Subtitle</label>
                                    <input type="text" name="subtitle" class="form-control" placeholder="e.g. Guaranteed">
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
                    <form method="POST" action="home-sections.php?section=hero">
                        <input type="hidden" name="item_id" id="editItemId" value="">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5"><i class="fa-solid fa-pen me-2"></i>Edit feature box</h1>
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
                                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Subtitle</label>
                                    <input type="text" name="subtitle" id="editItemSubtitle" class="form-control">
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
                    document.getElementById('editItemSubtitle').value = btn.getAttribute('data-subtitle');
                });
            });
        </script>

    <?php elseif ($active_key === 'brand' && $active_section): ?>

        <!-- Brand section content form -->
        <div class="card" style="border-radius:14px; padding:24px; margin-bottom:24px;">
            <h5 style="margin-bottom:16px;"><i class="fa-solid fa-mobile-screen-button me-2"></i>Brand Section Content</h5>

            <form method="POST" action="home-sections.php?section=brand">
                <input type="hidden" name="section_id" value="<?php echo (int) $active_section['id']; ?>">

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label" style="font-size:12.5px; color:#797979c5;">Kicker (small line above heading)</label>
                        <input type="text" name="kicker" class="form-control" value="<?php echo htmlspecialchars($active_section['kicker']); ?>">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label" style="font-size:12.5px; color:#797979c5;">
                            Heading &mdash; wrap the highlighted word in &lt;span&gt;&lt;/span&gt;, e.g. Which &lt;span&gt;brand&lt;/span&gt; is your phone
                        </label>
                        <input type="text" name="heading" class="form-control" value="<?php echo htmlspecialchars($active_section['heading']); ?>">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label" style="font-size:12.5px; color:#797979c5;">Trust banner text (below the cards)</label>
                        <textarea name="description" class="form-control" rows="2"><?php echo htmlspecialchars($active_section['description']); ?></textarea>
                    </div>
                    <input type="hidden" name="heading_highlight" value="">
                    <input type="hidden" name="extra_1" value="<?php echo htmlspecialchars($active_section['extra_1'] ?? ''); ?>">
                    <input type="hidden" name="extra_2" value="<?php echo htmlspecialchars($active_section['extra_2'] ?? ''); ?>">
                </div>

                <div class="mt-4">
                    <button type="submit" name="update_section" class="btn" style="background:#0B1E3F; color:#fff;">Save Brand Content</button>
                </div>
            </form>
        </div>

        <!-- Brand cards -->
        <div class="card" style="border-radius:14px; padding:24px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 style="margin:0;"><i class="fa-solid fa-id-card me-2"></i>Brand Cards</h5>
                <button type="button" class="btn btn-sm" style="background:#0B1E3F; color:#fff;" data-bs-toggle="modal" data-bs-target="#addItemModal">
                    <i class="fa-solid fa-plus me-1"></i> Add Card
                </button>
            </div>

            <div class="row row-cols-1 row-cols-sm-2 g-3">
                <?php foreach ($active_items as $item): ?>
                    <div class="col">
                        <div class="card h-100" style="border-radius:12px; padding:14px; text-align:center;">
                            <?php if (!empty($item['image'])): ?>
                                <img src="../<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" style="height:90px; object-fit:contain; margin:0 auto;">
                            <?php endif; ?>
                            <?php if (!empty($item['icon'])): ?>
                                <img src="../<?php echo htmlspecialchars($item['icon']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?> icon" style="height:28px; object-fit:contain; margin:8px auto 0;">
                            <?php endif; ?>
                            <h6 style="margin:8px 0 0;"><?php echo htmlspecialchars($item['title']); ?></h6>
                            <small style="color:#797979;"><?php echo htmlspecialchars($item['subtitle']); ?></small>
                            <div class="d-flex justify-content-center gap-2 mt-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                    data-bs-toggle="modal" data-bs-target="#editItemModal"
                                    data-id="<?php echo (int) $item['id']; ?>"
                                    data-icon="<?php echo htmlspecialchars($item['icon']); ?>"
                                    data-title="<?php echo htmlspecialchars($item['title']); ?>"
                                    data-subtitle="<?php echo htmlspecialchars($item['subtitle']); ?>">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <a href="home-sections.php?delete_item=<?php echo (int) $item['id']; ?>"
                                   class="btn btn-sm delete-btn" style="background:#fdeaea; color:#c0392b; border:none;"
                                   onclick="return confirm('Delete this brand card?');">
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
                    <form method="POST" action="home-sections.php?section=brand" enctype="multipart/form-data">
                        <input type="hidden" name="section_id" value="<?php echo (int) $active_section['id']; ?>">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5"><i class="fa-solid fa-plus me-2"></i>Add brand card</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Title (brand name)</label>
                                    <input type="text" name="title" class="form-control" placeholder="e.g. Apple" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Subtitle</label>
                                    <input type="text" name="subtitle" class="form-control" placeholder="e.g. iPhone &bull; Pro series">
                                </div>
                                <div class="col-12">
                                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Card image (large device photo)</label>
                                    <input type="file" name="image" class="form-control" accept="image/*">
                                </div>
                                <div class="col-12">
                                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Small brand icon</label>
                                    <input type="file" name="icon_image" class="form-control" accept="image/*">
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
                    <form method="POST" action="home-sections.php?section=brand" enctype="multipart/form-data">
                        <input type="hidden" name="item_id" id="editItemId" value="">
                        <input type="hidden" name="icon" id="editItemIcon" value="">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5"><i class="fa-solid fa-pen me-2"></i>Edit brand card</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Title (brand name)</label>
                                    <input type="text" name="title" id="editItemTitle" class="form-control" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Subtitle</label>
                                    <input type="text" name="subtitle" id="editItemSubtitle" class="form-control">
                                </div>
                                <div class="col-12">
                                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Card image (leave empty to keep current)</label>
                                    <input type="file" name="image" class="form-control" accept="image/*">
                                </div>
                                <div class="col-12">
                                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Small brand icon (leave empty to keep current)</label>
                                    <input type="file" name="icon_image" class="form-control" accept="image/*">
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
                    document.getElementById('editItemSubtitle').value = btn.getAttribute('data-subtitle');
                });
            });
        </script>
    <?php elseif ($active_key === 'process' && $active_section): ?>

    <div class="card" style="border-radius:14px; padding:24px; margin-bottom:24px;">
        <h5 style="margin-bottom:16px;"><i class="fa-solid fa-diagram-project me-2"></i>How It Works — Section Content</h5>

        <form method="POST" action="home-sections.php?section=process">
            <input type="hidden" name="section_id" value="<?php echo (int) $active_section['id']; ?>">

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Kicker (small line above heading)</label>
                    <input type="text" name="kicker" class="form-control" value="<?php echo htmlspecialchars($active_section['kicker']); ?>">
                </div>
                <div class="col-md-8">
                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">
                        Heading &mdash; wrap the highlighted word in &lt;span&gt;&lt;/span&gt;, e.g. Sell Your Phone in &lt;span&gt;3&lt;/span&gt; Easy Steps
                    </label>
                    <input type="text" name="heading" class="form-control" value="<?php echo htmlspecialchars($active_section['heading']); ?>">
                </div>
                <div class="col-md-12">
                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Subtitle text</label>
                    <textarea name="description" class="form-control" rows="2"><?php echo htmlspecialchars($active_section['description']); ?></textarea>
                </div>
                <input type="hidden" name="heading_highlight" value="">
                <input type="hidden" name="extra_1" value="<?php echo htmlspecialchars($active_section['extra_1'] ?? ''); ?>">
                <input type="hidden" name="extra_2" value="<?php echo htmlspecialchars($active_section['extra_2'] ?? ''); ?>">
            </div>

            <div class="mt-4">
                <button type="submit" name="update_section" class="btn" style="background:#0B1E3F; color:#fff;">Save Section Content</button>
            </div>
        </form>
    </div>

    <div class="card" style="border-radius:14px; padding:24px;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 style="margin:0;"><i class="fa-solid fa-list-ol me-2"></i>Process Steps</h5>
            <button type="button" class="btn btn-sm" style="background:#0B1E3F; color:#fff;" data-bs-toggle="modal" data-bs-target="#addItemModal">
                <i class="fa-solid fa-plus me-1"></i> Add Step
            </button>
        </div>

        <div class="row row-cols-1 row-cols-sm-3 g-3">
            <?php foreach ($active_items as $index => $item): ?>
                <div class="col">
                    <div class="card h-100" style="border-radius:12px; padding:14px; text-align:center;">
                        <span style="font-size:12px; color:#797979;">Step <?php echo $index + 1; ?></span>
                        <i class="<?php echo htmlspecialchars($item['icon']); ?>" style="font-size:20px; color:#0B1E3F; margin-top:6px;"></i>
                        <h6 style="margin:8px 0 0;"><?php echo htmlspecialchars($item['title']); ?></h6>
                        <small style="color:#797979;"><?php echo htmlspecialchars($item['subtitle']); ?></small>
                        <div class="d-flex justify-content-center gap-2 mt-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                data-bs-toggle="modal" data-bs-target="#editItemModal"
                                data-id="<?php echo (int) $item['id']; ?>"
                                data-icon="<?php echo htmlspecialchars($item['icon']); ?>"
                                data-title="<?php echo htmlspecialchars($item['title']); ?>"
                                data-subtitle="<?php echo htmlspecialchars($item['subtitle']); ?>">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <a href="home-sections.php?delete_item=<?php echo (int) $item['id']; ?>"
                               class="btn btn-sm delete-btn" style="background:#fdeaea; color:#c0392b; border:none;"
                               onclick="return confirm('Delete this step?');">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="modal fade" id="addItemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:14px;">
                <form method="POST" action="home-sections.php?section=process">
                    <input type="hidden" name="section_id" value="<?php echo (int) $active_section['id']; ?>">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5"><i class="fa-solid fa-plus me-2"></i>Add process step</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Icon (Font Awesome class)</label>
                                <input type="text" name="icon" class="form-control" placeholder="e.g. fa-solid fa-truck-fast" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Title</label>
                                <input type="text" name="title" class="form-control" placeholder="e.g. Schedule Free Pickup" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Description</label>
                                <textarea name="subtitle" class="form-control" rows="3" placeholder="e.g. Choose your preferred time slot..."></textarea>
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

    <div class="modal fade" id="editItemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:14px;">
                <form method="POST" action="home-sections.php?section=process">
                    <input type="hidden" name="item_id" id="editItemId" value="">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5"><i class="fa-solid fa-pen me-2"></i>Edit process step</h1>
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
                                <textarea name="subtitle" id="editItemSubtitle" class="form-control" rows="3"></textarea>
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
                document.getElementById('editItemSubtitle').value = btn.getAttribute('data-subtitle');
            });
        });
    </script>
    <?php elseif ($active_key === 'chooseus' && $active_section): ?>

    <div class="card" style="border-radius:14px; padding:24px; margin-bottom:24px;">
        <h5 style="margin-bottom:16px;"><i class="fa-solid fa-star me-2"></i>Why Choose Us — Section Content</h5>

        <form method="POST" action="home-sections.php?section=chooseus" enctype="multipart/form-data">
            <input type="hidden" name="section_id" value="<?php echo (int) $active_section['id']; ?>">

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Kicker (small line above heading)</label>
                    <input type="text" name="kicker" class="form-control" value="<?php echo htmlspecialchars($active_section['kicker']); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">
                        Heading &mdash; wrap the highlighted word in &lt;span&gt;&lt;/span&gt;, e.g. Why Choose &lt;span&gt;SellMyPhoneDubai&lt;/span&gt;
                    </label>
                    <input type="text" name="heading" class="form-control" value="<?php echo htmlspecialchars($active_section['heading']); ?>">
                </div>
                <div class="col-md-12">
                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Subtitle text</label>
                    <textarea name="description" class="form-control" rows="2"><?php echo htmlspecialchars($active_section['description']); ?></textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Rating score</label>
                    <input type="text" name="extra_1" class="form-control" value="<?php echo htmlspecialchars($active_section['extra_1']); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Rating caption</label>
                    <input type="text" name="extra_2" class="form-control" value="<?php echo htmlspecialchars($active_section['extra_2']); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Visual image (leave empty to keep current)</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>
            </div>

            <?php if (!empty($active_section['image'])): ?>
                <img src="../<?php echo htmlspecialchars($active_section['image']); ?>" alt="Current visual image" style="max-height:80px; margin-top:14px; border-radius:8px;">
            <?php endif; ?>

            <div class="mt-4">
                <button type="submit" name="update_section" class="btn" style="background:#0B1E3F; color:#fff;">Save Section Content</button>
            </div>
        </form>
    </div>

    <div class="card" style="border-radius:14px; padding:24px;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 style="margin:0;"><i class="fa-solid fa-list-check me-2"></i>Feature Rows</h5>
            <button type="button" class="btn btn-sm" style="background:#0B1E3F; color:#fff;" data-bs-toggle="modal" data-bs-target="#addItemModal">
                <i class="fa-solid fa-plus me-1"></i> Add Feature
            </button>
        </div>

        <div class="row row-cols-1 row-cols-sm-3 g-3">
            <?php foreach ($active_items as $item): ?>
                <div class="col">
                    <div class="card h-100" style="border-radius:12px; padding:14px; text-align:center;">
                        <i class="<?php echo htmlspecialchars($item['icon']); ?>" style="font-size:20px; color:#0B1E3F;"></i>
                        <h6 style="margin:8px 0 0;"><?php echo htmlspecialchars($item['title']); ?></h6>
                        <small style="color:#797979;"><?php echo htmlspecialchars($item['subtitle']); ?></small>
                        <div class="d-flex justify-content-center gap-2 mt-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                data-bs-toggle="modal" data-bs-target="#editItemModal"
                                data-id="<?php echo (int) $item['id']; ?>"
                                data-icon="<?php echo htmlspecialchars($item['icon']); ?>"
                                data-title="<?php echo htmlspecialchars($item['title']); ?>"
                                data-subtitle="<?php echo htmlspecialchars($item['subtitle']); ?>">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <a href="home-sections.php?delete_item=<?php echo (int) $item['id']; ?>"
                               class="btn btn-sm delete-btn" style="background:#fdeaea; color:#c0392b; border:none;"
                               onclick="return confirm('Delete this feature?');">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="modal fade" id="addItemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:14px;">
                <form method="POST" action="home-sections.php?section=chooseus">
                    <input type="hidden" name="section_id" value="<?php echo (int) $active_section['id']; ?>">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5"><i class="fa-solid fa-plus me-2"></i>Add feature row</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Icon (Font Awesome class)</label>
                                <input type="text" name="icon" class="form-control" placeholder="e.g. fas fa-award" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Title</label>
                                <input type="text" name="title" class="form-control" placeholder="e.g. Best Price Guarantee" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Description</label>
                                <textarea name="subtitle" class="form-control" rows="3" placeholder="e.g. Get the best price for your phone..."></textarea>
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

    <div class="modal fade" id="editItemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:14px;">
                <form method="POST" action="home-sections.php?section=chooseus">
                    <input type="hidden" name="item_id" id="editItemId" value="">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5"><i class="fa-solid fa-pen me-2"></i>Edit feature row</h1>
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
                                <textarea name="subtitle" id="editItemSubtitle" class="form-control" rows="3"></textarea>
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
                document.getElementById('editItemSubtitle').value = btn.getAttribute('data-subtitle');
            });
        });
    </script>
<?php elseif ($active_key === 'testimonials' && $active_section): ?>

    <div class="card" style="border-radius:14px; padding:24px; margin-bottom:24px;">
        <h5 style="margin-bottom:16px;"><i class="fa-solid fa-comment-dots me-2"></i>Testimonials — Section Content</h5>

        <form method="POST" action="home-sections.php?section=testimonials">
            <input type="hidden" name="section_id" value="<?php echo (int) $active_section['id']; ?>">

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Kicker (small line above heading)</label>
                    <input type="text" name="kicker" class="form-control" value="<?php echo htmlspecialchars($active_section['kicker']); ?>">
                </div>
                <div class="col-md-8">
                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Heading</label>
                    <input type="text" name="heading" class="form-control" value="<?php echo htmlspecialchars($active_section['heading']); ?>">
                </div>
                <div class="col-md-12">
                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Subtitle text</label>
                    <textarea name="description" class="form-control" rows="2"><?php echo htmlspecialchars($active_section['description']); ?></textarea>
                </div>
                <input type="hidden" name="heading_highlight" value="">
                <input type="hidden" name="extra_1" value="<?php echo htmlspecialchars($active_section['extra_1'] ?? ''); ?>">
                <input type="hidden" name="extra_2" value="<?php echo htmlspecialchars($active_section['extra_2'] ?? ''); ?>">
            </div>

            <div class="mt-4">
                <button type="submit" name="update_section" class="btn" style="background:#0B1E3F; color:#fff;">Save Section Content</button>
            </div>
        </form>
    </div>

    <div class="card" style="border-radius:14px; padding:24px;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 style="margin:0;"><i class="fa-solid fa-quote-left me-2"></i>Testimonials</h5>
            <button type="button" class="btn btn-sm" style="background:#0B1E3F; color:#fff;" data-bs-toggle="modal" data-bs-target="#addItemModal">
                <i class="fa-solid fa-plus me-1"></i> Add Testimonial
            </button>
        </div>

        <div class="row row-cols-1 row-cols-sm-3 g-3">
            <?php foreach ($active_items as $item): ?>
                <div class="col">
                    <div class="card h-100" style="border-radius:12px; padding:14px;">
                        <small style="color:#797979;">Rating: <?php echo htmlspecialchars($item['icon']); ?>/5</small>
                        <h6 style="margin:6px 0 0;"><?php echo htmlspecialchars($item['title']); ?></h6>
                        <small style="color:#797979;"><?php echo htmlspecialchars($item['subtitle']); ?></small>
                        <p style="font-size:13px; margin-top:8px;"><?php echo htmlspecialchars($item['content']); ?></p>
                        <div class="d-flex justify-content-center gap-2 mt-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                data-bs-toggle="modal" data-bs-target="#editItemModal"
                                data-id="<?php echo (int) $item['id']; ?>"
                                data-icon="<?php echo htmlspecialchars($item['icon']); ?>"
                                data-title="<?php echo htmlspecialchars($item['title']); ?>"
                                data-subtitle="<?php echo htmlspecialchars($item['subtitle']); ?>"
                                data-content="<?php echo htmlspecialchars($item['content']); ?>">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <a href="home-sections.php?delete_item=<?php echo (int) $item['id']; ?>"
                               class="btn btn-sm delete-btn" style="background:#fdeaea; color:#c0392b; border:none;"
                               onclick="return confirm('Delete this testimonial?');">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="modal fade" id="addItemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:14px;">
                <form method="POST" action="home-sections.php?section=testimonials">
                    <input type="hidden" name="section_id" value="<?php echo (int) $active_section['id']; ?>">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5"><i class="fa-solid fa-plus me-2"></i>Add testimonial</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Rating (1-5, e.g. 4.5)</label>
                                <input type="text" name="icon" class="form-control" placeholder="e.g. 5" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Customer name</label>
                                <input type="text" name="title" class="form-control" placeholder="e.g. Fatima Al Zaabi" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Location</label>
                                <input type="text" name="subtitle" class="form-control" placeholder="e.g. Dubai Marina, Dubai">
                            </div>
                            <div class="col-12">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Review text</label>
                                <textarea name="content" class="form-control" rows="3" placeholder="e.g. Sold my Samsung S24 Ultra here..."></textarea>
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

    <div class="modal fade" id="editItemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:14px;">
                <form method="POST" action="home-sections.php?section=testimonials">
                    <input type="hidden" name="item_id" id="editItemId" value="">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5"><i class="fa-solid fa-pen me-2"></i>Edit testimonial</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Rating (1-5, e.g. 4.5)</label>
                                <input type="text" name="icon" id="editItemIcon" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Customer name</label>
                                <input type="text" name="title" id="editItemTitle" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Location</label>
                                <input type="text" name="subtitle" id="editItemSubtitle" class="form-control">
                            </div>
                            <div class="col-12">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Review text</label>
                                <textarea name="content" id="editItemContent" class="form-control" rows="3"></textarea>
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
                document.getElementById('editItemSubtitle').value = btn.getAttribute('data-subtitle');
                document.getElementById('editItemContent').value = btn.getAttribute('data-content');
            });
        });
    </script>
    
               <?php elseif ($active_key === 'faq' && $active_section): ?>

<div class="card" style="border-radius:14px; padding:24px; margin-bottom:24px;">
    <h5 style="margin-bottom:16px;"><i class="fa-solid fa-circle-question me-2"></i>FAQ — Section Content</h5>

    <form method="POST" action="home-sections.php?section=faq">
        <input type="hidden" name="section_id" value="<?php echo (int) $active_section['id']; ?>">

        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Kicker (small line above heading)</label>
                <input type="text" name="kicker" class="form-control" value="<?php echo htmlspecialchars($active_section['kicker']); ?>">
            </div>
            <div class="col-md-8">
                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Heading</label>
                <input type="text" name="heading" class="form-control" value="<?php echo htmlspecialchars($active_section['heading']); ?>">
            </div>
            <div class="col-md-12">
                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Subtitle text</label>
                <textarea name="description" class="form-control" rows="2"><?php echo htmlspecialchars($active_section['description']); ?></textarea>
            </div>
            <input type="hidden" name="heading_highlight" value="">
            <input type="hidden" name="extra_1" value="<?php echo htmlspecialchars($active_section['extra_1'] ?? ''); ?>">
            <input type="hidden" name="extra_2" value="<?php echo htmlspecialchars($active_section['extra_2'] ?? ''); ?>">
        </div>

        <div class="mt-4">
            <button type="submit" name="update_section" class="btn" style="background:#0B1E3F; color:#fff;">Save Section Content</button>
        </div>
    </form>
</div>

<div class="card" style="border-radius:14px; padding:24px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 style="margin:0;"><i class="fa-solid fa-list-ul me-2"></i>FAQ Questions</h5>
        <button type="button" class="btn btn-sm" style="background:#0B1E3F; color:#fff;" data-bs-toggle="modal" data-bs-target="#addItemModal">
            <i class="fa-solid fa-plus me-1"></i> Add Question
        </button>
    </div>

    <div class="row row-cols-1 g-3">
        <?php foreach ($active_items as $item): ?>
            <div class="col">
                <div class="card h-100" style="border-radius:12px; padding:14px 18px;">
                    <h6 style="margin:0 0 6px;"><?php echo htmlspecialchars($item['title']); ?></h6>
                    <small style="color:#797979;"><?php echo htmlspecialchars($item['subtitle']); ?></small>
                    <div class="d-flex justify-content-end gap-2 mt-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary"
                            data-bs-toggle="modal" data-bs-target="#editItemModal"
                            data-id="<?php echo (int) $item['id']; ?>"
                            data-title="<?php echo htmlspecialchars($item['title']); ?>"
                            data-subtitle="<?php echo htmlspecialchars($item['subtitle']); ?>">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <a href="home-sections.php?delete_item=<?php echo (int) $item['id']; ?>"
                           class="btn btn-sm delete-btn" style="background:#fdeaea; color:#c0392b; border:none;"
                           onclick="return confirm('Delete this question?');">
                            <i class="fa-solid fa-trash"></i>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="modal fade" id="addItemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px;">
            <form method="POST" action="home-sections.php?section=faq">
                <input type="hidden" name="section_id" value="<?php echo (int) $active_section['id']; ?>">
                <input type="hidden" name="icon" value="">
                <input type="hidden" name="content" value="">
                <div class="modal-header">
                    <h1 class="modal-title fs-5"><i class="fa-solid fa-plus me-2"></i>Add FAQ question</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label" style="font-size:12.5px; color:#797979c5;">Question</label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. How do I sell my phone in Dubai?" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label" style="font-size:12.5px; color:#797979c5;">Answer</label>
                            <textarea name="subtitle" class="form-control" rows="3" placeholder="Write the answer here..." required></textarea>
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

<div class="modal fade" id="editItemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px;">
            <form method="POST" action="home-sections.php?section=faq">
                <input type="hidden" name="item_id" id="editItemId" value="">
                <input type="hidden" name="icon" value="">
                <input type="hidden" name="content" value="">
                <div class="modal-header">
                    <h1 class="modal-title fs-5"><i class="fa-solid fa-pen me-2"></i>Edit FAQ question</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label" style="font-size:12.5px; color:#797979c5;">Question</label>
                            <input type="text" name="title" id="editItemTitle" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label" style="font-size:12.5px; color:#797979c5;">Answer</label>
                            <textarea name="subtitle" id="editItemSubtitle" class="form-control" rows="3" required></textarea>
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
<?php elseif ($active_key === 'about_con' && $active_section): ?>

    <div class="card" style="border-radius:14px; padding:24px; margin-bottom:24px;">
        <h5 style="margin-bottom:16px;"><i class="fa-solid fa-circle-info me-2"></i>About / CTA — Section Content</h5>

        <form method="POST" action="home-sections.php?section=about_con" enctype="multipart/form-data">
            <input type="hidden" name="section_id" value="<?php echo (int) $active_section['id']; ?>">

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Kicker (small line above heading)</label>
                    <input type="text" name="kicker" class="form-control" value="<?php echo htmlspecialchars($active_section['kicker']); ?>">
                </div>
                <div class="col-md-8">
                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">
                        Heading &mdash; use &lt;br&gt; for a line break, e.g. Ready to Sell Your Phone with &lt;br&gt; Dubai's
                    </label>
                    <input type="text" name="heading" class="form-control" value="<?php echo htmlspecialchars($active_section['heading']); ?>">
                </div>
                <div class="col-md-12">
                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Heading highlight (gold colored part)</label>
                    <input type="text" name="heading_highlight" class="form-control" value="<?php echo htmlspecialchars($active_section['heading_highlight']); ?>">
                </div>
                <div class="col-md-12">
                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Description</label>
                    <textarea name="description" class="form-control" rows="2"><?php echo htmlspecialchars($active_section['description']); ?></textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Final CTA bar heading</label>
                    <input type="text" name="extra_1" class="form-control" value="<?php echo htmlspecialchars($active_section['extra_1']); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Final CTA bar subtext</label>
                    <input type="text" name="extra_2" class="form-control" value="<?php echo htmlspecialchars($active_section['extra_2']); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Final CTA bar image (leave empty to keep current)</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>
            </div>

            <?php if (!empty($active_section['image'])): ?>
                <img src="../<?php echo htmlspecialchars($active_section['image']); ?>" alt="Current CTA image" style="max-height:80px; margin-top:14px; border-radius:8px;">
            <?php endif; ?>

            <div class="mt-4">
                <button type="submit" name="update_section" class="btn" style="background:#0B1E3F; color:#fff;">Save Section Content</button>
            </div>
        </form>
    </div>

    <div class="card" style="border-radius:14px; padding:24px;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 style="margin:0;"><i class="fa-solid fa-shield-halved me-2"></i>Trust Badges</h5>
            <button type="button" class="btn btn-sm" style="background:#0B1E3F; color:#fff;" data-bs-toggle="modal" data-bs-target="#addItemModal">
                <i class="fa-solid fa-plus me-1"></i> Add Badge
            </button>
        </div>

        <div class="row row-cols-1 row-cols-sm-3 g-3">
            <?php foreach ($active_items as $item): ?>
                <div class="col">
                    <div class="card h-100" style="border-radius:12px; padding:14px; text-align:center;">
                        <i class="<?php echo htmlspecialchars($item['icon']); ?>" style="font-size:20px; color:#0B1E3F;"></i>
                        <h6 style="margin:8px 0 0;"><?php echo htmlspecialchars($item['title']); ?></h6>
                        <div class="d-flex justify-content-center gap-2 mt-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                data-bs-toggle="modal" data-bs-target="#editItemModal"
                                data-id="<?php echo (int) $item['id']; ?>"
                                data-icon="<?php echo htmlspecialchars($item['icon']); ?>"
                                data-title="<?php echo htmlspecialchars($item['title']); ?>">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <a href="home-sections.php?delete_item=<?php echo (int) $item['id']; ?>"
                               class="btn btn-sm delete-btn" style="background:#fdeaea; color:#c0392b; border:none;"
                               onclick="return confirm('Delete this badge?');">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="modal fade" id="addItemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:14px;">
                <form method="POST" action="home-sections.php?section=about_con">
                    <input type="hidden" name="section_id" value="<?php echo (int) $active_section['id']; ?>">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5"><i class="fa-solid fa-plus me-2"></i>Add trust badge</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Icon (Font Awesome class)</label>
                                <input type="text" name="icon" class="form-control" placeholder="e.g. fa-solid fa-truck-fast" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Badge text</label>
                                <input type="text" name="title" class="form-control" placeholder="e.g. Same Day Pickup" required>
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

    <div class="modal fade" id="editItemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:14px;">
                <form method="POST" action="home-sections.php?section=about_con">
                    <input type="hidden" name="item_id" id="editItemId" value="">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5"><i class="fa-solid fa-pen me-2"></i>Edit trust badge</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Icon (Font Awesome class)</label>
                                <input type="text" name="icon" id="editItemIcon" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Badge text</label>
                                <input type="text" name="title" id="editItemTitle" class="form-control" required>
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
            });
        });
    </script>

<?php endif; ?>


</div>
</body>

</html>