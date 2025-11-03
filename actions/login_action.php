<?php
require_once __DIR__ . '/../settings/db_cred.php';
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/customer_controller.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(false, 'Invalid request method');
}

$email = sanitize($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

$controller = new CustomerController();
$result = $controller->login($email, $password);

if ($result['success']) {
    $_SESSION['customer'] = $result['data'];
    
    // Also set individual session variables for compatibility
    $_SESSION['user_id'] = $result['data']['customer_id'];
    $_SESSION['user_email'] = $result['data']['customer_email'];
    $_SESSION['user_name'] = $result['data']['customer_name'];
    $_SESSION['user_role'] = $result['data']['user_role'];
    
    json_response(true, 'Login successful', $result['data']);
} else {
    json_response(false, $result['message']);
}
?>
