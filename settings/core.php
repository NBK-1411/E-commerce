<?php
session_start();

// Check if user is logged in
function is_logged_in() {
    return isset($_SESSION['customer']) && !empty($_SESSION['customer']);
}

// Check if user is admin
function is_admin() {
    return is_logged_in() && isset($_SESSION['customer']['user_role']) && $_SESSION['customer']['user_role'] == 1;
}

// Require login
function require_login() {
    if (!is_logged_in()) {
        header('Location: /public/login.php');
        exit;
    }
}

// Require admin
function require_admin() {
    if (!is_admin()) {
        header('Location: /index.php');
        exit;
    }
}

// Get current user
function get_current_customer() {
    return $_SESSION['customer'] ?? null;
}

// Sanitize input
function sanitize($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// JSON response helper
function json_response($success, $message, $data = []) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}
?>
