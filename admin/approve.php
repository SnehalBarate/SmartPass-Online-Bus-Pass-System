<?php
//Use PHPMailer classes for automated notifications
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//Autoload Composer's PHPMailer
require '../vendor/autoload.php'; 

session_start();
require_once("../config.php");

// Admin Authentication Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { 
    exit("Unauthorized access"); 
}

if (isset($_POST['approve_btn'])) {
    $id = intval($_POST['app_id']);
    $expiry_date = $_POST['expiry_date']; // Date set by Admin

    // Step 1: Fetch Student Info for the Email
    $fetch_user = mysqli_query($conn, "SELECT u.email, u.name, a.full_name 
                                       FROM applications a 
                                       JOIN users u ON a.user_id = u.id 
                                       WHERE a.id = $id LIMIT 1");
    $user_data = mysqli_fetch_assoc($fetch_user);
    
    $student_email = $user_data['email'];
    $student_name  = $user_data['name'];

    // Step 2: Update Application Status in DB
    $update_query = "UPDATE applications SET status = 'Approved', expiry_date = '$expiry_date' WHERE id = $id";
    
    if (mysqli_query($conn, $update_query)) {
        
        // Step 3: Trigger Automated Email Notification
        $mail = new PHPMailer(true);

        try {
            // SMTP Server Settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; // Use Gmail SMTP
            $mail->SMTPAuth   = true;
            $mail->Username   = 'your-admin-email@gmail.com'; // Your Gmail
            $mail->Password   = 'your-app-password';         // Gmail App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Email Metadata
            $mail->setFrom('no-reply@smartpass.com', 'SmartPass System');
            $mail->addAddress($student_email, $student_name);

            // Email Content Template
            $mail->isHTML(true);
            $mail->Subject = 'Your Bus Pass is Approved! - SmartPass';
            $mail->Body    = "
                <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #eee;'>
                    <h2 style='color: #2563eb;'>Hello, $student_name!</h2>
                    <p>Good news! Your bus pass application has been <strong>Approved</strong>.</p>
                    <p><strong>Validity:</strong> Your pass is valid until: <span style='color: #dc3545;'>$expiry_date</span></p>
                    <hr>
                    <p>You can now log in to your dashboard to view and download your <strong>Digital QR Pass</strong>.</p>
                    <p>Thank you for using SmartPass.</p>
                </div>
            ";

            $mail->send();
            header("Location: dashboard.php?status=approved_and_emailed");
        } catch (Exception $e) {
            // If email fails, the pass is still approved in DB
            header("Location: dashboard.php?status=approved_but_email_failed");
        }
    } else {
        header("Location: dashboard.php?status=error");
    }
}
?>