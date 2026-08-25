<?php
require_once(__DIR__ . "/../admin/include/db-connect.php");

$process_section = [
    'kicker' => 'PROCESS',
    'heading' => 'Sell Your Phone in <span>3</span> Easy Steps',
    'description' => 'Our simple and transparent process makes selling your phone quick, safe, and profitable',
];
$process_items = [];

$stmt = mysqli_prepare($conn, "SELECT * FROM home_sections WHERE section_key = 'process' LIMIT 1");
mysqli_stmt_execute($stmt);
$process_result = mysqli_stmt_get_result($stmt);
if ($process_row = mysqli_fetch_assoc($process_result)) {
    foreach (['kicker', 'heading', 'description'] as $field) {
        if (!empty($process_row[$field])) {
            $process_section[$field] = $process_row[$field];
        }
    }

    $items_stmt = mysqli_prepare($conn, "SELECT * FROM home_section_items WHERE section_id = ? ORDER BY sort_order ASC");
    mysqli_stmt_bind_param($items_stmt, "i", $process_row['id']);
    mysqli_stmt_execute($items_stmt);
    $items_result = mysqli_stmt_get_result($items_stmt);
    while ($item = mysqli_fetch_assoc($items_result)) {
        $process_items[] = $item;
    }
}
?>
<section class="process" id="process">
    <div class="section-header">
        <span class="section-tag"><?php echo htmlspecialchars($process_section['kicker']); ?></span>
        <h2 class="section-title"><?php echo $process_section['heading']; ?></h2>
        <p class="section-subtitle"><?php echo htmlspecialchars($process_section['description']); ?></p>
    </div>
    <div class="process-container">
        <div class="process-flex" style="display: flex; flex-direction: row; ">
            <?php $step_classes = ['step-one', 'step-two', 'step-three']; ?>
            <?php foreach ($process_items as $index => $item): ?>
            <div class="<?php echo $step_classes[$index] ?? 'step-one'; ?>">
                <div class="process-card">
                    <div class="step-num"><?php echo $index + 1; ?></div>
                    <div class="pro-icon">
                        <span><i class="<?php echo htmlspecialchars($item['icon']); ?>"></i></span>
                    </div>
                    <h3 style="color:#E8C97A; margin-top:5px;margin-bottom:15px";>
                        <?php echo htmlspecialchars($item['title']); ?>
                    </h3>
                    <p style="color:rgba(254, 254, 254, 0.91);"><?php echo htmlspecialchars($item['subtitle']); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>