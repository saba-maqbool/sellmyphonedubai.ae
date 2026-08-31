<?php
require_once(__DIR__ . "/../admin/include/db-connect.php");

$featured_apple_models = [];
$featured_result = mysqli_query($conn, "
    SELECT m.id, m.model_name, m.image, mp.base
    FROM models m
    INNER JOIN model_pricing mp ON mp.model_id = m.id
    WHERE m.brand = 'Apple'
    ORDER BY mp.base DESC
    LIMIT 8
");
if ($featured_result) {
    while ($row = mysqli_fetch_assoc($featured_result)) {
        $featured_apple_models[] = $row;
    }
}
?>
<section class="apple-featured-section" id="apple-featured-section">
    <div class="section-header">
        <span class="section-tag">BEST OF APPLE</span>
        <h2 class="section-title">Top Apple Devices We Buy</h2>
        <p class="section-subtitle">Best market prices for the most in-demand Apple devices in Dubai</p>
    </div>

    <?php if (!empty($featured_apple_models)): ?>
    <div class="apple-featured-grid">
        <?php foreach ($featured_apple_models as $i => $fm): ?>
        <button type="button" class="apple-featured-card" onclick="quickSelectAppleModel(<?php echo (int) $fm['id']; ?>)">
            <?php if ($i === 0): ?><span class="apple-featured-badge">Most Popular</span><?php endif; ?>
            <span class="apple-featured-img-wrap">
                <img src="<?php echo htmlspecialchars($fm['image']); ?>" alt="<?php echo htmlspecialchars($fm['model_name']); ?>" class="apple-featured-img" loading="lazy">
            </span>
            <span class="apple-featured-name"><?php echo htmlspecialchars($fm['model_name']); ?></span>
            <span class="apple-featured-price">From AED <?php echo number_format((float) $fm['base']); ?></span>
            <span class="apple-featured-cta">Get Quote <i class="fa-solid fa-arrow-right"></i></span>
        </button>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</section>

<script>
function quickSelectAppleModel(modelId) {
    var catalogWrap = document.getElementById('apple-catalog-wrap');
    if (catalogWrap) {
        catalogWrap.style.display = 'block';
        catalogWrap.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
    setTimeout(function () {
        var card = document.querySelector('#pills-home .model-card[data-model-id="' + modelId + '"]');
        if (card) {
            card.click();
        }
    }, 500);
}
</script>