<?php
/**
 * Lab requirement: update_product_action.php
 * Receives data from product update form, invokes controller, returns message
 */

// Start output buffering to prevent any unwanted output
ob_start();

require_once __DIR__ . '/../settings/db_cred.php';
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/perfume_controller.php';

// Clear any output that might have been generated
ob_clean();

header('Content-Type: application/json');
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(false, 'Invalid request method');
}

$id = (int)($_POST['id'] ?? 0);
$name = sanitize($_POST['name'] ?? '');
$brand_id = (int)($_POST['brand_id'] ?? 0);

// Handle multiple category_ids - FormData sends as category_ids[]
// PHP automatically parses category_ids[] into $_POST['category_ids'] as an array
$category_ids = [];
if (isset($_POST['category_ids'])) {
    // If it's already an array (from FormData with [] notation)
    if (is_array($_POST['category_ids'])) {
        $category_ids = $_POST['category_ids'];
    } 
    // If it's a string (single value), convert to array
    elseif (is_string($_POST['category_ids']) && !empty($_POST['category_ids'])) {
        $category_ids = [$_POST['category_ids']];
    }
}

// Ensure all values are integers and filter out invalid values
$category_ids = array_map('intval', $category_ids);
$category_ids = array_filter($category_ids, function($id) {
    return $id > 0;
});

if (empty($category_ids)) {
    ob_end_clean();
    json_response(false, 'At least one category is required');
}

$price = (float)($_POST['price'] ?? 0);
$description = sanitize($_POST['description'] ?? '');
// Don't sanitize image paths with htmlspecialchars - just trim and validate
$image = trim($_POST['image'] ?? '');
$image_url = trim($_POST['image_url'] ?? '');
$keyword = sanitize($_POST['keyword'] ?? '');
$notes = sanitize($_POST['notes'] ?? '');
$badge = sanitize($_POST['badge'] ?? '');

if (!$id) {
    json_response(false, 'Product ID is required');
}

// Use image URL if provided, otherwise use uploaded image path
if (!empty($image_url)) {
    $image = $image_url;
}

// Get brand name from brand_id if provided
$brand_name = '';
if ($brand_id > 0) {
    require_once __DIR__ . '/../classes/brand_class.php';
    $brandClass = new Brand();
    $brand = $brandClass->getById($brand_id);
    if ($brand) {
        $brand_name = $brand['name'];
    }
}

// If no brand_id provided, use default
if (empty($brand_name)) {
    $brand_name = 'Generic';
}

try {
    $controller = new PerfumeController();
    $result = $controller->update($id, $name, $brand_name, $category_ids, $price, 0, $description, $image, $notes, $badge, $keyword);

    // Clear output buffer before sending JSON response
    ob_end_clean();
    json_response($result['success'], $result['message'], $result['data'] ?? []);
} catch (Exception $e) {
    // Clear output buffer and send error response
    ob_end_clean();
    error_log("Error in update_product_action.php: " . $e->getMessage());
    json_response(false, 'An error occurred: ' . $e->getMessage(), []);
} catch (Error $e) {
    // Clear output buffer and send error response for fatal errors
    ob_end_clean();
    error_log("Fatal error in update_product_action.php: " . $e->getMessage());
    json_response(false, 'A fatal error occurred: ' . $e->getMessage(), []);
}
?>

