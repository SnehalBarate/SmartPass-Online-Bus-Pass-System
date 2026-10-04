<?php
session_start();

/* store role BEFORE destroying session */
$role = $_SESSION['role'] ?? null;

/* destroy all session data */
$_SESSION = [];

/* delete session cookie (VERY IMPORTANT) */
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

/* finally destroy session */
session_destroy();

/* role-based redirect */
if ($role === 'admin') {
    header("Location: admin_login.php");   // admin login
} else {
    header("Location: user_login.php");    // student login
}
exit;
