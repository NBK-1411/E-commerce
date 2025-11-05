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

// Get the absolute file system path to the uploads folder
function get_uploads_path() {
    return __DIR__ . '/../uploads';
}

// Get the web-accessible URL path for uploads folder
// This ensures uploads paths work correctly on both local and live servers
function get_uploads_url($image_path = '') {
    // If image_path is empty, return base uploads URL
    if (empty($image_path)) {
        $image_path = '';
    } else {
        $image_path = ltrim($image_path, '/');
    }
    
    // Get the base path of the project
    $script_name = $_SERVER['SCRIPT_NAME'];
    $base_path = dirname($script_name);
    
    // If we're at root level, get the project folder name
    if ($base_path === '/' || $base_path === '.' || empty($base_path)) {
        $real_script_dir = dirname($_SERVER['SCRIPT_FILENAME']);
        $real_doc_root = $_SERVER['DOCUMENT_ROOT'];
        $relative_path = str_replace($real_doc_root, '', $real_script_dir);
        $base_path = rtrim($relative_path, '/') ?: '';
    } else {
        $base_path = rtrim($base_path, '/') ?: '';
    }
    
    // If image_path starts with 'uploads/', remove it since we'll add it
    if (substr($image_path, 0, 8) === 'uploads/') {
        $image_path = substr($image_path, 8);
    }
    
    // Build the URL path
    $url_path = $base_path . '/uploads';
    if (!empty($image_path)) {
        $url_path .= '/' . $image_path;
    }
    
    return $url_path;
}

// Normalize image path for display - handles both /uploads/ paths and other paths
function normalize_image_path($image_path) {
    if (empty($image_path)) {
        return '';
    }
    
    $image_path = trim($image_path);
    
    // If it's already a full URL (http/https), use as-is
    if (substr($image_path, 0, 4) === 'http') {
        return $image_path;
    }
    
    // If path starts with /uploads/, use get_uploads_url to normalize it
    if (substr($image_path, 0, 9) === '/uploads/') {
        $relative_path = substr($image_path, 9); // Remove /uploads/ prefix
        return get_uploads_url($relative_path);
    }
    
    // For other paths starting with /, prepend base path
    if (substr($image_path, 0, 1) === '/') {
        $script_name = $_SERVER['SCRIPT_NAME'];
        $base_path = dirname($script_name);
        if ($base_path === '/' || $base_path === '.' || empty($base_path)) {
            $real_script_dir = dirname($_SERVER['SCRIPT_FILENAME']);
            $real_doc_root = $_SERVER['DOCUMENT_ROOT'];
            $relative_path = str_replace($real_doc_root, '', $real_script_dir);
            $base_path = rtrim($relative_path, '/') ?: '';
        } else {
            $base_path = rtrim($base_path, '/') ?: '';
        }
        return $base_path . $image_path;
    }
    
    // For relative paths, return as-is (caller should handle context)
    return $image_path;
}
?>
