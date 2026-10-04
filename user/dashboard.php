<<<<<<< HEAD
<?php
session_start();
require_once "../config.php";

/* Standard student auth check */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../user_login.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$name = $_SESSION['name'];

/* Fetch the most recent application for this student */
$stmt = mysqli_prepare(
    $conn,
    "SELECT * FROM applications 
     WHERE user_id = ? 
     ORDER BY id DESC 
     LIMIT 1"
);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$app = mysqli_fetch_assoc($result);

/* Process derived values */
$status = $app['status'] ?? 'NOT APPLIED';
$route  = ($app) ? $app['route_from'] . " → " . $app['route_to'] : "No Route Set";
$validity = $app['expiry_date'] ?? "N/A";

/* EXPIRY LOGIC: Check if the pass is expiring soon or already expired */
$expiry_warning = "";
$is_expired = false;

if ($status === 'Approved' && !empty($app['expiry_date'])) {
    $today = new DateTime(); // Current system date
    $expiry = new DateTime($app['expiry_date']); // Expiry date from DB
    
    // Calculate the difference in days
    $interval = $today->diff($expiry);
    $days_left = (int)$interval->format('%r%a'); 

    if ($days_left <= 5 && $days_left > 0) {
        // Warning for passes expiring within 5 days
        $expiry_warning = "Your pass will expire in $days_left days. Please apply for renewal soon!";
    } elseif ($days_left <= 0) {
        // Notification for passes that have already expired
        $expiry_warning = "Your pass has EXPIRED. Please submit a new application.";
        $is_expired = true;
    }
}

/* Logic to determine if the student can submit a new application */
$canApply = !($app && in_array($app['status'], ['Pending','Approved'])) || $is_expired;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard | SmartPass</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --sidebar-bg: #0f172a;
            --sidebar-hover: #1e293b;
            --bg-light: #f8fafc;
            --text-main: #1e293b;
            --text-muted: #64748b;
        }

        body { margin: 0; font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg-light); color: var(--text-main); }
        .layout { display: flex; }

        /* --- SIDEBAR --- */
        .sidebar { width: 260px; background: var(--sidebar-bg); color: #fff; min-height: 100vh; position: fixed; padding: 30px 20px; box-sizing: border-box; }
        .sidebar h2 { font-weight: 800; font-size: 24px; margin-bottom: 40px; display: flex; align-items: center; gap: 10px; color: #fff; }
        .sidebar a { display: flex; align-items: center; gap: 12px; color: #94a3b8; padding: 14px 18px; border-radius: 12px; text-decoration: none; margin-bottom: 10px; font-weight: 600; transition: 0.3s; }
        .sidebar a:hover { background: var(--sidebar-hover); color: #fff; }
        .sidebar a.active { background: var(--primary); color: #fff; box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.3); }
        .logout { margin-top: 40px; color: #f87171 !important; border: 1px solid rgba(248, 113, 113, 0.2); }

        /* --- MAIN CONTENT --- */
        .main { flex: 1; margin-left: 260px; padding: 40px; box-sizing: border-box; }
        
        /* Welcome Gradient Banner */
        .welcome { background: linear-gradient(135deg, var(--primary), #a855f7); color: #fff; padding: 35px; border-radius: 24px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 20px 25px -5px rgba(99, 102, 241, 0.2); }
        .welcome h2 { margin: 0; font-size: 28px; font-weight: 800; }
        .avatar { width: 70px; height: 70px; border-radius: 20px; background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(10px); display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 28px; border: 1px solid rgba(255, 255, 255, 0.3); }

        /* Warning Banner Styling */
        .expiry-alert { background: #fffbeb; border-left: 5px solid #f59e0b; color: #92400e; padding: 20px; border-radius: 16px; display: flex; align-items: center; gap: 15px; margin-bottom: 25px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }

        /* Stats Cards */
        .cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 25px; margin-top: 35px; }
        .card { background: #fff; border-radius: 20px; padding: 30px 20px; text-align: center; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.05); transition: 0.3s; }
        .card:hover { transform: translateY(-5px); }
        .card i { color: var(--primary); margin-bottom: 15px; }
        .card h4 { margin: 0 0 10px; font-size: 14px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; }
        .card p { margin: 0; font-size: 18px; font-weight: 700; color: var(--text-main); }

        .badge { padding: 8px 18px; border-radius: 50px; font-size: 12px; font-weight: 800; text-transform: uppercase; }
        .pending { background: #fff7ed; color: #c2410c; }
        .approved { background: #f0fdf4; color: #15803d; }
        .notapplied { background: #f1f5f9; color: #475569; }
        .expired { background: #fee2e2; color: #991b1b; }

        /* Progress Section */
        .progress-section { background: #fff; border-radius: 24px; padding: 35px; margin-top: 35px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .apply-btn { display: inline-flex; align-items: center; gap: 10px; margin-top: 20px; background: var(--primary); color: #fff; padding: 14px 30px; border-radius: 14px; text-decoration: none; font-weight: 700; transition: 0.3s; }
        .apply-btn:hover { background: var(--primary-dark); box-shadow: 0 10px 15px rgba(99, 102, 241, 0.3); }
        .download-btn { background: #10b981; color: white; padding: 10px 20px; border-radius: 10px; text-decoration: none; font-weight: 700; display: inline-flex; align-items: center; gap: 8px; margin-top: 15px; }
    </style>
</head>
<body>

<div class="layout">
    <div class="sidebar">
        <h2><i class="fa fa-bus-simple"></i> SmartPass</h2>
        <a href="dashboard.php" class="active"><i class="fa fa-grid-2 me-2"></i> Dashboard</a>
        <a href="apply.php"><i class="fa fa-paper-plane"></i> Apply Pass</a>
        <a href="profile.php"><i class="fa fa-user"></i> Profile</a>
        <a href="../logout.php" class="logout"><i class="fa fa-sign-out"></i> Logout</a>
    </div>

    <div class="main">

        <?php if ($expiry_warning): ?>
            <div class="expiry-alert">
                <i class="fa-solid fa-triangle-exclamation fs-3"></i>
                <div>
                    <h5 class="m-0 fw-bold">Important Notice</h5>
                    <p class="m-0 small"><?= $expiry_warning ?></p>
                </div>
            </div>
        <?php endif; ?>

        <div class="welcome">
            <div>
                <h2>Welcome, <?= htmlspecialchars($name) ?> 👋</h2>
                <p>Track your application and manage your digital bus pass.</p>
            </div>
            <div class="avatar">
                <?= strtoupper(substr($name,0,1)) ?>
            </div>
        </div>

        <div class="cards">
            <div class="card">
                <i class="fa fa-id-card-clip fa-3x"></i>
                <h4>Pass Status</h4>
                <div class="mt-2">
                    <span class="badge <?= $is_expired ? 'expired' : strtolower(str_replace(' ','',$status)) ?>">
                        <?= $is_expired ? 'EXPIRED' : htmlspecialchars($status) ?>
                    </span>
                </div>
            </div>

            <div class="card">
                <i class="fa-solid fa-route fa-3x"></i>
                <h4>Active Route</h4>
                <p><?= htmlspecialchars($route) ?></p>
            </div>

            <div class="card">
                <i class="fa fa-calendar-check fa-3x"></i>
                <h4>Validity</h4>
                <p><?= htmlspecialchars($validity) ?></p>
            </div>
        </div>

        <div class="progress-section">
            <h3>Application Tracking</h3>

            <?php if ($canApply): ?>
                <p>You currently do not have an active pass. Submit a new application to travel.</p>
                <a href="apply.php" class="apply-btn">Apply For Pass <i class="fa fa-arrow-right"></i></a>
            <?php else: ?>
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p>Your application is currently <b><?= htmlspecialchars($status) ?></b>.</p>
                        <small class="text-muted">Once approved, you can download your scannable digital pass here.</small>
                    </div>
                    <?php if($status === 'Approved' && !$is_expired): ?>
                        <a href="view_pass.php" class="download-btn"><i class="fa fa-download"></i> View Digital Pass</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
=======
<?php
session_start();
require_once "../config.php";

/* Standard student auth check */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../user_login.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$name = $_SESSION['name'];

/* Fetch the most recent application for this student */
$stmt = mysqli_prepare(
    $conn,
    "SELECT * FROM applications 
     WHERE user_id = ? 
     ORDER BY id DESC 
     LIMIT 1"
);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$app = mysqli_fetch_assoc($result);

/* Process derived values */
$status = $app['status'] ?? 'NOT APPLIED';
$route  = ($app) ? $app['route_from'] . " → " . $app['route_to'] : "No Route Set";
$validity = $app['expiry_date'] ?? "N/A";

/* EXPIRY LOGIC: Check if the pass is expiring soon or already expired */
$expiry_warning = "";
$is_expired = false;

if ($status === 'Approved' && !empty($app['expiry_date'])) {
    $today = new DateTime(); // Current system date
    $expiry = new DateTime($app['expiry_date']); // Expiry date from DB
    
    // Calculate the difference in days
    $interval = $today->diff($expiry);
    $days_left = (int)$interval->format('%r%a'); 

    if ($days_left <= 5 && $days_left > 0) {
        // Warning for passes expiring within 5 days
        $expiry_warning = "Your pass will expire in $days_left days. Please apply for renewal soon!";
    } elseif ($days_left <= 0) {
        // Notification for passes that have already expired
        $expiry_warning = "Your pass has EXPIRED. Please submit a new application.";
        $is_expired = true;
    }
}

/* Logic to determine if the student can submit a new application */
$canApply = !($app && in_array($app['status'], ['Pending','Approved'])) || $is_expired;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard | SmartPass</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --sidebar-bg: #0f172a;
            --sidebar-hover: #1e293b;
            --bg-light: #f8fafc;
            --text-main: #1e293b;
            --text-muted: #64748b;
        }

        body { margin: 0; font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg-light); color: var(--text-main); }
        .layout { display: flex; }

        /* --- SIDEBAR --- */
        .sidebar { width: 260px; background: var(--sidebar-bg); color: #fff; min-height: 100vh; position: fixed; padding: 30px 20px; box-sizing: border-box; }
        .sidebar h2 { font-weight: 800; font-size: 24px; margin-bottom: 40px; display: flex; align-items: center; gap: 10px; color: #fff; }
        .sidebar a { display: flex; align-items: center; gap: 12px; color: #94a3b8; padding: 14px 18px; border-radius: 12px; text-decoration: none; margin-bottom: 10px; font-weight: 600; transition: 0.3s; }
        .sidebar a:hover { background: var(--sidebar-hover); color: #fff; }
        .sidebar a.active { background: var(--primary); color: #fff; box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.3); }
        .logout { margin-top: 40px; color: #f87171 !important; border: 1px solid rgba(248, 113, 113, 0.2); }

        /* --- MAIN CONTENT --- */
        .main { flex: 1; margin-left: 260px; padding: 40px; box-sizing: border-box; }
        
        /* Welcome Gradient Banner */
        .welcome { background: linear-gradient(135deg, var(--primary), #a855f7); color: #fff; padding: 35px; border-radius: 24px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 20px 25px -5px rgba(99, 102, 241, 0.2); }
        .welcome h2 { margin: 0; font-size: 28px; font-weight: 800; }
        .avatar { width: 70px; height: 70px; border-radius: 20px; background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(10px); display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 28px; border: 1px solid rgba(255, 255, 255, 0.3); }

        /* Warning Banner Styling */
        .expiry-alert { background: #fffbeb; border-left: 5px solid #f59e0b; color: #92400e; padding: 20px; border-radius: 16px; display: flex; align-items: center; gap: 15px; margin-bottom: 25px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }

        /* Stats Cards */
        .cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 25px; margin-top: 35px; }
        .card { background: #fff; border-radius: 20px; padding: 30px 20px; text-align: center; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.05); transition: 0.3s; }
        .card:hover { transform: translateY(-5px); }
        .card i { color: var(--primary); margin-bottom: 15px; }
        .card h4 { margin: 0 0 10px; font-size: 14px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; }
        .card p { margin: 0; font-size: 18px; font-weight: 700; color: var(--text-main); }

        .badge { padding: 8px 18px; border-radius: 50px; font-size: 12px; font-weight: 800; text-transform: uppercase; }
        .pending { background: #fff7ed; color: #c2410c; }
        .approved { background: #f0fdf4; color: #15803d; }
        .notapplied { background: #f1f5f9; color: #475569; }
        .expired { background: #fee2e2; color: #991b1b; }

        /* Progress Section */
        .progress-section { background: #fff; border-radius: 24px; padding: 35px; margin-top: 35px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .apply-btn { display: inline-flex; align-items: center; gap: 10px; margin-top: 20px; background: var(--primary); color: #fff; padding: 14px 30px; border-radius: 14px; text-decoration: none; font-weight: 700; transition: 0.3s; }
        .apply-btn:hover { background: var(--primary-dark); box-shadow: 0 10px 15px rgba(99, 102, 241, 0.3); }
        .download-btn { background: #10b981; color: white; padding: 10px 20px; border-radius: 10px; text-decoration: none; font-weight: 700; display: inline-flex; align-items: center; gap: 8px; margin-top: 15px; }
    </style>
</head>
<body>

<div class="layout">
    <div class="sidebar">
        <h2><i class="fa fa-bus-simple"></i> SmartPass</h2>
        <a href="dashboard.php" class="active"><i class="fa fa-grid-2 me-2"></i> Dashboard</a>
        <a href="apply.php"><i class="fa fa-paper-plane"></i> Apply Pass</a>
        <a href="profile.php"><i class="fa fa-user"></i> Profile</a>
        <a href="../logout.php" class="logout"><i class="fa fa-sign-out"></i> Logout</a>
    </div>

    <div class="main">

        <?php if ($expiry_warning): ?>
            <div class="expiry-alert">
                <i class="fa-solid fa-triangle-exclamation fs-3"></i>
                <div>
                    <h5 class="m-0 fw-bold">Important Notice</h5>
                    <p class="m-0 small"><?= $expiry_warning ?></p>
                </div>
            </div>
        <?php endif; ?>

        <div class="welcome">
            <div>
                <h2>Welcome, <?= htmlspecialchars($name) ?> 👋</h2>
                <p>Track your application and manage your digital bus pass.</p>
            </div>
            <div class="avatar">
                <?= strtoupper(substr($name,0,1)) ?>
            </div>
        </div>

        <div class="cards">
            <div class="card">
                <i class="fa fa-id-card-clip fa-3x"></i>
                <h4>Pass Status</h4>
                <div class="mt-2">
                    <span class="badge <?= $is_expired ? 'expired' : strtolower(str_replace(' ','',$status)) ?>">
                        <?= $is_expired ? 'EXPIRED' : htmlspecialchars($status) ?>
                    </span>
                </div>
            </div>

            <div class="card">
                <i class="fa-solid fa-route fa-3x"></i>
                <h4>Active Route</h4>
                <p><?= htmlspecialchars($route) ?></p>
            </div>

            <div class="card">
                <i class="fa fa-calendar-check fa-3x"></i>
                <h4>Validity</h4>
                <p><?= htmlspecialchars($validity) ?></p>
            </div>
        </div>

        <div class="progress-section">
            <h3>Application Tracking</h3>

            <?php if ($canApply): ?>
                <p>You currently do not have an active pass. Submit a new application to travel.</p>
                <a href="apply.php" class="apply-btn">Apply For Pass <i class="fa fa-arrow-right"></i></a>
            <?php else: ?>
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p>Your application is currently <b><?= htmlspecialchars($status) ?></b>.</p>
                        <small class="text-muted">Once approved, you can download your scannable digital pass here.</small>
                    </div>
                    <?php if($status === 'Approved' && !$is_expired): ?>
                        <a href="view_pass.php" class="download-btn"><i class="fa fa-download"></i> View Digital Pass</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
>>>>>>> origin/main
</html>