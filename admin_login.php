<?php

session_start();
require_once "config.php";

$error = "";

// ================= ADMIN LOGIN =================
if (isset($_POST['login'])) {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {

        $error = "Please enter username and password.";

    } else {

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
        $admin = mysqli_fetch_assoc($result);

        if ($admin && $password === $admin['password']) {

            session_regenerate_id(true);

            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['user_id'] = $admin['admin_id'];
            $_SESSION['name'] = $admin['username'];
            $_SESSION['role'] = 'admin';

            header("Location: admin/dashboard.php");
            exit;

        } else {

            $error = "Invalid admin username or password.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Login | SmartPass</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
    >

    <style>

        body {
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
            background: #f1f5f9;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            background: #fff;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }

        .icon {
            font-size: 50px;
            color: #2563eb;
            margin-bottom: 15px;
        }

        .form-control {
            padding: 13px;
            border-radius: 10px;
        }

        .btn-login {
            width: 100%;
            padding: 13px;
            border: none;
            border-radius: 10px;
            background: #2563eb;
            color: #fff;
            font-weight: 700;
        }

        .btn-login:hover {
            background: #1d4ed8;
        }

    </style>

</head>

<body>

<div class="login-card">

    <div class="text-center">

        <div class="icon">
            <i class="fa-solid fa-user-shield"></i>
        </div>

        <h3 class="fw-bold">
            SmartPass Admin
        </h3>

        <p class="text-muted">
            Login to manage bus pass applications
        </p>

    </div>

    <?php if ($error): ?>

        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php endif; ?>

    <form method="POST">

        <div class="mb-3">

            <label class="form-label fw-bold">
                Username
            </label>

            <input
                type="text"
                name="username"
                class="form-control"
                required
            >

        </div>

        <div class="mb-4">

            <label class="form-label fw-bold">
                Password
            </label>

            <input
                type="password"
                name="password"
                class="form-control"
                required
            >

        </div>

        <button
            type="submit"
            name="login"
            class="btn-login"
        >
            Login
        </button>

    </form>

</div>

</body>

</html>
