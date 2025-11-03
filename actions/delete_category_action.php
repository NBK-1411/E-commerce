<?php
require_once __DIR__ . '/../settings/db_cred.php';
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/category_controller.php';

header('Content-Type: application/json');
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(false, 'Invalid request method');
}

$id = (int)($_POST['id'] ?? 0);
$controller = new CategoryController();
$result = $controller->delete($id);

json_response($result['success'], $result['message']);
?>
