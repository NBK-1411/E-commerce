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
        $project_root = get_project_root_web_path();
        $login_path = rtrim($project_root, '/') . '/public/login.php';
        header('Location: ' . $login_path);
        exit;
    }
}

// Require admin
function require_admin() {
    if (!is_admin()) {
        $project_root = get_project_root_web_path();
        $index_path = rtrim($project_root, '/') . '/index.php';
        header('Location: ' . $index_path);
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

// Get the absolute file system path to the uploads folder
function get_uploads_path() {
    return __DIR__ . '/../uploads';
}

// Build the base URL (protocol + host)
function get_base_url() {
    $is_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['SERVER_PORT'] ?? '') == 443);
    $scheme = $is_https ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
    return $scheme . '://' . $host;
}

// Determine the project root path relative to the web server document root
function get_project_root_web_path() {
    static $cached = null;
    if ($cached !== null) {
        return $cached;
    }

    $doc_root = isset($_SERVER['DOCUMENT_ROOT']) ? realpath($_SERVER['DOCUMENT_ROOT']) : '';
    $script_dir = isset($_SERVER['SCRIPT_FILENAME']) ? realpath(dirname($_SERVER['SCRIPT_FILENAME'])) : '';

    $project_root = '';
    if ($doc_root && $script_dir) {
        $current_dir = $script_dir;
        while ($current_dir && $current_dir !== dirname($current_dir)) {
            if (file_exists($current_dir . DIRECTORY_SEPARATOR . 'index.php')) {
                $project_root = str_replace($doc_root, '', $current_dir);
                break;
            }
            if ($current_dir === $doc_root) {
                break;
            }
            $current_dir = dirname($current_dir);
        }
    }

    $project_root = str_replace('\\', '/', $project_root);
    $project_root = '/' . ltrim($project_root, '/');
    if ($project_root === '/') {
        $project_root = '';
    }

    $cached = $project_root;
    return $cached;
}

// Get the web-accessible URL path for uploads folder
function get_uploads_url($image_path = '') {
    $base_url = get_base_url();
    $project_root = get_project_root_web_path();
    $uploads_base = rtrim($base_url . $project_root, '/') . '/uploads';

    $image_path = trim($image_path);
    if ($image_path === '') {
        return $uploads_base;
    }

    $relative = ltrim($image_path, '/');
    if (strpos($relative, 'uploads/') === 0) {
        $relative = substr($relative, 8);
    }

    return $uploads_base . '/' . $relative;
}

// Normalize image path for display - maps uploads to remote server
function normalize_image_path($image_path) {
    if (empty($image_path)) {
        return '';
    }

    $image_path = trim($image_path);

    // If it's already a full URL (http/https), use as-is
    if (preg_match('/^https?:\/\//i', $image_path)) {
        return $image_path;
    }

    // Map uploads paths - just return the path as-is starting from uploads/
    // Database: uploads/u3/p1/image.jpg
    // Output: uploads/u3/p1/image.jpg (server handles the rest)
    if (strpos($image_path, '/uploads/') === 0 || strpos($image_path, 'uploads/') === 0) {
        return ltrim($image_path, '/');
    }

    $base_url = get_base_url();
    $project_root = get_project_root_web_path();
    $project_base_url = rtrim($base_url . $project_root, '/');

    // Absolute path within the project
    if (strpos($image_path, '/') === 0) {
        return $project_base_url . $image_path;
    }

    // Relative path within the project
    return $project_base_url . '/' . ltrim($image_path, '/');
}
?>
