<?php
declare(strict_types=1);
header('Content-Type: application/json');

require_once __DIR__ . '/../settings/core.php';
require_admin();
require_once __DIR__ . '/../settings/db_class.php';

$id = (int)($_POST['category_id'] ?? $_POST['cat_id'] ?? 0);
if ($id <= 0) { echo json_encode(['success'=>false,'message'=>'Invalid category']); exit; }

try {
  $db = new DB();
  $ok = $db->write("DELETE FROM categories WHERE cat_id = ?", [$id], "i");
  if (!$ok) throw new Exception('Delete failed');

  echo json_encode(['success'=>true, 'message'=>'Category deleted successfully']);
} catch (Throwable $e) {
  echo json_encode(['success'=>false, 'message'=>'Could not delete category']);
}
