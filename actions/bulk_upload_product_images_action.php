<?php
/**
 * Bulk image upload handler - handles multiple files in one request
 * Path structure: uploads/u{user_id}/temp/image_name.png
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
    
    if (!$user_id) {
        json_response(false, 'User not logged in', []);
        exit;
    }
    
    // Check if files were uploaded
    if (!isset($_FILES['images']) || empty($_FILES['images']['name'][0])) {
        json_response(false, 'No image files uploaded', []);
        exit;
    }
    
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    // Get uploads path
    $uploads_base = get_uploads_path();
    if (!is_dir($uploads_base)) {
        json_response(false, 'Uploads folder not found', []);
        exit;
    }
    
    if (!is_writable($uploads_base)) {
        json_response(false, 'Uploads folder is not writable', []);
        exit;
    }
    
    // Create user directory
    $user_dir = $uploads_base . '/u' . $user_id;
    if (!is_dir($user_dir)) {
        if (!@mkdir($user_dir, 0755, true)) {
            json_response(false, 'Failed to create user directory', []);
            exit;
        }
    }
    
    // Create temp directory for bulk uploads
    $temp_dir = $user_dir . '/temp';
    if (!is_dir($temp_dir)) {
        if (!@mkdir($temp_dir, 0755, true)) {
            json_response(false, 'Failed to create temp directory', []);
            exit;
        }
    }
    
    if (!is_writable($temp_dir)) {
        json_response(false, 'Temp directory is not writable', []);
        exit;
    }
    
    // Process each uploaded file
    $results = [];
    $success_count = 0;
    $error_count = 0;
    
    $file_count = count($_FILES['images']['name']);
    
    for ($i = 0; $i < $file_count; $i++) {
        $file_name = $_FILES['images']['name'][$i];
        $file_tmp = $_FILES['images']['tmp_name'][$i];
        $file_size = $_FILES['images']['size'][$i];
        $file_error = $_FILES['images']['error'][$i];
        
        // Check for upload errors
        if ($file_error !== UPLOAD_ERR_OK) {
            $results[] = [
                'filename' => $file_name,
                'success' => false,
                'error' => 'Upload error code: ' . $file_error
            ];
            $error_count++;
            continue;
        }
        
        // Validate file type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file_tmp);
        finfo_close($finfo);
        
        if (!in_array($mime_type, $allowed_types)) {
            $results[] = [
                'filename' => $file_name,
                'success' => false,
                'error' => 'Invalid file type. Only JPEG, PNG, GIF, and WebP allowed'
            ];
            $error_count++;
            continue;
        }
        
        // Validate file size
        if ($file_size > $max_size) {
            $results[] = [
                'filename' => $file_name,
                'success' => false,
                'error' => 'File size exceeds 5MB limit'
            ];
            $error_count++;
            continue;
        }
        
        // Generate unique filename
        $extension = pathinfo($file_name, PATHINFO_EXTENSION);
        $base_name = pathinfo($file_name, PATHINFO_FILENAME);
        $base_name = preg_replace('/[^a-zA-Z0-9_-]/', '', $base_name);
        $unique_filename = $base_name . '_' . time() . '_' . $i . '.' . $extension;
        $file_path = $temp_dir . '/' . $unique_filename;
        
        // Verify path is within uploads directory (security)
        $real_uploads = realpath($uploads_base);
        $real_temp = realpath($temp_dir);
        
        if (!$real_temp || strpos($real_temp, $real_uploads) !== 0) {
            $results[] = [
                'filename' => $file_name,
                'success' => false,
                'error' => 'Invalid upload path'
            ];
            $error_count++;
            continue;
        }
        
        // Move uploaded file
        if (!move_uploaded_file($file_tmp, $file_path)) {
            $results[] = [
                'filename' => $file_name,
                'success' => false,
                'error' => 'Failed to save file'
            ];
            $error_count++;
            continue;
        }
        
        // Build database path
        $db_path = 'uploads/u' . $user_id . '/temp/' . $unique_filename;
        
        $results[] = [
            'filename' => $file_name,
            'success' => true,
            'path' => $db_path,
            'saved_as' => $unique_filename
        ];
        $success_count++;
    }
    
    // Return results
    json_response(true, "$success_count files uploaded successfully, $error_count failed", [
        'total' => $file_count,
        'success_count' => $success_count,
        'error_count' => $error_count,
        'results' => $results
    ]);
    
} catch (Exception $e) {
    error_log("Error in bulk_upload_product_images_action.php: " . $e->getMessage());
    json_response(false, 'Error uploading images: ' . $e->getMessage(), []);
} catch (Error $e) {
    error_log("Fatal error in bulk_upload_product_images_action.php: " . $e->getMessage());
    json_response(false, 'Fatal error: ' . $e->getMessage(), []);
}
?>

