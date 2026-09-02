<?php $flex_dir = ($content['image_position'] ?? 'right') === 'left' ? 'row-reverse' : 'row'; ?>
<section class="dynamic-page-section builder-text-image">
    <div class="dynamic-page-container" style="display:flex; flex-direction:<?php echo $flex_dir; ?>; align-items:center; gap:48px; flex-wrap:wrap; padding:40px 20px;">
        <div style="flex:1 1 420px;">
            <?php if (!empty($content['kicker'])): ?>
                <span class="pickup-eyebrow"><?php echo htmlspecialchars($content['kicker']); ?></span>
            <?php endif; ?>
            <h2 class="pickup-title"><?php echo htmlspecialchars($content['heading'] ?? ''); ?></h2>
            <p class="pickup-desc"><?php echo nl2br(htmlspecialchars($content['description'] ?? '')); ?></p>
            <?php if (!empty($content['button_text']) && !empty($content['button_link'])): ?>
                <a href="<?php echo htmlspecialchars($content['button_link']); ?>" class="btn-primary"><?php echo htmlspecialchars($content['button_text']); ?></a>
            <?php endif; ?>
        </div>
        <?php if (!empty($content['image'])): ?>
            <div style="flex:1 1 380px;">
                <img src="<?php echo htmlspecialchars($content['image']); ?>" alt="<?php echo htmlspecialchars($content['heading'] ?? ''); ?>" style="width:100%; border-radius:16px; display:block;">
            </div>
        <?php endif; ?>
    </div>
</section>