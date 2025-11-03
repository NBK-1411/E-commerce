<?php
require_once __DIR__ . '/../classes/category_class.php';

class CategoryController {
    private $category;

    public function __construct() {
        $this->category = new Category();
    }

    // Lab requirement: add_category_ctr($kwargs) method
    public function add_category_ctr($kwargs) {
        $name = $kwargs['name'] ?? '';
        $user_id = $kwargs['user_id'] ?? null;
        
        if (empty($name)) {
            return ['success' => false, 'message' => 'Category name is required'];
        }

        if ($this->category->nameExists($name)) {
            return ['success' => false, 'message' => 'Category already exists'];
        }

        if (!$user_id) {
            return ['success' => false, 'message' => 'User ID is required'];
        }

        return $this->category->create($name, $user_id);
    }

    public function create($name, $user_id = null) {
        if (empty($name)) {
            return ['success' => false, 'message' => 'Category name is required'];
        }

        if ($this->category->nameExists($name)) {
            return ['success' => false, 'message' => 'Category already exists'];
        }

        if (!$user_id) {
            return ['success' => false, 'message' => 'User ID is required'];
        }

        return $this->category->create($name, $user_id);
    }

    public function getAll($user_id = null) {
        return $this->category->getAll($user_id);
    }

    public function getById($id) {
        return $this->category->getById($id);
    }

    public function update($id, $name) {
        if (empty($name)) {
            return ['success' => false, 'message' => 'Category name is required'];
        }

        if ($this->category->nameExists($name, $id)) {
            return ['success' => false, 'message' => 'Category name already exists'];
        }

        return $this->category->update($id, $name);
    }

    public function delete($id) {
        return $this->category->delete($id);
    }
}
?>
