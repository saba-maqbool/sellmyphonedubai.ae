<?php
require_once("admin/include/db-connect.php");
header('Content-Type: application/json');

$response = ["success" => false, "message" => "Something went wrong."];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '' || $email === '' || $message === '') {
        $response["message"] = "Please fill in your name, email and message.";
        echo json_encode($response);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response["message"] = "Please enter a valid email address.";
        echo json_encode($response);
        exit;
    }

    $stmt = mysqli_prepare($conn, "INSERT INTO contact_messages (name, email, phone, subject, message, status, created_at) VALUES (?, ?, ?, ?, ?, 'unread', NOW())");
    if ($stmt === false) {
        $response["message"] = "DB error preparing insert: " . mysqli_error($conn);
        echo json_encode($response);
        exit;
    }

    mysqli_stmt_bind_param($stmt, "sssss", $name, $email, $phone, $subject, $message);

    if (mysqli_stmt_execute($stmt)) {
        $response["success"] = true;
        $response["message"] = "Thanks for reaching out! We'll get back to you within 24 hours.";
    } else {
        $response["message"] = "Could not send your message: " . mysqli_stmt_error($stmt);
    }
}

echo json_encode($response);