<?php
session_start();
require_once("../config.php");
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { exit; }

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    mysqli_query($conn, "UPDATE applications SET status = 'Rejected' WHERE id = $id");
    header("Location: dashboard.php?status=rejected");
}
?>