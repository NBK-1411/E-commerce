<?php
require_once 'config.php';

// Handle Login
if (isset($_POST['action']) && $_POST['action'] === 'login') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        header('Location: login.php?error=Please fill in all fields');
        exit();
    }
    
    $stmt = $conn->prepare("SELECT customer_id, customer_name, customer_email, customer_pass, user_role FROM customer WHERE customer_email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $customer = $result->fetch_assoc();
    
    if ($customer && password_verify($password, $customer['customer_pass'])) {
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
    $stmt->close();
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
    
    // Check if email already exists (email is unique in customer table)
    $check_stmt = $conn->prepare("SELECT customer_id FROM customer WHERE customer_email = ?");
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        $check_stmt->close();
        header('Location: signup.php?error=Email already registered');
        exit();
    }
    $check_stmt->close();
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Default user_role is 2 (customer) - 1 is admin
    $user_role = 2;
    
    $insert_stmt = $conn->prepare("
        INSERT INTO customer (customer_name, customer_email, customer_pass, customer_country, customer_city, customer_contact, user_role) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $insert_stmt->bind_param("ssssssi", $customer_name, $email, $hashed_password, $country, $city, $contact, $user_role);
    
    if ($insert_stmt->execute()) {
        $customer_id = $insert_stmt->insert_id;
        
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
        header('Location: signup.php?error=An error occurred. Please try again.');
        exit();
    }
    $insert_stmt->close();
}

// If no valid action, redirect to login
header('Location: login.php');
exit();
?>
