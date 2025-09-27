<?php
declare(strict_types=1);
header('Content-Type: application/json');

require_once __DIR__ . '/../settings/core.php';
require_admin();
require_once __DIR__ . '/../settings/db_class.php';

try {
  $db = new DB();
  [$ok, $rows] = $db->read(
    "SELECT cat_id AS category_id, cat_name AS category_name
     FROM categories ORDER BY cat_name ASC"
  );
  if (!$ok) throw new Exception('Read failed');
  echo json_encode(['success'=>true, 'data'=>$rows]);
} catch (Throwable $e) {
  echo json_encode(['success'=>false, 'message'=>'Failed to fetch categories']);
}
