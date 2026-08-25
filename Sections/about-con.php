
<?php
require_once(__DIR__ . "/../admin/include/db-connect.php");

$about_section = [
    'kicker' => 'WE COME TO YOU',
    'heading' => "Ready to Sell Your Phone with <br> Dubai's",
    'heading_highlight' => 'Most Trusted Buyer?',
    'description' => 'Your phone loses value every day. Get the best cash price in Dubai now with our fast, safe, and easy service. We offer free pickup and instant payment right at your doorstep.',
    'image' => 'imgs/hero.webp',
    'extra_1' => 'Ready to Sell Your Phone?',
    'extra_2' => 'Get an instant quote now and sell your phone in minutes.',
];
$about_badges = [];

$stmt = mysqli_prepare($conn, "SELECT * FROM home_sections WHERE section_key = 'about_con' LIMIT 1");
mysqli_stmt_execute($stmt);
$about_result = mysqli_stmt_get_result($stmt);
if ($about_row = mysqli_fetch_assoc($about_result)) {
    foreach (['kicker', 'heading', 'heading_highlight', 'description', 'image', 'extra_1', 'extra_2'] as $field) {
        if (!empty($about_row[$field])) {
            $about_section[$field] = $about_row[$field];
        }
    }

    $items_stmt = mysqli_prepare($conn, "SELECT * FROM home_section_items WHERE section_id = ? ORDER BY sort_order ASC");
    mysqli_stmt_bind_param($items_stmt, "i", $about_row['id']);
    mysqli_stmt_execute($items_stmt);
    $items_result = mysqli_stmt_get_result($items_stmt);
    while ($item = mysqli_fetch_assoc($items_result)) {
        $about_badges[] = $item;
    }
}
?>
<section class="pickup-section" id="about-con">
    <div class="pickup-container">
        <div class="pickup-map-wrap">
            <iframe
                class="pickup-map"
                src="https://maps.google.com/maps?q=Dubai,UAE&z=11&output=embed"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                title="Dubai coverage map">
            </iframe>
        </div>

        <div class="pickup-content">

            <span class="pickup-eyebrow"><?php echo htmlspecialchars($about_section['kicker']); ?></span>
            <h2 class="pickup-title"><?php echo $about_section['heading']; ?><span style="color:var(--gold) ;"> <?php echo htmlspecialchars($about_section['heading_highlight']); ?></span></h2>
            <p class="pickup-desc">
                <?php echo htmlspecialchars($about_section['description']); ?>
            </p>

            <div class="pickup-badges">
                <?php foreach ($about_badges as $badge): ?>
                <span class="pickup-badge">
                    <span class="pickup-badge-icon"><i class="<?php echo htmlspecialchars($badge['icon']); ?>"></i></span>
                    <?php echo htmlspecialchars($badge['title']); ?>
                </span>
                <?php endforeach; ?>
            </div>

                <div class="con-buttons">
            <a href="contact" class="btn-primary">
                <i class="fas fa-phone-alt"></i>
                Connect with us 
            </a>

            <a href="https://wa.me/+971502166562" class="btn-primary" target="_blank" rel="noopener">
                <i class="fab fa-whatsapp"></i>
                WhatsApp for Quote
            </a>
        </div>

        </div>

    </div>
</section>

<section class="final-cta-bar-wrap">
    <div class="final-cta-bar">

        <div class="final-cta-img-wrap">
            <img src="<?php echo htmlspecialchars($about_section['image']); ?>" alt="Sell your phone" class="final-cta-img">
        </div>

        <div class="final-cta-text">
            <h3 class="final-cta-heading"><?php echo htmlspecialchars($about_section['extra_1']); ?></h3>
            <p class="final-cta-sub"><?php echo htmlspecialchars($about_section['extra_2']); ?></p>
        </div>

        <div class="final-cta-action">
            <a href="index.php?page=home#brands-section" class="btn-final-cta">Get My Quote</a>
            <span class="final-cta-tags">Fast &bull; Secure &bull; Best Price</span>
        </div>

    </div>
</section>
