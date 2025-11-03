<?php
require_once __DIR__ . '/../classes/order_class.php';

class OrderController {
    private $order;

    public function __construct() {
        $this->order = new Order();
    }

    public function create($customer_id, $total_amount) {
        if ($total_amount <= 0) {
            return ['success' => false, 'message' => 'Invalid order amount'];
        }

        return $this->order->create($customer_id, $total_amount);
    }

    public function addItem($order_id, $product_id, $quantity) {
        // Match dbforlab.sql schema: orderdetails table with product_id and qty
        return $this->order->addItem($order_id, $product_id, $quantity);
    }

    public function getByCustomer($customer_id) {
        return $this->order->getByCustomer($customer_id);
    }

    public function getAll() {
        return $this->order->getAll();
    }

    public function getById($id) {
        return $this->order->getById($id);
    }

    public function getOrderDetails($order_id) {
        return $this->order->getOrderDetails($order_id);
    }

    public function updateStatus($order_id, $status) {
        $valid_statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        if (!in_array($status, $valid_statuses)) {
            return ['success' => false, 'message' => 'Invalid status'];
        }

        return $this->order->updateStatus($order_id, $status);
    }

    public function createPayment($customer_id, $order_id, $amount, $currency = 'USD') {
        return $this->order->createPayment($customer_id, $order_id, $amount, $currency);
    }
}
?>
