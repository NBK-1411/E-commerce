<?php
/**
 * Lab requirement: Upload product images to uploads/ folder
 * Path structure: uploads/u{user_id}/p{product_id}/image_name.png
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);
ob_start();

require_once __DIR__ . '/../settings/db_cred.php';
require_once __DIR__ . '/../settings/core.php';

ob_end_clean();
header('Content-Type: application/json');

try {
    require_admin();
    
    $user = get_current_customer();
    $user_id = $user['customer_id'] ?? null;
    $product_id = (int)($_POST['product_id'] ?? 0);
    
    if (!$user_id) {
        json_response(false, 'User not logged in', []);
        exit;
    }
    
    // Check if file was uploaded
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        json_response(false, 'No image file uploaded or upload error', []);
        exit;
    }
    
    $file = $_FILES['image'];
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    // Validate file type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mime_type, $allowed_types)) {
        json_response(false, 'Invalid file type. Only JPEG, PNG, GIF, and WebP images are allowed', []);
        exit;
    }
    
    // Validate file size
    if ($file['size'] > $max_size) {
        json_response(false, 'File size exceeds 5MB limit', []);
        exit;
    }
    
    // Lab requirement: Verify uploads folder exists (created by server admin)
    // Use helper function to get uploads path - works on both local and live servers
    // (core.php is already required at the top of this file)
    $uploads_base = get_uploads_path();
    if (!is_dir($uploads_base)) {
        json_response(false, 'Uploads folder not found. Please contact server administrator', []);
        exit;
    }
    
    // Verify uploads base directory is writable
    if (!is_writable($uploads_base)) {
        error_log("Uploads base directory is not writable: {$uploads_base}");
        json_response(false, 'Uploads folder is not writable. Please check server permissions.', []);
        exit;
    }
    
    // Lab requirement: Create user directory structure inside uploads/
    // Structure: uploads/u{user_id}/p{product_id}/image_name.png
    $user_dir = $uploads_base . '/u' . $user_id;
    if (!is_dir($user_dir)) {
        if (!@mkdir($user_dir, 0755, true)) {
            $error = error_get_last();
            $errorMsg = $error ? $error['message'] : 'Unknown error';
            error_log("Failed to create user directory: {$user_dir}. Error: {$errorMsg}");
            json_response(false, 'Failed to create user directory. Please check server permissions.', []);
            exit;
        }
        // Verify directory was created
        if (!is_dir($user_dir)) {
            error_log("User directory still does not exist after mkdir: {$user_dir}");
            json_response(false, 'Failed to create user directory. Directory creation failed.', []);
            exit;
        }
    }
    
    // Verify write permissions on user directory
    if (!is_writable($user_dir)) {
        error_log("User directory is not writable: {$user_dir}");
        json_response(false, 'User directory is not writable. Please check server permissions.', []);
        exit;
    }
    
    // If product_id is provided, create product subdirectory
    if ($product_id > 0) {
        $product_dir = $user_dir . '/p' . $product_id;
        if (!is_dir($product_dir)) {
            if (!@mkdir($product_dir, 0755, true)) {
                $error = error_get_last();
                $errorMsg = $error ? $error['message'] : 'Unknown error';
                error_log("Failed to create product directory: {$product_dir}. Error: {$errorMsg}");
                json_response(false, 'Failed to create product directory. Please check server permissions.', []);
                exit;
            }
        }
        $target_dir = $product_dir;
    } else {
        // If no product_id yet (during creation), use temp directory
        $temp_dir = $user_dir . '/temp';
        if (!is_dir($temp_dir)) {
            if (!@mkdir($temp_dir, 0755, true)) {
                $error = error_get_last();
                $errorMsg = $error ? $error['message'] : 'Unknown error';
                error_log("Failed to create temp directory: {$temp_dir}. Error: {$errorMsg}");
                json_response(false, 'Failed to create temp directory. Please check server permissions.', []);
                exit;
            }
        }
        $target_dir = $temp_dir;
    }
    
    // Verify target directory is writable
    if (!is_writable($target_dir)) {
        error_log("Target directory is not writable: {$target_dir}");
        json_response(false, 'Target directory is not writable. Please check server permissions.', []);
        exit;
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $base_name = pathinfo($file['name'], PATHINFO_FILENAME);
    $base_name = preg_replace('/[^a-zA-Z0-9_-]/', '', $base_name); // Sanitize filename
    $filename = $base_name . '_' . time() . '.' . $extension;
    $file_path = $target_dir . '/' . $filename;
    
    // Verify the path is within uploads/ directory (security check)
    $real_uploads = realpath($uploads_base);
    $real_file = realpath(dirname($file_path));
    
    if (!$real_file || strpos($real_file, $real_uploads) !== 0) {
        json_response(false, 'Invalid upload path. File must be stored inside uploads/ directory', []);
        exit;
    }
    
    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $file_path)) {
        json_response(false, 'Failed to save uploaded file', []);
        exit;
    }
    
    // Return relative path from project root
    $relative_path = '/uploads/u' . $user_id;
    if ($product_id > 0) {
        $relative_path .= '/p' . $product_id;
    } else {
        $relative_path .= '/temp';
    }
    $relative_path .= '/' . $filename;
    
    json_response(true, 'Image uploaded successfully', [
        'path' => $relative_path,
        'filename' => $filename,
        'full_path' => $file_path
    ]);
    
} catch (Exception $e) {
    error_log("Error in upload_product_image_action.php: " . $e->getMessage());
    json_response(false, 'Error uploading image: ' . $e->getMessage(), []);
} catch (Error $e) {
    error_log("Fatal error in upload_product_image_action.php: " . $e->getMessage());
    json_response(false, 'Fatal error: ' . $e->getMessage(), []);
}
?>

