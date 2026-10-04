<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "bus_pass_db";
$port = 3307;

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
?>
