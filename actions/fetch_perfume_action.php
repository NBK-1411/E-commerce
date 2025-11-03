<?php
require_once __DIR__ . '/../settings/db_cred.php';
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/perfume_controller.php';

header('Content-Type: application/json');
require_admin();

$controller = new PerfumeController();
$perfumes = $controller->getAll();

json_response(true, 'Perfumes fetched', $perfumes);
?>
