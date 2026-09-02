<?php
include("include/db-connect.php");
include("include/auth-check.php");

$success_msg = "";
$error_msg = "";

// Slugs already used by the front-end router (index.php) — a custom page
// can never use one of these, or it would silently hijack that URL.
$reserved_slugs = ['home', 'about', 'apple', 'blog', 'blog-details', 'testimonials', 'contact', 'admin', '404', 'index'];

function make_slug($text) {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

// ---------- ADD PAGE ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_page'])) {
    $title             = trim($_POST['title'] ?? '');
    $slug              = trim($_POST['slug'] ?? '');
    $content           = $_POST['content'] ?? '';
    $status            = ($_POST['status'] ?? 'draft') === 'published' ? 'published' : 'draft';
    $meta_title        = trim($_POST['meta_title'] ?? '');
    $meta_description  = trim($_POST['meta_description'] ?? '');
    $meta_keywords     = trim($_POST['meta_keywords'] ?? '');
    $meta_robots       = trim($_POST['meta_robots'] ?? '') ?: 'index, follow';
    $canonical_url     = trim($_POST['canonical_url'] ?? '');

    $slug = $slug !== '' ? make_slug($slug) : make_slug($title);

    if ($title === '') {
        $error_msg = "Title is required.";
    } elseif ($slug === '') {
        $error_msg = "Could not generate a valid slug from that title. Please set a slug manually.";
    } elseif (in_array($slug, $reserved_slugs, true)) {
        $error_msg = "\"$slug\" is a reserved URL and can't be used for a custom page. Please choose a different slug.";
    } else {
        $check = mysqli_prepare($conn, "SELECT id FROM pages WHERE slug = ?");
        mysqli_stmt_bind_param($check, "s", $slug);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {
            $error_msg = "A page with the slug \"$slug\" already exists. Please choose a different slug.";
        } else {
            $stmt = mysqli_prepare($conn, "
                INSERT INTO pages (title, slug, content, status, meta_title, meta_description, meta_keywords, meta_robots, canonical_url)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            mysqli_stmt_bind_param(
                $stmt, "sssssssss",
                $title, $slug, $content, $status,
                $meta_title, $meta_description, $meta_keywords, $meta_robots, $canonical_url
            );

            if (mysqli_stmt_execute($stmt)) {
                header("Location: pages.php?added=1");
                exit;
            } else {
                $error_msg = "Could not add page: " . mysqli_error($conn);
            }
        }
        mysqli_stmt_close($check);
    }
}

// ---------- EDIT PAGE ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_page'])) {
    $id                = (int) ($_POST['page_id'] ?? 0);
    $title             = trim($_POST['title'] ?? '');
    $slug              = trim($_POST['slug'] ?? '');
    $content           = $_POST['content'] ?? '';
    $status            = ($_POST['status'] ?? 'draft') === 'published' ? 'published' : 'draft';
    $meta_title        = trim($_POST['meta_title'] ?? '');
    $meta_description  = trim($_POST['meta_description'] ?? '');
    $meta_keywords     = trim($_POST['meta_keywords'] ?? '');
    $meta_robots       = trim($_POST['meta_robots'] ?? '') ?: 'index, follow';
    $canonical_url     = trim($_POST['canonical_url'] ?? '');

    $slug = $slug !== '' ? make_slug($slug) : make_slug($title);

    if ($title === '') {
        $error_msg = "Title is required.";
    } elseif ($slug === '') {
        $error_msg = "Could not generate a valid slug from that title. Please set a slug manually.";
    } elseif (in_array($slug, $reserved_slugs, true)) {
        $error_msg = "\"$slug\" is a reserved URL and can't be used for a custom page. Please choose a different slug.";
    } else {
        $check = mysqli_prepare($conn, "SELECT id FROM pages WHERE slug = ? AND id != ?");
        mysqli_stmt_bind_param($check, "si", $slug, $id);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {
            $error_msg = "A page with the slug \"$slug\" already exists. Please choose a different slug.";
        } else {
            $stmt = mysqli_prepare($conn, "
                UPDATE pages SET title=?, slug=?, content=?, status=?, meta_title=?, meta_description=?, meta_keywords=?, meta_robots=?, canonical_url=?
                WHERE id=?
            ");
            mysqli_stmt_bind_param(
                $stmt, "sssssssssi",
                $title, $slug, $content, $status,
                $meta_title, $meta_description, $meta_keywords, $meta_robots, $canonical_url, $id
            );

            if (mysqli_stmt_execute($stmt)) {
                header("Location: pages.php?updated=1");
                exit;
            } else {
                $error_msg = "Could not update page: " . mysqli_error($conn);
            }
        }
        mysqli_stmt_close($check);
    }
}

// ---------- DELETE PAGE ----------
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $del = mysqli_prepare($conn, "DELETE FROM pages WHERE id = ?");
    mysqli_stmt_bind_param($del, "i", $id);

    if (mysqli_stmt_execute($del)) {
        header("Location: pages.php?deleted=1");
        exit;
    } else {
        $error_msg = "Delete failed: " . mysqli_error($conn);
    }
}

require_once('include/a-header.php');
require_once('section/sidebar.php');

$pages_list = [];
$result = mysqli_query($conn, "SELECT * FROM pages ORDER BY id DESC");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $pages_list[] = $row;
    }
} else {
    $error_msg = $error_msg ?: ("Could not load pages: " . mysqli_error($conn));
}
?>

<div class="main-content">
    <div class="content-header">
        <div>
            <h1 class="main-h1">Pages</h1>
            <p class="current-date">Create and manage custom pages for the website</p>
        </div>
        <div class="admin-avatar">AD</div>
    </div>

    <?php if (isset($_GET['added'])): ?>
        <div class="alert alert-success" style="border-radius:10px;">Page was added successfully.</div>
    <?php endif; ?>
    <?php if (isset($_GET['updated'])): ?>
        <div class="alert alert-success" style="border-radius:10px;">Page was updated successfully.</div>
    <?php endif; ?>
    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success" style="border-radius:10px;">Page was deleted successfully.</div>
    <?php endif; ?>
    <?php if ($success_msg): ?>
        <div class="alert alert-success" style="border-radius:10px;"><?php echo $success_msg; ?></div>
    <?php endif; ?>
    <?php if ($error_msg): ?>
        <div class="alert alert-danger" style="border-radius:10px;"><?php echo htmlspecialchars($error_msg); ?></div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                <?php if (isset($_POST['edit_page'])): ?>
                    var el = document.getElementById('editPageModal<?php echo (int) ($_POST['page_id'] ?? 0); ?>');
                    if (el) new bootstrap.Modal(el).show();
                <?php else: ?>
                    new bootstrap.Modal(document.getElementById('addPageModal')).show();
                <?php endif; ?>
            });
        </script>
    <?php endif; ?>

    <div class="d-flex justify-content-end mb-4">
        <button type="button" class="btn" style="background-color:#0B1E3F; color:#fff;"
            data-bs-toggle="modal" data-bs-target="#addPageModal">
            <i class="fa-solid fa-plus me-1"></i> Add New Page
        </button>
    </div>

    <!-- Add Page Modal -->
    <div class="modal fade" id="addPageModal" tabindex="-1" aria-labelledby="addPageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="border-radius:14px;">
                <form method="POST" action="pages.php" id="addPageForm">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="addPageModalLabel">
                            <i class="fa-solid fa-plus me-2"></i>Add new page
                        </h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Title</label>
                                <input type="text" name="title" id="pageTitle" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Status</label>
                                <select name="status" class="form-control">
                                    <option value="draft">Draft</option>
                                    <option value="published">Published</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Slug (page URL — leave blank to auto-generate from title)</label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background:#f9fafb;">/?page=</span>
                                    <input type="text" name="slug" id="pageSlug" class="form-control" placeholder="e.g. terms-and-conditions">
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Content</label>
                                <textarea name="content" id="pageContent" class="form-control" rows="8"></textarea>
                            </div>
                            <div class="col-12"><hr></div>
                            <div class="col-12">
                                <p style="font-size:12.5px; color:#0B1E3F; font-weight:600; margin-bottom:4px;">SEO (optional — leave blank to auto-generate from title)</p>
                            </div>
                            <div class="col-12">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Meta Title</label>
                                <input type="text" name="meta_title" class="form-control" maxlength="255">
                            </div>
                            <div class="col-12">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Meta Description</label>
                                <textarea name="meta_description" class="form-control" rows="2" maxlength="500"></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Meta Keywords</label>
                                <input type="text" name="meta_keywords" class="form-control" maxlength="500" placeholder="comma, separated, keywords">
                            </div>
                            <div class="col-12">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Meta Robots</label>
                                <input type="text" name="meta_robots" class="form-control" maxlength="255" placeholder="e.g. index, follow (leave blank for default)">
                            </div>
                            <div class="col-12">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Canonical URL</label>
                                <input type="url" name="canonical_url" class="form-control" maxlength="255" placeholder="Leave blank to use the page's own URL">
                            </div>
                            <div class="col-12"><label class="form-label">OG Title</label><input type="text" name="og_title" class="form-control"></div>
                                <div class="col-12"><label class="form-label">OG Description</label><textarea name="og_description" class="form-control" rows="2"></textarea></div>
                                <div class="col-12"><label class="form-label">OG Image</label><input type="file" name="og_image" class="form-control" accept=".jpg,.jpeg,.png,.webp"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="add_page" class="btn" style="background:#0B1E3F; color:#fff;">Add Page</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Pages table -->
    <div class="table-responsive" style="border:1px solid #eef1f5; border-radius:14px;">
        <table class="table align-middle mb-0" style="font-size:13px;">
            <thead style="background:#f9fafb;">
                <tr>
                    <th style="color:#0B1E3F;">ID</th>
                    <th style="color:#0B1E3F;">Title</th>
                    <th style="color:#0B1E3F;">Slug</th>
                    <th style="color:#0B1E3F;">Status</th>
                    <th style="color:#0B1E3F;">Created</th>
                    <th class="text-center" style="color:#0B1E3F;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pages_list)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-3">No pages yet. Click "Add New Page" to create one.</td></tr>
                <?php else: ?>
                    <?php foreach ($pages_list as $p): ?>
                        <tr>
                            <td><?php echo (int) $p['id']; ?></td>
                            <td class="fw-semibold" style="color:#333;"><?php echo htmlspecialchars($p['title']); ?></td>
                            <td style="color:#555;">/?page=<?php echo htmlspecialchars($p['slug']); ?></td>
                            <td>
                                <?php if ($p['status'] === 'published'): ?>
                                    <span class="badge" style="background:#e6f7ee; color:#1a8f4c;">Published</span>
                                <?php else: ?>
                                    <span class="badge" style="background:#f3f3f3; color:#777;">Draft</span>
                                <?php endif; ?>
                            </td>
                            <td style="color:#555;"><?php echo htmlspecialchars($p['created_at'] ?? ''); ?></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm"
                                    style="background:#eef1f5; color:#0B1E3F; border:none;"
                                    data-bs-toggle="modal" data-bs-target="#editPageModal<?php echo (int) $p['id']; ?>">
                                    <i class="fa-solid fa-pen"></i> Edit
                                </button>
                                <a href="pages.php?delete=<?php echo (int) $p['id']; ?>"
                                   class="btn btn-sm delete-btn"
                                   style="background:#fdeaea; color:#c0392b; border:none;"
                                   onclick="return confirm('Delete this page? This cannot be undone.');">
                                   <i class="fa-solid fa-trash"></i> Delete
                                </a>
                                <a href="page-builder.php?page_id=<?php echo (int) $p['id']; ?>" class="btn btn-sm" style="background:#eef1f5; color:#0B1E3F; border:none;">
    <i class="fa-solid fa-layer-group"></i> Sections
</a>
                            </td>
                        </tr>

                        <!-- Edit Page Modal -->
                        <div class="modal fade" id="editPageModal<?php echo (int) $p['id']; ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content" style="border-radius:14px;">
                                    <form method="POST" action="pages.php" class="editPageForm">
                                        <input type="hidden" name="page_id" value="<?php echo (int) $p['id']; ?>">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5"><i class="fa-solid fa-pen me-2"></i>Edit page</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row g-3">
                                                <div class="col-md-8">
                                                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Title</label>
                                                    <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($p['title']); ?>" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Status</label>
                                                    <select name="status" class="form-control">
                                                        <option value="draft" <?php echo $p['status'] === 'draft' ? 'selected' : ''; ?>>Draft</option>
                                                        <option value="published" <?php echo $p['status'] === 'published' ? 'selected' : ''; ?>>Published</option>
                                                    </select>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Slug (page URL)</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text" style="background:#f9fafb;">/?page=</span>
                                                        <input type="text" name="slug" class="form-control" value="<?php echo htmlspecialchars($p['slug']); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Content</label>
                                                    <textarea name="content" class="form-control editPageContent" rows="8"><?php echo htmlspecialchars($p['content']); ?></textarea>
                                                </div>
                                                <div class="col-12"><hr></div>
                                                <div class="col-12">
                                                    <p style="font-size:12.5px; color:#0B1E3F; font-weight:600; margin-bottom:4px;">SEO</p>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Meta Title</label>
                                                    <input type="text" name="meta_title" class="form-control" maxlength="255" value="<?php echo htmlspecialchars($p['meta_title'] ?? ''); ?>">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Meta Description</label>
                                                    <textarea name="meta_description" class="form-control" rows="2" maxlength="500"><?php echo htmlspecialchars($p['meta_description'] ?? ''); ?></textarea>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Meta Keywords</label>
                                                    <input type="text" name="meta_keywords" class="form-control" maxlength="500" value="<?php echo htmlspecialchars($p['meta_keywords'] ?? ''); ?>">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Meta Robots</label>
                                                    <input type="text" name="meta_robots" class="form-control" maxlength="255" value="<?php echo htmlspecialchars($p['meta_robots'] ?? ''); ?>">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label" style="font-size:12.5px; color:#797979c5;">Canonical URL</label>
                                                    <input type="url" name="canonical_url" class="form-control" maxlength="255" value="<?php echo htmlspecialchars($p['canonical_url'] ?? ''); ?>">
                                                </div>
                                                <div class="col-12"><label class="form-label">OG Title</label><input type="text" name="og_title" class="form-control"></div>
<div class="col-12"><label class="form-label">OG Description</label><textarea name="og_description" class="form-control" rows="2"></textarea></div>
<div class="col-12"><label class="form-label">OG Image</label><input type="file" name="og_image" class="form-control" accept=".jpg,.jpeg,.png,.webp"></div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" name="edit_page" class="btn" style="background:#0B1E3F; color:#fff;">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.ckeditor.com/4.22.1/full/ckeditor.js"></script>
<script>
       document.addEventListener('DOMContentLoaded', function () {
        // Give each edit-page content field a unique id up front (cheap, doesn't need visibility)
        document.querySelectorAll('.editPageContent').forEach(function (el, i) {
            el.id = 'editPageContent_' + i;
        });

        document.addEventListener('shown.bs.modal', function (e) {
            var pageField = e.target.querySelector('#pageContent');
            if (pageField && !CKEDITOR.instances[pageField.id]) {
                CKEDITOR.replace(pageField.id, { height: 300 });
            }
            e.target.querySelectorAll('.editPageContent').forEach(function (el) {
                if (!CKEDITOR.instances[el.id]) {
                    CKEDITOR.replace(el.id, { height: 300 });
                }
            });
        });

        document.querySelectorAll('#addPageForm, .editPageForm').forEach(function (form) {
            form.addEventListener('submit', function () {
                for (var name in CKEDITOR.instances) {
                    CKEDITOR.instances[name].updateElement();
                }
            });
        });

      
        var titleInput = document.getElementById('pageTitle');
        var slugInput = document.getElementById('pageSlug');
        var slugTouched = false;
        if (slugInput) {
            slugInput.addEventListener('input', function () { slugTouched = true; });
        }
        if (titleInput) {
            titleInput.addEventListener('input', function () {
                if (slugTouched) return;
                slugInput.value = titleInput.value
                    .toLowerCase()
                    .trim()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+$/g, '');
            });
        }
    });
</script>

</body>
</html>