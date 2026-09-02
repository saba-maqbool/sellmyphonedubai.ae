<?php
require_once(__DIR__ . "/../admin/include/db-connect.php");

$featured_section = [
    'kicker'      => 'BEST OF SAMSUNG',
    'heading'     => 'Top Samsung Devices We Buy',
    'description' => 'Sell your Samsung Galaxy in Dubai at competitive market prices with our easy device valuation service. We buy popular Samsung devices including the Galaxy S25 Ultra, Galaxy S25+, Galaxy Z Fold 6, Galaxy Z Flip 6, Galaxy S24 Ultra, and other Galaxy models — all at the best Samsung resale price in UAE.',
];

$stmt = mysqli_prepare($conn, "SELECT * FROM home_sections WHERE section_key = 'samsung_featured' LIMIT 1");
mysqli_stmt_execute($stmt);
$featured_section_result = mysqli_stmt_get_result($stmt);
if ($featured_section_row = mysqli_fetch_assoc($featured_section_result)) {
    foreach (['kicker', 'heading', 'description'] as $field) {
        if (!empty($featured_section_row[$field])) {
            $featured_section[$field] = $featured_section_row[$field];
        }
    }
}

$featured_samsung_models = [];
$featured_result = mysqli_query($conn, "
    SELECT m.id, m.model_name, m.image, mp.base
    FROM models m
    INNER JOIN model_pricing mp ON mp.model_id = m.id
    WHERE m.brand = 'Samsung'
    ORDER BY mp.base DESC
    LIMIT 8
");
if ($featured_result) {
    while ($row = mysqli_fetch_assoc($featured_result)) {
        $featured_samsung_models[] = $row;
    }
}
?>
<section class="apple-featured-section" id="samsung-featured-section">
    <div class="section-header">
        <span class="section-tag"><?php echo htmlspecialchars($featured_section['kicker']); ?></span>
        <h2 class="section-title"><?php echo $featured_section['heading']; ?></h2>
        <p class="section-subtitle"><?php echo htmlspecialchars($featured_section['description']); ?></p>
    </div>

    <?php if (!empty($featured_samsung_models)): ?>
    <div class="apple-featured-grid">
        <?php foreach ($featured_samsung_models as $i => $fm): ?>
        <button type="button" class="apple-featured-card" onclick="quickSelectSamsungModel(<?php echo (int) $fm['id']; ?>)">
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
function quickSelectSamsungModel(modelId) {
    var catalogWrap = document.getElementById('samsung-catalog-wrap');
    if (catalogWrap) {
        catalogWrap.classList.add('is-active');
        catalogWrap.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
    setTimeout(function () {
        var card = document.querySelector('#pills-models .model-card[data-model-id="' + modelId + '"]');
        if (card) { card.click(); }
    }, 500);
}
</script>