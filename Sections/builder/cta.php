<?php $cta_sub = $content['subtitle'] ?? ($content['subtext'] ?? ''); ?>
<section class="dynamic-page-section builder-cta">
    <div class="final-cta-bar-wrap">
        <div class="final-cta-bar" style="padding:32px 48px;">
            <div class="final-cta-text">
                <?php if (!empty($content['kicker'])): ?>
                    <span class="sec-span"><?php echo htmlspecialchars($content['kicker']); ?></span>
                <?php endif; ?>
                <h2 class="final-cta-heading"><?php echo htmlspecialchars($content['heading'] ?? ''); ?></h2>
                <p class="final-cta-sub"><?php echo htmlspecialchars($cta_sub); ?></p>
            </div>
            <?php if (!empty($content['button_text']) && !empty($content['button_link'])): ?>
                <div class="final-cta-action">
                    <a href="<?php echo htmlspecialchars($content['button_link']); ?>" class="btn-final-cta"><?php echo htmlspecialchars($content['button_text']); ?></a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>