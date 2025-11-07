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

// Normalize image path for display - uses the path from database as-is
function normalize_image_path($image_path) {
    if (empty($image_path)) {
        return '';
    }
    
    $image_path = trim($image_path);
    
    // If it's already a full URL (http/https), use as-is
    if (substr($image_path, 0, 4) === 'http') {
        return $image_path;
    }
    
    // Get the project root path - works for both local and live server
    $script_name = $_SERVER['SCRIPT_NAME'];
    $script_dir = dirname($_SERVER['SCRIPT_FILENAME']);
    $doc_root = $_SERVER['DOCUMENT_ROOT'];
    
    // Find project root by looking for index.php
    $project_root = '';
    $current_dir = $script_dir;
    while ($current_dir !== $doc_root && $current_dir !== dirname($current_dir)) {
        if (file_exists($current_dir . '/index.php')) {
            $project_root = str_replace($doc_root, '', $current_dir);
            break;
        }
        $current_dir = dirname($current_dir);
    }
    
    $base_path = rtrim($project_root, '/') ?: '';
    
    // Use the path from database as-is - just prepend project root
    // Database path format: /uploads/u{userId}/p{productId}/image.jpg or uploads/u{userId}/p{productId}/image.jpg
    
    // If path starts with /, prepend project root
    if (substr($image_path, 0, 1) === '/') {
        return $base_path . $image_path;
    }
    
    // If path doesn't start with /, prepend project root and add /
    return $base_path . '/' . $image_path;
}
?>
