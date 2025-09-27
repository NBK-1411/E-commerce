<?php
declare(strict_types=1);
header('Content-Type: application/json');

require_once __DIR__ . '/../settings/core.php';
require_admin();
require_once __DIR__ . '/../settings/db_class.php';

$name = trim($_POST['category_name'] ?? $_POST['cat_name'] ?? '');
if ($name === '') { echo json_encode(['success'=>false,'message'=>'Category name required']); exit; }

try {
  $db = new DB();

  // prevent duplicates
  [$okE, $rowsE] = $db->read("SELECT 1 FROM categories WHERE cat_name = ? LIMIT 1", [$name], "s");
  if ($okE && !empty($rowsE)) { echo json_encode(['success'=>false,'message'=>'Category already exists']); exit; }

  $ok = $db->write("INSERT INTO categories (cat_name) VALUES (?)", [$name], "s");
  if (!$ok) throw new Exception('Insert failed');

  echo json_encode(['success'=>true, 'message'=>'Category added successfully']);
} catch (Throwable $e) {
  echo json_encode(['success'=>false, 'message'=>'Could not add category']);
}
