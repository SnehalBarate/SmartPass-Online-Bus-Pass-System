<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../user_login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Application Not Allowed</title>

    <style>
        body{
            margin:0;
            font-family:Segoe UI, Arial, sans-serif;
            background:#f1f5f9;
            display:flex;
            justify-content:center;
            align-items:center;
            height:100vh;
        }
        .card{
            background:#fff;
            width:520px;
            padding:35px 30px;
            border-radius:14px;
            text-align:center;
            box-shadow:0 8px 25px rgba(0,0,0,0.08);
        }
        h2{
            color:#dc2626;
            margin-bottom:15px;
            font-size:26px;
        }
        p{
            color:#334155;
            font-size:15.5px;
            line-height:1.6;
            margin:10px 0;
        }
        p b{
            color:#000;
        }
        .btn{
            margin-top:20px;
            display:inline-block;
            padding:10px 26px;
            background:#111827;
            color:#fff;
            border-radius:6px;
            text-decoration:none;
            font-size:15px;
            transition:0.2s;
        }
        .btn:hover{
            background:#000;
        }
    </style>
</head>
<body>

<div class="card">
    <h2>Application Not Allowed</h2>

    <p>
        You already have a <b>Pending</b> bus pass application.
    </p>

    <p>
        You can apply again only after your current pass is
        <b>Rejected</b> or <b>Expired</b>.
    </p>

    <a href="dashboard.php" class="btn">Go Back to Dashboard</a>
</div>

</body>
</html>
