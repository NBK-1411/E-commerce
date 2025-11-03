<?php
require_once __DIR__ . '/../settings/db_cred.php';
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/perfume_controller.php';

header('Content-Type: application/json');
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(false, 'Invalid request method');
}

$name = sanitize($_POST['name'] ?? '');
$brand_name = sanitize($_POST['brand'] ?? ''); // This is brand name, will be converted to brand_id
$category_id = (int)($_POST['category_id'] ?? 0);
$price = (float)($_POST['price'] ?? 0);
$stock = (int)($_POST['stock'] ?? 0); // Note: products table doesn't have stock, but we'll pass it for compatibility
$description = sanitize($_POST['description'] ?? '');
$image = sanitize($_POST['image'] ?? '');
$notes = sanitize($_POST['notes'] ?? '');
$badge = sanitize($_POST['badge'] ?? '');

$controller = new PerfumeController();
$result = $controller->create($name, $brand_name, $category_id, $price, $stock, $description, $image, $notes, $badge);

json_response($result['success'], $result['message'], $result['data'] ?? []);
?>
