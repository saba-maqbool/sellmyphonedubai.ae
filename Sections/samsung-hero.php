<?php
require_once(__DIR__ . "/../admin/include/db-connect.php");
require_once(__DIR__ . "/../Includes/get-whatsapp.php");

$samsung_hero = [
    'kicker'            => 'SAMSUNG TRADE-IN DUBAI',
    'heading'           => 'Sell Your',
    'heading_highlight' => 'Samsung',
    'extra_1'           => 'Galaxy in Dubai',
    'description'       => 'Sell your Samsung Galaxy in Dubai at the best price — brand new, original, and used Galaxy phones including the latest Galaxy S25 Ultra, Galaxy Z Fold 6/Flip 6, Galaxy S24, Galaxy S23, and Galaxy A Series, all factory unlocked and 100% genuine. Compare the best Samsung price in UAE with exclusive Samsung buyback deals, free doorstep pickup, and easy Samsung trade-in offers, then get same-day cash for your Samsung phone in Dubai.',
    'image'             => 'imgs/samsung-card.png',
    'image_alt'         => 'Sell your Samsung Galaxy phone in Dubai for the best price',
];

$stmt = mysqli_prepare($conn, "SELECT * FROM home_sections WHERE section_key = 'samsung_hero' LIMIT 1");
mysqli_stmt_execute($stmt);
$samsung_hero_result = mysqli_stmt_get_result($stmt);
if ($samsung_hero_row = mysqli_fetch_assoc($samsung_hero_result)) {
    foreach (['kicker', 'heading', 'heading_highlight', 'extra_1', 'description', 'image', 'image_alt'] as $field) {
        if (!empty($samsung_hero_row[$field])) {
            $samsung_hero[$field] = $samsung_hero_row[$field];
        }
    }
}
?>
<section class="apple-hero-section" id="samsung-hero-section">
    <div class="apple-hero-bg"></div>
    <div class="apple-hero-overlay"></div>
    <div class="apple-hero-glow"></div>

    <div class="apple-hero-container">
        <div class="apple-hero-content">
            <span class="apple-hero-kicker"><?php echo htmlspecialchars($samsung_hero['kicker']); ?></span>
            <h1 class="apple-hero-title"><?php echo htmlspecialchars($samsung_hero['heading']); ?> <span><?php echo htmlspecialchars($samsung_hero['heading_highlight']); ?></span> <?php echo htmlspecialchars($samsung_hero['extra_1']); ?></h1>
            <p class="apple-hero-desc"><?php echo nl2br(htmlspecialchars($samsung_hero['description'])); ?></p>
            <div class="apple-hero-cta-row">
                <a href="#series-catalog-section" class="apple-hero-btn-primary">Get Instant Quote <i class="fa-solid fa-arrow-right"></i></a>
                <a href="<?php echo htmlspecialchars($whatsapp_link); ?>" target="_blank" rel="noopener" class="apple-hero-btn-outline"><i class="fa-brands fa-whatsapp"></i> Chat on WhatsApp</a>
            </div>
        </div>
        <div class="apple-hero-visual">
            <div class="apple-hero-img-wrap">
                <img src="<?php echo htmlspecialchars($samsung_hero['image']); ?>" alt="<?php echo htmlspecialchars($samsung_hero['image_alt']); ?>" class="apple-hero-img" loading="eager">
            </div>
        </div>
    </div>
</section>