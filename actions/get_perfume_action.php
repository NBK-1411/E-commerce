<?php
require_once __DIR__ . '/../settings/db_cred.php';
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/perfume_controller.php';

header('Content-Type: application/json');
require_admin();

$id = (int)($_GET['id'] ?? 0);

if (!$id) {
    json_response(false, 'Product ID is required');
}

$controller = new PerfumeController();
$product = $controller->getById($id);

if ($product) {
    json_response(true, 'Product fetched', $product);
} else {
    json_response(false, 'Product not found');
}
?>

