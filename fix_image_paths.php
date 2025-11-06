<?php
/**
 * Script to fix image paths in the database
 * This will check if files exist and update paths if needed
 */

require_once __DIR__ . '/settings/db_cred.php';
require_once __DIR__ . '/settings/db_class.php';
require_once __DIR__ . '/settings/core.php';

header('Content-Type: text/html; charset=utf-8');

$db = new Database();
$db->connect();

// Get all products with image paths
$query = "SELECT product_id, product_title, product_image FROM products WHERE product_image IS NOT NULL AND product_image != ''";
$products = $db->read($query);

echo "<h1>Fix Image Paths</h1>";
echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
echo "<tr><th>Product ID</th><th>Product Name</th><th>Current Path</th><th>File Exists?</th><th>Action</th></tr>";

$uploads_path = get_uploads_path();

foreach ($products as $product) {
    $product_id = $product['product_id'];
    $product_name = $product['product_title'];
    $current_path = $product['product_image'];
    
    // Check if file exists at current path
    $file_exists = false;
    $correct_path = $current_path;
    
    if (!empty($current_path)) {
        // Remove /uploads/ prefix if present
        $relative_path = $current_path;
        if (substr($relative_path, 0, 9) === '/uploads/') {
            $relative_path = substr($relative_path, 9);
        }
        $full_path = $uploads_path . '/' . $relative_path;
        $file_exists = file_exists($full_path);
        
        // If file doesn't exist, try to find it
        if (!$file_exists) {
            // Try to find the file by searching in uploads folder
            $filename = basename($current_path);
            $search_pattern = $uploads_path . '/*/temp/' . $filename;
            $found_files = glob($search_pattern);
            
            if (empty($found_files)) {
                // Try without temp folder
                $search_pattern = $uploads_path . '/*/' . $filename;
                $found_files = glob($search_pattern);
            }
            
            if (!empty($found_files)) {
                // Found the file, update the path
                $found_file = $found_files[0];
                $relative_found = str_replace($uploads_path . '/', '', $found_file);
                $correct_path = '/uploads/' . $relative_found;
                
                // Update database
                $update_query = "UPDATE products SET product_image = ? WHERE product_id = ?";
                $db->write($update_query, 'si', [$correct_path, $product_id]);
                
                echo "<tr style='background-color: #d4edda;'>";
                echo "<td>" . htmlspecialchars($product_id) . "</td>";
                echo "<td>" . htmlspecialchars($product_name) . "</td>";
                echo "<td>" . htmlspecialchars($current_path) . "</td>";
                echo "<td>NO (found and fixed)</td>";
                echo "<td>Updated to: " . htmlspecialchars($correct_path) . "</td>";
                echo "</tr>";
                continue;
            }
        }
    }
    
    echo "<tr style='" . ($file_exists ? "background-color: #d4edda;" : "background-color: #f8d7da;") . "'>";
    echo "<td>" . htmlspecialchars($product_id) . "</td>";
    echo "<td>" . htmlspecialchars($product_name) . "</td>";
    echo "<td>" . htmlspecialchars($current_path) . "</td>";
    echo "<td>" . ($file_exists ? 'YES' : 'NO') . "</td>";
    echo "<td>" . ($file_exists ? 'OK' : 'File not found') . "</td>";
    echo "</tr>";
}

echo "</table>";

echo "<h2>Summary</h2>";
echo "<p>Check the table above. Files that were found and fixed are highlighted in green.</p>";
echo "<p>Files that still don't exist need to be re-uploaded.</p>";
?>

