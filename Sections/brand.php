<?php
require_once(__DIR__ . "/../admin/include/db-connect.php");
require_once(__DIR__ . "/../Includes/get-whatsapp.php");

$brand_section = [
    'kicker' => 'GET YOUR QUOTE',
    'heading' => 'Which <span>brand</span> is your phone',
    'description' => 'We buy all models in any condition. Get the best price guaranteed!',
];
$brand_items = [];

$stmt = mysqli_prepare($conn, "SELECT * FROM home_sections WHERE section_key = 'brand' LIMIT 1");
mysqli_stmt_execute($stmt);
$brand_result = mysqli_stmt_get_result($stmt);
if ($brand_row = mysqli_fetch_assoc($brand_result)) {
    foreach (['kicker', 'heading', 'description'] as $field) {
        if (!empty($brand_row[$field])) {
            $brand_section[$field] = $brand_row[$field];
        }
    }

    $items_stmt = mysqli_prepare($conn, "SELECT * FROM home_section_items WHERE section_id = ? ORDER BY sort_order ASC");
    mysqli_stmt_bind_param($items_stmt, "i", $brand_row['id']);
    mysqli_stmt_execute($items_stmt);
    $items_result = mysqli_stmt_get_result($items_stmt);
    while ($item = mysqli_fetch_assoc($items_result)) {
        $brand_items[] = $item;
    }
}
?>
<div class="valuation-step" id="valuation-step">

    <div class="section-header">
        <span class="sec-span"><?php echo htmlspecialchars($brand_section['kicker']); ?> </span>
        <h2 class="section-title" style="margin-top:10px;"><?php echo $brand_section['heading']; ?></h2>
    </div>

    <div class="step-tracker" id="valuationTracker">

        <button type="button" class="step-tab active" data-step="1" id="tracker-step-1">
            <i class="fa-solid fa-mobile-screen-button"></i>
            <span class="step-label">1. Brand</span>
        </button>

        <button type="button" class="step-tab" data-step="2" id="tracker-step-2" disabled>
            <i class="fa-solid fa-mobile-retro"></i>
            <span class="step-label">2. Model</span>
        </button>

        <button type="button" class="step-tab" data-step="3" id="tracker-step-3" disabled>
            <i class="fa-solid fa-database"></i>
            <span class="step-label">3. Storage</span>
        </button>

        <button type="button" class="step-tab" data-step="4" id="tracker-step-4" disabled>
            <i class="fa-solid fa-shield-heart"></i>
            <span class="step-label">4. Condition</span>
        </button>

        <button type="button" class="step-tab" data-step="5" id="tracker-step-5" disabled>
            <i class="fa-solid fa-plug"></i>
            <span class="step-label">5. Accessories</span>
        </button>

        <button type="button" class="step-tab" data-step="6" id="tracker-step-6" disabled>
            <i class="fa-solid fa-money-check-dollar"></i>
            <span class="step-label">6. Get Price</span>
        </button>

    </div>

    <section class="brands-section step-panel active" id="brands-section">

        <div class="brands-grid">

            <?php foreach ($brand_items as $item):
                $isApple = stripos($item['title'], 'apple') !== false;
                $tabId = $isApple ? 'apple-tab' : 'samsung-tab';
                $onclick = $isApple ? 'showApple()' : 'showSamsung()';
                $cardClass = $isApple ? 'card-one' : 'card-two';
                $mediaClass = $isApple ? 'brand-card-media' : 'brand-card-media-sam';
            ?>
            <button class="<?php echo $cardClass; ?> brand-card"
                id="<?php echo $tabId; ?>"
                type="button"
                onclick="<?php echo $onclick; ?>">

                <div class="<?php echo $mediaClass; ?>">
                    <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?> devices">
                </div>

                <div class="brand-card-body">

                    <div class="brand-icon">
                        <img src="<?php echo htmlspecialchars($item['icon']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                    </div>

                    <div class="brand-card-text">
                        <h3 class="brand-h3"><?php echo htmlspecialchars($item['title']); ?></h3>
                        <p class="brand-sub"><?php echo htmlspecialchars($item['subtitle']); ?></p>
                    </div>

                    <span class="brand-cta">Select Brand <i class="fa-solid fa-arrow-right"></i></span>

                </div>

            </button>
            <?php endforeach; ?>

        </div>

        <div class="trust-banner">
            <i class="ti ti-shield-check"></i>
            <?php echo htmlspecialchars($brand_section['description']); ?>
        </div>

    </section>

</div>
<?php include ("Sections/apple.php"); ?>
<?php include ("Sections/samsung.php"); ?>
<?php include ("Sections/modalform.php"); ?>