<?php
require_once __DIR__ . '/../settings/db_cred.php';
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/brand_controller.php';

header('Content-Type: application/json');
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(false, 'Invalid request method');
}

// Lab requirement: Track which user created the brand
$user = get_current_customer();
$user_id = $user['customer_id'] ?? null;

if (!$user_id) {
    json_response(false, 'User not logged in');
}

$name = sanitize($_POST['name'] ?? '');

// Category is no longer required - new brands are available for all categories
$controller = new BrandController();

// Use the Lab-required method add_brand_ctr
// Category_id is now ignored and set to NULL internally so brand is available for all categories
$result = $controller->add_brand_ctr(['name' => $name, 'user_id' => $user_id]);

json_response($result['success'], $result['message'], $result['data'] ?? []);
?>

