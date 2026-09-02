<section class="dynamic-page-section builder-rich-text">
    <div class="dynamic-page-container">
        <?php if (!empty($content['kicker']) || !empty($content['heading']) || !empty($content['subtitle'])): ?>
        <div style="text-align:center; margin-bottom:20px;">
            <?php if (!empty($content['kicker'])): ?>
                <span class="sec-span"><?php echo htmlspecialchars($content['kicker']); ?></span>
            <?php endif; ?>
            <?php if (!empty($content['heading'])): ?>
                <h2 class="section-title"><?php echo htmlspecialchars($content['heading']); ?></h2>
            <?php endif; ?>
            <?php if (!empty($content['subtitle'])): ?>
                <p class="section-subtitle"><?php echo htmlspecialchars($content['subtitle']); ?></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <?php echo $content['html'] ?? ''; ?>
    </div>
</section>