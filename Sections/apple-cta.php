<?php
require_once(__DIR__ . "/../admin/include/db-connect.php");
require_once(__DIR__ . "/../Includes/get-whatsapp.php");
?>
<section class="apple-cta-section">
    <div class="apple-cta-box">
        <div class="apple-cta-text">
            <h3 class="apple-cta-heading">Ready to Sell Your Apple Device?</h3>
            <p class="apple-cta-sub">Get an instant quote now, or chat with us directly on WhatsApp for a quick response.</p>
        </div>
        <div class="apple-cta-actions">
            <a href="#apple-catalog-wrap" class="apple-hero-btn-primary" onclick="showAppleCatalog(event)">Get Instant Quote <i class="fa-solid fa-arrow-right"></i></a>
            <a href="<?php echo htmlspecialchars($whatsapp_link); ?>" target="_blank" rel="noopener" class="apple-hero-btn-outline apple-cta-outline"><i class="fa-brands fa-whatsapp"></i> Chat on WhatsApp</a>
        </div>
    </div>
</section>