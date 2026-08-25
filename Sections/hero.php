<?php
require_once(__DIR__ . "/../admin/include/db-connect.php");

$hero = [
    'kicker' => 'The smarter way to',
    'heading' => 'Sell your phone',
    'heading_highlight' => 'in Dubai',
    'description' => 'Get the best price for your used iPhone, Samsung and more. Quick, secure &amp; trusted.',
    'image' => 'imgs/heroo.png',
    'extra_1' => '4.9/5',
    'extra_2' => 'Based on 2,500+ reviews',
];
$hero_features = [];

$stmt = mysqli_prepare($conn, "SELECT * FROM home_sections WHERE section_key = 'hero' LIMIT 1");
mysqli_stmt_execute($stmt);
$hero_result = mysqli_stmt_get_result($stmt);
if ($hero_row = mysqli_fetch_assoc($hero_result)) {
    foreach (['kicker', 'heading', 'heading_highlight', 'description', 'image', 'extra_1', 'extra_2'] as $field) {
        if (!empty($hero_row[$field])) {
            $hero[$field] = $hero_row[$field];
        }
    }

    $items_stmt = mysqli_prepare($conn, "SELECT * FROM home_section_items WHERE section_id = ? ORDER BY sort_order ASC");
    mysqli_stmt_bind_param($items_stmt, "i", $hero_row['id']);
    mysqli_stmt_execute($items_stmt);
    $items_result = mysqli_stmt_get_result($items_stmt);
    while ($item = mysqli_fetch_assoc($items_result)) {
        $hero_features[] = $item;
    }
}
?>
<section class="hero-section" id="hero-section">
  <div class="hero-bg"></div>
  <div class="hero-overlay"></div>

  <div class="hero-container">

    <div class="hero-content">
      <span class="hero-span"><?php echo htmlspecialchars($hero['kicker']); ?></span>
      <h1 class="hero-title"><?php echo htmlspecialchars($hero['heading']); ?><br><span><?php echo htmlspecialchars($hero['heading_highlight']); ?></span></h1>
      <p class="hero-desc"><?php echo $hero['description']; ?></p>

      <div class="hero-features">
        <?php foreach ($hero_features as $feature): ?>
        <div class="feature-item">
          <span class="feature-icon"><i class="<?php echo htmlspecialchars($feature['icon']); ?>"></i></span>
          <span class="feature-text"><?php echo htmlspecialchars($feature['title']); ?><br><small><?php echo htmlspecialchars($feature['subtitle']); ?></small></span>
        </div>
        <?php endforeach; ?>
      </div>

      <div class="hero-google-rating">
        <i class="fa-brands fa-google"></i>
        <span class="rating-score"><?php echo htmlspecialchars($hero['extra_1']); ?></span>
        <span class="rating-stars">★★★★★</span>
        <span class="rating-count"><?php echo htmlspecialchars($hero['extra_2']); ?></span>
      </div>
    </div>

  </div>

  
  <div class="hero-phone-visual">
    <img src="<?php echo htmlspecialchars($hero['image']); ?>" alt="Sell your iPhone in Dubai" class="hero-phone-img">
  </div>

  <div class="hero-quote-card">
    <span class="fa-kicker">Free Doorstep Pickup</span>
    <div class="fa-kicker-line"></div>

    <h3 class="quote-form-title">Book a Free Pick-up</h3>

    <form class="quote-form" id="pickupForm" onsubmit="return submitPickupForm(event);">

        <div id="pickupFormAlert" style="display:none; font-size:12px; padding:8px 10px; border-radius:8px;"></div>

        <label class="quote-field">
            <input type="text" class="quote-input" name="name" placeholder="Full Name" required>
        </label>

        <label class="quote-field">
            <input type="tel" class="quote-input" name="phone" placeholder="WhatsApp Number" required>
        </label>

        <a href="#valuation-step" class="btn-quote-continue" id="pickupSubmitBtn">
            Get Free Pickup
        </a>

    </form>

    <span class="fa-footnote"><i class="fa-solid fa-lock" style="color:#E8C97A"></i>100% Secure and Trusted</span>
  </div>

</section>