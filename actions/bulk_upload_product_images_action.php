<?php
/**
 * Bulk image upload handler - uploads to remote server
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
    
    // Remote upload server URL
    $remote_upload_url = 'http://169.239.251.102:442/~nana.hayford/upload.php';
    
    // Process each uploaded file and send to remote server
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
        
        // Upload file to remote server using cURL
        $ch = curl_init();
        
        // Create CURLFile object for the file
        $cfile = new CURLFile($file_tmp, $mime_type, $file_name);
        
        // Prepare POST data
        $post_data = [
            'file' => $cfile,
            'user_id' => $user_id
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
            $results[] = [
                'filename' => $file_name,
                'success' => false,
                'error' => 'Failed to upload to remote server: ' . ($curl_error ?: 'HTTP ' . $http_code)
            ];
            $error_count++;
            continue;
        }
        
        // Generate unique filename
        $extension = pathinfo($file_name, PATHINFO_EXTENSION);
        $base_name = pathinfo($file_name, PATHINFO_FILENAME);
        $base_name = preg_replace('/[^a-zA-Z0-9_-]/', '', $base_name);
        $unique_filename = $base_name . '_' . time() . '_' . uniqid() . '.' . $extension;
        
        // Store path in old format for compatibility: uploads/u{user_id}/temp/filename.jpg
        // Even though file is on remote server, we use this path format for display logic
        $db_path = 'uploads/u' . $user_id . '/temp/' . $unique_filename;
        
        $results[] = [
            'filename' => $file_name,
            'success' => true,
            'path' => $db_path,
            'saved_as' => $unique_filename,
            'remote_url' => 'http://169.239.251.102:442/~nana.hayford/uploads/' . $unique_filename
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

