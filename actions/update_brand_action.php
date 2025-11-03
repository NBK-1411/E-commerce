<?php
require_once __DIR__ . '/../settings/db_cred.php';
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/brand_controller.php';

header('Content-Type: application/json');
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(false, 'Invalid request method');
}

$id = (int)($_POST['id'] ?? 0);
$name = sanitize($_POST['name'] ?? '');

if (!$id) {
    json_response(false, 'Brand ID is required');
}

// Category is no longer used when updating - brands are available for all categories
// We only update the brand name and preserve the existing category_id (which should be NULL for new brands)
$controller = new BrandController();
$result = $controller->update($id, $name, null);

json_response($result['success'], $result['message'], $result['data'] ?? []);
?>

