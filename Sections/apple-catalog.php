<?php
require_once(__DIR__ . "/../admin/include/db-connect.php");

// Defaults — shown until content is edited on the "Sell iPhone Page" admin screen
$catalog_section = [
    'kicker'      => 'BROWSE BY SERIES',
    'heading'     => 'Shop by <span>iPhone Series</span>',
    'description' => 'Explore our complete range of iPhone series, from the latest models to popular previous generations. Discover powerful performance, advanced camera features, and a variety of storage options to suit your need. Browse the iPhone 17, iPhone 16, iPhone 15, iPhone 14, and iPhone 13 Series to compare your options and find the perfect iPhone for your everyday lifestyle.',
];
$catalog_items = [];

$stmt = mysqli_prepare($conn, "SELECT * FROM home_sections WHERE section_key = 'apple_catalog' LIMIT 1");
mysqli_stmt_execute($stmt);
$catalog_result = mysqli_stmt_get_result($stmt);
if ($catalog_row = mysqli_fetch_assoc($catalog_result)) {
    foreach (['kicker', 'heading', 'description'] as $field) {
        if (!empty($catalog_row[$field])) {
            $catalog_section[$field] = $catalog_row[$field];
        }
    }

    $items_stmt = mysqli_prepare($conn, "SELECT * FROM home_section_items WHERE section_id = ? ORDER BY sort_order ASC");
    mysqli_stmt_bind_param($items_stmt, "i", $catalog_row['id']);
    mysqli_stmt_execute($items_stmt);
    $items_result = mysqli_stmt_get_result($items_stmt);
    $db_items = [];
    while ($item = mysqli_fetch_assoc($items_result)) {
        $db_items[] = $item;
    }
    if (!empty($db_items)) {
        $catalog_items = $db_items;
    }
}

// Fallback cards if nothing saved in DB yet (first load, before admin adds series)
if (empty($catalog_items)) {
    $catalog_items = [
        ['image' => 'imgs/iphone 17 series.webp',      'title' => 'iPhone 17 Series', 'link' => 'iphone 17'],
        ['image' => 'imgs/iphone 16 series (2).webp',  'title' => 'iPhone 16 Series', 'link' => 'iphone 16'],
        ['image' => 'imgs/iphone 15 series.png',       'title' => 'iPhone 15 Series', 'link' => 'iphone 15'],
        ['image' => 'imgs/series 14.jpg',               'title' => 'iPhone 14 Series', 'link' => 'iphone 14'],
        ['image' => 'imgs/iphone 13 series.png',        'title' => 'iPhone 13 Series', 'link' => 'iphone 13'],
    ];
}
?>
<section class="series-catalog-section" id="series-catalog-section">
    <div class="section-header">
        <span class="section-tag"><i class="fa-brands fa-apple"></i> <?php echo htmlspecialchars($catalog_section['kicker']); ?></span>
        <h2 class="section-title"><?php echo $catalog_section['heading']; ?></h2>
        <p class="section-subtitle"><?php echo htmlspecialchars($catalog_section['description']); ?></p>
    </div>

    <div class="series-grid">
        <?php foreach ($catalog_items as $series): ?>
            <div class="series-card">
                <div class="series-card-img-wrap">
                    <img src="<?php echo htmlspecialchars($series['image']); ?>" alt="<?php echo htmlspecialchars($series['title']); ?>" class="series-card-img">
                </div>
                <h3 class="series-card-title"><?php echo htmlspecialchars($series['title']); ?></h3>
                <?php if (!empty($series['subtitle'])): ?>
                    <a href="<?php echo htmlspecialchars($series['subtitle']); ?>" class="series-view-all-btn">
                        View All <i class="fa-solid fa-arrow-right"></i>
                    </a>
                <?php else: ?>
                    <button type="button" class="series-view-all-btn" onclick="filterAppleSeries('<?php echo htmlspecialchars($series['link']); ?>')">
                        View All <i class="fa-solid fa-arrow-right"></i>
                    </button>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<script>
function filterAppleSeries(seriesPrefix){
    var catalogWrap = document.getElementById('apple-catalog-wrap');
    if (!catalogWrap) return;

    catalogWrap.style.display = 'block';

    var cards = document.querySelectorAll('#pills-home .model-card');
    cards.forEach(function(card){
        var name = (card.getAttribute('data-model-name') || '').toLowerCase();
        var col = card.closest('.col');
        if (name.indexOf(seriesPrefix.toLowerCase()) === 0) {
            if (col) col.style.display = '';
        } else {
            if (col) col.style.display = 'none';
        }
    });

    catalogWrap.scrollIntoView({ behavior: 'smooth', block: 'start' });
}
</script>