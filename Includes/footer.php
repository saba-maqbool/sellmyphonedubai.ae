<?php
require_once(__DIR__ . "/../admin/include/db-connect.php");
require_once(__DIR__ . "/get-whatsapp.php");
require_once(__DIR__ . "/get-site-settings.php");
?>
<footer class="footer" style="color:white; width: 100%;">
     <div class="footer-bg"></div>

        <div class="footer-container">

            <div class="footer-column footer-col-brand">
                <h3 class="h3f"><?php echo htmlspecialchars($site_settings['site_title']); ?></h3>
                <div class="footer-underline"></div>
                <p>
                    <?php echo htmlspecialchars($site_settings['footer_about_text']); ?>
                </p>

                <span class="footer-follow-label">FOLLOW US</span>
                <div class="footer-social">
                    <a href="<?php echo htmlspecialchars($site_settings['facebook_url']); ?>" aria-label="Facebook" target="_blank" rel="noopener"><i class="fab fa-facebook-f"></i></a>
                    <a href="<?php echo htmlspecialchars($site_settings['instagram_url']); ?>" aria-label="Instagram" target="_blank" rel="noopener"><i class="fab fa-instagram"></i></a>
                    <a href="<?php echo htmlspecialchars($site_settings['twitter_url']); ?>" aria-label="Twitter" target="_blank" rel="noopener"><i class="fab fa-twitter"></i></a>
                    <a href="<?php echo htmlspecialchars($site_settings['linkedin_url']); ?>" aria-label="LinkedIn" target="_blank" rel="noopener"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>

            <div class="footer-divider"></div>

            <div class="footer-column">
                <h3 class="h3f-sm">Quick Links</h3>
                <div class="footer-underline"></div>
                <ul>
                    <li>Brands</li>
                    <li>Why choose Us</li>
                    <li>Testimonials</li>
                    <li>Contact</li>
                </ul>
            </div>
            <div class="footer-divider"></div>

            <div class="footer-column">
                <h3 class="h3f-sm">Contact Info</h3>
                <div class="footer-underline"></div>

                <div class="footer-contact-list">
                    <div class="footer-contact-item">
                        <span class="footer-contact-icon"><i class="fas fa-phone-alt"></i></span>
                        <span><?php echo htmlspecialchars($site_settings['footer_phone']); ?></span>
                    </div>
                     <div class="footer-contact-item">
                        <span class="footer-contact-icon"><i class="fab fa-whatsapp"></i></span>
                        <a class="footer-contact-link" href="https://wa.me/<?php echo preg_replace('/\D/', '', $site_settings['footer_whatsapp']); ?>" target="_blank" rel="noopener"><?php echo htmlspecialchars($site_settings['footer_whatsapp']); ?></a>
                    </div>
                    <div class="footer-contact-item">
                        <span class="footer-contact-icon"><i class="fas fa-envelope"></i></span>
                        <a class="footer-contact-link" href="https://mail.google.com/mail/?view=cm&fs=1&to=<?php echo urlencode($site_settings['footer_email']); ?>" target="_blank" rel="noopener"><?php echo htmlspecialchars($site_settings['footer_email']); ?></a>
                    </div>
                    <div class="footer-contact-item footer-contact-item-last">
                        <span class="footer-contact-icon"><i class="fas fa-map-marker-alt"></i></span>
                        <span><?php echo nl2br(htmlspecialchars($site_settings['footer_address'])); ?></span>
                    </div>
                </div>
            </div>

        </div>

        <hr>

        <div class="footer-bottom">
            <p class="footer-p"><?php echo htmlspecialchars($site_settings['copyright_text']); ?>
                <span class="footer-bottom-sep">|</span>
                <i class="fas fa-heart footer-heart-icon"></i> Proudly serving all of Dubai
            </p>
        </div>

    </footer>

    <a href="<?php echo htmlspecialchars($whatsapp_link); ?>" class="whatsapp-float" target="_blank" rel="noopener" aria-label="Chat on WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>

    <?php require_once("script.php") ?>

    </body>

    </html>