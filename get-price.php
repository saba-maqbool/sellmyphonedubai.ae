<?php
require_once("admin/include/db-connect.php");
header('Content-Type: application/json');

$model_id = (int) ($_POST['model_id'] ?? 0);
$storage_option_id = (int) ($_POST['storage'] ?? 0);
$condition_key = $_POST['condition'] ?? '';
$accessories = $_POST['accessories'] ?? [];
$allowed_condition = ['condition_flawless', 'condition_good', 'condition_fair'];
$allowed_accessories = ['acc_charger', 'acc_box', 'acc_earbuds', 'acc_warranty'];

if (!$model_id || !$storage_option_id || !in_array($condition_key, $allowed_condition)) {
    http_response_code(400);
    echo json_encode(["error" => "Missing or invalid selection."]);
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT * FROM model_pricing WHERE model_id = ?");
if ($stmt === false) {
    http_response_code(500);
    echo json_encode(["error" => "DB error preparing pricing lookup: " . mysqli_error($conn)]);
    exit;
}
mysqli_stmt_bind_param($stmt, "i", $model_id);
mysqli_stmt_execute($stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$row) {
    echo json_encode(["price" => 0, "note" => "No pricing has been set for this model yet."]);
    exit;
}

$storage_stmt = mysqli_prepare($conn, "SELECT price_delta FROM model_storage_options WHERE id = ? AND model_id = ?");
mysqli_stmt_bind_param($storage_stmt, "ii", $storage_option_id, $model_id);
mysqli_stmt_execute($storage_stmt);
$storage_row = mysqli_fetch_assoc(mysqli_stmt_get_result($storage_stmt));

if (!$storage_row) {
    echo json_encode(["error" => "Invalid storage option for this model."]);
    exit;
}

$price = (float) ($row['base'] ?? 0) + (float) $storage_row['price_delta'] + (float) ($row[$condition_key] ?? 0);

foreach ($accessories as $acc) {
    if (in_array($acc, $allowed_accessories)) {
        $price += (float) ($row[$acc] ?? 0);
    }
}

echo json_encode(["price" => max(0, $price)]);