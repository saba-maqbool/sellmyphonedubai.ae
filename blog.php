<?php
$pageTitle = "blog";
require_once("admin/include/db-connect.php");
$blogs_result = mysqli_query($conn, "SELECT * FROM blogs WHERE status = 'published' ORDER BY created_at DESC");
$blog_posts = [];
if ($blogs_result) {
    while ($row = mysqli_fetch_assoc($blogs_result)) {
        $blog_posts[] = $row;
    }
}
?>
<section class="blogs" id="blogs">
    <div class="blogs-titlecard">
        <h3 class="section-tit">Our Blog</h3>
        <p class="section-subtit">Stay updated with the latest mobile phone trends, selling tips, and technology insights.
        </p>
    </div>

    <?php if (count($blog_posts) > 0): ?>
    <div class="blogs-grid">
        <?php foreach ($blog_posts as $post): ?>
                <a class="blog-card" href="blog/<?php echo urlencode($post['slug']); ?>">
            <div class="blog-card-img">
                <img src="<?php echo htmlspecialchars($post['image'] ?: 'imgs/hero.webp'); ?>" alt="<?php echo htmlspecialchars($post['image_alt'] ?: $post['title']); ?>">
                <?php if (!empty($post['category'])): ?>
                <span class="blog-card-tag"><?php echo htmlspecialchars($post['category']); ?></span>
                <?php endif; ?>
            </div>
            <div class="blog-card-body">
                <span class="blog-card-date"><?php echo date('M d, Y', strtotime($post['created_at'])); ?></span>
                <h4 class="blog-card-title"><?php echo htmlspecialchars($post['title']); ?></h4>
                <p class="blog-card-excerpt"><?php echo htmlspecialchars($post['excerpt']); ?></p>
                <div class="blog-card-footer">
                    <span class="blog-card-author"><?php echo htmlspecialchars($post['author']); ?></span>
                    <span class="blog-card-link">Read More <i class="fas fa-arrow-right"></i></span>
                </div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div id="noResults" class="no-results" style="display: block;">
        <div class="no-results-icon">
            <i class="fas fa-newspaper"></i>
        </div>
        <h3 class="h3r">No Blog Posts Found</h3>
        <p class="text-muted" id="noResultsText">Check back soon for new articles!</p>
    </div>
    <?php endif; ?>
</section>