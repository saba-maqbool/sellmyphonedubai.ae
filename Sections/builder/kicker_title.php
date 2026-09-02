<section class="dynamic-page-section builder-kicker-title" style="text-align:center; padding:30px 20px;">
    <div class="dynamic-page-container">
        <?php if (!empty($content['kicker'])): ?>
            <span class="sec-span"><?php echo htmlspecialchars($content['kicker']); ?></span>
        <?php endif; ?>
        <h2 class="section-title"><?php echo htmlspecialchars($content['heading'] ?? ''); ?></h2>
        <?php if (!empty($content['subtitle'])): ?>
            <p class="section-subtitle"><?php echo htmlspecialchars($content['subtitle']); ?></p>
        <?php endif; ?>
    </div>
</section>