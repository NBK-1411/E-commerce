<?php
// Prevent any output
@ob_end_clean();

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Unset all session variables
$_SESSION = array();

// Destroy the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-42000, '/');
    setcookie(session_name(), '', time()-42000);
}

// Destroy the session
if (session_status() === PHP_SESSION_ACTIVE) {
    session_destroy();
}

// Redirect to home page - use relative path from actions folder
header('Location: ../index.php');
exit();
?>
