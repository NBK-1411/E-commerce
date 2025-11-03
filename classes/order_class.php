<?php
require_once __DIR__ . '/../settings/db_cred.php';
require_once __DIR__ . '/../settings/db_class.php';

class Order {
    private $db;

    public function __construct() {
        $this->db = new Database();
        $this->db->connect();
    }

    // Create order matching dbforlab.sql schema: order_id, customer_id, invoice_no, order_date, order_status
    public function create($customer_id, $total_amount, $status = 'pending') {
        // Generate invoice number (using order_id as invoice_no for now, or use timestamp)
        $invoice_no = time(); // Or generate unique invoice number
        $query = "INSERT INTO orders (customer_id, invoice_no, order_date, order_status) VALUES (?, ?, CURDATE(), ?)";
        return $this->db->write($query, 'iis', [$customer_id, $invoice_no, $status]);
    }

    // Add order detail matching dbforlab.sql schema: orderdetails table with order_id, product_id, qty
    public function addItem($order_id, $product_id, $quantity) {
        $query = "INSERT INTO orderdetails (order_id, product_id, qty) VALUES (?, ?, ?)";
        return $this->db->write($query, 'iii', [$order_id, $product_id, $quantity]);
    }

    // Get orders by customer - match dbforlab.sql schema
    public function getByCustomer($customer_id) {
        $query = "SELECT order_id, customer_id, invoice_no, order_date, order_status 
                  FROM orders 
                  WHERE customer_id = ? 
                  ORDER BY order_date DESC, order_id DESC";
        return $this->db->read($query, 'i', [$customer_id]);
    }

    // Get all orders - match dbforlab.sql schema
    public function getAll() {
        $query = "SELECT o.order_id, o.customer_id, o.invoice_no, o.order_date, o.order_status,
                         c.customer_name, c.customer_email
                  FROM orders o 
                  JOIN customer c ON o.customer_id = c.customer_id 
                  ORDER BY o.order_date DESC, o.order_id DESC";
        return $this->db->read($query);
    }

    // Get order by ID - match dbforlab.sql schema
    public function getById($id) {
        $query = "SELECT order_id, customer_id, invoice_no, order_date, order_status 
                  FROM orders 
                  WHERE order_id = ?";
        $result = $this->db->read($query, 'i', [$id]);
        return $result[0] ?? null;
    }

    // Get order details (items) - match dbforlab.sql schema
    public function getOrderDetails($order_id) {
        $query = "SELECT od.order_id, od.product_id, od.qty, 
                         p.product_title, p.product_price
                  FROM orderdetails od
                  JOIN products p ON od.product_id = p.product_id
                  WHERE od.order_id = ?";
        return $this->db->read($query, 'i', [$order_id]);
    }

    // Update order status - match dbforlab.sql schema
    public function updateStatus($order_id, $status) {
        $query = "UPDATE orders SET order_status = ? WHERE order_id = ?";
        return $this->db->write($query, 'si', [$status, $order_id]);
    }

    // Create payment record - match dbforlab.sql schema: payment table
    public function createPayment($customer_id, $order_id, $amount, $currency = 'USD') {
        $query = "INSERT INTO payment (amt, customer_id, order_id, currency, payment_date) 
                  VALUES (?, ?, ?, ?, CURDATE())";
        return $this->db->write($query, 'diis', [$amount, $customer_id, $order_id, $currency]);
    }
}
?>
