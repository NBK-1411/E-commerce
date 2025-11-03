<?php
require_once __DIR__ . '/../settings/db_cred.php';
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/customer_controller.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $country = sanitize($_POST['country'] ?? '');
    $city = sanitize($_POST['city'] ?? '');
    $contact = sanitize($_POST['contact'] ?? '');

    $controller = new CustomerController();
    $result = $controller->register($name, $email, $password, $country, $city, $contact);

    echo json_encode($result);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
exit;
?>
