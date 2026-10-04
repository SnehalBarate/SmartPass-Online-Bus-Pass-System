<?php
session_start();
require_once "../config.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../admin_login.php");
    exit;
}

$query = "SELECT user_id, name, email
          FROM users
          WHERE role = 'student'
          ORDER BY user_id DESC";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Database error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Students | SmartPass</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

<style>
body {
    background: #f8fafc;
    font-family: 'Segoe UI', sans-serif;
}

.wrapper {
    max-width: 1000px;
    margin: 45px auto;
    padding: 0 20px;
}

.card-box {
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,.06);
    overflow: hidden;
    border: 1px solid #e2e8f0;
}

.header {
    padding: 30px;
}

.table thead th {
    background: #e2e8f0;
    color: #1e293b;
    padding: 15px;
}

.table tbody td {
    padding: 16px;
    vertical-align: middle;
}

.avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e2e8f0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin-right: 12px;
}
</style>
</head>

<body>

<div class="wrapper">

    <div class="card-box">

        <div class="header d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold mb-1">
                    <i class="fa fa-users me-2"></i>Student Directory
                </h2>
                <p class="text-muted mb-0">Registered students list</p>
            </div>

            <a href="dashboard.php" class="btn btn-outline-dark">
                <i class="fa fa-arrow-left me-2"></i>Dashboard
            </a>
        </div>

        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student Name</th>
                        <th>Email Address</th>
                    </tr>
                </thead>

                <tbody>

                <?php
                $count = 1;

                while ($row = mysqli_fetch_assoc($result)):
                    $name = $row['name'] ?? '';
                    $email = $row['email'] ?? '';
                    $initial = strtoupper(substr($name, 0, 1));
                ?>

                <tr>
                    <td><?= $count++ ?></td>

                    <td>
                        <span class="avatar">
                            <?= htmlspecialchars($initial) ?>
                        </span>

                        <strong>
                            <?= htmlspecialchars($name) ?>
                        </strong>
                    </td>

                    <td class="text-muted">
                        <?= htmlspecialchars($email) ?>
                    </td>
                </tr>

                <?php endwhile; ?>

                <?php if (mysqli_num_rows($result) === 0): ?>
                <tr>
                    <td colspan="3" class="text-center text-muted py-4">
                        No students registered.
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
