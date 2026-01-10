<?php
// ================== SESSION START ==================
session_start();

// ================== AUTH CHECK ==================
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../user_login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Application Submitted | SmartPass</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        body{
            min-height:100vh;
            display:flex;
            align-items:center;
            justify-content:center;
            background:#f1f5f9;
            font-family:Segoe UI, sans-serif;
        }
        .success-card{
            background:#fff;
            padding:45px;
            border-radius:20px;
            text-align:center;
            max-width:500px;
            width:100%;
            box-shadow:0 15px 30px rgba(0,0,0,0.1);
        }
        .success-icon{
            font-size:70px;
            color:#22c55e;
            margin-bottom:20px;
        }
        .btn-dashboard{
            background:#2563eb;
            color:#fff;
            padding:12px 30px;
            border-radius:30px;
            font-weight:700;
            text-decoration:none;
        }
        .btn-dashboard:hover{
            background:#1e40af;
            color:#fff;
        }
    </style>
</head>

<body>

<div class="success-card">
    <div class="success-icon">
        <i class="fa-solid fa-circle-check"></i>
    </div>

    <h3 class="fw-bold mb-3">Application Submitted Successfully!</h3>

    <p class="text-muted">
        Your bus pass application has been submitted.<br>
        Please wait while the admin verifies your documents.
    </p>

    <div class="mt-4">
        <a href="dashboard.php" class="btn-dashboard">
            Go to Dashboard
        </a>
    </div>
</div>

</body>
</html>
