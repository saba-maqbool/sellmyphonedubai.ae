<?php
require_once(__DIR__ . "/../admin/include/db-connect.php");
require_once(__DIR__ . "/../Includes/get-whatsapp.php");

// Defaults — shown until content is added/edited on the "Sell iPhone Page" admin screen
$apple_hero = [
    'kicker'            => 'APPLE TRADE-IN DUBAI',
    'heading'           => 'Sell Your',
    'heading_highlight' => 'Apple',
    'extra_1'           => 'Devices in Dubai', // text shown after the highlighted word
    'description'       => 'Buy iPhone in Dubai at the best price — brand new, original, and used iPhones including the iPhone 17 Pro Max, iPhone 16, and iPhone 15, all factory unlocked and 100% genuine. Compare the lowest iPhone price in UAE with exclusive deals and easy trade-in offers. Order your fully tested, warranted iPhone online with same-day delivery all over Dubai.',
    'image'             => 'imgs/apple-hero.png',
    'image_alt'         => 'Sell your iPhone in Dubai',
];

$stmt = mysqli_prepare($conn, "SELECT * FROM home_sections WHERE section_key = 'apple_hero' LIMIT 1");
mysqli_stmt_execute($stmt);
$apple_hero_result = mysqli_stmt_get_result($stmt);
if ($apple_hero_row = mysqli_fetch_assoc($apple_hero_result)) {
    foreach (['kicker', 'heading', 'heading_highlight', 'extra_1', 'description', 'image', 'image_alt'] as $field) {
        if (!empty($apple_hero_row[$field])) {
            $apple_hero[$field] = $apple_hero_row[$field];
        }
    }
}
?>
<section class="apple-hero-section" id="apple-hero-section">
    <div class="apple-hero-bg"></div>
    <div class="apple-hero-overlay"></div>
    <div class="apple-hero-glow"></div>

    <div class="apple-hero-container">

        <div class="apple-hero-content">
            <span class="apple-hero-kicker"><?php echo htmlspecialchars($apple_hero['kicker']); ?></span>

            <h1 class="apple-hero-title"><?php echo htmlspecialchars($apple_hero['heading']); ?> <span><?php echo htmlspecialchars($apple_hero['heading_highlight']); ?></span> <?php echo htmlspecialchars($apple_hero['extra_1']); ?></h1>

            <p class="apple-hero-desc"><?php echo nl2br(htmlspecialchars($apple_hero['description'])); ?></p>
            <div class="apple-hero-cta-row">
                <a href="#series-catalog-section" class="apple-hero-btn-primary">Get Instant Quote <i class="fa-solid fa-arrow-right"></i></a>
                <a href="<?php echo htmlspecialchars($whatsapp_link); ?>" target="_blank" rel="noopener" class="apple-hero-btn-outline"><i class="fa-brands fa-whatsapp"></i> Chat on WhatsApp</a>
            </div>
        </div>

        <div class="apple-hero-visual">
            <div class="apple-hero-img-wrap">
                <img src="<?php echo htmlspecialchars($apple_hero['image']); ?>" alt="<?php echo htmlspecialchars($apple_hero['image_alt']); ?>" class="apple-hero-img" loading="eager">
            </div>
        </div>

    </div>
</section>