<?php
require_once(__DIR__ . "/../admin/include/db-connect.php");

$mission_items = [
    ['icon' => 'fa-solid fa-circle-check', 'title' => 'Our Mission', 'content' => 'To give people in Dubai the fastest and easiest way to sell their phones for the best price. We make upgrading your technology simple and fair for everyone.'],
    ['icon' => 'fa-solid fa-eye', 'title' => 'Our Vision', 'content' => 'To be the best place in Dubai to sell your phone. We promise to give everyone a great experience and the best price.'],
    ['icon' => 'fa-solid fa-handshake', 'title' => 'Our Values', 'content' => 'We believe in being honest, doing the right thing, and always putting you first. We work hard to find new ways to help and care for our community.'],
];

$stmt = mysqli_prepare($conn, "SELECT id FROM home_sections WHERE section_key = 'about_mission' LIMIT 1");
mysqli_stmt_execute($stmt);
$mission_result = mysqli_stmt_get_result($stmt);
if ($mission_row = mysqli_fetch_assoc($mission_result)) {
    $items_stmt = mysqli_prepare($conn, "SELECT * FROM home_section_items WHERE section_id = ? ORDER BY sort_order ASC");
    mysqli_stmt_bind_param($items_stmt, "i", $mission_row['id']);
    mysqli_stmt_execute($items_stmt);
    $items_result = mysqli_stmt_get_result($items_stmt);
    $db_items = [];
    while ($item = mysqli_fetch_assoc($items_result)) {
        $db_items[] = $item;
    }
    if (!empty($db_items)) {
        $mission_items = $db_items;
    }
}

$card_classes = ['mission-card', 'vision-card', 'value-card'];
$icon_classes = ['mission-icon', 'vision-icon', 'value-icon'];
?>
<section class="mission" id="mission">
    <div class="mission-container">
        <?php foreach ($mission_items as $i => $item): ?>
        <div class="<?php echo $card_classes[$i] ?? 'mission-card'; ?>">
            <div class="<?php echo $icon_classes[$i] ?? 'mission-icon'; ?>">
                <i class="<?php echo htmlspecialchars($item['icon']); ?>"></i>
            </div>
            <h3 class="h3m"><?php echo htmlspecialchars($item['title']); ?></h3>
            <p class="p"><?php echo htmlspecialchars($item['content']); ?></p>
        </div>
        <?php endforeach; ?>
    </div>
</section>