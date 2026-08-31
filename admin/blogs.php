<?php
include("include/db-connect.php");
include("include/auth-check.php");

$success_msg = "";
$error_msg = "";
$upload_dir = "../imgs/";
$allowed_ext = ["jpg", "jpeg", "png", "webp"];

function make_slug($text) {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_blog'])) {
    $title = trim($_POST['title'] ?? '');
    $excerpt = trim($_POST['excerpt'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $status = $_POST['status'] === 'draft' ? 'draft' : 'published';
    $meta_title = trim($_POST['meta_title'] ?? '');
    $meta_description = trim($_POST['meta_description'] ?? '');
    $meta_keywords = trim($_POST['meta_keywords'] ?? '');
    $meta_robots = trim($_POST['meta_robots'] ?? '');
    $image_alt = trim($_POST['image_alt'] ?? '');

    if ($title === '' || $content === '') {
        $error_msg = "Title and content are required.";
    } else {
        $slug = make_slug($title);
        $image_path = null;

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed_ext)) {
                $filename = uniqid('blog_') . '.' . $ext;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $filename)) {
                    $image_path = "imgs/" . $filename;
                }
            }
        }

        $stmt = mysqli_prepare($conn, "INSERT INTO blogs (title, slug, excerpt, content, image, image_alt, category, author, status, meta_title, meta_description, meta_keywords, meta_robots) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sssssssssssss", $title, $slug, $excerpt, $content, $image_path, $image_alt, $category, $author, $status, $meta_title, $meta_description, $meta_keywords, $meta_robots);

        if (mysqli_stmt_execute($stmt)) {
            $success_msg = "Blog post added successfully.";
        } else {
            $error_msg = "Could not add blog post: " . mysqli_error($conn);
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_blog'])) {
    $id = (int) $_POST['blog_id'];
    $title = trim($_POST['title'] ?? '');
    $excerpt = trim($_POST['excerpt'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $status = $_POST['status'] === 'draft' ? 'draft' : 'published';
    $meta_title = trim($_POST['meta_title'] ?? '');
    $meta_description = trim($_POST['meta_description'] ?? '');
    $meta_keywords = trim($_POST['meta_keywords'] ?? '');
    $meta_robots = trim($_POST['meta_robots'] ?? '');
    $image_alt = trim($_POST['image_alt'] ?? '');

    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed_ext)) {
            $filename = uniqid('blog_') . '.' . $ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $filename)) {
                $image_path = "imgs/" . $filename;
            }
        }
    }

    if ($image_path) {
        $stmt = mysqli_prepare($conn, "UPDATE blogs SET title=?, excerpt=?, content=?, category=?, author=?, status=?, meta_title=?, meta_description=?, meta_keywords=?, meta_robots=?, image=?, image_alt=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "ssssssssssssi", $title, $excerpt, $content, $category, $author, $status, $meta_title, $meta_description, $meta_keywords, $meta_robots, $image_path, $image_alt, $id);
    } else {
        $stmt = mysqli_prepare($conn, "UPDATE blogs SET title=?, excerpt=?, content=?, category=?, author=?, status=?, meta_title=?, meta_description=?, meta_keywords=?, meta_robots=?, image_alt=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "sssssssssssi", $title, $excerpt, $content, $category, $author, $status, $meta_title, $meta_description, $meta_keywords, $meta_robots, $image_alt, $id);
    }

    if (mysqli_stmt_execute($stmt)) {
        $success_msg = "Blog post updated successfully.";
    } else {
        $error_msg = "Could not update blog post: " . mysqli_error($conn);
    }
}

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];

    $find = mysqli_prepare($conn, "SELECT image FROM blogs WHERE id = ?");
    mysqli_stmt_bind_param($find, "i", $id);
    mysqli_stmt_execute($find);
    $found = mysqli_stmt_get_result($find);
    $row = mysqli_fetch_assoc($found);

    if ($row) {
        $del = mysqli_prepare($conn, "DELETE FROM blogs WHERE id = ?");
        mysqli_stmt_bind_param($del, "i", $id);

        if (mysqli_stmt_execute($del)) {
            $image_file = __DIR__ . "/../" . $row['image'];
            if ($row['image'] && file_exists($image_file)) {
                unlink($image_file);
            }
            header("Location: blogs-section.php?deleted=1");
            exit;
        } else {
            $error_msg = "Delete failed: " . mysqli_error($conn);
        }
    } else {
        $error_msg = "Blog post not found.";
    }
}

require_once('include/a-header.php');
require_once('section/sidebar.php');

$blogs_result = mysqli_query($conn, "SELECT * FROM blogs ORDER BY created_at DESC");
$blogs_list = [];
if ($blogs_result) {
    while ($row = mysqli_fetch_assoc($blogs_result)) {
        $blogs_list[] = $row;
    }
}
?>

<div class="main-content">
    <div class="content-header">
        <div>
            <h1 class="main-h1">Blog Posts</h1>
            <p class="current-date">Create and manage articles shown on your public blog page</p>
        </div>
        <div class="admin-avatar">AD</div>
    </div>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success" style="border-radius:10px;">Blog post deleted successfully.</div>
    <?php endif; ?>
    <?php if ($success_msg): ?>
        <div class="alert alert-success" style="border-radius:10px;"><?php echo $success_msg; ?></div>
    <?php endif; ?>
    <?php if ($error_msg): ?>
        <div class="alert alert-danger" style="border-radius:10px;"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <div class="d-flex justify-content-end mb-3">
        <button type="button" class="btn" style="background:#0B1E3F; color:#fff;" data-bs-toggle="modal" data-bs-target="#addBlogModal">
            <i class="fa-solid fa-plus me-1"></i> Add Blog Post
        </button>
    </div>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-3">
        <?php foreach ($blogs_list as $blog): ?>
            <div class="col">
                <div class="card h-100" style="border-radius:14px; padding:16px;">
                    <?php if (!empty($blog['image'])): ?>
                        <img src="../<?php echo htmlspecialchars($blog['image']); ?>" alt="<?php echo htmlspecialchars($blog['image_alt'] ?: $blog['title']); ?>" style="height:140px; object-fit:cover; border-radius:10px;">
                    <?php endif; ?>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <span class="badge" style="background:<?php echo $blog['status'] === 'published' ? '#e9f9ee' : '#fff4e0'; ?>; color:<?php echo $blog['status'] === 'published' ? '#1e7e34' : '#a66a00'; ?>;"><?php echo htmlspecialchars(ucfirst($blog['status'])); ?></span>
                        <small style="color:#797979;"><?php echo htmlspecialchars($blog['category']); ?></small>
                    </div>
                    <h6 style="margin:10px 0 4px;"><?php echo htmlspecialchars($blog['title']); ?></h6>
                    <small style="color:#797979;"><?php echo htmlspecialchars($blog['excerpt']); ?></small>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <small style="color:#797979;"><?php echo htmlspecialchars($blog['author']); ?></small>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                data-bs-toggle="modal" data-bs-target="#editBlogModal"
                                data-id="<?php echo (int) $blog['id']; ?>"
                                data-title="<?php echo htmlspecialchars($blog['title']); ?>"
                                data-excerpt="<?php echo htmlspecialchars($blog['excerpt']); ?>"
                                data-content="<?php echo htmlspecialchars($blog['content']); ?>"
                                data-category="<?php echo htmlspecialchars($blog['category']); ?>"
                                data-author="<?php echo htmlspecialchars($blog['author']); ?>"
                                data-status="<?php echo htmlspecialchars($blog['status']); ?>"
                                data-meta-title="<?php echo htmlspecialchars($blog['meta_title'] ?? ''); ?>"
                                data-meta-description="<?php echo htmlspecialchars($blog['meta_description'] ?? ''); ?>"
                                data-meta-keywords="<?php echo htmlspecialchars($blog['meta_keywords'] ?? ''); ?>"
                                data-meta-robots="<?php echo htmlspecialchars($blog['meta_robots'] ?? ''); ?>"
                                data-image-alt="<?php echo htmlspecialchars($blog['image_alt'] ?? ''); ?>">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <a href="blogs.php?delete=<?php echo (int) $blog['id']; ?>"
                               class="btn btn-sm delete-btn" style="background:#fdeaea; color:#c0392b; border:none;"
                               onclick="return confirm('Delete this blog post?');">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="modal fade" id="addBlogModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="border-radius:14px;">
                <form method="POST" action="blogs.php" enctype="multipart/form-data" id="addBlogForm">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5"><i class="fa-solid fa-plus me-2"></i>Add blog post</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Title</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Category</label>
                                <input type="text" name="category" class="form-control" placeholder="e.g. Selling Tips">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Author</label>
                                <input type="text" name="author" class="form-control" placeholder="e.g. Admin">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Status</label>
                                <select name="status" class="form-control">
                                    <option value="published">Published</option>
                                    <option value="draft">Draft</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Excerpt (short summary for the card)</label>
                                <textarea name="excerpt" class="form-control" rows="2" required></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Content</label>
                                <textarea name="content" id="blogContent" class="form-control" rows="8"></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Cover image</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                            </div>
                            <div class="col-12">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Image Alt Text (for SEO & accessibility)</label>
                                <input type="text" name="image_alt" class="form-control" placeholder="Describe the image, e.g. Man handing over an iPhone for cash in Dubai">
                            </div>
                            <div class="col-12"><hr></div>
                            <div class="col-12">
                                <p style="font-size:12.5px; color:#0B1E3F; font-weight:600; margin-bottom:4px;">SEO (optional — leave blank to auto-generate from title/excerpt)</p>
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
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="add_blog" class="btn" style="background:#0B1E3F; color:#fff;">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editBlogModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="border-radius:14px;">
                <form method="POST" action="blogs.php" enctype="multipart/form-data" id="editBlogForm">
                    <input type="hidden" name="blog_id" id="editBlogId" value="">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5"><i class="fa-solid fa-pen me-2"></i>Edit blog post</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Title</label>
                                <input type="text" name="title" id="editBlogTitle" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Category</label>
                                <input type="text" name="category" id="editBlogCategory" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Author</label>
                                <input type="text" name="author" id="editBlogAuthor" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Status</label>
                                <select name="status" id="editBlogStatus" class="form-control">
                                    <option value="published">Published</option>
                                    <option value="draft">Draft</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Excerpt (short summary for the card)</label>
                                <textarea name="excerpt" id="editBlogExcerpt" class="form-control" rows="2" required></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Content</label>
                                <textarea name="content" id="editBlogContent" class="form-control" rows="8"></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Cover image (leave empty to keep current)</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                            </div>
                            <div class="col-12">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Image Alt Text (for SEO & accessibility)</label>
                                <input type="text" name="image_alt" id="editBlogImageAlt" class="form-control" placeholder="Describe the image, e.g. Man handing over an iPhone for cash in Dubai">
                            </div>
                            <div class="col-12"><hr></div>
                            <div class="col-12">
                                <p style="font-size:12.5px; color:#0B1E3F; font-weight:600; margin-bottom:4px;">SEO (optional — leave blank to auto-generate from title/excerpt)</p>
                            </div>
                            <div class="col-12">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Meta Title</label>
                                <input type="text" name="meta_title" id="editBlogMetaTitle" class="form-control" maxlength="255">
                            </div>
                            <div class="col-12">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Meta Description</label>
                                <textarea name="meta_description" id="editBlogMetaDescription" class="form-control" rows="2" maxlength="500"></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Meta Keywords</label>
                                <input type="text" name="meta_keywords" id="editBlogMetaKeywords" class="form-control" maxlength="500" placeholder="comma, separated, keywords">
                            </div>
                            <div class="col-12">
                                <label class="form-label" style="font-size:12.5px; color:#797979c5;">Meta Robots</label>
                                <input type="text" name="meta_robots" id="editBlogMetaRobots" class="form-control" maxlength="255" placeholder="e.g. index, follow (leave blank for default)">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="edit_blog" class="btn" style="background:#0B1E3F; color:#fff;">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- CKEditor 4 (Full package) - loaded only here, only for the blog "Content" field -->
    <!-- "full" package includes Font Color, Background Color, Font Family/Size, Styles, Table, Image, etc. -->
    <script src="https://cdn.ckeditor.com/4.25.2-lts/full/ckeditor.js"></script>
    <script>
        var addBlogEditor = null;
        var editBlogEditor = null;

        document.addEventListener('DOMContentLoaded', function () {
            // Init CKEditor on the Add-blog content field
            addBlogEditor = CKEDITOR.replace('blogContent', {
                height: 250,
                toolbarGroups: [
                    { name: 'clipboard', groups: ['clipboard', 'undo'] },
                    { name: 'editing', groups: ['find', 'selection', 'spellchecker'] },
                    { name: 'links' },
                    { name: 'insert' },
                    '/',
                    { name: 'basicstyles', groups: ['basicstyles', 'cleanup'] },
                    { name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align'] },
                    { name: 'styles' },
                    { name: 'colors' },
                    { name: 'tools' }
                ],
                removeButtons: 'Save,NewPage,Preview,Print,Templates,Cut,Copy,Paste,PasteText,PasteFromWord,Find,Replace,SelectAll,Scayt,Form,Flash,Smiley,PageBreak,Iframe,ShowBlocks,About'
            });

            // Init CKEditor on the Edit-blog content field
            editBlogEditor = CKEDITOR.replace('editBlogContent', {
                height: 250,
                toolbarGroups: [
                    { name: 'clipboard', groups: ['clipboard', 'undo'] },
                    { name: 'editing', groups: ['find', 'selection', 'spellchecker'] },
                    { name: 'links' },
                    { name: 'insert' },
                    '/',
                    { name: 'basicstyles', groups: ['basicstyles', 'cleanup'] },
                    { name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align'] },
                    { name: 'styles' },
                    { name: 'colors' },
                    { name: 'tools' }
                ],
                removeButtons: 'Save,NewPage,Preview,Print,Templates,Cut,Copy,Paste,PasteText,PasteFromWord,Find,Replace,SelectAll,Scayt,Form,Flash,Smiley,PageBreak,Iframe,ShowBlocks,About'
            });

            var editModal = document.getElementById('editBlogModal');
            editModal.addEventListener('show.bs.modal', function (event) {
                var btn = event.relatedTarget;
                document.getElementById('editBlogId').value = btn.getAttribute('data-id');
                document.getElementById('editBlogTitle').value = btn.getAttribute('data-title');
                document.getElementById('editBlogExcerpt').value = btn.getAttribute('data-excerpt');
                document.getElementById('editBlogCategory').value = btn.getAttribute('data-category');
                document.getElementById('editBlogAuthor').value = btn.getAttribute('data-author');
                document.getElementById('editBlogStatus').value = btn.getAttribute('data-status');
                document.getElementById('editBlogMetaTitle').value = btn.getAttribute('data-meta-title') || '';
                document.getElementById('editBlogMetaDescription').value = btn.getAttribute('data-meta-description') || '';
                document.getElementById('editBlogMetaKeywords').value = btn.getAttribute('data-meta-keywords') || '';
                document.getElementById('editBlogMetaRobots').value = btn.getAttribute('data-meta-robots') || '';
                document.getElementById('editBlogImageAlt').value = btn.getAttribute('data-image-alt') || '';

                // Push the existing HTML content into CKEditor (not into the raw textarea)
                var rawContent = btn.getAttribute('data-content') || '';
                if (editBlogEditor && editBlogEditor.status === 'ready') {
                    editBlogEditor.setData(rawContent);
                } else if (editBlogEditor) {
                    editBlogEditor.on('instanceReady', function () {
                        editBlogEditor.setData(rawContent);
                    });
                }
            });

            // Sync CKEditor's HTML back into the hidden textarea before each form submits
            var addForm = document.getElementById('addBlogForm');
            addForm.addEventListener('submit', function (e) {
                if (addBlogEditor) {
                    addBlogEditor.updateElement();
                }
                if (!document.getElementById('blogContent').value.trim()) {
                    e.preventDefault();
                    alert('Content is required.');
                }
            });

            var editForm = document.getElementById('editBlogForm');
            editForm.addEventListener('submit', function (e) {
                if (editBlogEditor) {
                    editBlogEditor.updateElement();
                }
                if (!document.getElementById('editBlogContent').value.trim()) {
                    e.preventDefault();
                    alert('Content is required.');
                }
            });
        });
    </script>

</div>
</body>

</html>