<?php
require_once("admin/include/db-connect.php");
header('Content-Type: application/json');

$model_id = (int) ($_GET['model_id'] ?? 0);

if (!$model_id) {
    http_response_code(400);
    echo json_encode(["error" => "Missing model_id."]);
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT * FROM model_pricing WHERE model_id = ?");
mysqli_stmt_bind_param($stmt, "i", $model_id);
mysqli_stmt_execute($stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

$result = [
    "base" => 0,
    "condition_flawless" => 0, "condition_good" => 0, "condition_fair" => 0,
    "acc_charger" => 0, "acc_box" => 0, "acc_earbuds" => 0, "acc_warranty" => 0,
];
if ($row) {
    foreach ($result as $key => $default) {
        $result[$key] = (float) ($row[$key] ?? 0);
    }
}

$storage_stmt = mysqli_prepare($conn, "SELECT id, label, price_delta FROM model_storage_options WHERE model_id = ? ORDER BY sort_order, id");
mysqli_stmt_bind_param($storage_stmt, "i", $model_id);
mysqli_stmt_execute($storage_stmt);
$storage_result = mysqli_stmt_get_result($storage_stmt);
$storage_options = [];
while ($srow = mysqli_fetch_assoc($storage_result)) {
    $storage_options[] = [
        "id" => (int) $srow['id'],
        "label" => $srow['label'],
        "price_delta" => (float) $srow['price_delta'],
    ];
}
$result["storage_options"] = $storage_options;

echo json_encode($result);