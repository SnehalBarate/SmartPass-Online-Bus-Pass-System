<?php
session_start();
require_once("../config.php");

// ================= AUTHENTICATION CHECK =================
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../user_login.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];

// ================= FETCH APPROVED APPLICATION =================
$query = "SELECT * FROM applications WHERE user_id='$user_id' AND status='Approved' LIMIT 1";
$result = mysqli_query($conn, $query);
$pass = mysqli_fetch_assoc($result);

// Redirect back to dashboard if no approved pass exists
if (!$pass) {
    header("Location: dashboard.php");
    exit;
}

// ================= QR DATA =================
$name  = strtoupper($pass['full_name']);
$route = $pass['route_from'] . " to " . $pass['route_to'];
$id    = $pass['id'];

$qrContent = "SMARTPASS VERIFIED\nID: #SP-$id\nName: $name\nRoute: $route\nStatus: ACTIVE";

// Reliable QR API
$qrCodeURL = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($qrContent);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital Pass | SmartPass</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body {
            background: #f4f7fe;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .pass-card {
            width: 400px;
            margin: 60px auto;
            border-radius: 25px;
            background: #fff !important;
            box-shadow: 0 20px 50px rgba(0,0,0,0.1);
            overflow: hidden;
            border: 1px solid #eee;
            position: relative;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .pass-header {
            background: #0f172a !important;
            color: white !important;
            padding: 30px 20px;
            text-align: center;
            -webkit-print-color-adjust: exact !important;
        }

        .qr-holder {
            width: 170px;
            height: 170px;
            background: white !important;
            margin: -85px auto 20px;
            padding: 10px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 5;
            -webkit-print-color-adjust: exact !important;
        }

        .qr-holder img {
            width: 100%;
            height: 100%;
            border-radius: 10px;
        }

        .pass-content {
            padding: 10px 35px 35px;
        }

        .label {
            font-size: 10px;
            color: #94a3b8 !important;
            text-transform: uppercase;
            font-weight: 800;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }

        .value {
            font-size: 16px;
            color: #1e293b !important;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .route-info {
            background: #f8fafc !important;
            padding: 15px;
            border-radius: 15px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid #e2e8f0;
            -webkit-print-color-adjust: exact !important;
        }

        .status-badge {
            background: #dcfce7 !important;
            color: #15803d !important;
            padding: 5px 15px;
            border-radius: 50px;
            font-size: 11px;
            font-weight: 800;
            -webkit-print-color-adjust: exact !important;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: white !important;
            }

            .pass-card {
                margin: 0 auto !important;
                box-shadow: none !important;
                border: 1px solid #000 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .pass-header {
                background-color: #0f172a !important;
                color: white !important;
            }

            .text-primary {
                color: #0d6efd !important;
            }

            .text-danger {
                color: #dc3545 !important;
            }
        }
    </style>
</head>

<body>

<div class="pass-card">

    <div class="pass-header">
        <h5 class="m-0 fw-bold">
            <i class="fa-solid fa-bus-simple me-2"></i> SMART PASS
        </h5>
        <p class="small opacity-50 m-0">OFFICIAL STUDENT TRANSPORT CARD</p>
    </div>

    <div class="pass-content text-center">

        <div class="qr-holder">
            <img src="<?php echo $qrCodeURL; ?>" alt="QR Code Verification">
        </div>

        <div class="text-start mt-3">

            <div class="label">Passenger Name</div>
            <div class="value text-primary"><?php echo htmlspecialchars($name); ?></div>

            <div class="label">Institution</div>
            <div class="value"><?php echo htmlspecialchars($pass['college']); ?></div>

            <div class="route-info">

                <div class="text-center" style="flex:1">
                    <div class="label">From</div>
                    <div class="value mb-0" style="font-size:14px;">
                        <?php echo htmlspecialchars($pass['route_from']); ?>
                    </div>
                </div>

                <div style="flex:0.5" class="text-center">
                    <i class="fa-solid fa-arrow-right text-muted"></i>
                </div>

                <div class="text-center" style="flex:1">
                    <div class="label">To</div>
                    <div class="value mb-0" style="font-size:14px;">
                        <?php echo htmlspecialchars($pass['route_to']); ?>
                    </div>
                </div>

            </div>

            <div class="d-flex justify-content-between align-items-center">

                <div>
                    <div class="label">Pass ID</div>
                    <div class="value mb-0">
                        #SP-<?php echo str_pad($pass['id'], 5, '0', STR_PAD_LEFT); ?>
                    </div>
                </div>

                <div class="status-badge">
                    <i class="fa-solid fa-circle-check me-1"></i> VERIFIED ACTIVE
                </div>

            </div>

        </div>

        <div class="no-print mt-4 pt-3 border-top">

            <button onclick="window.print()" class="btn btn-dark w-100 rounded-pill fw-bold py-2 mb-2">
                <i class="fa-solid fa-print me-2"></i> Print / Save as PDF
            </button>

            <a href="dashboard.php" class="btn btn-link btn-sm text-muted text-decoration-none">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Dashboard
            </a>

        </div>

    </div>

</div>

</body>
</html>
