<?php
require_once("admin/include/db-connect.php");
header('Content-Type: application/json');

$response = ["success" => false, "message" => "Something went wrong."];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if ($name === '' || $phone === '') {
        $response["message"] = "Please enter your name and phone number.";
        echo json_encode($response);
        exit;
    }

    $stmt = mysqli_prepare($conn, "INSERT INTO pickup_requests (name, phone, status, created_at) VALUES (?, ?, 'pending', NOW())");
    if ($stmt === false) {
        $response["message"] = "DB error preparing insert: " . mysqli_error($conn);
        echo json_encode($response);
        exit;
    }

    mysqli_stmt_bind_param($stmt, "ss", $name, $phone);

    if (mysqli_stmt_execute($stmt)) {
        $response["success"] = true;
        $response["message"] = "Thanks! We'll call you shortly to arrange your free pickup.";
    } else {
        $response["message"] = "Could not save your request: " . mysqli_stmt_error($stmt);
    }
}

echo json_encode($response);