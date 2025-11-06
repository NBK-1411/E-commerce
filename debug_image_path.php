<?php
/**
 * Debug script to check image paths
 * Access this file directly to see what paths are stored and how they're normalized
 */

require_once __DIR__ . '/settings/db_cred.php';
require_once __DIR__ . '/settings/core.php';
require_once __DIR__ . '/controllers/perfume_controller.php';

header('Content-Type: text/html; charset=utf-8');

$controller = new PerfumeController();
$products = $controller->getAll();

echo "<h1>Image Path Debug</h1>";
echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
echo "<tr><th>Product ID</th><th>Product Name</th><th>Raw Image Path (from DB)</th><th>Normalized Path</th><th>File Exists?</th></tr>";

foreach ($products as $product) {
    $raw_path = $product['image'] ?? '';
    $normalized = normalize_image_path($raw_path);
    
    // Check if file exists
    $file_exists = false;
    if (!empty($raw_path)) {
        // Try to check if file exists
        $uploads_path = get_uploads_path();
        // Remove /uploads/ prefix if present
        $relative_path = $raw_path;
        if (substr($relative_path, 0, 9) === '/uploads/') {
            $relative_path = substr($relative_path, 9);
        }
        $full_path = $uploads_path . '/' . $relative_path;
        $file_exists = file_exists($full_path);
    }
    
    echo "<tr>";
    echo "<td>" . htmlspecialchars($product['id'] ?? 'N/A') . "</td>";
    echo "<td>" . htmlspecialchars($product['name'] ?? 'N/A') . "</td>";
    echo "<td>" . htmlspecialchars($raw_path) . "</td>";
    echo "<td>" . htmlspecialchars($normalized) . "</td>";
    echo "<td>" . ($file_exists ? 'YES' : 'NO') . "</td>";
    echo "</tr>";
}

echo "</table>";

echo "<h2>Server Info</h2>";
echo "<pre>";
echo "SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'N/A') . "\n";
echo "SCRIPT_FILENAME: " . ($_SERVER['SCRIPT_FILENAME'] ?? 'N/A') . "\n";
echo "DOCUMENT_ROOT: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'N/A') . "\n";
echo "Uploads Path: " . get_uploads_path() . "\n";
echo "</pre>";
?>

