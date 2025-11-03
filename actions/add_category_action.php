<?php
require_once __DIR__ . '/../settings/db_cred.php';
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/category_controller.php';

header('Content-Type: application/json');
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(false, 'Invalid request method');
}

// Lab requirement: Track which user created the category
$user = get_current_customer();
$user_id = $user['customer_id'] ?? null;

if (!$user_id) {
    json_response(false, 'User not logged in');
}

$name = sanitize($_POST['name'] ?? '');
$controller = new CategoryController();

// Use the Lab-required method add_category_ctr
$result = $controller->add_category_ctr(['name' => $name, 'user_id' => $user_id]);

json_response($result['success'], $result['message'], $result['data'] ?? []);
?>
