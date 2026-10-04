<<<<<<< HEAD
<?php
session_start();
require_once("../config.php");

// 1. Auth Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../user_login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success_msg = "";
$error_msg = "";

// 2. Fetch Fresh Data
$user_query = mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id' LIMIT 1");
$user = mysqli_fetch_assoc($user_query);

// 3. Action: Update Personal Information
if (isset($_POST['update_profile'])) {
    $new_name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $new_phone = mysqli_real_escape_string($conn, trim($_POST['phone']));

    $update_sql = "UPDATE users SET name='$new_name', phone='$new_phone' WHERE id='$user_id'";
    
    if (mysqli_query($conn, $update_sql)) {
        $_SESSION['name'] = $new_name; // Sidebar refresh ke liye
        $user['name'] = $new_name;
        $user['phone'] = $new_phone;
        $success_msg = "Profile updated successfully! ✨";
    } else {
        $error_msg = "Database Error: " . mysqli_error($conn);
    }
}

// 4. Action: Change Password
if (isset($_POST['change_password'])) {
    $current = $_POST['current_password'];
    $new_p = $_POST['new_password'];
    $confirm_p = $_POST['confirm_password'];

    if (password_verify($current, $user['password'])) {
        if ($new_p === $confirm_p) {
            $hashed = password_hash($new_p, PASSWORD_DEFAULT);
            mysqli_query($conn, "UPDATE users SET password='$hashed' WHERE id='$user_id'");
            $success_msg = "Security settings updated! 🔐";
        } else { $error_msg = "New passwords do not match."; }
    } else { $error_msg = "Current password is incorrect."; }
}

// Profile Health Calculation
$completion = 60;
if (!empty($user['phone'])) $completion += 40;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile Settings | SmartPass</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        :root { --primary: #6366f1; --dark: #0f172a; --bg: #f8fafc; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); margin: 0; }

        /* Sidebar Style */
        .sidebar { width: 260px; height: 100vh; position: fixed; background: var(--dark); padding: 30px 20px; z-index: 1000; }
        .sidebar h4 { color: #fff; font-weight: 800; margin-bottom: 50px; }
        .sidebar a { color: #94a3b8; display: flex; align-items: center; padding: 12px 16px; border-radius: 12px; text-decoration: none; margin-bottom: 8px; font-weight: 600; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background: rgba(99, 102, 241, 0.15); color: #818cf8; }

        /* Main Layout */
        .main { margin-left: 260px; padding: 40px; }
        .header-banner { background: linear-gradient(135deg, var(--primary), #a855f7); border-radius: 30px; padding: 40px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; color: #fff; box-shadow: 0 10px 30px rgba(99, 102, 241, 0.2); }
        .avatar-circle { width: 80px; height: 80px; border-radius: 50%; background: #fff; display: flex; align-items: center; justify-content: center; font-size: 30px; font-weight: 800; color: var(--primary); border: 4px solid rgba(255,255,255,0.3); }

        /* Cards & Forms */
        .settings-card { background: #fff; border-radius: 25px; padding: 40px; box-shadow: 0 10px 40px rgba(0,0,0,0.02); }
        .nav-pills .nav-link { color: #64748b; font-weight: 700; padding: 15px; border-radius: 15px; margin-bottom: 10px; background: #fff; text-align: left; transition: 0.3s; }
        .nav-pills .nav-link.active { background: #fff !important; color: var(--primary) !important; border: 1px solid rgba(99,102,241,0.2); box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        
        .form-label { font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 8px; }
        .form-control { padding: 13px; border-radius: 14px; background: #f8fafc; border: 1px solid #e2e8f0; font-weight: 600; }
        .btn-update { background: var(--primary); color: #fff; border: none; padding: 15px; border-radius: 15px; font-weight: 700; width: 100%; transition: 0.3s; }
        .btn-update:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(99, 102, 241, 0.2); }
        
        @media(max-width: 992px) { .sidebar { display: none; } .main { margin-left: 0; padding: 20px; } }
    </style>
</head>
<body>

<div class="sidebar">
    <h4><i class="fa fa-bus-simple me-2"></i>SmartPass</h4>
    <nav>
        <a href="dashboard.php"><i class="fa fa-home me-2"></i> Dashboard</a>
        <a href="apply.php"><i class="fa fa-paper-plane me-2"></i> Apply Pass</a>
        <a href="profile.php" class="active"><i class="fa fa-user me-2"></i> Profile Settings</a>
       <a href="../logout.php" class="mt-5 text-danger"><i class="fa-solid fa-power-off me-2"></i> Logout</a>
    </nav>
</div>

<div class="main">
    <div class="header-banner">
        <div class="d-flex align-items-center gap-4">
            <div class="avatar-circle"><?= strtoupper(substr($user['name'] ?? 'U', 0, 2)) ?></div>
            <div>
                <h2 class="fw-800 mb-1"><?= htmlspecialchars($user['name']) ?></h2>
                <p class="mb-0 small fw-bold opacity-75"><?= htmlspecialchars($user['email']) ?></p>
            </div>
        </div>
        <div class="text-center d-none d-md-block">
            <small class="fw-bold">Profile Progress: <?= $completion ?>%</small>
            <div class="progress mt-2" style="height: 6px; width: 150px; background: rgba(255,255,255,0.2); border-radius: 10px;">
                <div class="progress-bar bg-white" style="width: <?= $completion ?>%; border-radius: 10px;"></div>
            </div>
        </div>
    </div>

    <?php if($success_msg) echo "<div class='alert alert-success border-0 rounded-4 mb-4 shadow-sm'>$success_msg</div>"; ?>
    <?php if($error_msg) echo "<div class='alert alert-danger border-0 rounded-4 mb-4 shadow-sm'>$error_msg</div>"; ?>

    <div class="row g-4">
        <div class="col-lg-3">
            <div class="nav flex-column nav-pills" id="pills-tab" role="tablist">
                <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#personal" type="button">
                    <i class="fa fa-id-badge me-2"></i> Personal Info
                </button>
                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#security" type="button">
                    <i class="fa fa-shield-halved me-2"></i> Security
                </button>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="settings-card">
                <div class="tab-content">
                    
                    <div class="tab-pane fade show active" id="personal">
                        <h5 class="fw-800 mb-4">Account Information</h5>
                        <form method="POST">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone Number</label>
                                    <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="+91 XXXXX XXXXX">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Official Email</label>
                                    <input type="email" class="form-control bg-light" value="<?= htmlspecialchars($user['email']) ?>" readonly>
                                </div>
                                <div class="col-12 mt-4">
                                    <button type="submit" name="update_profile" class="btn btn-update">Save Changes</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="tab-pane fade" id="security">
                        <h5 class="fw-800 mb-4">Change Password</h5>
                        <form method="POST">
                            <div class="mb-4">
                                <label class="form-label">Current Password</label>
                                <input type="password" name="current_password" class="form-control" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">New Password</label>
                                <input type="password" name="new_password" class="form-control" minlength="6" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" name="confirm_password" class="form-control" required>
                            </div>
                            <button type="submit" name="change_password" class="btn btn-update">Update Password</button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
=======
<?php
session_start();
require_once("../config.php");

// 1. Auth Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../user_login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];
$success_msg = "";
$error_msg = "";

// 2. Fetch Fresh Data
$user_query = mysqli_query($conn, "SELECT * FROM users WHERE user_id='$user_id' LIMIT 1");
$user = mysqli_fetch_assoc($user_query);

if (!$user) {
    $error_msg = "User account not found.";
}

// 3. Action: Update Personal Information
if (isset($_POST['update_profile'])) {
    $new_name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $new_phone = mysqli_real_escape_string($conn, trim($_POST['phone']));

    $update_sql = "UPDATE users SET name='$new_name', phone='$new_phone' WHERE user_id='$user_id'";

    if (mysqli_query($conn, $update_sql)) {
        $_SESSION['name'] = $new_name;
        $user['name'] = $new_name;
        $user['phone'] = $new_phone;
        $success_msg = "Profile updated successfully! ✨";
    } else {
        $error_msg = "Database Error: " . mysqli_error($conn);
    }
}

// 4. Action: Change Password
if (isset($_POST['change_password'])) {
    $current = $_POST['current_password'];
    $new_p = $_POST['new_password'];
    $confirm_p = $_POST['confirm_password'];

    if (password_verify($current, $user['password'])) {
        if ($new_p === $confirm_p) {
            $hashed = password_hash($new_p, PASSWORD_DEFAULT);

            mysqli_query(
                $conn,
                "UPDATE users SET password='$hashed' WHERE user_id='$user_id'"
            );

            $success_msg = "Security settings updated! 🔐";
        } else {
            $error_msg = "New passwords do not match.";
        }
    } else {
        $error_msg = "Current password is incorrect.";
    }
}

// Profile Health Calculation
$completion = 60;
if (!empty($user['phone'])) {
    $completion += 40;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile Settings | SmartPass</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        :root {
            --primary: #6366f1;
            --dark: #0f172a;
            --bg: #f8fafc;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            margin: 0;
        }

        /* Sidebar Style */
        .sidebar {
            width: 260px;
            height: 100vh;
            position: fixed;
            background: var(--dark);
            padding: 30px 20px;
            z-index: 1000;
        }

        .sidebar h4 {
            color: #fff;
            font-weight: 800;
            margin-bottom: 50px;
        }

        .sidebar a {
            color: #94a3b8;
            display: flex;
            align-items: center;
            padding: 12px 16px;
            border-radius: 12px;
            text-decoration: none;
            margin-bottom: 8px;
            font-weight: 600;
            transition: 0.3s;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: rgba(99, 102, 241, 0.15);
            color: #818cf8;
        }

        /* Main Layout */
        .main {
            margin-left: 260px;
            padding: 40px;
        }

        .header-banner {
            background: linear-gradient(135deg, var(--primary), #a855f7);
            border-radius: 30px;
            padding: 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            color: #fff;
            box-shadow: 0 10px 30px rgba(99, 102, 241, 0.2);
        }

        .avatar-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            font-weight: 800;
            color: var(--primary);
            border: 4px solid rgba(255, 255, 255, 0.3);
        }

        /* Cards & Forms */
        .settings-card {
            background: #fff;
            border-radius: 25px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.02);
        }

        .nav-pills .nav-link {
            color: #64748b;
            font-weight: 700;
            padding: 15px;
            border-radius: 15px;
            margin-bottom: 10px;
            background: #fff;
            text-align: left;
            transition: 0.3s;
        }

        .nav-pills .nav-link.active {
            background: #fff !important;
            color: var(--primary) !important;
            border: 1px solid rgba(99, 102, 241, 0.2);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .form-label {
            font-size: 11px;
            font-weight: 800;
            color: #94a3b8;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .form-control {
            padding: 13px;
            border-radius: 14px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            font-weight: 600;
        }

        .btn-update {
            background: var(--primary);
            color: #fff;
            border: none;
            padding: 15px;
            border-radius: 15px;
            font-weight: 700;
            width: 100%;
            transition: 0.3s;
        }

        .btn-update:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(99, 102, 241, 0.2);
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

<div class="sidebar">
    <h4>
        <i class="fa fa-bus-simple me-2"></i>SmartPass
    </h4>

    <nav>
        <a href="dashboard.php">
            <i class="fa fa-home me-2"></i> Dashboard
        </a>

        <a href="apply.php">
            <i class="fa fa-paper-plane me-2"></i> Apply Pass
        </a>

        <a href="profile.php" class="active">
            <i class="fa fa-user me-2"></i> Profile Settings
        </a>

        <a href="../logout.php" class="mt-5 text-danger">
            <i class="fa-solid fa-power-off me-2"></i> Logout
        </a>
    </nav>
</div>

<div class="main">

    <div class="header-banner">

        <div class="d-flex align-items-center gap-4">

            <div class="avatar-circle">
                <?= strtoupper(substr($user['name'] ?? 'U', 0, 2)) ?>
            </div>

            <div>
                <h2 class="fw-800 mb-1">
                    <?= htmlspecialchars($user['name'] ?? '') ?>
                </h2>

                <p class="mb-0 small fw-bold opacity-75">
                    <?= htmlspecialchars($user['email'] ?? '') ?>
                </p>
            </div>

        </div>

        <div class="text-center d-none d-md-block">
            <small class="fw-bold">
                Profile Progress: <?= $completion ?>%
            </small>

            <div class="progress mt-2"
                 style="height: 6px; width: 150px; background: rgba(255,255,255,0.2); border-radius: 10px;">

                <div class="progress-bar bg-white"
                     style="width: <?= $completion ?>%; border-radius: 10px;">
                </div>

            </div>
        </div>

    </div>

    <?php if ($success_msg): ?>
        <div class="alert alert-success border-0 rounded-4 mb-4 shadow-sm">
            <?= htmlspecialchars($success_msg) ?>
        </div>
    <?php endif; ?>

    <?php if ($error_msg): ?>
        <div class="alert alert-danger border-0 rounded-4 mb-4 shadow-sm">
            <?= htmlspecialchars($error_msg) ?>
        </div>
    <?php endif; ?>

    <div class="row g-4">

        <div class="col-lg-3">

            <div class="nav flex-column nav-pills" id="pills-tab" role="tablist">

                <button class="nav-link active"
                        data-bs-toggle="pill"
                        data-bs-target="#personal"
                        type="button">
                    <i class="fa fa-id-badge me-2"></i>
                    Personal Info
                </button>

                <button class="nav-link"
                        data-bs-toggle="pill"
                        data-bs-target="#security"
                        type="button">
                    <i class="fa fa-shield-halved me-2"></i>
                    Security
                </button>

            </div>

        </div>

        <div class="col-lg-9">

            <div class="settings-card">

                <div class="tab-content">

                    <!-- Personal Information -->
                    <div class="tab-pane fade show active" id="personal">

                        <h5 class="fw-800 mb-4">
                            Account Information
                        </h5>

                        <form method="POST">

                            <div class="row g-4">

                                <div class="col-md-6">

                                    <label class="form-label">
                                        Full Name
                                    </label>

                                    <input
                                        type="text"
                                        name="name"
                                        class="form-control"
                                        value="<?= htmlspecialchars($user['name'] ?? '') ?>"
                                        required
                                    >

                                </div>

                                <div class="col-md-6">

                                    <label class="form-label">
                                        Phone Number
                                    </label>

                                    <input
                                        type="text"
                                        name="phone"
                                        class="form-control"
                                        value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                                        placeholder="+91 XXXXX XXXXX"
                                    >

                                </div>

                                <div class="col-12">

                                    <label class="form-label">
                                        Official Email
                                    </label>

                                    <input
                                        type="email"
                                        class="form-control bg-light"
                                        value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                                        readonly
                                    >

                                </div>

                                <div class="col-12 mt-4">

                                    <button
                                        type="submit"
                                        name="update_profile"
                                        class="btn btn-update">
                                        Save Changes
                                    </button>

                                </div>

                            </div>

                        </form>

                    </div>

                    <!-- Security -->
                    <div class="tab-pane fade" id="security">

                        <h5 class="fw-800 mb-4">
                            Change Password
                        </h5>

                        <form method="POST">

                            <div class="mb-4">

                                <label class="form-label">
                                    Current Password
                                </label>

                                <input
                                    type="password"
                                    name="current_password"
                                    class="form-control"
                                    required
                                >

                            </div>

                            <div class="mb-4">

                                <label class="form-label">
                                    New Password
                                </label>

                                <input
                                    type="password"
                                    name="new_password"
                                    class="form-control"
                                    minlength="6"
                                    required
                                >

                            </div>

                            <div class="mb-4">

                                <label class="form-label">
                                    Confirm New Password
                                </label>

                                <input
                                    type="password"
                                    name="confirm_password"
                                    class="form-control"
                                    required
                                >

                            </div>

                            <button
                                type="submit"
                                name="change_password"
                                class="btn btn-update">
                                Update Password
                            </button>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
>>>>>>> origin/main
