<?php
require_once(__DIR__ . "/../admin/include/db-connect.php");

   $samsung_resale = [
    'kicker'            => 'ABOUT SAMSUNG RESALE VALUE',
    'heading'           => 'Samsung Resale Value in Dubai:',
    'heading_highlight' => 'Everything You Should Know',
    'description'       => "Samsung Galaxy phones are known for their vibrant AMOLED displays, powerful performance, and long-term One UI software updates. This is exactly why the Samsung resale value in Dubai stays competitive across almost every Galaxy series on the market, from the S Series flagships to the Z Fold and Flip line.",
    'extra_1'           => "In Dubai and across the UAE, demand for a used Samsung phone in Dubai remains consistently high. Buyers trust Samsung's build quality, camera performance, and reliability, which keeps Galaxy trade-in value strong even for older models like the Galaxy S22 and S21 Series.",
    'extra_2'           => "Whether you're upgrading to the latest Galaxy flagship or simply want to sell your Samsung in Dubai, knowing your device's current Samsung resale price helps you get the best possible offer at the right time — with free doorstep pickup and same-day payment.",
    'image'             => 'imgs/samsung.png',
    'image_alt'         => 'Samsung Galaxy resale value in Dubai',
    'button_text'       => 'Check Your Samsung Value',
    'button_link'       => '#series-catalog-section',
];

$stmt = mysqli_prepare($conn, "SELECT * FROM home_sections WHERE section_key = 'samsung_resale' LIMIT 1");
mysqli_stmt_execute($stmt);
$samsung_resale_result = mysqli_stmt_get_result($stmt);
if ($samsung_resale_row = mysqli_fetch_assoc($samsung_resale_result)) {
    foreach (['kicker', 'heading', 'heading_highlight', 'description', 'extra_1', 'extra_2', 'image', 'image_alt', 'button_text', 'button_link'] as $field) {
        if (!empty($samsung_resale_row[$field])) {
            $samsung_resale[$field] = $samsung_resale_row[$field];
        }
    }
}
?>
<section class="resale-value-section" id="samsung-resale-value-section">
    <div class="resale-value-container">
        <div class="resale-value-visual">
            <div class="resale-value-glow"></div>
            <div class="resale-value-img-wrap">
                <img src="<?php echo htmlspecialchars($samsung_resale['image']); ?>" alt="<?php echo htmlspecialchars($samsung_resale['image_alt']); ?>" class="resale-value-img" loading="lazy">
            </div>
        </div>
        <div class="resale-value-content">
            <span class="resale-value-tag"><i class="fa-solid fa-arrow-trend-up"></i> <?php echo htmlspecialchars($samsung_resale['kicker']); ?></span>
            <h2 class="resale-value-title"><?php echo htmlspecialchars($samsung_resale['heading']); ?><br><span><?php echo htmlspecialchars($samsung_resale['heading_highlight']); ?></span></h2>
            <p class="resale-value-desc"><?php echo nl2br(htmlspecialchars($samsung_resale['description'])); ?></p>
            <?php if (!empty($samsung_resale['extra_1'])): ?>
            <p class="resale-value-desc"><?php echo nl2br(htmlspecialchars($samsung_resale['extra_1'])); ?></p>
            <?php endif; ?>
            <?php if (!empty($samsung_resale['extra_2'])): ?>
            <p class="resale-value-desc"><?php echo nl2br(htmlspecialchars($samsung_resale['extra_2'])); ?></p>
            <?php endif; ?>
            <a href="<?php echo htmlspecialchars($samsung_resale['button_link']); ?>" class="resale-value-btn">
                <?php echo htmlspecialchars($samsung_resale['button_text']); ?> <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>