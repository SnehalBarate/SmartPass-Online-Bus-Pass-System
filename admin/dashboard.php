<?php
session_start();
require_once("../config.php");

/* ===== Security Check ===== */
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../admin_login.php");
    exit;
}

/* ===== Stats ===== */
$total_students = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT COUNT(*) AS c FROM users WHERE role='student'"
    )
)['c'];

$total_apps = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT COUNT(*) AS c FROM applications"
    )
)['c'];

$pending_apps = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT COUNT(*) AS c FROM applications WHERE status='Pending'"
    )
)['c'];

$approved_apps = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT COUNT(*) AS c FROM applications WHERE status='Approved'"
    )
)['c'];
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<title>Admin Dashboard | SmartPass</title>

<meta name="viewport" content="width=device-width, initial-scale=1">

<link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
    rel="stylesheet"
>

<link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
>

<style>

@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

body {
    font-family: 'Inter', sans-serif;
    background: linear-gradient(120deg,#f8fafc,#eef2ff);
    margin: 0;
}

/* Sidebar */

.sidebar {
    width: 270px;
    height: 100vh;
    position: fixed;
    background: linear-gradient(180deg,#0f172a,#1e293b);
    color: white;
    padding: 30px 20px;
}

.sidebar h3 {
    font-weight: 800;
    margin-bottom: 40px;
    color: #38bdf8;
}

.sidebar a {
    display: block;
    padding: 14px 18px;
    color: #cbd5f5;
    border-radius: 14px;
    text-decoration: none;
    margin-bottom: 10px;
    font-weight: 600;
    transition: 0.3s;
}

.sidebar a i {
    width: 22px;
}

.sidebar a:hover,
.sidebar a.active {
    background: rgba(255,255,255,0.1);
    color: #fff;
}

/* Main */

.main {
    margin-left: 270px;
    padding: 45px;
}

/* Header */

.header {
    background: linear-gradient(135deg,#6366f1,#8b5cf6);
    border-radius: 28px;
    padding: 35px;
    color: white;
    box-shadow: 0 25px 50px rgba(99,102,241,.35);
    margin-bottom: 40px;
}

/* Cards */

.stat-card {
    background: rgba(255,255,255,0.9);
    border-radius: 24px;
    padding: 28px;
    box-shadow: 0 20px 40px rgba(0,0,0,.06);
    transition: 0.3s;
}

.stat-card:hover {
    transform: translateY(-8px);
}

.stat-icon {
    width: 55px;
    height: 55px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    margin-bottom: 15px;
}

.icon-blue {
    background:#e0e7ff;
    color:#4338ca;
}

.icon-green {
    background:#dcfce7;
    color:#166534;
}

.icon-yellow {
    background:#fef9c3;
    color:#854d0e;
}

.icon-purple {
    background:#ede9fe;
    color:#6d28d9;
}

.stat-value {
    font-size: 32px;
    font-weight: 800;
    color: #0f172a;
}

.stat-label {
    font-size: 14px;
    font-weight: 600;
    color: #64748b;
}

/* Action Card */

.action-box {
    margin-top: 45px;
    background: linear-gradient(135deg,#ffffff,#f1f5f9);
    border-radius: 28px;
    padding: 35px;
    box-shadow: 0 25px 40px rgba(0,0,0,.05);
}

.btn-action {
    background: linear-gradient(135deg,#4f46e5,#7c3aed);
    color: white;
    border-radius: 999px;
    padding: 14px 34px;
    font-weight: 700;
    text-decoration: none;
    transition: 0.3s;
}

.btn-action:hover {
    box-shadow: 0 15px 30px rgba(79,70,229,.4);
    color: white;
}

</style>

</head>

<body>

<!-- Sidebar -->

<div class="sidebar">

    <h3>
        <i class="fa fa-bus me-2"></i>SmartPass
    </h3>

    <a href="dashboard.php" class="active">
        <i class="fa fa-chart-pie"></i>
        Dashboard
    </a>

    <a href="manage_passes.php">
        <i class="fa fa-id-card"></i>
        Applications
    </a>

    <a href="students.php">
        <i class="fa fa-users"></i>
        Students
    </a>

    <a href="settings.php">
        <i class="fa fa-cog"></i>
        Settings
    </a>

    <a href="../logout.php" class="text-danger mt-5">
        <i class="fa fa-power-off"></i>
        Logout
    </a>

</div>


<!-- Main -->

<div class="main">

    <!-- Header -->

    <div class="header d-flex justify-content-between align-items-center">

        <div>

            <h2 class="fw-bold mb-1">
                Admin Dashboard
            </h2>

            <p class="opacity-75 mb-0">
                Monitor and manage student bus pass requests
            </p>

        </div>

        <span class="badge bg-white text-dark rounded-pill px-4 py-2 shadow">

            <i class="fa fa-calendar me-2 text-primary"></i>

            <?= date('d M Y') ?>

        </span>

    </div>


    <!-- Stats -->

    <div class="row g-4">

        <!-- Total Students -->

        <div class="col-md-3">

            <div class="stat-card">

                <div class="stat-icon icon-blue">
                    <i class="fa fa-user-graduate"></i>
                </div>

                <div class="stat-value">
                    <?= $total_students ?>
                </div>

                <div class="stat-label">
                    Total Students
                </div>

            </div>

        </div>


        <!-- Applications -->

        <div class="col-md-3">

            <div class="stat-card">

                <div class="stat-icon icon-purple">
                    <i class="fa fa-file-lines"></i>
                </div>

                <div class="stat-value">
                    <?= $total_apps ?>
                </div>

                <div class="stat-label">
                    Applications
                </div>

            </div>

        </div>


        <!-- Pending -->

        <div class="col-md-3">

            <div class="stat-card">

                <div class="stat-icon icon-yellow">
                    <i class="fa fa-clock"></i>
                </div>

                <div class="stat-value">
                    <?= $pending_apps ?>
                </div>

                <div class="stat-label">
                    Pending
                </div>

            </div>

        </div>


        <!-- Approved -->

        <div class="col-md-3">

            <div class="stat-card">

                <div class="stat-icon icon-green">
                    <i class="fa fa-check-double"></i>
                </div>

                <div class="stat-value">
                    <?= $approved_apps ?>
                </div>

                <div class="stat-label">
                    Approved
                </div>

            </div>

        </div>

    </div>


    <!-- Action -->

    <div class="action-box d-flex justify-content-between align-items-center">

        <div>

            <h4 class="fw-bold mb-2">
                Process New Applications
            </h4>

            <p class="text-muted mb-0">
                Review, approve or reject student bus pass requests.
            </p>

        </div>

        <a href="manage_passes.php" class="btn-action">

            Manage Passes

            <i class="fa fa-arrow-right ms-2"></i>

        </a>

    </div>

</div>

</body>

</html>