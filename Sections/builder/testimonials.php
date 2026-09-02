<section class="dynamic-page-section builder-testimonials testimonials">
    <?php if (!empty($content['kicker']) || !empty($content['heading']) || !empty($content['subtitle'])): ?>
    <div style="text-align:center;">
        <?php if (!empty($content['kicker'])): ?>
            <span class="sec-span"><?php echo htmlspecialchars($content['kicker']); ?></span>
        <?php endif; ?>
        <?php if (!empty($content['heading'])): ?>
            <h2 class="section-title" style="color:#fff;"><?php echo htmlspecialchars($content['heading']); ?></h2>
        <?php endif; ?>
        <?php if (!empty($content['subtitle'])): ?>
            <p class="section-subtitle"><?php echo htmlspecialchars($content['subtitle']); ?></p>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    <div class="testimonials-grid">
        <?php foreach ($items as $t): ?>
            <div class="testimonial-card">
                <div class="testimonial-rating">
                    <?php for ($s = 0; $s < 5; $s++): ?>
                        <i class="fa-solid fa-star" style="<?php echo $s < (int)$t['rating'] ? '' : 'opacity:.25;'; ?>"></i>
                    <?php endfor; ?>
                </div>
                <p><?php echo htmlspecialchars($t['review']); ?></p>
                <div class="testimonial-author">
                    <?php if (!empty($t['image'])): ?>
                        <img src="<?php echo htmlspecialchars($t['image']); ?>" class="author-avatar" style="object-fit:cover;" alt="<?php echo htmlspecialchars($t['name']); ?>">
                    <?php else: ?>
                        <div class="author-avatar"><?php echo strtoupper(substr($t['name'], 0, 1)); ?></div>
                    <?php endif; ?>
                    <div>
                        <div class="author-name"><?php echo htmlspecialchars($t['name']); ?></div>
                        <?php if (!empty($t['designation'])): ?>
                            <small style="color:#c3c9d6;"><?php echo htmlspecialchars($t['designation']); ?></small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>