<<<<<<< HEAD
<?php
session_start();
include("../config.php");
$uid = $_SESSION['user_id'];
$res = mysqli_query($conn,"SELECT * FROM bus_pass WHERE user_id='$uid'");
?>
<!DOCTYPE html>
<html>
<head>
<title>Bus Pass Status</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
<div class="card shadow p-4">
<h3 class="text-center">My Bus Pass Status</h3>

<table class="table table-bordered mt-3">
<tr>
<th>Route</th><th>Type</th><th>Status</th><th>Date</th>
</tr>

<?php while($row=mysqli_fetch_assoc($res)){ ?>
<tr>
<td><?php echo $row['route']; ?></td>
<td><?php echo $row['pass_type']; ?></td>
<td><?php echo $row['status']; ?></td>
<td><?php echo $row['apply_date']; ?></td>
</tr>
<?php } ?>
</table>

<a href="dashboard.php" class="btn btn-secondary">Back</a>
</div>
</div>

</body>
</html>
=======
<?php
session_start();
include("../config.php");
$uid = $_SESSION['user_id'];
$res = mysqli_query($conn,"SELECT * FROM bus_pass WHERE user_id='$uid'");
?>
<!DOCTYPE html>
<html>
<head>
<title>Bus Pass Status</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
<div class="card shadow p-4">
<h3 class="text-center">My Bus Pass Status</h3>

<table class="table table-bordered mt-3">
<tr>
<th>Route</th><th>Type</th><th>Status</th><th>Date</th>
</tr>

<?php while($row=mysqli_fetch_assoc($res)){ ?>
<tr>
<td><?php echo $row['route']; ?></td>
<td><?php echo $row['pass_type']; ?></td>
<td><?php echo $row['status']; ?></td>
<td><?php echo $row['apply_date']; ?></td>
</tr>
<?php } ?>
</table>

<a href="dashboard.php" class="btn btn-secondary">Back</a>
</div>
</div>

</body>
</html>
>>>>>>> origin/main
