<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once "config.php";

$error = "";

if (isset($_POST['admin_login'])) {

    $username = trim($_POST['email']);
    $password = $_POST['password'];

    // ================= ADMIN TABLE LOGIN =================
    $stmt = mysqli_prepare(
        $conn,
        "SELECT admin_id, username, password
         FROM admin
         WHERE username = ?
         LIMIT 1"
    );

    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($admin = mysqli_fetch_assoc($result)) {

        // Current admin table stores password as plain text
        if ($password === $admin['password']) {

            session_unset();
            session_regenerate_id(true);

            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['user_id'] = $admin['admin_id'];
            $_SESSION['name'] = $admin['username'];
            $_SESSION['role'] = 'admin';

            header("Location: admin/dashboard.php");
            exit;

        } else {
            $error = "Invalid password";
        }

    } else {
        $error = "Admin not found";
    }
}
?>
