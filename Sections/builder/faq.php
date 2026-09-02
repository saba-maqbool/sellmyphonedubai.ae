<section class="dynamic-page-section builder-faq faq-section">
    <?php if (!empty($content['kicker']) || !empty($content['heading']) || !empty($content['subtitle'])): ?>
    <div style="text-align:center;">
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
    <div class="faq-container">
        <?php foreach ($items as $faq): ?>
            <div class="faq-item">
                <button type="button" class="faq-question" onclick="this.parentElement.classList.toggle('faq-item-active')">
                    <h3 class="faq-h3"><?php echo htmlspecialchars($faq['question']); ?></h3>
                    <i class="fa-solid fa-chevron-down faq-toggle-icon"></i>
                </button>
                <div class="faq-answer"><p><?php echo nl2br(htmlspecialchars($faq['answer'])); ?></p></div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php if (!empty($items)): ?>
<script type="application/ld+json">
<?php
$faq_schema = ['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => []];
foreach ($items as $faq) {
    $faq_schema['mainEntity'][] = [
        '@type' => 'Question',
        'name' => $faq['question'],
        'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq['answer']],
    ];
}
echo json_encode($faq_schema, JSON_UNESCAPED_SLASHES);
?>
</script>
<?php endif; ?>