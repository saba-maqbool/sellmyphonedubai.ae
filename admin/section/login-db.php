<?php
session_start();
include("include/db-connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = mysqli_prepare($conn, "SELECT * FROM admin_user Where username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    $login_ok = false;

    if ($user) {
        if (password_verify($password, $user['password'])) {
            $login_ok = true;
        }
        else if ($password === $user['password']) {
            $login_ok = true;
            $new_hash = password_hash($password, PASSWORD_DEFAULT);
            $upd = mysqli_prepare($conn, "UPDATE admin_user SET password = ? WHERE id = ?");
            mysqli_stmt_bind_param($upd, "si" , $new_hash, $user['id']);
            mysqli_stmt_execute($upd);

        }
    }
    if ($login_ok) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $user['username'];
        header("Location: dashboard.php");
        exit;
    } else{
        $error = "Invalid Username/Password";
    }

}
?>
           