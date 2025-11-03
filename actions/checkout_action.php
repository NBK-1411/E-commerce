<?php
require_once __DIR__ . '/../settings/db_cred.php';
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/cart_controller.php';
require_once __DIR__ . '/../controllers/order_controller.php';

header('Content-Type: application/json');
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(false, 'Invalid request method');
}

$customer_id = get_current_customer()['customer_id'];
$cartController = new CartController($customer_id);
$orderController = new OrderController();

$items = $cartController->getItems();
$total = $cartController->getTotal();

if (empty($items)) {
    json_response(false, 'Cart is empty');
}

// Create order
$orderResult = $orderController->create($customer_id, $total);

if (!$orderResult['success']) {
    json_response(false, 'Failed to create order');
}

$order_id = $orderResult['insert_id'];

// Add items to orderdetails (matching dbforlab.sql schema)
foreach ($items as $item) {
    // Use product_id (from products table) and qty (not quantity)
    $product_id = $item['perfume_id'] ?? $item['product_id']; // Support both naming conventions
    $orderController->addItem($order_id, $product_id, $item['quantity']);
}

// Create payment record (matching dbforlab.sql schema)
$paymentResult = $orderController->createPayment($customer_id, $order_id, $total);

// Clear cart
$cartController->clearCart();

json_response(true, 'Order placed successfully', ['order_id' => $order_id]);
?>
