<?php
// Use the same database configuration as the rest of the application
require_once __DIR__ . '/settings/db_cred.php';
require_once __DIR__ . '/settings/db_class.php';
require_once __DIR__ . '/settings/core.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Create database connection
$db = new Database();
$db->connect();

// Handle Login
if (isset($_POST['action']) && $_POST['action'] === 'login') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        header('Location: login.php?error=Please fill in all fields');
        exit();
    }
    
    try {
        $query = "SELECT customer_id, customer_name, customer_email, customer_pass, user_role FROM customer WHERE customer_email = ?";
        $result = $db->read($query, 's', [$email]);
        
        if (!empty($result) && isset($result[0])) {
            $customer = $result[0];
            
            if (password_verify($password, $customer['customer_pass'])) {
                // Login successful
                $_SESSION['customer'] = [
                    'customer_id' => $customer['customer_id'],
                    'customer_name' => $customer['customer_name'],
                    'customer_email' => $customer['customer_email'],
                    'user_role' => $customer['user_role']
                ];
                
                // Also set individual session variables for compatibility
                $_SESSION['user_id'] = $customer['customer_id'];
                $_SESSION['user_email'] = $customer['customer_email'];
                $_SESSION['user_name'] = $customer['customer_name'];
                $_SESSION['user_role'] = $customer['user_role'];
                
                // Redirect based on role (1 = admin, 2 = customer)
                if ($customer['user_role'] == 1) {
                    header('Location: admin.php');
                } else {
                    header('Location: index.php');
                }
                exit();
            } else {
                header('Location: login.php?error=Invalid email or password');
                exit();
            }
        } else {
            header('Location: login.php?error=Invalid email or password');
            exit();
        }
    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
        header('Location: login.php?error=An error occurred. Please try again.');
        exit();
    }
}

// Handle Signup
if (isset($_POST['action']) && $_POST['action'] === 'signup') {
    $customer_name = htmlspecialchars(trim($_POST['full_name'] ?? $_POST['customer_name'] ?? ''));
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $country = htmlspecialchars(trim($_POST['country'] ?? ''));
    $city = htmlspecialchars(trim($_POST['city'] ?? ''));
    $contact = htmlspecialchars(trim($_POST['contact'] ?? ''));
    
    // Validation
    if (empty($customer_name) || empty($email) || empty($password) || empty($country) || empty($city) || empty($contact)) {
        header('Location: signup.php?error=Please fill in all required fields');
        exit();
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: signup.php?error=Invalid email address');
        exit();
    }
    
    if (strlen($password) < 8) {
        header('Location: signup.php?error=Password must be at least 8 characters');
        exit();
    }
    
    if ($password !== $confirm_password) {
        header('Location: signup.php?error=Passwords do not match');
        exit();
    }
    
    try {
        // Check if email already exists
        $check_query = "SELECT customer_id FROM customer WHERE customer_email = ?";
        $check_result = $db->read($check_query, 's', [$email]);
        
        if (!empty($check_result)) {
            header('Location: signup.php?error=Email already registered');
            exit();
        }
        
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Default user_role is 2 (customer) - 1 is admin
        $user_role = 2;
        
        // Insert new customer
        $insert_query = "INSERT INTO customer (customer_name, customer_email, customer_pass, customer_country, customer_city, customer_contact, user_role) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $result = $db->write($insert_query, 'ssssssi', [
            $customer_name, 
            $email, 
            $hashed_password, 
            $country, 
            $city, 
            $contact, 
            $user_role
        ]);
        
        if ($result['success']) {
            $customer_id = $result['insert_id'];
            
            // Auto login after signup
            $_SESSION['customer'] = [
                'customer_id' => $customer_id,
                'customer_name' => $customer_name,
                'customer_email' => $email,
                'user_role' => $user_role
            ];
            
            // Also set individual session variables for compatibility
            $_SESSION['user_id'] = $customer_id;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_name'] = $customer_name;
            $_SESSION['user_role'] = $user_role;
            
            header('Location: index.php?welcome=1');
            exit();
        } else {
            error_log("Registration error: " . $result['message']);
            header('Location: signup.php?error=Registration failed: ' . htmlspecialchars($result['message']));
            exit();
        }
    } catch (Exception $e) {
        error_log("Registration error: " . $e->getMessage());
        header('Location: signup.php?error=An error occurred. Please try again.');
        exit();
    }
}

// If no valid action, redirect to login
header('Location: login.php');
exit();
?>
