<?php
require_once __DIR__ . '/../settings/db_cred.php';
require_once __DIR__ . '/../settings/db_class.php';

class Customer {
    private $db;

    public function __construct() {
        $this->db = new Database();
        $this->db->connect();
    }

    public function register($name, $email, $password, $country, $city, $contact, $role = 2) {
        if ($this->emailExists($email)) {
            return ['success' => false, 'message' => 'Email already registered'];
        }

        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        
        $query = "INSERT INTO customer (customer_name, customer_email, customer_pass, customer_country, customer_city, customer_contact, user_role) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $result = $this->db->write($query, 'ssssssi', [$name, $email, $hashed_password, $country, $city, $contact, $role]);
        
        if ($result['success']) {
            return ['success' => true, 'message' => 'Registration successful', 'data' => ['customer_id' => $result['insert_id']]];
        } else {
            return ['success' => false, 'message' => $result['message']];
        }
    }

    public function login($email, $password) {
        // Get customer with password in single query
        $query = "SELECT customer_id, customer_name, customer_email, customer_pass, user_role FROM customer WHERE customer_email = ?";
        $result = $this->db->read($query, 's', [$email]);
        
        if (empty($result)) {
            return ['success' => false, 'message' => 'Email not found'];
        }
        
        $customer = $result[0];
        
        // Verify password
        if (!password_verify($password, $customer['customer_pass'])) {
            return ['success' => false, 'message' => 'Invalid password'];
        }
        
        // Remove password from returned data for security
        unset($customer['customer_pass']);
        
        return ['success' => true, 'data' => $customer];
    }

    public function getById($id) {
        $query = "SELECT customer_id, customer_name, customer_email, customer_country, customer_city, customer_contact, user_role FROM customer WHERE customer_id = ?";
        $result = $this->db->read($query, 'i', [$id]);
        return $result[0] ?? null;
    }

    public function emailExists($email) {
        $query = "SELECT customer_id FROM customer WHERE customer_email = ?";
        $result = $this->db->read($query, 's', [$email]);
        return !empty($result);
    }
}
?>
