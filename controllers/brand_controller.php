<?php
require_once __DIR__ . '/../classes/brand_class.php';

class BrandController {
    private $brand;

    public function __construct() {
        $this->brand = new Brand();
    }

    // Lab requirement: add_brand_ctr($kwargs) method
    // New brands are now available for all categories (category_id is set to NULL)
    public function add_brand_ctr($kwargs) {
        $name = $kwargs['name'] ?? '';
        $category_id = null; // Always set to NULL so brand is available for all categories
        $user_id = $kwargs['user_id'] ?? null;
        
        if (empty($name)) {
            return ['success' => false, 'message' => 'Brand name is required'];
        }

        if (!$user_id) {
            return ['success' => false, 'message' => 'User ID is required'];
        }

        // Check if brand name already exists (brands with NULL category_id must be globally unique)
        if ($this->brand->nameExistsInCategory($name, null)) {
            return ['success' => false, 'message' => 'Brand with this name already exists'];
        }

        return $this->brand->create($name, $category_id, $user_id);
    }

    public function getAll($user_id = null) {
        return $this->brand->getAll($user_id);
    }

    public function getById($id) {
        return $this->brand->getById($id);
    }

    public function update($id, $name, $category_id) {
        if (empty($name)) {
            return ['success' => false, 'message' => 'Brand name is required'];
        }

        // Get the existing brand to preserve its category_id
        $existingBrand = $this->brand->getById($id);
        if (!$existingBrand) {
            return ['success' => false, 'message' => 'Brand not found'];
        }

        // Use existing category_id if provided, otherwise preserve the current one
        // For new brands, category_id will be NULL (available for all categories)
        $finalCategoryId = $category_id !== null ? $category_id : ($existingBrand['category_id'] ?? null);

        // Check if brand name already exists (checking for NULL category_id brands which are globally unique)
        if ($this->brand->nameExistsInCategory($name, $finalCategoryId, $id)) {
            return ['success' => false, 'message' => 'Brand with this name already exists'];
        }

        return $this->brand->update($id, $name, $finalCategoryId);
    }

    public function delete($id) {
        return $this->brand->delete($id);
    }
    
    public function getByCategory($category_id) {
        return $this->brand->getByCategory($category_id);
    }
}
?>

