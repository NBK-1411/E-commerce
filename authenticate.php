<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors, but log them
ini_set('log_errors', 1);

// Start output buffering immediately to catch any output
if (!ob_get_level()) {
    ob_start();
}

// Use the same database configuration as the rest of the application
require_once __DIR__ . '/settings/db_cred.php';
require_once __DIR__ . '/settings/db_class.php';
require_once __DIR__ . '/settings/core.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Clear any output that might have been sent by includes
ob_clean();

// Create database connection
try {
    $db = new Database();
    $db->connect();
} catch (Exception $e) {
    error_log("Database connection error in authenticate.php: " . $e->getMessage());
    error_log("Database connection error details: " . print_r($e, true));
    ob_end_clean();
    header('Location: signup.php?error=Database connection failed. Please try again later.');
    exit();
} catch (Error $e) {
    error_log("Database connection fatal error in authenticate.php: " . $e->getMessage());
    ob_end_clean();
    header('Location: signup.php?error=Database connection failed. Please contact support.');
    exit();
}

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
                // Build absolute URL for redirect
                $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
                $script_dir = dirname($_SERVER['SCRIPT_NAME']);
                
                if ($script_dir === '/' || $script_dir === '.') {
                    $redirect_url = $protocol . '://' . $host . '/' . ($customer['user_role'] == 1 ? 'admin.php' : 'index.php');
                } else {
                    $redirect_url = $protocol . '://' . $host . rtrim($script_dir, '/') . '/' . ($customer['user_role'] == 1 ? 'admin.php' : 'index.php');
                }
                
                header('Location: ' . $redirect_url);
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
            error_log("Registration attempt with existing email: " . $email);
            header('Location: signup.php?error=Email already registered');
            exit();
        }
        
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        if ($hashed_password === false) {
            error_log("Password hashing failed");
            header('Location: signup.php?error=Password hashing failed. Please try again.');
            exit();
        }
        
        // Default user_role is 2 (customer) - 1 is admin
        $user_role = 2;
        
        // Insert new customer
        $insert_query = "INSERT INTO customer (customer_name, customer_email, customer_pass, customer_country, customer_city, customer_contact, user_role) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        error_log("Attempting to insert customer: " . $email);
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
            error_log("Customer registered successfully. ID: " . $customer_id);
            
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
            
            error_log("Session set, redirecting to index.php");
            
            // Build absolute URL for redirect to work on both local and live server
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
            $script_dir = dirname($_SERVER['SCRIPT_NAME']);
            
            // If script is in root, use root, otherwise use the directory
            if ($script_dir === '/' || $script_dir === '.') {
                $redirect_url = $protocol . '://' . $host . '/index.php?welcome=1';
            } else {
                $redirect_url = $protocol . '://' . $host . rtrim($script_dir, '/') . '/index.php?welcome=1';
            }
            
            error_log("Redirect URL: " . $redirect_url);
            header('Location: ' . $redirect_url);
            exit();
        } else {
            error_log("Registration error: " . $result['message']);
            error_log("Registration failed for email: " . $email);
            header('Location: signup.php?error=Registration failed: ' . urlencode($result['message']));
            exit();
        }
    } catch (Exception $e) {
        error_log("Registration exception: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        header('Location: signup.php?error=An error occurred: ' . urlencode($e->getMessage()));
        exit();
    } catch (Error $e) {
        error_log("Registration fatal error: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        header('Location: signup.php?error=A fatal error occurred. Please contact support.');
        exit();
    }
}

// If no valid action, redirect to login
error_log("authenticate.php called without valid action. POST data: " . print_r($_POST, true));
error_log("SERVER info: " . print_r($_SERVER, true));

// Try to redirect
if (!headers_sent()) {
    header('Location: login.php');
    exit();
} else {
    // If headers already sent, show error message
    ob_end_clean();
    echo "<!DOCTYPE html><html><head><title>Error</title></head><body>";
    echo "<h1>Error</h1>";
    echo "<p>No valid action received. Please go back to the <a href='login.php'>login page</a>.</p>";
    echo "<p>POST data: " . htmlspecialchars(print_r($_POST, true)) . "</p>";
    echo "</body></html>";
    exit();
}
?>
