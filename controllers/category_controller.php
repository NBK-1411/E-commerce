<?php
// controllers/category_controller.php
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../classes/category_class.php';

function add_category_ctr(array $in): array {
  if (!is_logged_in()) return [false, 'Not authenticated'];
  $owner = (int)($_SESSION['customer_id'] ?? 0);
  $name  = trim($in['category_name'] ?? $in['cat_name'] ?? '');

  if ($name === '') return [false, 'Category name required'];

  $m = new Category();
  if ($m->exists_name_for_owner($owner, $name)) return [false, 'Category already exists'];
  return $m->add($owner, $name) ? [true, 'Category added successfully'] : [false, 'Could not add category'];
}

function fetch_categories_ctr(): array {
  if (!is_logged_in()) return [false, 'Not authenticated', []];
  $owner = (int)($_SESSION['customer_id'] ?? 0);
  $m = new Category();
  $rows = $m->list_by_owner($owner);
  return [true, 'OK', $rows];
}

function update_category_ctr(array $in): array {
  if (!is_logged_in()) return [false, 'Not authenticated'];
  $owner = (int)($_SESSION['customer_id'] ?? 0);
  $id    = (int)($in['category_id'] ?? $in['cat_id'] ?? 0);
  $name  = trim($in['category_name'] ?? $in['cat_name'] ?? '');

  if ($id <= 0 || $name === '') return [false, 'Invalid input'];

  $m = new Category();
  if ($m->exists_name_for_owner($owner, $name, $id)) return [false, 'Another category already uses that name'];
  return $m->update($owner, $id, $name) ? [true, 'Category updated successfully'] : [false, 'Could not update category'];
}

function delete_category_ctr(array $in): array {
  if (!is_logged_in()) return [false, 'Not authenticated'];
  $owner = (int)($_SESSION['customer_id'] ?? 0);
  $id    = (int)($in['category_id'] ?? $in['cat_id'] ?? 0);
  if ($id <= 0) return [false, 'Invalid category'];
  $m = new Category();
  return $m->delete($owner, $id) ? [true, 'Category deleted successfully'] : [false, 'Could not delete category'];
}
