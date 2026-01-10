<?php
session_start();
require_once("../config.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../admin_login.php");
    exit;
}

$msg = "";
if (isset($_POST['update_pass'])) {
    $new_pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $admin_id = $_SESSION['admin_id'] ?? $_SESSION['user_id'];
    
    if($admin_id) {
        $update = "UPDATE users SET password = '$new_pass' WHERE id = '$admin_id'";
        if (mysqli_query($conn, $update)) {
            $msg = "<div class='alert alert-success border-0 small shadow-sm'>Password updated successfully!</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Settings | SmartPass</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body { background: #f0f2f5; font-family: 'Inter', sans-serif; }
        .settings-card { max-width: 450px; margin: 80px auto; background: #fff; border: 1px solid #e0e6ed; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); padding: 30px; }
        .form-control { border-radius: 8px; padding: 12px; border: 1px solid #d1d5db; font-size: 14px; }
        .form-control:focus { box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); border-color: #2563eb; }
        .btn-update { background: #1e293b; color: #fff; border: none; padding: 12px; border-radius: 8px; font-weight: 600; width: 100%; transition: 0.2s; }
        .btn-update:hover { background: #0f172a; }
    </style>
</head>
<body>
<div class="container">
    <div class="settings-card">
        <div class="text-center mb-4">
            <div class="bg-light d-inline-block p-3 rounded-circle mb-3">
                <i class="fa fa-shield-halved fa-2x text-primary"></i>
            </div>
            <h5 class="fw-bold m-0">Account Security</h5>
            <p class="text-muted small">Update your administrative password</p>
        </div>

        <?= $msg ?>

        <form method="POST">
            <div class="mb-4">
                <label class="form-label small fw-bold text-secondary">New Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fa fa-lock text-muted"></i></span>
                    <input type="password" name="password" class="form-control border-start-0 shadow-none" placeholder="Enter at least 6 characters" required>
                </div>
            </div>
            <button type="submit" name="update_pass" class="btn-update">Save Changes</button>
            <div class="text-center mt-3">
                <a href="dashboard.php" class="text-decoration-none small text-muted"><i class="fa fa-arrow-left me-1"></i> Back to Dashboard</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>