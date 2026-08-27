<?php
require_once(__DIR__ . "/../admin/include/db-connect.php");

$faq_section = [
    'kicker' => 'FAQ',
    'heading' => 'Frequently Asked Questions',
    'description' => 'Everything you need to know before you sell your phone in Dubai',
];

// Default FAQ items (used until the "faq" section is populated in the admin panel)
$faq_items = [
    [
        'title' => 'How do I sell my phone in Dubai?',
        'subtitle' => 'Simply select your phone model and condition to get an instant quote, book a free doorstep pickup anywhere in Dubai or the UAE, and get paid in cash the same day after a quick inspection.',
    ],
    [
        'title' => 'How much can I sell my phone for?',
        'subtitle' => 'Your price depends on the model, storage, and condition of your device. Use our instant quote tool above to get an accurate, no-obligation price for your iPhone, Samsung, or any other phone.',
    ],
    [
        'title' => 'Can I sell my phone with a cracked or broken screen?',
        'subtitle' => 'Yes. We buy phones in any condition, including cracked screens, water damage, or other faults. The price will be adjusted based on the damage, but you can still sell your phone for cash today.',
    ],
    [
        'title' => 'Is it safe to sell my old phone online?',
        'subtitle' => 'Absolutely. We securely wipe all personal data from your device during inspection, and every transaction is documented, so you can sell your used phone online with full peace of mind.',
    ],
    [
        'title' => 'Do you offer free pickup across the UAE?',
        'subtitle' => 'Yes, we offer free doorstep pickup across Dubai and other emirates. Just book a pickup slot that works for you, and our team will collect your phone and pay you on the spot.',
    ],
    [
        'title' => 'How fast will I get paid after I sell my phone?',
        'subtitle' => 'Payment is instant. Once our team inspects your phone at pickup and confirms its condition matches your quote, you get paid in cash or bank transfer the same day.',
    ],
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
        <div class="faq-item<?php echo $index === 0 ? ' faq-item-active' : ''; ?>">
            <button class="faq-question" type="button" aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>">
                <span><?php echo htmlspecialchars($item['title']); ?></span>
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
