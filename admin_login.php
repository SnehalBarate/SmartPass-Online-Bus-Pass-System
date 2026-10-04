<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once "config.php";

$error = "";

if (isset($_POST['admin_login'])) {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // ==========================================
    // SECURE ADMIN LOGIN USING PREPARED STATEMENT
    // ==========================================

    $stmt = mysqli_prepare(
        $conn,
        "SELECT id, name, password
         FROM users
         WHERE email = ? AND role = 'admin'
         LIMIT 1"
    );

    if ($stmt) {

        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) === 1) {

            $admin = mysqli_fetch_assoc($result);

            // Verify hashed password
            if (password_verify($password, $admin['password'])) {

                // Prevent session fixation
                session_unset();
                session_regenerate_id(true);

                $_SESSION['user_id'] = $admin['id'];
                $_SESSION['name'] = $admin['name'];
                $_SESSION['role'] = 'admin';

                // Redirect to admin dashboard
                header("Location: admin/dashboard.php");
                exit;

            } else {

                $error = "Invalid password";
            }

        } else {

            $error = "Admin not found";
        }

        mysqli_stmt_close($stmt);

    } else {

        $error = "Something went wrong. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>Admin Login | SmartPass</title>

    <!-- Bootstrap 5 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Font Awesome -->
    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        rel="stylesheet"
    >

    <style>

        body {
            min-height: 100vh;
            background: linear-gradient(180deg, #e8f0fe, #ffffff);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            border-radius: 14px;
            border: 1px solid #d6d9e0;
            background: #fff;
        }

        .login-header {
            background: #f9fafc;
            padding: 22px;
            text-align: center;
            border-bottom: 1px solid #d6d9e0;
        }

        .login-header h3 {
            margin: 0;
            font-weight: 600;
            color: #333;
        }

        .login-body {
            padding: 28px;
        }

        .form-label {
            font-weight: 500;
            color: #333;
        }

        .form-control {
            border-radius: 8px;
            padding: 10px;
            border: 1px solid #cfd4dc;
        }

        .form-control:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.15rem rgba(78,115,223,.25);
        }

        .btn-login {
            border-radius: 8px;
            padding: 10px;
            font-weight: 500;
        }

        .back-home {
            display: block;
            text-align: center;
            margin-top: 18px;
            color: #6c757d;
            text-decoration: none;
            font-size: 14px;
        }

        .back-home:hover {
            color: #212529;
        }

    </style>

</head>

<body>

<div class="card login-card shadow-sm">

    <!-- Header -->
    <div class="login-header">

        <h3>
            <i class="fa-solid fa-user-shield me-2"></i>
            Admin Login
        </h3>

        <small class="text-muted">
            SmartPass Management System
        </small>

    </div>


    <!-- Login Body -->
    <div class="login-body">

        <?php if ($error != "") { ?>

            <div class="alert alert-danger text-center">
                <?php echo htmlspecialchars($error); ?>
            </div>

        <?php } ?>


        <!-- Login Form -->
        <form method="POST" autocomplete="off">

            <!-- Autofill blocker -->
            <input type="text" style="display:none">
            <input type="password" style="display:none">


            <!-- Email -->
            <div class="mb-3">

                <label class="form-label">
                    <i class="fa-solid fa-envelope me-1"></i>
                    Email
                </label>

                <input
                    type="email"
                    name="email"
                    class="form-control"
                    autocomplete="off"
                    readonly
                    onfocus="this.removeAttribute('readonly');"
                    required
                >

            </div>


            <!-- Password -->
            <div class="mb-3">

                <label class="form-label">
                    <i class="fa-solid fa-lock me-1"></i>
                    Password
                </label>

                <input
                    type="password"
                    name="password"
                    class="form-control"
                    autocomplete="new-password"
                    readonly
                    onfocus="this.removeAttribute('readonly');"
                    required
                >

            </div>


            <!-- Login Button -->
            <div class="d-grid mt-4">

                <button
                    type="submit"
                    name="admin_login"
                    class="btn btn-dark btn-login"
                >
                    <i class="fa-solid fa-right-to-bracket me-1"></i>
                    Login
                </button>

            </div>

        </form>


        <!-- Back to Home -->
        <a href="index.php" class="back-home">

            <i class="fa-solid fa-arrow-left me-1"></i>
            Back to Home

        </a>

    </div>

</div>

</body>

</html>