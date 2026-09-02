<?php
require_once(__DIR__ . "/../admin/include/db-connect.php");

// Defaults — shown until content is edited on the "Sell iPhone Page" admin screen
$apple_resale = [
    'kicker'            => 'ABOUT IPHONE RESALE VALUE',
    'heading'           => 'iPhone Resale Value in Dubai:',
    'heading_highlight' => 'Everything You Should Know',
    'description'       => "iPhones are built to last. With premium materials, powerful performance, and long-term iOS updates, they continue to deliver an exceptional experience year after year. This is exactly why the iPhone resale value in Dubai holds stronger than almost any other smartphone on the market.",
    'extra_1'           => "In Dubai and across the UAE, demand for a used iPhone in Dubai remains consistently high. Buyers trust Apple's quality, reliability, and security, which keeps iPhone trade-in value strong even for older models.",
    'extra_2'           => "Whether you're upgrading to the latest iPhone or simply want to sell your iPhone in Dubai, knowing your device's current iPhone resale price helps you get the best possible offer at the right time.",
    'image'             => 'imgs/about-apple.png',
    'image_alt'         => 'iPhone resale value in Dubai',
    'button_text'       => 'Check Your iPhone Value',
    'button_link'       => '#series-catalog-section',
];

$stmt = mysqli_prepare($conn, "SELECT * FROM home_sections WHERE section_key = 'apple_resale' LIMIT 1");
mysqli_stmt_execute($stmt);
$apple_resale_result = mysqli_stmt_get_result($stmt);
if ($apple_resale_row = mysqli_fetch_assoc($apple_resale_result)) {
    foreach (['kicker', 'heading', 'heading_highlight', 'description', 'extra_1', 'extra_2', 'image', 'image_alt', 'button_text', 'button_link'] as $field) {
        if (!empty($apple_resale_row[$field])) {
            $apple_resale[$field] = $apple_resale_row[$field];
        }
    }
}
?>
<section class="resale-value-section" id="resale-value-section">
    <div class="resale-value-container">

        <div class="resale-value-visual">
            <div class="resale-value-glow"></div>
            <div class="resale-value-img-wrap">
                <img src="<?php echo htmlspecialchars($apple_resale['image']); ?>" alt="<?php echo htmlspecialchars($apple_resale['image_alt']); ?>" class="resale-value-img" loading="lazy">
            </div>
        </div>

        <div class="resale-value-content">
            <span class="resale-value-tag"><i class="fa-solid fa-arrow-trend-up"></i> <?php echo htmlspecialchars($apple_resale['kicker']); ?></span>
            <h2 class="resale-value-title"><?php echo htmlspecialchars($apple_resale['heading']); ?><br><span><?php echo htmlspecialchars($apple_resale['heading_highlight']); ?></span></h2>

            <p class="resale-value-desc"><?php echo nl2br(htmlspecialchars($apple_resale['description'])); ?></p>

            <?php if (!empty($apple_resale['extra_1'])): ?>
            <p class="resale-value-desc"><?php echo nl2br(htmlspecialchars($apple_resale['extra_1'])); ?></p>
            <?php endif; ?>

            <?php if (!empty($apple_resale['extra_2'])): ?>
            <p class="resale-value-desc"><?php echo nl2br(htmlspecialchars($apple_resale['extra_2'])); ?></p>
            <?php endif; ?>

            <a href="<?php echo htmlspecialchars($apple_resale['button_link']); ?>" class="resale-value-btn">
                <?php echo htmlspecialchars($apple_resale['button_text']); ?> <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

    </div>
</section>