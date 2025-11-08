<?php
/**
 * Upload product images to remote server
 * Remote URL: http://169.239.251.102:442/~nana.hayford/upload.php
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
    
    // Generate unique filename for remote upload
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
    
    // Upload to remote server using cURL
    $remote_upload_url = 'http://169.239.251.102:442/~nana.hayford/upload.php';
    
    $ch = curl_init();
    
    // Create CURLFile object for the file
    $cfile = new CURLFile($file['tmp_name'], $mime_type, $file['name']);
    
    // Prepare POST data
    $post_data = [
        'file' => $cfile,
        'user_id' => $user_id,
        'product_id' => $product_id
    ];
    
    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $remote_upload_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    // Execute the request
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);
    
    // Check if upload was successful
    if ($http_code !== 200 || $curl_error) {
        json_response(false, 'Failed to upload to remote server: ' . ($curl_error ?: 'HTTP ' . $http_code), []);
        exit;
    }
    
    // Store path in old format for compatibility: uploads/u{user_id}/p{product_id}/filename.jpg
    // Even though file is on remote server, we use this path format for display logic
    $db_path = 'uploads/u' . $user_id;
    if ($product_id > 0) {
        $db_path .= '/p' . $product_id;
    } else {
        $db_path .= '/temp';
    }
    $db_path .= '/' . $filename;
    
    json_response(true, 'Image uploaded successfully to remote server', [
        'path' => $db_path,
        'filename' => $filename,
        'remote_url' => 'http://169.239.251.102:442/~nana.hayford/uploads/' . $filename
    ]);
    
} catch (Exception $e) {
    error_log("Error in upload_product_image_action.php: " . $e->getMessage());
    json_response(false, 'Error uploading image: ' . $e->getMessage(), []);
} catch (Error $e) {
    error_log("Fatal error in upload_product_image_action.php: " . $e->getMessage());
    json_response(false, 'Fatal error: ' . $e->getMessage(), []);
}
?>

