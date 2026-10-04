<?php
session_start();
require_once("../config.php");

/* Security Check */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../admin_login.php");
    exit;
}

/* Fetch Students */
$result = mysqli_query(
    $conn,
    "SELECT id, name, email 
     FROM users 
     WHERE role='student' 
     ORDER BY id ASC"
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Students | Admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

<style>
body{
    background:#eef1f5;
    font-family:"Segoe UI", system-ui, sans-serif;
    color:#374151;
}

/* Center Wrapper */
.page-wrapper{
    min-height:100vh;
    display:flex;
    align-items:flex-start;
    justify-content:center;
    padding-top:40px;
}

/* Card */
.students-card{
    width:100%;
    max-width:900px;
    background:#ffffff;
    border:1px solid #d1d5db;
    border-radius:14px;
    box-shadow:0 14px 32px rgba(0,0,0,0.08);
    overflow:hidden;
}

/* Header */
.card-header{
    padding:22px 26px;
    border-bottom:1px solid #e5e7eb;
    background:#f9fafb;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.card-header h4{
    margin:0;
    font-weight:700;
    color:#111827;
}

.card-header small{
    color:#6b7280;
}

/* Avatar */
.avatar{
    width:36px;
    height:36px;
    background:#e5e7eb;
    color:#1f2937;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:700;
}

/* Table */
.table thead th{
    background:#e5e7eb;
    font-size:13px;
    font-weight:700;
    color:#111827;
    border-bottom:2px solid #cbd5e1;
}

.table tbody td{
    font-size:14px;
    padding:14px;
    border-bottom:1px solid #e5e7eb;
}

.table tbody tr:hover{
    background:#f1f5f9;
}

/* Back Button */
.btn-back{
    font-size:13px;
    font-weight:600;
}
</style>
</head>

<body>

<div class="page-wrapper">

    <div class="students-card">

        <!-- Header -->
        <div class="card-header">
            <div>
                <h4><i class="fa fa-users me-2"></i>Student Directory</h4>
                <small>Registered students list</small>
            </div>
            <a href="dashboard.php" class="btn btn-sm btn-outline-secondary btn-back">
                <i class="fa fa-arrow-left me-1"></i> Dashboard
            </a>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th width="70">#</th>
                        <th>Student Name</th>
                        <th>Email Address</th>
                    </tr>
                </thead>
                <tbody>
                <?php $i=1; while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td class="text-muted"><?= $i++ ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar">
                                    <?= strtoupper(substr($row['name'],0,1)) ?>
                                </div>
                                <span class="fw-semibold">
                                    <?= htmlspecialchars($row['name']) ?>
                                </span>
                            </div>
                        </td>
                        <td class="text-muted">
                            <?= htmlspecialchars($row['email']) ?>
                        </td>
                    </tr>
                <?php endwhile; ?>

                <?php if(mysqli_num_rows($result)==0): ?>
                    <tr>
                        <td colspan="3" class="text-center text-muted py-4">
                            No students found
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

</div>

</body>
</html>
