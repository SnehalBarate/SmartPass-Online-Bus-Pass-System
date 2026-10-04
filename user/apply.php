<?php
// ================== ERROR REPORTING ==================
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ================== SESSION & DB ==================
session_start();
require_once "../config.php";

// ================== AUTH CHECK ==================
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../user_login.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];

// ================== CHECK EXISTING APPLICATION ==================
$check = mysqli_prepare(
    $conn,
    "SELECT id FROM applications
     WHERE user_id = ?
     AND status IN ('Pending','Approved')
     LIMIT 1"
);

mysqli_stmt_bind_param($check, "i", $user_id);
mysqli_stmt_execute($check);
$result = mysqli_stmt_get_result($check);

if (mysqli_num_rows($result) > 0 && !isset($_POST['apply'])) {
    header("Location: application_not_allowed.php");
    exit;
}

// ================== FORM SUBMIT LOGIC ==================
if (isset($_POST['apply'])) {

    $full_name = trim($_POST['full_name']);
    $college   = trim($_POST['college']);
    $from      = trim($_POST['route_from']);
    $to        = trim($_POST['route_to']);

    $upload_dir = "../uploads/";

    $docs = [
        'photo',
        'bonafide_doc',
        'college_form',
        'id_card_doc',
        'aadhar_doc'
    ];

    // ================== CHECK FILE UPLOAD ==================
    foreach ($docs as $doc) {

        if (!isset($_FILES[$doc])) {
            die("File missing: " . $doc);
        }

        if ($_FILES[$doc]['error'] !== 0) {
            die(
                "Upload error in " . $doc .
                " | Error code: " . $_FILES[$doc]['error']
            );
        }
    }

    // ================== UPLOAD FILES ==================
    $files = [];

    foreach ($docs as $doc) {

        $original_name = basename($_FILES[$doc]['name']);
        $filename = time() . "_" . $original_name;

        $target = $upload_dir . $filename;

        if (!move_uploaded_file($_FILES[$doc]['tmp_name'], $target)) {
            die("Could not save uploaded file: " . $doc);
        }

        $files[$doc] = $filename;
    }

    // ================== INSERT APPLICATION ==================
    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO applications
        (
            user_id,
            photo,
            full_name,
            college,
            route_from,
            route_to,
            bonafide_doc,
            college_form,
            id_card_doc,
            aadhar_doc,
            status
        )
        VALUES (?,?,?,?,?,?,?,?,?,?,'Pending')"
    );

    if (!$stmt) {
        die("Database prepare error: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param(
        $stmt,
        "isssssssss",
        $user_id,
        $files['photo'],
        $full_name,
        $college,
        $from,
        $to,
        $files['bonafide_doc'],
        $files['college_form'],
        $files['id_card_doc'],
        $files['aadhar_doc']
    );

    if (!mysqli_stmt_execute($stmt)) {
        die("Database insert error: " . mysqli_stmt_error($stmt));
    }

    header("Location: application_success.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply Pass | SmartPass</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        :root {
            --primary: #4f46e5;
            --primary-light: #eef2ff;
            --sidebar-bg: #0f172a;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --bg-body: #f1f5f9;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-dark);
        }

        .sidebar {
            width: 280px;
            height: 100vh;
            position: fixed;
            background: var(--sidebar-bg);
            padding: 40px 24px;
            color: #fff;
        }

        .sidebar h2 {
            font-weight: 800;
            font-size: 22px;
            margin-bottom: 50px;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #94a3b8;
            padding: 14px 20px;
            text-decoration: none;
            border-radius: 12px;
            margin-bottom: 8px;
            font-weight: 600;
            transition: 0.3s;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: var(--primary);
            color: #fff;
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.4);
        }

        .main-content {
            margin-left: 280px;
            padding: 50px;
        }

        .form-container {
            max-width: 900px;
            background: #fff;
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05);
        }

        .section-header {
            margin-bottom: 30px;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 15px;
        }

        .section-header h4 {
            font-weight: 800;
            color: var(--text-dark);
        }

        .form-label {
            font-weight: 600;
            font-size: 14px;
            color: var(--text-muted);
            margin-bottom: 8px;
        }

        .form-control {
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            padding: 12px 16px;
            background: #f8fafc;
            transition: 0.3s;
        }

        .form-control:focus {
            background: #fff;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }

        .upload-box {
            background: #f8fafc;
            border: 2px dashed #e2e8f0;
            border-radius: 16px;
            padding: 20px;
            transition: 0.3s;
            text-align: center;
        }

        .upload-box:hover {
            border-color: var(--primary);
            background: var(--primary-light);
        }

        .upload-box i {
            font-size: 24px;
            color: var(--primary);
            margin-bottom: 10px;
        }

        .btn-apply {
            background: var(--primary);
            color: #fff;
            font-weight: 800;
            border: none;
            padding: 16px;
            border-radius: 16px;
            width: 100%;
            margin-top: 30px;
            transition: 0.3s;
            font-size: 16px;
        }

        .btn-apply:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 25px -5px rgba(79, 70, 229, 0.4);
        }
    </style>
</head>

<body>

<div class="sidebar">
    <h2>
        <i class="fa-solid fa-bus-simple me-2"></i> SmartPass
    </h2>

    <a href="dashboard.php">
        <i class="fa-solid fa-house"></i> Dashboard
    </a>

    <a href="apply.php" class="active">
        <i class="fa-solid fa-file-signature"></i> Apply Pass
    </a>

    <a href="profile.php">
        <i class="fa-solid fa-user-gear"></i> Profile
    </a>

    <a href="../logout.php"
       style="margin-top: 50px; color: #f87171;">
        <i class="fa-solid fa-power-off"></i> Logout
    </a>
</div>

<div class="main-content">
    <div class="form-container mx-auto">

        <div class="section-header">
            <h4>Application for Bus Pass</h4>
            <p class="text-muted m-0">
                Please fill in your details and upload clear documents.
            </p>
        </div>

        <form method="POST" action="apply.php" enctype="multipart/form-data">

            <div class="row g-4 mb-5">

                <div class="col-md-6">
                    <label class="form-label">Full Name</label>
                    <input
                        type="text"
                        name="full_name"
                        class="form-control"
                        placeholder="As per documents"
                        required
                    >
                </div>

                <div class="col-md-6">
                    <label class="form-label">College / Institution</label>
                    <input
                        type="text"
                        name="college"
                        class="form-control"
                        placeholder="Full college name"
                        required
                    >
                </div>

                <div class="col-md-6">
                    <label class="form-label">Starting Point (From)</label>
                    <input
                        type="text"
                        name="route_from"
                        class="form-control"
                        placeholder="Home stop"
                        required
                    >
                </div>

                <div class="col-md-6">
                    <label class="form-label">Destination (To)</label>
                    <input
                        type="text"
                        name="route_to"
                        class="form-control"
                        placeholder="College stop"
                        required
                    >
                </div>

            </div>

            <div class="section-header">
                <h4>Document Verification</h4>
            </div>

            <div class="row g-4">

                <div class="col-md-4">
                    <div class="upload-box">
                        <i class="fa-solid fa-camera"></i>

                        <label class="form-label d-block">
                            Passport Photo
                        </label>

                        <input
                            type="file"
                            name="photo"
                            class="form-control form-control-sm"
                            accept="image/*"
                            required
                        >
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="upload-box">
                        <i class="fa-solid fa-file-contract"></i>

                        <label class="form-label d-block">
                            Bonafide
                        </label>

                        <input
                            type="file"
                            name="bonafide_doc"
                            class="form-control form-control-sm"
                            required
                        >
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="upload-box">
                        <i class="fa-solid fa-stamp"></i>

                        <label class="form-label d-block">
                            College Form
                        </label>

                        <input
                            type="file"
                            name="college_form"
                            class="form-control form-control-sm"
                            required
                        >
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="upload-box">
                        <i class="fa-solid fa-id-card"></i>

                        <label class="form-label d-block">
                            College ID Card
                        </label>

                        <input
                            type="file"
                            name="id_card_doc"
                            class="form-control form-control-sm"
                            required
                        >
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="upload-box">
                        <i class="fa-solid fa-address-card"></i>

                        <label class="form-label d-block">
                            Aadhar Card
                        </label>

                        <input
                            type="file"
                            name="aadhar_doc"
                            class="form-control form-control-sm"
                            required
                        >
                    </div>
                </div>

            </div>

            <button
                type="submit"
                name="apply"
                class="btn-apply"
            >
                Submit Application
                <i class="fa-solid fa-paper-plane ms-2"></i>
            </button>

        </form>

    </div>
</div>

</body>
</html>
