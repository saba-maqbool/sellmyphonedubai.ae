<?php
require_once(__DIR__ . "/../admin/include/db-connect.php");
require_once(__DIR__ . "/../Includes/get-whatsapp.php");
?>
<section class="apple-hero-section" id="apple-hero-section">
    <div class="apple-hero-glow"></div>

    <div class="apple-hero-container">

        <div class="apple-hero-content">
            <span class="apple-hero-kicker"><i class="fa-brands fa-apple"></i> APPLE TRADE-IN DUBAI</span>

            <h1 class="apple-hero-title">Sell Your <span>Apple</span> Devices in Dubai</h1>

            <p class="apple-hero-desc">iphone device _ get a fair, transparent price in minutes. Free doorstep pickup and same-day secure payment, anywhere in Dubai.</p>

            <div class="apple-hero-stats">
                <div class="apple-hero-stat">
                    <span class="apple-hero-stat-icon"><i class="fa-solid fa-shield-heart"></i></span>
                    <span class="apple-hero-stat-text">100% Genuine<br><small>Every device checked</small></span>
                </div>
                <div class="apple-hero-stat">
                    <span class="apple-hero-stat-icon"><i class="fa-solid fa-truck-fast"></i></span>
                    <span class="apple-hero-stat-text">Free Pickup<br><small>Across all of Dubai</small></span>
                </div>
                <div class="apple-hero-stat">
                    <span class="apple-hero-stat-icon"><i class="fa-solid fa-bolt"></i></span>
                    <span class="apple-hero-stat-text">Instant Payment<br><small>Paid the same day</small></span>
                </div>
            </div>

            <div class="apple-hero-cta-row">
                <a href="#apple-catalog-wrap" class="apple-hero-btn-primary">Get Instant Quote <i class="fa-solid fa-arrow-right"></i></a>
                <a href="<?php echo htmlspecialchars($whatsapp_link); ?>" target="_blank" rel="noopener" class="apple-hero-btn-outline"><i class="fa-brands fa-whatsapp"></i> Chat on WhatsApp</a>
            </div>
        </div>

        <div class="apple-hero-visual">
            <div class="apple-hero-img-wrap">
                <img src="imgs/apple-hero.png" alt="Sell your iPhone in Dubai" class="apple-hero-img" loading="eager">
            </div>
        </div>

    </div>
</section>