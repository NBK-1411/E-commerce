<?php
require_once __DIR__ . '/../settings/db_cred.php';
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/cart_controller.php';

header('Content-Type: application/json');
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(false, 'Invalid request method');
}

// Match dbforlab.sql schema: cart table uses p_id (product_id), not cart_item_id
$product_id = (int)($_POST['product_id'] ?? $_POST['cart_item_id'] ?? 0);
$quantity = (int)($_POST['quantity'] ?? 1);
$customer_id = get_current_customer()['customer_id'];

$controller = new CartController($customer_id);
$result = $controller->updateQuantity($product_id, $quantity);

json_response($result['success'], $result['message']);
?>
