<?php
require_once __DIR__ . '/../classes/customer_class.php';

class CustomerController {
    private $customer;

    public function __construct() {
        $this->customer = new Customer();
    }

    public function register($name, $email, $password, $country, $city, $contact) {
        // Validation
        if (empty($name) || empty($email) || empty($password) || empty($country) || empty($city) || empty($contact)) {
            return ['success' => false, 'message' => 'All fields are required'];
        }

        if (strlen($password) < 8) {
            return ['success' => false, 'message' => 'Password must be at least 8 characters'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }

        if ($this->customer->emailExists($email)) {
            return ['success' => false, 'message' => 'Email already registered'];
        }

        return $this->customer->register($name, $email, $password, $country, $city, $contact);
    }

    public function login($email, $password) {
        if (empty($email) || empty($password)) {
            return ['success' => false, 'message' => 'Email and password are required'];
        }

        return $this->customer->login($email, $password);
    }
}
?>
