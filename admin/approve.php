<?php

// ============================================================
// PHPMailer Classes
// ============================================================

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


// ============================================================
// PHPMailer Autoload
// ============================================================

require '../vendor/autoload.php';


// ============================================================
// Session & Database
// ============================================================

session_start();
require_once "../config.php";


// ============================================================
// ADMIN AUTHENTICATION CHECK
// ============================================================

if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'admin'
) {
    header("Location: ../admin_login.php");
    exit;
}


// ============================================================
// APPROVE APPLICATION
// ============================================================

if (isset($_POST['approve_btn'])) {

    // Get Application ID
    $id = filter_input(INPUT_POST, 'app_id', FILTER_VALIDATE_INT);

    // Get Expiry Date
    $expiry_date = trim($_POST['expiry_date'] ?? '');


    // --------------------------------------------------------
    // Validate Application ID
    // --------------------------------------------------------

    if (!$id || $id <= 0) {
        header("Location: dashboard.php?status=invalid_application");
        exit;
    }


    // --------------------------------------------------------
    // Validate Expiry Date
    // --------------------------------------------------------

    if (empty($expiry_date)) {
        header("Location: dashboard.php?status=invalid_expiry");
        exit;
    }


    // ========================================================
    // STEP 1: FETCH STUDENT INFORMATION
    // ========================================================

    $fetch_stmt = mysqli_prepare(
        $conn,
        "SELECT 
            u.email,
            u.name,
            a.full_name,
            a.status
         FROM applications a
         JOIN users u ON a.user_id = u.id
         WHERE a.id = ?
         LIMIT 1"
    );

    mysqli_stmt_bind_param(
        $fetch_stmt,
        "i",
        $id
    );

    mysqli_stmt_execute($fetch_stmt);

    $fetch_result = mysqli_stmt_get_result($fetch_stmt);

    $user_data = mysqli_fetch_assoc($fetch_result);


    // --------------------------------------------------------
    // Application Not Found
    // --------------------------------------------------------

    if (!$user_data) {
        header("Location: dashboard.php?status=application_not_found");
        exit;
    }


    // --------------------------------------------------------
    // Prevent Re-approving an already approved application
    // --------------------------------------------------------

    if ($user_data['status'] !== 'Pending') {
        header("Location: dashboard.php?status=already_processed");
        exit;
    }


    $student_email = $user_data['email'];
    $student_name  = $user_data['name'];


    // ========================================================
    // STEP 2: UPDATE APPLICATION
    // ========================================================

    $update_stmt = mysqli_prepare(
        $conn,
        "UPDATE applications
         SET status = 'Approved',
             expiry_date = ?
         WHERE id = ?
         AND status = 'Pending'"
    );

    mysqli_stmt_bind_param(
        $update_stmt,
        "si",
        $expiry_date,
        $id
    );


    // ========================================================
    // STEP 3: DATABASE UPDATE
    // ========================================================

    if (mysqli_stmt_execute($update_stmt)) {


        // ====================================================
        // STEP 4: SEND EMAIL NOTIFICATION
        // ====================================================

        $mail = new PHPMailer(true);

        try {

            // ------------------------------------------------
            // SMTP Settings
            // ------------------------------------------------

            $mail->isSMTP();

            $mail->Host = 'smtp.gmail.com';

            $mail->SMTPAuth = true;

            /*
             * Replace these with your actual Gmail
             * and Gmail App Password.
             */
            $mail->Username = 'your-admin-email@gmail.com';

            $mail->Password = 'your-app-password';

            $mail->SMTPSecure =
                PHPMailer::ENCRYPTION_STARTTLS;

            $mail->Port = 587;


            // ------------------------------------------------
            // Email Sender
            // ------------------------------------------------

            $mail->setFrom(
                'no-reply@smartpass.com',
                'SmartPass System'
            );


            // ------------------------------------------------
            // Student Email
            // ------------------------------------------------

            $mail->addAddress(
                $student_email,
                $student_name
            );


            // ------------------------------------------------
            // Email Content
            // ------------------------------------------------

            $mail->isHTML(true);

            $mail->Subject =
                'Your Bus Pass is Approved! - SmartPass';


            $mail->Body = "

                <div style='
                    font-family: Arial, sans-serif;
                    padding: 20px;
                    border: 1px solid #eee;
                    border-radius: 10px;
                '>

                    <h2 style='color:#2563eb;'>
                        Hello, " . htmlspecialchars($student_name) . "!
                    </h2>

                    <p>
                        Good news! Your bus pass application
                        has been
                        <strong>Approved</strong>.
                    </p>

                    <p>
                        <strong>Validity:</strong>
                        Your pass is valid until:
                        <span style='color:#dc3545;'>
                            " . htmlspecialchars($expiry_date) . "
                        </span>
                    </p>

                    <hr>

                    <p>
                        You can now log in to your dashboard
                        to view and download your
                        <strong>Digital QR Pass</strong>.
                    </p>

                    <p>
                        Thank you for using
                        <strong>SmartPass</strong>.
                    </p>

                </div>

            ";


            // ------------------------------------------------
            // Send Email
            // ------------------------------------------------

            $mail->send();


            // Email + Approval successful

            header(
                "Location: dashboard.php?status=approved_and_emailed"
            );

            exit;


        } catch (Exception $e) {

            /*
             * Application is already approved in database.
             * Only email sending failed.
             */

            header(
                "Location: dashboard.php?status=approved_but_email_failed"
            );

            exit;
        }


    } else {

        // Database update failed

        header(
            "Location: dashboard.php?status=error"
        );

        exit;
    }
}


// ============================================================
// If Page Is Opened Directly
// ============================================================

header("Location: dashboard.php");
exit;

?>