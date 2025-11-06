<?php
/**
 * Lab requirement: add_product_action.php
 * Receives data from product creation form, invokes controller, returns message
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

$user = get_current_customer();
$user_id = $user['customer_id'] ?? null;

if (!$user_id) {
    json_response(false, 'User not logged in');
}

// Lab requirement: Collect all product fields
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
$image_url = trim($_POST['image_url'] ?? ''); // Alternative image URL
$keyword = sanitize($_POST['keyword'] ?? '');
$notes = sanitize($_POST['notes'] ?? '');
$badge = sanitize($_POST['badge'] ?? '');

// Use image URL if provided, otherwise use uploaded image path
if (!empty($image_url)) {
    $image = $image_url;
} else if (empty($image)) {
    // If no image uploaded and no URL provided, set default
    $image = '';
}

try {
    $controller = new PerfumeController();

    // Lab requirement: Use add_product_ctr method
    $result = $controller->add_product_ctr([
        'name' => $name,
        'brand_id' => $brand_id,
        'category_ids' => $category_ids, // Now supports multiple categories
        'price' => $price,
        'description' => $description,
        'image' => $image,
        'keyword' => $keyword,
        'notes' => $notes,
        'badge' => $badge
    ]);

    // Clear output buffer before sending JSON response
    ob_end_clean();
    json_response($result['success'], $result['message'], $result['data'] ?? []);
} catch (Exception $e) {
    // Clear output buffer and send error response
    ob_end_clean();
    error_log("Error in add_product_action.php: " . $e->getMessage());
    json_response(false, 'An error occurred: ' . $e->getMessage(), []);
} catch (Error $e) {
    // Clear output buffer and send error response for fatal errors
    ob_end_clean();
    error_log("Fatal error in add_product_action.php: " . $e->getMessage());
    json_response(false, 'A fatal error occurred: ' . $e->getMessage(), []);
}
?>

