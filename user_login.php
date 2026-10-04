<?php
// ================= ERROR DISPLAY =================
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ================= SESSION START =================
session_start();

// ================= DB CONNECTION =================
require_once "config.php";

$error = "";

// ================= FORM SUBMIT =================
if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = mysqli_prepare(
        $conn,
        "SELECT user_id, name, password FROM users
         WHERE email = ? AND role = 'student' LIMIT 1"
    );

    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $row['password'])) {
            session_unset();
            session_regenerate_id(true);

            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['name'] = $row['name'];
            $_SESSION['role'] = 'student';

            header("Location: user/dashboard.php");
            exit;
        } else {
            $error = "Invalid password";
        }
    } else {
        $error = "Student account not found";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login | SmartPass</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(rgba(15, 23, 42, 0.75), rgba(15, 23, 42, 0.75)),
                        url('https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            background: rgba(255, 255, 255, 0.98);
            padding: 40px;
            border-radius: 24px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.4);
        }

        .brand-icon {
            font-size: 45px;
            color: #2563eb;
            text-align: center;
            margin-bottom: 10px;
        }

        .header-text {
            text-align: center;
            margin-bottom: 30px;
        }

        .header-text h3 {
            font-weight: 800;
            color: #1e293b;
            margin: 0;
        }

        .form-label {
            font-weight: 700;
            color: #475569;
            font-size: 13px;
            text-transform: uppercase;
        }

        .input-group-text {
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-right: none;
            color: #64748b;
            border-radius: 12px 0 0 12px;
        }

        .form-control {
            border: 2px solid #e2e8f0;
            border-left: none;
            border-radius: 0 12px 12px 0;
            padding: 12px;
            background: #f8fafc;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #e2e8f0;
            background: #fff;
        }

        .btn-login {
            background: #2563eb;
            color: white;
            font-weight: 700;
            padding: 14px;
            border-radius: 12px;
            border: none;
            width: 100%;
            margin-top: 10px;
            transition: 0.3s;
        }

        .btn-login:hover {
            background: #1e40af;
            transform: translateY(-2px);
        }

        .register-link {
            text-align: center;
            margin-top: 25px;
            font-size: 14px;
            color: #64748b;
        }

        .register-link a {
            color: #2563eb;
            text-decoration: none;
            font-weight: 700;
        }
    </style>
</head>

<body>

<div class="login-card">

    <div class="brand-icon">
        <i class="fa-solid fa-bus-simple"></i>
    </div>

    <div class="header-text">
        <h3>Student Login</h3>
        <p class="text-muted small fw-bold">Welcome back to SmartPass</p>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger py-2 small text-center fw-bold">
            <i class="fa-solid fa-circle-xmark me-2"></i>
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" autocomplete="off">

        <div class="mb-3">
            <label class="form-label">Email Address</label>

            <div class="input-group">
                <span class="input-group-text">
                    <i class="fa-solid fa-envelope"></i>
                </span>

                <input
                    type="email"
                    name="email"
                    class="form-control"
                    value=""
                    autocomplete="off"
                    required
                >
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label">Password</label>

            <div class="input-group">
                <span class="input-group-text">
                    <i class="fa-solid fa-lock"></i>
                </span>

                <input
                    type="password"
                    name="password"
                    class="form-control"
                    value=""
                    autocomplete="new-password"
                    required
                >
            </div>
        </div>

        <button type="submit" name="login" class="btn btn-login">
            Sign In
            <i class="fa-solid fa-arrow-right-to-bracket ms-2"></i>
        </button>

    </form>

    <div class="register-link">
        New student?
        <a href="user/register.php">Create Account</a>
    </div>

</div>

</body>
</html>
