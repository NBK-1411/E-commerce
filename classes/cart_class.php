<?php
require_once __DIR__ . '/../settings/db_cred.php';
require_once __DIR__ . '/../settings/db_class.php';

class Cart {
    private $db;
    private $customer_id;

    public function __construct($customer_id = null) {
        $this->db = new Database();
        $this->db->connect();
        $this->customer_id = $customer_id;
    }

    // Match dbforlab.sql schema: cart table with p_id, ip_add, c_id, qty
    public function addItem($product_id, $quantity = 1) {
        if (!$this->customer_id) {
            return ['success' => false, 'message' => 'Customer not logged in'];
        }

        // Get IP address
        $ip_add = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

        // Check if item already exists in cart
        $query = "SELECT qty FROM cart WHERE c_id = ? AND p_id = ?";
        $result = $this->db->read($query, 'ii', [$this->customer_id, $product_id]);

        if (!empty($result)) {
            $new_qty = $result[0]['qty'] + $quantity;
            $query = "UPDATE cart SET qty = ? WHERE c_id = ? AND p_id = ?";
            return $this->db->write($query, 'iii', [$new_qty, $this->customer_id, $product_id]);
        } else {
            $query = "INSERT INTO cart (p_id, ip_add, c_id, qty) VALUES (?, ?, ?, ?)";
            return $this->db->write($query, 'isii', [$product_id, $ip_add, $this->customer_id, $quantity]);
        }
    }

    public function getItems() {
        if (!$this->customer_id) {
            return [];
        }

        // Match dbforlab.sql schema: cart table with p_id, c_id, qty
        // Join with products table to get product details
        $query = "SELECT c.p_id as product_id, c.p_id as perfume_id, c.qty as quantity,
                         p.product_title as name, p.product_price as price, p.product_image as image
                  FROM cart c 
                  JOIN products p ON c.p_id = p.product_id 
                  WHERE c.c_id = ? 
                  ORDER BY c.p_id DESC";
        $result = $this->db->read($query, 'i', [$this->customer_id]);
        return is_array($result) ? $result : [];
    }

    public function removeItem($product_id) {
        // Match dbforlab.sql schema: cart table with p_id and c_id
        $query = "DELETE FROM cart WHERE p_id = ? AND c_id = ?";
        return $this->db->write($query, 'ii', [$product_id, $this->customer_id]);
    }

    public function updateQuantity($product_id, $quantity) {
        // Match dbforlab.sql schema: cart table with p_id, c_id, qty
        if ($quantity <= 0) {
            return $this->removeItem($product_id);
        }
        $query = "UPDATE cart SET qty = ? WHERE p_id = ? AND c_id = ?";
        return $this->db->write($query, 'iii', [$quantity, $product_id, $this->customer_id]);
    }

    public function clearCart() {
        // Match dbforlab.sql schema: cart table with c_id
        $query = "DELETE FROM cart WHERE c_id = ?";
        return $this->db->write($query, 'i', [$this->customer_id]);
    }

    public function getTotal() {
        $items = $this->getItems();
        $total = 0;
        foreach ($items as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }
}
?>
