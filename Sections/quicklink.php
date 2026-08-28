<?php
require_once(__DIR__ . "/../admin/include/db-connect.php");

$quicklink_section = [
    'kicker' => 'QUICK LINKS',
    'heading' => 'Quick Access',
    'description' => 'Find what you need quickly with our helpful resources',
];
$quicklink_items = [];

$stmt = mysqli_prepare($conn, "SELECT * FROM home_sections WHERE section_key = 'quicklink' LIMIT 1");
mysqli_stmt_execute($stmt);
$quicklink_result = mysqli_stmt_get_result($stmt);
if ($quicklink_row = mysqli_fetch_assoc($quicklink_result)) {
    foreach (['kicker', 'heading', 'description'] as $field) {
        if (!empty($quicklink_row[$field])) {
            $quicklink_section[$field] = $quicklink_row[$field];
        }
    }

    $items_stmt = mysqli_prepare($conn, "SELECT * FROM home_section_items WHERE section_id = ? ORDER BY sort_order ASC");
    mysqli_stmt_bind_param($items_stmt, "i", $quicklink_row['id']);
    mysqli_stmt_execute($items_stmt);
    $items_result = mysqli_stmt_get_result($items_stmt);
    while ($item = mysqli_fetch_assoc($items_result)) {
        $quicklink_items[] = $item;
    }
}
?>
<section class="quick-link" id="quick-link">
        <div class="section-header">
            <span class="section-tag"><?php echo htmlspecialchars($quicklink_section['kicker']); ?></span>
            <h2 class="section-title"><?php echo htmlspecialchars($quicklink_section['heading']); ?></h2>
            <p class="section-subtitle"><?php echo htmlspecialchars($quicklink_section['description']); ?></p>
        </div>
        <div class="quick-link-container">
            <div class="quick-links-grid">
                <?php foreach ($quicklink_items as $item):
                    $card_link = !empty($item['link']) ? $item['link'] : '#';
                ?>
                <a href="<?php echo htmlspecialchars($card_link); ?>" class="quick-link-card">
                    <div class="quick-link-icon">
                        <i class="<?php echo htmlspecialchars($item['icon']); ?>"></i>
                    </div>
                    <h3 class="h3s"><?php echo htmlspecialchars($item['title']); ?></h3>
                    <p><?php echo htmlspecialchars($item['subtitle']); ?></p>
                    <h5><?php echo htmlspecialchars($item['content']); ?>
                        <i class="fas fa-arrow-right"></i>
                    </h5>
                </a>
                <?php endforeach; ?>
            </div>

        </div>

    </section>