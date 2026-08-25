<?php
include("include/db-connect.php");
include("include/auth-check.php");

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$ids = $input['ids'] ?? [];
$status = trim($input['status'] ?? '');

$allowed_statuses = ['pending', 'contacted', 'completed', 'cancelled'];

if (empty($ids) || !is_array($ids) || !in_array($status, $allowed_statuses, true)) {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

$ids = array_values(array_filter(array_map('intval', $ids), fn($id) => $id > 0));

if (empty($ids)) {
    echo json_encode(['success' => false, 'message' => 'No valid lead IDs provided.']);
    exit;
}

$placeholders = implode(',', array_fill(0, count($ids), '?'));
$sql = "UPDATE leads SET status = ? WHERE id IN ($placeholders)";

$stmt = mysqli_prepare($conn, $sql);
$bind_types = 's' . str_repeat('i', count($ids));
mysqli_stmt_bind_param($stmt, $bind_types, $status, ...$ids);
$ok = mysqli_stmt_execute($stmt);

echo json_encode(['success' => (bool) $ok]);