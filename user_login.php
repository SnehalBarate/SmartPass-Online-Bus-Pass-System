<?php
session_start();
require_once "config.php";

$error = "";

if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'student') {
    header("Location: user/dashboard.php");
    exit;
}

if (isset($_POST['login'])) {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = "Please enter email and password.";
    } else {

        $stmt = mysqli_prepare(
            $conn,
            "SELECT user_id, name, email, password, role
             FROM users
             WHERE email = ? AND role = 'student'
             LIMIT 1"
        );

        if (!$stmt) {
            $error = "Database error: " . mysqli_error($conn);
        } else {

            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);

            if ($result && mysqli_num_rows($result) === 1) {

                $row = mysqli_fetch_assoc($result);

                if (password_verify($password, $row['password'])) {

                    session_regenerate_id(true);

                    $_SESSION['user_id'] = (int)$row['user_id'];
                    $_SESSION['name'] = $row['name'];
                    $_SESSION['email'] = $row['email'];
                    $_SESSION['role'] = 'student';

                    header("Location: user/dashboard.php");
                    exit;

                } else {
                    $error = "Invalid email or password.";
                }

            } else {
                $error = "Invalid email or password.";
            }

            mysqli_stmt_close($stmt);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Student Login | SmartPass</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
        rel="stylesheet"
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap"
        rel="stylesheet"
    >

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #eef2ff, #f8fafc);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            width: 100%;
            max-width: 430px;
            background: #fff;
            padding: 40px;
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.10);
        }

        .logo {
            width: 65px;
            height: 65px;
            margin: 0 auto 20px;
            border-radius: 18px;
            background: #4f46e5;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        }

        h2 {
            font-weight: 800;
            color: #1e293b;
            text-align: center;
            margin-bottom: 8px;
        }

        .subtitle {
            text-align: center;
            color: #64748b;
            margin-bottom: 30px;
        }

        .form-label {
            font-weight: 600;
            color: #334155;
        }

        .form-control {
            padding: 13px 15px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
        }

        .form-control:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.10);
        }

        .btn-login {
            width: 100%;
            border: none;
            padding: 14px;
            border-radius: 12px;
            background: #4f46e5;
            color: #fff;
            font-weight: 700;
            margin-top: 10px;
        }

        .btn-login:hover {
            background: #4338ca;
        }

        .links {
            text-align: center;
            margin-top: 22px;
        }

        .links a {
            color: #4f46e5;
            text-decoration: none;
            font-weight: 600;
        }

        .alert {
            border-radius: 12px;
            font-size: 14px;
        }
    </style>
</head>

<body>

<div class="login-card">

    <div class="logo">
        <i class="fa-solid fa-bus"></i>
    </div>

    <h2>Student Login</h2>

    <p class="subtitle">
        Login to your SmartPass account
    </p>

    <?php if ($error !== ''): ?>
        <div class="alert alert-danger">
            <i class="fa-solid fa-circle-exclamation me-2"></i>
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="user_login.php">

        <div class="mb-3">
            <label class="form-label">Email Address</label>

            <input
                type="email"
                name="email"
                class="form-control"
                placeholder="Enter your email"
                required
            >
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>

            <input
                type="password"
                name="password"
                class="form-control"
                placeholder="Enter your password"
                required
            >
        </div>

        <div class="text-end mb-3">
            <a
                href="forgot_password.php"
                style="color:#4f46e5;text-decoration:none;font-size:14px;font-weight:600;"
            >
                Forgot Password?
            </a>
        </div>

        <button
            type="submit"
            name="login"
            class="btn-login"
        >
            Login
            <i class="fa-solid fa-arrow-right ms-2"></i>
        </button>

    </form>

    <div class="links">
        Don't have an account?
        <a href="user/register.php">Register</a>
    </div>

</div>

</body>
</html>