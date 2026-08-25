<?php
require_once(__DIR__ . "/../admin/include/db-connect.php");

$story = [
    'kicker'            => 'OUR STORY',
    'heading'           => 'Making it Easy to Sell Your',
    'heading_highlight' => 'Phone in Dubai',
    'description'       => "Since 2018, we've been helping you sell your used phones in Dubai without the stress. We're here to make sure you get a fair price, quickly and easily.",
];
$story_stats = [];

$stmt = mysqli_prepare($conn, "SELECT * FROM home_sections WHERE section_key = 'about_story' LIMIT 1");
mysqli_stmt_execute($stmt);
$story_result = mysqli_stmt_get_result($stmt);
if ($story_row = mysqli_fetch_assoc($story_result)) {
    foreach (['kicker', 'heading', 'heading_highlight', 'description'] as $field) {
        if (!empty($story_row[$field])) {
            $story[$field] = $story_row[$field];
        }
    }

    $items_stmt = mysqli_prepare($conn, "SELECT * FROM home_section_items WHERE section_id = ? ORDER BY sort_order ASC");
    mysqli_stmt_bind_param($items_stmt, "i", $story_row['id']);
    mysqli_stmt_execute($items_stmt);
    $items_result = mysqli_stmt_get_result($items_stmt);
    while ($item = mysqli_fetch_assoc($items_result)) {
        $story_stats[] = $item;
    }
}
?>
<section class="story" id="story">
    <div class="section-header">
        <span class="section-tag"><?php echo htmlspecialchars($story['kicker']); ?></span>
        <h3 class="section-title"><?php echo htmlspecialchars($story['heading']); ?> <br> <span><?php echo htmlspecialchars($story['heading_highlight']); ?></span></h3>
        <p class="section-subtitle"><?php echo htmlspecialchars($story['description']); ?></p>
    </div>
    <div class="about-container">
        <?php foreach ($story_stats as $stat): ?>
        <div class="about-card">
            <h3 class="h3"><?php echo htmlspecialchars($stat['title']); ?></h3>
            <p class="p"><?php echo htmlspecialchars($stat['subtitle']); ?></p>
        </div>
        <?php endforeach; ?>
    </div>
</section>