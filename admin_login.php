<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once "config.php";

$error = "";

if (isset($_POST['admin_login'])) {

    $username = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = "Please enter username and password";
    } else {

        $stmt = mysqli_prepare(
            $conn,
            "SELECT admin_id, username, password
             FROM admin
             WHERE username = ?
             LIMIT 1"
        );

        if (!$stmt) {
            die("Database error: " . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);

        mysqli_stmt_bind_result(
            $stmt,
            $admin_id,
            $admin_username,
            $admin_password
        );

        if (mysqli_stmt_fetch($stmt)) {

            // Existing admin password is stored as plain text
            if ($password === $admin_password) {

                session_unset();
                session_regenerate_id(true);

                $_SESSION['admin_id'] = $admin_id;
                $_SESSION['user_id'] = $admin_id;
                $_SESSION['name'] = $admin_username;
                $_SESSION['role'] = 'admin';

                mysqli_stmt_close($stmt);

                header("Location: admin/dashboard.php");
                exit;

            } else {
                $error = "Invalid password";
            }

        } else {
            $error = "Admin not found";
        }

        mysqli_stmt_close($stmt);
    }
}
?>
