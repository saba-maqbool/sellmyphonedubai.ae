<?php
require_once(__DIR__ . "/../admin/include/db-connect.php");

$guide_section = [
    'kicker' => 'ABOUT US',
    'heading' => 'Selling Your Phone in Dubai, Made Simple',
    'description' => "Every phone loses value the moment a newer model is announced...", // fallback, poora text yahan bhi rakh sakte hain
];

$stmt = mysqli_prepare($conn, "SELECT * FROM home_sections WHERE section_key = 'guide' LIMIT 1");
mysqli_stmt_execute($stmt);
$guide_result = mysqli_stmt_get_result($stmt);
if ($guide_row = mysqli_fetch_assoc($guide_result)) {
    foreach (['kicker', 'heading', 'description'] as $field) {
        if (!empty($guide_row[$field])) {
            $guide_section[$field] = $guide_row[$field];
        }
    }
}

// paragraphs ko double-newline par split karke safely render karte hain
$guide_paragraphs = preg_split('/\r\n\r\n|\n\n/', trim($guide_section['description']));
?>
<section class="guide-section" id="guide-section">
    <div class="guide-container">
        <span class="guide-eyebrow"><?php echo htmlspecialchars($guide_section['kicker']); ?></span>
        <h2 class="guide-title"><?php echo htmlspecialchars($guide_section['heading']); ?></h2>

        <div class="guide-content" id="guideContent">
            <?php foreach ($guide_paragraphs as $para): ?>
                <p><?php echo nl2br(htmlspecialchars(trim($para))); ?></p>
            <?php endforeach; ?>
        </div>

        <button type="button" class="guide-toggle-btn" id="guideToggleBtn" aria-expanded="false" aria-controls="guideContent">
            Read More <i class="fas fa-chevron-down"></i>
        </button>
    </div>
</section>

<script>
(function () {
    var btn = document.getElementById('guideToggleBtn');
    var content = document.getElementById('guideContent');
    if (!btn || !content) return;

    btn.addEventListener('click', function () {
        var expanded = content.classList.toggle('guide-content-expanded');
        btn.setAttribute('aria-expanded', expanded ? 'true' : 'false');
        btn.innerHTML = expanded
            ? 'Read Less <i class="fas fa-chevron-up"></i>'
            : 'Read More <i class="fas fa-chevron-down"></i>';
    });
})();
</script>