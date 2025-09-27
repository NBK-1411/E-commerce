<?php
declare(strict_types=1);
header('Content-Type: application/json');

require_once __DIR__ . '/../settings/core.php';
require_admin();
require_once __DIR__ . '/../settings/db_class.php';

$id   = (int)($_POST['category_id'] ?? $_POST['cat_id'] ?? 0);
$name = trim($_POST['category_name'] ?? $_POST['cat_name'] ?? '');

if ($id <= 0 || $name === '') { echo json_encode(['success'=>false,'message'=>'Invalid input']); exit; }

try {
  $db = new DB();

  // prevent naming clash with other rows
  [$okE, $rowsE] = $db->read(
    "SELECT 1 FROM categories WHERE cat_name = ? AND cat_id <> ? LIMIT 1",
    [$name, $id], "si"
  );
  if ($okE && !empty($rowsE)) { echo json_encode(['success'=>false,'message'=>'Another category already uses that name']); exit; }

  $ok = $db->write("UPDATE categories SET cat_name = ? WHERE cat_id = ?", [$name, $id], "si");
  if (!$ok) throw new Exception('Update failed');

  echo json_encode(['success'=>true, 'message'=>'Category updated successfully']);
} catch (Throwable $e) {
  echo json_encode(['success'=>false, 'message'=>'Could not update category']);
}
