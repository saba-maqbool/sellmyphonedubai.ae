<?php
include("include/db-connect.php");
include("include/auth-check.php");

$pages = [
    'home'    => 'Home Page',
    'about'   => 'About Page',
    'contact' => 'Contact Page',
    'blogs'   => 'Blogs Listing Page',
];

$success_msg = "";
$error_msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $page_key         = $_POST['page_key'] ?? '';
    $meta_title       = trim($_POST['meta_title'] ?? '');
    $meta_description = trim($_POST['meta_description'] ?? '');
    $meta_keywords    = trim($_POST['meta_keywords'] ?? '');
    $meta_robots      = trim($_POST['meta_robots'] ?? 'index, follow');

    if (!array_key_exists($page_key, $pages)) {
        $error_msg = "Invalid page.";
    } else {
        $stmt = mysqli_prepare($conn, "
            INSERT INTO page_meta (page_key, meta_title, meta_description, meta_keywords, meta_robots)
            VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                meta_title=VALUES(meta_title),
                meta_description=VALUES(meta_description),
                meta_keywords=VALUES(meta_keywords),
                meta_robots=VALUES(meta_robots)
        ");
        mysqli_stmt_bind_param($stmt, "sssss", $page_key, $meta_title, $meta_description, $meta_keywords, $meta_robots);

        if (mysqli_stmt_execute($stmt)) {
            $success_msg = "SEO meta for \"" . htmlspecialchars($pages[$page_key]) . "\" updated successfully.";
        } else {
            $error_msg = "Could not save: " . mysqli_stmt_error($stmt);
        }
    }
}

// Fetch all existing rows keyed by page_key
$rows = [];
$result = mysqli_query($conn, "SELECT * FROM page_meta");
if ($result) {
    while ($r = mysqli_fetch_assoc($result)) {
        $rows[$r['page_key']] = $r;
    }
}

$active = $_GET['page_key'] ?? 'home';
if (!array_key_exists($active, $pages)) $active = 'home';
?>
<?php include("include/a-header.php"); ?>
<?php include("section/sidebar.php"); ?>

<div class="main-content">

    <div class="content-header">
        <h2><i class="fa-solid fa-magnifying-glass-chart"></i> Page SEO</h2>
        <p class="text-muted">Title, description and keywords shown to Google &amp; social media for each page.</p>
    </div>

    <?php if ($success_msg): ?>
        <div class="alert alert-success"><?php echo $success_msg; ?></div>
    <?php endif; ?>
    <?php if ($error_msg): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error_msg); ?></div>
    <?php endif; ?>

    <ul class="nav nav-tabs mb-4">
        <?php foreach ($pages as $key => $label): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo $active === $key ? 'active' : ''; ?>" href="page-meta.php?page_key=<?php echo urlencode($key); ?>">
                    <?php echo htmlspecialchars($label); ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>

    <?php $r = $rows[$active] ?? []; ?>

    <form method="POST" class="card p-4" style="border-radius:14px; max-width:760px;">
        <input type="hidden" name="page_key" value="<?php echo htmlspecialchars($active); ?>">

        <h5 class="mb-3"><?php echo htmlspecialchars($pages[$active]); ?></h5>

        <div class="mb-3">
            <label class="form-label fw-semibold">Meta Title</label>
            <input type="text" name="meta_title" class="form-control" maxlength="255"
                value="<?php echo htmlspecialchars($r['meta_title'] ?? ''); ?>">
            <small class="text-muted">Shown as the browser tab title and Google's search result headline. Keep it under ~60 characters.</small>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Meta Description</label>
            <textarea name="meta_description" class="form-control" rows="3" maxlength="500"><?php echo htmlspecialchars($r['meta_description'] ?? ''); ?></textarea>
            <small class="text-muted">The snippet shown under the title in Google search results. Aim for 150–160 characters.</small>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Meta Keywords</label>
            <input type="text" name="meta_keywords" class="form-control" maxlength="500"
                value="<?php echo htmlspecialchars($r['meta_keywords'] ?? ''); ?>">
            <small class="text-muted">Comma-separated, e.g. "sell phone dubai, sell iphone dubai".</small>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Robots</label>
            <input type="text" name="meta_robots" class="form-control" maxlength="255"
                value="<?php echo htmlspecialchars($r['meta_robots'] ?? 'index, follow'); ?>"
                placeholder="e.g. index, follow">
            <small class="text-muted">Tells search engines whether to index/follow this page — e.g. "index, follow" (default, visible in search) or "noindex, nofollow" (hide completely).</small>
        </div>

        <button type="submit" class="btn btn-primary mt-2">Save</button>
    </form>
</div>

</body>
</html>