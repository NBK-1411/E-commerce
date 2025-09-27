<?php
// /actions/logout.php
// Destroys the session and redirects back to your app's index.php reliably.

session_start();

// Wipe session data
$_SESSION = [];

// Remove session cookie (avoids “ghost” sessions in some browsers)
if (ini_get('session.use_cookies')) {
  $p = session_get_cookie_params();
  setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}

// Destroy the session
session_destroy();

// Build a safe absolute web path to /index.php (one level up from /actions)
$base = dirname(dirname($_SERVER['SCRIPT_NAME']));   // e.g. /mvc_auth_from_user_html
if ($base === DIRECTORY_SEPARATOR) $base = '';
$target = $base . '/index.php';

// Redirect and stop script
header('Location: ' . $target);
exit;
