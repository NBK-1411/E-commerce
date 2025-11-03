<?php
require_once __DIR__ . '/../classes/perfume_class.php';

class PerfumeController {
    private $perfume;

    public function __construct() {
        $this->perfume = new Perfume();
    }

    // Lab requirement: add_product_ctr($kwargs) method
    // Updated to handle multiple categories
    public function add_product_ctr($kwargs) {
        $name = $kwargs['name'] ?? '';
        $brand_id = (int)($kwargs['brand_id'] ?? 0);
        
        // Handle multiple category_ids - can be array or comma-separated string
        $category_ids = $kwargs['category_ids'] ?? $kwargs['category_id'] ?? [];
        if (is_string($category_ids) && !empty($category_ids)) {
            // If it's a comma-separated string, convert to array
            $category_ids = explode(',', $category_ids);
            $category_ids = array_filter(array_map('trim', $category_ids));
        }
        if (!is_array($category_ids)) {
            $category_ids = !empty($category_ids) && $category_ids > 0 ? [$category_ids] : [];
        }
        $category_ids = array_filter(array_map('intval', $category_ids), function($id) {
            return $id > 0;
        });
        
        $price = (float)($kwargs['price'] ?? 0);
        $description = $kwargs['description'] ?? '';
        $image = $kwargs['image'] ?? '';
        $keyword = $kwargs['keyword'] ?? '';
        $notes = $kwargs['notes'] ?? '';
        $badge = $kwargs['badge'] ?? '';
        
        if (empty($name) || empty($category_ids) || empty($price)) {
            return ['success' => false, 'message' => 'Product Title, at least one Category, and Price are required'];
        }
        
        if ($price <= 0) {
            return ['success' => false, 'message' => 'Price must be positive'];
        }
        
        // Get brand name from brand_id if provided
        $brand_name = '';
        if ($brand_id > 0) {
            require_once __DIR__ . '/../classes/brand_class.php';
            $brandClass = new Brand();
            $brand = $brandClass->getById($brand_id);
            if ($brand) {
                $brand_name = $brand['name'];
            }
        }
        
        // If no brand_id provided, use default
        if (empty($brand_name)) {
            $brand_name = 'Generic';
        }
        
        return $this->perfume->create($name, $brand_name, $category_ids, $price, 0, $description, $image, $notes, $badge, $keyword);
    }

    public function create($name, $brand_name, $category_id, $price, $stock, $description, $image = null, $notes = null, $badge = null, $keyword = null) {
        if (empty($name) || empty($category_id) || empty($price)) {
            return ['success' => false, 'message' => 'All required fields must be filled'];
        }

        if ($price <= 0) {
            return ['success' => false, 'message' => 'Price must be positive'];
        }

        // Note: stock parameter is kept for compatibility but products table doesn't have stock field
        return $this->perfume->create($name, $brand_name, $category_id, $price, $stock, $description, $image, $notes, $badge);
    }

    public function getAll() {
        return $this->perfume->getAll();
    }

    public function getById($id) {
        return $this->perfume->getById($id);
    }

    public function getByCategory($category_id) {
        return $this->perfume->getByCategory($category_id);
    }

    public function update($id, $name, $brand_name, $category_ids, $price, $stock, $description, $image = null, $notes = null, $badge = null, $keyword = null) {
        // Handle multiple category_ids - can be array or comma-separated string
        if (is_string($category_ids) && !empty($category_ids)) {
            // If it's a comma-separated string, convert to array
            $category_ids = explode(',', $category_ids);
            $category_ids = array_filter(array_map('trim', $category_ids));
        }
        if (!is_array($category_ids)) {
            $category_ids = !empty($category_ids) && $category_ids > 0 ? [$category_ids] : [];
        }
        $category_ids = array_filter(array_map('intval', $category_ids), function($id) {
            return $id > 0;
        });
        
        if (empty($name) || empty($category_ids) || empty($price)) {
            return ['success' => false, 'message' => 'Product Title, at least one Category, and Price are required'];
        }

        if ($price <= 0) {
            return ['success' => false, 'message' => 'Price must be positive'];
        }

        // Note: stock parameter is kept for compatibility but products table doesn't have stock field
        return $this->perfume->update($id, $name, $brand_name, $category_ids, $price, $stock, $description, $image, $notes, $badge, $keyword);
    }

    public function delete($id) {
        return $this->perfume->delete($id);
    }

    public function getFeatured($limit = 6) {
        return $this->perfume->getFeatured($limit);
    }
}
?>
