<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../user_login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Error | SmartPass</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .error-card {
            background: white;
            width: 450px;
            padding: 40px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }

        .error-icon {
            font-size: 55px;
            color: #dc3545;
            margin-bottom: 20px;
        }

        .btn-dashboard {
            background: #0d6efd;
            color: white;
            padding: 10px 25px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-dashboard:hover {
            background: #0b5ed7;
            color: white;
        }
    </style>
</head>

<body>

<div class="error-card">
    <div class="error-icon">✖</div>

    <h3 class="fw-bold mb-3">Application Submission Failed</h3>

    <p class="text-muted">
        Some documents could not be uploaded.<br>
        Please check all files and try again.
    </p>

    <div class="mt-4">
        <a href="apply.php" class="btn-dashboard">
            Try Again
        </a>
    </div>
</div>

</body>
</html>
