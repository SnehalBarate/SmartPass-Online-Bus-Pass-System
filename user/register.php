<?php
// ================= ERROR REPORTING =================
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ================= DB CONNECTION =================
require_once "../config.php";

$message = "";

// ================= FORM SUBMIT =================
if (isset($_POST['register'])) {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $role     = 'student';

    $check = mysqli_prepare($conn, "SELECT user_id FROM users WHERE email = ? LIMIT 1");
    mysqli_stmt_bind_param($check, "s", $email);
    mysqli_stmt_execute($check);
    mysqli_stmt_store_result($check);

    if (mysqli_stmt_num_rows($check) > 0) {
        $message = "Email is already registered!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)"
        );

        mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $hashed_password, $role);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: ../user_login.php?registration=success");
            exit;
        } else {
            $message = "Registration failed!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | SmartPass</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        body {
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(rgba(15, 23, 42, 0.8), rgba(15, 23, 42, 0.8)),
                        url('https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?q=80&w=2070&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
        }

        .register-card {
            width: 100%;
            max-width: 420px;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .header-section i {
            font-size: 50px;
            color: #2563eb;
            margin-bottom: 15px;
        }

        .header-section h3 {
            font-weight: 800;
            color: #1e293b;
            letter-spacing: -1px;
        }

        .input-group-custom {
            background: #f8fafc;
            border-radius: 12px;
            padding: 5px 15px;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            border: 2px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .input-group-custom:focus-within {
            border-color: #2563eb;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        .input-group-custom i {
            color: #64748b;
            margin-right: 12px;
            font-size: 18px;
        }

        .input-group-custom input {
            border: none;
            background: transparent;
            width: 100%;
            padding: 12px 0;
            outline: none;
            font-weight: 600;
            color: #1e293b;
        }

        .btn-register {
            background: #2563eb;
            color: white;
            border: none;
            padding: 16px;
            border-radius: 12px;
            font-weight: 700;
            width: 100%;
            margin-top: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: 0.3s ease;
        }

        .btn-register:hover {
            background: #1e40af;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.3);
        }

        .login-link a {
            color: #2563eb;
            font-weight: 700;
            text-decoration: none;
        }
    </style>
</head>

<body>

    <div class="register-card text-center">

        <div class="header-section mb-4">
            <i class="fa-solid fa-bus-simple"></i>
            <h3>Get Started</h3>
            <p class="text-muted small fw-bold">
                Register for your Digital Bus Pass
            </p>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-danger py-2 small fw-bold">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <form method="POST" autocomplete="off">

            <div class="input-group-custom">
                <i class="fa-solid fa-user-graduate"></i>
                <input
                    type="text"
                    name="name"
                    placeholder="Full Name"
                    value=""
                    autocomplete="off"
                    required
                >
            </div>

            <div class="input-group-custom">
                <i class="fa-solid fa-envelope"></i>
                <input
                    type="email"
                    name="email"
                    placeholder="Email Address"
                    value=""
                    autocomplete="off"
                    required
                >
            </div>

            <div class="input-group-custom">
                <i class="fa-solid fa-lock"></i>
                <input
                    type="password"
                    name="password"
                    placeholder="Create Password"
                    value=""
                    autocomplete="new-password"
                    required
                >
            </div>

            <button type="submit" name="register" class="btn-register">
                Register <i class="fa-solid fa-arrow-right ms-2"></i>
            </button>

        </form>

        <div class="mt-4 small text-muted">
            Already have an account?
            <a href="../user_login.php">Login here</a>
        </div>

    </div>

</body>
</html>
