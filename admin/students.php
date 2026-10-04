<?php
session_start();
require_once "../config.php";

// Admin authentication
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../admin_login.php");
    exit();
}

// Fetch all students
$query = "SELECT user_id, name, email, phone FROM users WHERE role = 'student' ORDER BY user_id DESC";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Database Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students - SmartPass</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f4f6f9;
        }

        .navbar {
            background: #0d6efd;
        }

        .navbar-brand,
        .nav-link {
            color: white !important;
        }

        .container {
            margin-top: 40px;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }

        .table th {
            background: #0d6efd;
            color: white;
        }

        .table {
            vertical-align: middle;
        }
    </style>
</head>

<body>

<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="dashboard.php">
            SmartPass Admin
        </a>

        <div>
            <a class="nav-link d-inline-block" href="dashboard.php">Dashboard</a>
            <a class="nav-link d-inline-block" href="manage_passes.php">Manage Passes</a>
            <a class="nav-link d-inline-block" href="students.php">Students</a>
            <a class="nav-link d-inline-block" href="../logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="container">

    <div class="card p-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">Students</h2>
                <p class="text-muted mb-0">Registered student accounts</p>
            </div>

            <span class="badge bg-primary fs-6">
                <?php echo mysqli_num_rows($result); ?> Students
            </span>
        </div>

        <div class="table-responsive">

            <table class="table table-bordered table-hover text-center">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                    </tr>
                </thead>

                <tbody>

                <?php if (mysqli_num_rows($result) > 0): ?>

                    <?php while ($row = mysqli_fetch_assoc($result)): ?>

                        <tr>
                            <td>
                                <?php echo htmlspecialchars($row['user_id']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row['name']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($row['email']); ?>
                            </td>

                            <td>
                                <?php
                                echo !empty($row['phone'])
                                    ? htmlspecialchars($row['phone'])
                                    : 'Not Provided';
                                ?>
                            </td>
                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="4" class="text-muted py-4">
                            No students found.
                        </td>
                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
