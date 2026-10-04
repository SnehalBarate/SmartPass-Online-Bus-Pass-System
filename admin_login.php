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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - SmartPass</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f4f7fb;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-container {
            width: 400px;
            background: white;
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }

        .login-container h2 {
            text-align: center;
            margin-bottom: 8px;
            color: #222;
        }

        .login-container p {
            text-align: center;
            color: #777;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 7px;
            font-weight: 600;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 7px;
            font-size: 15px;
            outline: none;
        }

        .form-group input:focus {
            border-color: #007bff;
        }

        .login-btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 7px;
            background: #007bff;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        .login-btn:hover {
            background: #0056b3;
        }

        .error {
            background: #ffe5e5;
            color: #d00000;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 18px;
            text-align: center;
        }
    </style>
</head>

<body>

<div class="login-container">

    <h2>Admin Login</h2>
    <p>SmartPass Online Bus Pass System</p>

    <?php if ($error !== ""): ?>
        <div class="error">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <div class="form-group">
            <label>Username</label>
            <input
                type="text"
                name="email"
                placeholder="Enter admin username"
                required
            >
        </div>

        <div class="form-group">
            <label>Password</label>
            <input
                type="password"
                name="password"
                placeholder="Enter password"
                required
            >
        </div>

        <button type="submit" name="admin_login" class="login-btn">
            Login
        </button>

    </form>

</div>

</body>
</html>
