<?php
include("include/db-connect.php");
include("include/auth-check.php");
header('Content-Type: application/json');

$page_id = (int) ($_POST['page_id'] ?? 0);
$order = json_decode($_POST['order'] ?? '[]', true);

if (!$page_id || !is_array($order)) {
    echo json_encode(['success' => false]);
    exit;
}

foreach ($order as $index => $section_id) {
    $section_id = (int) $section_id;
    $stmt = mysqli_prepare($conn, "UPDATE page_sections SET sort_order = ? WHERE id = ? AND page_id = ?");
    mysqli_stmt_bind_param($stmt, "iii", $index, $section_id, $page_id);
    mysqli_stmt_execute($stmt);
}

echo json_encode(['success' => true]);