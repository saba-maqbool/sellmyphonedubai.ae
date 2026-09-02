<?php
require_once(__DIR__ . "/../admin/include/db-connect.php");

$catalog_section = [
    'kicker'      => 'BROWSE BY SERIES',
    'heading'     => 'Shop by <span>Galaxy Series</span>',
    'description' => 'Explore our complete range of Samsung Galaxy series, from the latest flagships to popular previous generations. Discover powerful performance, and a variety of storage options to suit your need. Browse the Galaxy S25, Galaxy Z Fold & Flip, Galaxy S24, Galaxy S23, and Galaxy A Series to compare your options and find the best resale value for your Samsung phone in Dubai.',
];
$catalog_items = [];

$stmt = mysqli_prepare($conn, "SELECT * FROM home_sections WHERE section_key = 'samsung_catalog' LIMIT 1");
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

if (empty($catalog_items)) {
    // Keyword must match the start of the model_name values stored in the
    // models table (e.g. "Samsung S25 Ultra", "Samsung Z Fold 4", "Samsung A56"),
    // which all begin with "Samsung", not "Galaxy".
    $catalog_items = [
        ['image' => 'imgs/s-series.png',       'title' => 'S Series', 'link' => 'samsung s'],
        ['image' => 'imgs/samsung-series.png', 'title' => 'Z Series', 'link' => 'samsung z'],
        ['image' => 'imgs/samsung-card.png',   'title' => 'A Series', 'link' => 'samsung a'],
    ];
}
?>
<section class="series-catalog-section" id="series-catalog-section">
    <div class="section-header">
        <span class="section-tag"><i class="fa-solid fa-mobile-screen-button"></i> <?php echo htmlspecialchars($catalog_section['kicker']); ?></span>
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
                    <button type="button" class="series-view-all-btn" onclick="filterSamsungSeries('<?php echo htmlspecialchars($series['link']); ?>')">
                        View All <i class="fa-solid fa-arrow-right"></i>
                    </button>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<script>
function filterSamsungSeries(seriesPrefix){
    var catalogWrap = document.getElementById('samsung-catalog-wrap');
    if (!catalogWrap) return;
    catalogWrap.classList.add('is-active');

    var cards = document.querySelectorAll('#pills-models .model-card');
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