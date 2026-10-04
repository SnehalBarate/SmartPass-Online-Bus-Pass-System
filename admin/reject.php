<?php
// ================= SESSION =================
session_start();

// ================= DATABASE =================
require_once("../config.php");

// ================= ADMIN SECURITY CHECK =================
if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'admin'
) {
    header("Location: ../admin_login.php");
    exit;
}

// ================= CHECK APPLICATION ID =================
if (!isset($_GET['id'])) {
    header("Location: dashboard.php?status=invalid_request");
    exit;
}

// Convert ID to integer
$id = intval($_GET['id']);

if ($id <= 0) {
    header("Location: dashboard.php?status=invalid_application");
    exit;
}

// ================= CHECK APPLICATION =================
$stmt = mysqli_prepare(
    $conn,
    "SELECT id, status 
     FROM applications 
     WHERE id = ? 
     LIMIT 1"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$application = mysqli_fetch_assoc($result);

// Application does not exist
if (!$application) {
    header("Location: dashboard.php?status=application_not_found");
    exit;
}

// ================= ONLY PENDING CAN BE REJECTED =================
if ($application['status'] !== 'Pending') {
    header("Location: dashboard.php?status=already_processed");
    exit;
}

// ================= REJECT APPLICATION =================
$update = mysqli_prepare(
    $conn,
    "UPDATE applications
     SET status = 'Rejected',
         expiry_date = NULL
     WHERE id = ?
     AND status = 'Pending'"
);

mysqli_stmt_bind_param($update, "i", $id);

// ================= EXECUTE =================
if (mysqli_stmt_execute($update)) {

    header("Location: dashboard.php?status=rejected");
    exit;

} else {

    header("Location: dashboard.php?status=error");
    exit;
}
?>