<?php
require_once(__DIR__ . "/../admin/include/db-connect.php");

$chooseus_section = [
    'kicker' => 'BENEFITS',
    'heading' => 'Why Choose <span>SellMyPhoneDubai</span>',
    'description' => 'Why choose SellMyPhoneDubai for your phone selling needs?',
    'image' => 'imgs/choos-us.png',
    'extra_1' => '4.9/5',
    'extra_2' => 'Trusted by 2500+ Happy Customers',
];
$chooseus_items = [];

$stmt = mysqli_prepare($conn, "SELECT * FROM home_sections WHERE section_key = 'chooseus' LIMIT 1");
mysqli_stmt_execute($stmt);
$chooseus_result = mysqli_stmt_get_result($stmt);
if ($chooseus_row = mysqli_fetch_assoc($chooseus_result)) {
    foreach (['kicker', 'heading', 'description', 'image', 'extra_1', 'extra_2'] as $field) {
        if (!empty($chooseus_row[$field])) {
            $chooseus_section[$field] = $chooseus_row[$field];
        }
    }

    $items_stmt = mysqli_prepare($conn, "SELECT * FROM home_section_items WHERE section_id = ? ORDER BY sort_order ASC");
    mysqli_stmt_bind_param($items_stmt, "i", $chooseus_row['id']);
    mysqli_stmt_execute($items_stmt);
    $items_result = mysqli_stmt_get_result($items_stmt);
    while ($item = mysqli_fetch_assoc($items_result)) {
        $chooseus_items[] = $item;
    }
}
?>
<section class="why-choose-us" id="why-choose-us">
    <div class="section-header">
        <span class="section-tag"><?php echo htmlspecialchars($chooseus_section['kicker']); ?></span>
        <h3 class="section-title"><?php echo $chooseus_section['heading']; ?></h3>
        <p class="section-subtitle"><?php echo htmlspecialchars($chooseus_section['description']); ?></p>
    </div>

    <div class="why-container">

        <div class="why-left">

            <?php foreach ($chooseus_items as $index => $item): ?>
            <div class="why-feature-row<?php echo $index === count($chooseus_items) - 1 ? ' why-feature-row-last' : ''; ?>">
                <div class="why-feature-icon"><i class="<?php echo htmlspecialchars($item['icon']); ?>"></i></div>
                <div class="why-feature-content">
                    <h3 class="f-h3"><?php echo htmlspecialchars($item['title']); ?></h3>
                    <p class="f-p"><?php echo htmlspecialchars($item['subtitle']); ?></p>
                </div>
            </div>
            <?php endforeach; ?>

        </div>

        <div class="why-right">
            <div class="why-visual">
                <div class="why-visual-circle"></div>
                <img src="<?php echo htmlspecialchars($chooseus_section['image']); ?>" alt="Phones" class="why-visual-img">
            </div>

            <div class="why-rating-card">
                <div class="why-avatars">
                    <img src="imgs/avater1.webp" alt="" class="why-avatar">
                    <img src="imgs/avater4.webp" alt="" class="why-avatar">
                    <img src="imgs/avater2.webp" alt="" class="why-avatar">
                     <img src="imgs/avater3.webp" alt="" class="why-avatar">
                </div>
                <div class="why-rating-divider"></div>
                <div class="why-rating-stars">
                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    <span class="why-rating-score"><?php echo htmlspecialchars($chooseus_section['extra_1']); ?></span>
                    <span class="why-rating-text"> <?php echo htmlspecialchars($chooseus_section['extra_2']); ?></span>
                </div>
            </div>
        </div>

    </div>
</section>