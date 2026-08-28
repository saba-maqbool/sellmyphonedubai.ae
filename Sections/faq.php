<?php
require_once(__DIR__ . "/../admin/include/db-connect.php");

$faq_section = [
    'kicker' => 'FAQ',
    'heading' => 'Frequently Asked Questions',
    'description' => 'Everything you need to know before you sell your phone in Dubai',
];


$stmt = mysqli_prepare($conn, "SELECT * FROM home_sections WHERE section_key = 'faq' LIMIT 1");
mysqli_stmt_execute($stmt);
$faq_result = mysqli_stmt_get_result($stmt);
if ($faq_row = mysqli_fetch_assoc($faq_result)) {
    foreach (['kicker', 'heading', 'description'] as $field) {
        if (!empty($faq_row[$field])) {
            $faq_section[$field] = $faq_row[$field];
        }
    }

    $items_stmt = mysqli_prepare($conn, "SELECT * FROM home_section_items WHERE section_id = ? ORDER BY sort_order ASC");
    mysqli_stmt_bind_param($items_stmt, "i", $faq_row['id']);
    mysqli_stmt_execute($items_stmt);
    $items_result = mysqli_stmt_get_result($items_stmt);
    $db_faq_items = [];
    while ($item = mysqli_fetch_assoc($items_result)) {
        $db_faq_items[] = $item;
    }
    if (!empty($db_faq_items)) {
        $faq_items = $db_faq_items;
    }
}
?>
<section class="faq-section" id="faq-section">
    <div class="section-header">
        <span class="section-tag"><?php echo htmlspecialchars($faq_section['kicker']); ?></span>
        <h2 class="section-title"><?php echo htmlspecialchars($faq_section['heading']); ?></h2>
        <p class="section-subtitle"><?php echo htmlspecialchars($faq_section['description']); ?></p>
    </div>

    <div class="faq-container">
    <?php foreach ($faq_items as $index => $item): ?>
    <div class="faq-item">
        <button class="faq-question" type="button" aria-expanded="false">
            <h4 class="faq-h3"><?php echo htmlspecialchars($item['title']); ?></h4>
            <i class="fas fa-chevron-down faq-toggle-icon"></i>
        </button>
        <div class="faq-answer">
            <p><?php echo htmlspecialchars($item['subtitle']); ?></p>
        </div>
    </div>
    <?php endforeach; ?>
</div>
</section>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    <?php foreach ($faq_items as $index => $item): ?>
    {
      "@type": "Question",
      "name": <?php echo json_encode($item['title']); ?>,
      "acceptedAnswer": {
        "@type": "Answer",
        "text": <?php echo json_encode($item['subtitle']); ?>
      }
    }<?php echo $index < count($faq_items) - 1 ? ',' : ''; ?>
    <?php endforeach; ?>
  ]
}
</script>

<script>
document.querySelectorAll('.faq-question').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var item = btn.closest('.faq-item');
        var isActive = item.classList.contains('faq-item-active');

        document.querySelectorAll('.faq-item').forEach(function (el) {
            el.classList.remove('faq-item-active');
            el.querySelector('.faq-question').setAttribute('aria-expanded', 'false');
        });

        if (!isActive) {
            item.classList.add('faq-item-active');
            btn.setAttribute('aria-expanded', 'true');
        }
    });
});
</script>
