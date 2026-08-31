<?php
require_once("admin/include/db-connect.php");

$slug = $_GET['slug'] ?? '';
$post = null;

if ($slug !== '') {
    $stmt = mysqli_prepare($conn, "SELECT * FROM blogs WHERE slug = ? AND status = 'published' LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $slug);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $post = mysqli_fetch_assoc($result);
}

$pageTitle = $post ? $post['title'] : "Blog Post Not Found";

$related_posts = [];
if ($post) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM blogs WHERE status = 'published' AND id != ? ORDER BY created_at DESC LIMIT 5");
    mysqli_stmt_bind_param($stmt, "i", $post['id']);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    while ($r = mysqli_fetch_assoc($res)) {
        $related_posts[] = $r;
    }
}

$read_minutes = 1;
if ($post) {
    $word_count   = str_word_count(strip_tags($post['content']));
    $read_minutes = max(1, (int) ceil($word_count / 200));
}

if ($post) {
    $meta_title       = !empty($post['meta_title']) ? $post['meta_title'] : (htmlspecialchars($post['title']) . " | Sell My Phone Dubai Blog");
    $meta_description = !empty($post['meta_description']) ? $post['meta_description'] : (!empty($post['excerpt']) ? $post['excerpt'] : strip_tags($post['content']));
    $meta_keywords    = !empty($post['meta_keywords']) ? $post['meta_keywords'] : (!empty($post['category']) ? $post['category'] . ", sell my phone dubai" : "sell my phone dubai blog");
    $meta_robots      = !empty($post['meta_robots']) ? $post['meta_robots'] : "index, follow";
    if (!empty($post['image'])) {
        $meta_image = $post['image'];
    }
} else {
    $meta_title       = "Blog Post Not Found | Sell My Phone Dubai";
    $meta_description = "The article you're looking for could not be found.";
    $meta_robots      = "noindex, follow";
}
?>

<section class="blog-details" id="blog-details">
    <?php if ($post): ?>
        <a href="<?php echo $base_path; ?>/blog" class="blog-back-link"><i class="fas fa-arrow-left"></i> Back to Blogs</a>

        <div class="blog-details-titlecard">
            <?php if (!empty($post['category'])): ?>
                <span class="blog-card-tag blog-details-tag"><?php echo htmlspecialchars($post['category']); ?></span>
            <?php endif; ?>
            <h1 class="blog-details-title"><?php echo htmlspecialchars($post['title']); ?></h1>
            <div class="blog-details-meta">
                <span><i class="fa-regular fa-calendar"></i> <?php echo date('F d, Y', strtotime($post['created_at'])); ?></span>
                <span><i class="fa-regular fa-user"></i> <?php echo htmlspecialchars($post['author']); ?></span>
                <span><i class="fa-regular fa-clock"></i> <?php echo $read_minutes; ?> min read</span>
            </div>
        </div>

        <div class="blog-details-layout">
            <div class="blog-details-main">
                <?php if (!empty($post['image'])): ?>
                <div class="blog-details-img">
                    <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="<?php echo htmlspecialchars($post['image_alt'] ?: $post['title']); ?>">
                </div>
                <?php endif; ?>

                <div class="blog-details-content">
                    <?php echo $post['content']; ?>
                </div>
            </div>

            <?php if (count($related_posts) > 0): ?>
            <aside class="blog-related">
                <h3 class="blog-related-title">Recent Posts</h3>
                <div class="blog-related-list">
                    <?php foreach ($related_posts as $rp): ?>
                    <a href="<?php echo $base_path; ?>/blog/<?php echo urlencode($rp['slug']); ?>" class="blog-related-item">
                        <div class="blog-related-img">
                            <img src="<?php echo htmlspecialchars($rp['image'] ?: $base_path . '/imgs/hero.webp'); ?>" alt="<?php echo htmlspecialchars($rp['image_alt'] ?: $rp['title']); ?>">
                        </div>
                        <div class="blog-related-info">
                            <h4><?php echo htmlspecialchars($rp['title']); ?></h4>
                            <span class="blog-related-date"><?php echo date('M d, Y', strtotime($rp['created_at'])); ?></span>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            </aside>
            <?php endif; ?>
        </div>

    <?php else: ?>
        <div id="noResults" class="no-results" style="display: block;">
            <div class="no-results-icon">
                <i class="fas fa-newspaper"></i>
            </div>
            <h3 class="h3r">Blog Post Not Found</h3>
            <p class="text-muted">This article may have been removed or the link is incorrect.</p>
            <a href="<?php echo $base_path; ?>/blog" class="blog-card-link" style="margin-top:16px;">Back to Blog <i class="fas fa-arrow-right"></i></a>
        </div>
    <?php endif; ?>
</section>