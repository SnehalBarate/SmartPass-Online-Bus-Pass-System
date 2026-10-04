<?php
session_start();
require_once "../config.php";

// ================== ADMIN AUTH CHECK ==================
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../admin_login.php");
    exit;
}

// ================== UPDATE APPLICATION STATUS ==================
if (isset($_GET['action_id']) && isset($_GET['new_status'])) {

    $id = (int)$_GET['action_id'];
    $status = $_GET['new_status'];

    // Allow only valid statuses
    $allowed_status = ['Pending', 'Approved', 'Rejected'];

    if (!in_array($status, $allowed_status)) {
        die("Invalid application status.");
    }

    $stmt = mysqli_prepare(
        $conn,
        "UPDATE applications SET status = ? WHERE id = ?"
    );

    if (!$stmt) {
        die("Database prepare error: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "si", $status, $id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: manage_passes.php?success=1");
        exit;
    } else {
        die("Error updating application: " . mysqli_stmt_error($stmt));
    }
}

// ================== FETCH APPLICATIONS ==================
$query = "
    SELECT *
    FROM applications
    ORDER BY id DESC
";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Database error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Applications | SmartPass</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
        rel="stylesheet"
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap"
        rel="stylesheet"
    >

    <style>
        body {
            margin: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8fafc;
            color: #1e293b;
        }

        .sidebar {
            width: 260px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: #0f172a;
            padding: 30px 20px;
        }

        .sidebar h3 {
            color: white;
            font-weight: 800;
            margin-bottom: 40px;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #94a3b8;
            text-decoration: none;
            padding: 13px 16px;
            border-radius: 10px;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #4f46e5;
            color: white;
        }

        .main {
            margin-left: 260px;
            padding: 40px;
        }

        .page-header {
            margin-bottom: 30px;
        }

        .page-header h2 {
            font-weight: 800;
            margin-bottom: 5px;
        }

        .application-card {
            background: white;
            border-radius: 18px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.05);
        }

        .application-title {
            font-weight: 800;
            font-size: 20px;
        }

        .info-label {
            font-size: 12px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
        }

        .info-value {
            font-weight: 600;
            margin-bottom: 15px;
        }

        .status {
            display: inline-block;
            padding: 7px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }

        .status-pending {
            background: #fff7ed;
            color: #ea580c;
        }

        .status-approved {
            background: #ecfdf5;
            color: #059669;
        }

        .status-rejected {
            background: #fef2f2;
            color: #dc2626;
        }

        .document-link {
            text-decoration: none;
            font-weight: 600;
            color: #4f46e5;
            margin-right: 15px;
        }

        .document-link:hover {
            text-decoration: underline;
        }

        .btn-approve {
            background: #16a34a;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 9px 15px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
        }

        .btn-reject {
            background: #dc2626;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 9px 15px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
        }

        .btn-approve:hover,
        .btn-reject:hover {
            color: white;
            opacity: 0.9;
        }

        @media(max-width: 992px) {
            .sidebar {
                display: none;
            }

            .main {
                margin-left: 0;
                padding: 20px;
            }
        }
    </style>
</head>

<body>

<!-- ================== SIDEBAR ================== -->

<div class="sidebar">

    <h3>
        <i class="fa-solid fa-bus-simple me-2"></i>
        SmartPass
    </h3>

    <a href="dashboard.php">
        <i class="fa-solid fa-house"></i>
        Dashboard
    </a>

    <a href="manage_passes.php" class="active">
        <i class="fa-solid fa-file-circle-check"></i>
        Applications
    </a>

    <a href="students.php">
        <i class="fa-solid fa-users"></i>
        Students
    </a>

    <a href="../logout.php" style="margin-top:40px;color:#f87171;">
        <i class="fa-solid fa-power-off"></i>
        Logout
    </a>

</div>

<!-- ================== MAIN ================== -->

<div class="main">

    <div class="page-header">
        <h2>Manage Applications</h2>
        <p class="text-muted">
            Review and manage student bus pass applications.
        </p>
    </div>

    <?php if (isset($_GET['success'])): ?>

        <div class="alert alert-success rounded-3">
            Application status updated successfully.
        </div>

    <?php endif; ?>


    <?php if (mysqli_num_rows($result) === 0): ?>

        <div class="application-card text-center">
            <i class="fa-solid fa-folder-open fs-1 text-muted mb-3"></i>

            <h5>No applications found</h5>

            <p class="text-muted mb-0">
                There are currently no bus pass applications.
            </p>
        </div>

    <?php else: ?>


        <?php while ($row = mysqli_fetch_assoc($result)): ?>

            <div class="application-card">

                <div class="d-flex justify-content-between align-items-start mb-4">

                    <div>
                        <div class="application-title">
                            <?= htmlspecialchars($row['full_name']) ?>
                        </div>

                        <small class="text-muted">
                            Application ID: #<?= (int)$row['id'] ?>
                        </small>
                    </div>


                    <?php
                    $status_class = 'status-pending';

                    if ($row['status'] === 'Approved') {
                        $status_class = 'status-approved';
                    } elseif ($row['status'] === 'Rejected') {
                        $status_class = 'status-rejected';
                    }
                    ?>

                    <span class="status <?= $status_class ?>">
                        <?= htmlspecialchars($row['status']) ?>
                    </span>

                </div>


                <div class="row">

                    <div class="col-md-4">

                        <div class="info-label">
                            College
                        </div>

                        <div class="info-value">
                            <?= htmlspecialchars($row['college']) ?>
                        </div>

                    </div>


                    <div class="col-md-4">

                        <div class="info-label">
                            Route
                        </div>

                        <div class="info-value">
                            <?= htmlspecialchars($row['route_from']) ?>
                            →
                            <?= htmlspecialchars($row['route_to']) ?>
                        </div>

                    </div>


                    <div class="col-md-4">

                        <div class="info-label">
                            Applied On
                        </div>

                        <div class="info-value">
                            <?= htmlspecialchars($row['created_at']) ?>
                        </div>

                    </div>

                </div>


                <hr>


                <!-- ================== DOCUMENTS ================== -->

                <div class="mb-4">

                    <div class="info-label mb-2">
                        Documents
                    </div>

                    <?php if (!empty($row['photo'])): ?>
                        <a
                            href="../uploads/<?= htmlspecialchars($row['photo']) ?>"
                            target="_blank"
                            class="document-link"
                        >
                            <i class="fa-solid fa-image"></i>
                            Photo
                        </a>
                    <?php endif; ?>


                    <?php if (!empty($row['bonafide_doc'])): ?>
                        <a
                            href="../uploads/<?= htmlspecialchars($row['bonafide_doc']) ?>"
                            target="_blank"
                            class="document-link"
                        >
                            <i class="fa-solid fa-file"></i>
                            Bonafide
                        </a>
                    <?php endif; ?>


                    <?php if (!empty($row['college_form'])): ?>
                        <a
                            href="../uploads/<?= htmlspecialchars($row['college_form']) ?>"
                            target="_blank"
                            class="document-link"
                        >
                            <i class="fa-solid fa-file"></i>
                            College Form
                        </a>
                    <?php endif; ?>


                    <?php if (!empty($row['id_card_doc'])): ?>
                        <a
                            href="../uploads/<?= htmlspecialchars($row['id_card_doc']) ?>"
                            target="_blank"
                            class="document-link"
                        >
                            <i class="fa-solid fa-id-card"></i>
                            ID Card
                        </a>
                    <?php endif; ?>


                    <?php if (!empty($row['aadhar_doc'])): ?>
                        <a
                            href="../uploads/<?= htmlspecialchars($row['aadhar_doc']) ?>"
                            target="_blank"
                            class="document-link"
                        >
                            <i class="fa-solid fa-address-card"></i>
                            Aadhar
                        </a>
                    <?php endif; ?>

                </div>


                <!-- ================== ACTIONS ================== -->

                <?php if ($row['status'] === 'Pending'): ?>

                    <div class="d-flex gap-2">

                        <a
                            href="manage_passes.php?action_id=<?= (int)$row['id'] ?>&new_status=Approved"
                            class="btn-approve"
                            onclick="return confirm('Approve this application?');"
                        >
                            <i class="fa-solid fa-check me-1"></i>
                            Approve
                        </a>


                        <a
                            href="manage_passes.php?action_id=<?= (int)$row['id'] ?>&new_status=Rejected"
                            class="btn-reject"
                            onclick="return confirm('Reject this application?');"
                        >
                            <i class="fa-solid fa-xmark me-1"></i>
                            Reject
                        </a>

                    </div>

                <?php endif; ?>

            </div>

        <?php endwhile; ?>

    <?php endif; ?>

</div>

</body>
</html>
