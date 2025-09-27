<?php
// classes/category_class.php
require_once __DIR__ . '/../settings/db_class.php';

class Category {
  private DB $db;
  public function __construct(){ $this->db = new DB(); }

  public function add(int $ownerId, string $name): bool {
    $sql = "INSERT INTO categories (cat_name, cat_owner_id) VALUES (?, ?)";
    return $this->db->write($sql, [$name, $ownerId], "si");
  }

  public function list_by_owner(int $ownerId): array {
    [$ok, $rows] = $this->db->read(
      "SELECT cat_id AS category_id, cat_name AS category_name
         FROM categories
        WHERE cat_owner_id = ?
        ORDER BY cat_name ASC",
      [$ownerId], "i"
    );
    return ($ok && $rows) ? $rows : [];
  }

  public function update(int $ownerId, int $catId, string $name): bool {
    // prevent overwriting someone else’s category and enforce per-user uniqueness
    $sql = "UPDATE categories
               SET cat_name = ?
             WHERE cat_id = ? AND cat_owner_id = ?";
    return $this->db->write($sql, [$name, $catId, $ownerId], "sii");
  }

  public function delete(int $ownerId, int $catId): bool {
    $sql = "DELETE FROM categories WHERE cat_id = ? AND cat_owner_id = ?";
    return $this->db->write($sql, [$catId, $ownerId], "ii");
  }

  public function exists_name_for_owner(int $ownerId, string $name, ?int $excludeId = null): bool {
    if ($excludeId) {
      [$ok, $rows] = $this->db->read(
        "SELECT 1 FROM categories
          WHERE cat_owner_id = ? AND cat_name = ? AND cat_id <> ?
          LIMIT 1",
        [$ownerId, $name, $excludeId], "isi"
      );
    } else {
      [$ok, $rows] = $this->db->read(
        "SELECT 1 FROM categories
          WHERE cat_owner_id = ? AND cat_name = ?
          LIMIT 1",
        [$ownerId, $name], "is"
      );
    }
    return $ok && !empty($rows);
  }
}
