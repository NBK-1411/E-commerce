<?php
require_once __DIR__ . '/../settings/db_cred.php';
require_once __DIR__ . '/../settings/db_class.php';

class Brand {
    private $db;

    public function __construct() {
        try {
            $this->db = new Database();
            $this->db->connect();
        } catch (Exception $e) {
            error_log("Brand class database connection error: " . $e->getMessage());
            throw $e;
        }
    }

    // Create brand with category_id and user_id tracking
    // When category_id is NULL, brand is available for all categories
    public function create($name, $category_id, $user_id) {
        // First check if category_id column exists in brands table
        try {
            $checkQuery = "SHOW COLUMNS FROM brands LIKE 'category_id'";
            $columns = $this->db->read($checkQuery);
            
            if (empty($columns)) {
                // Add category_id column if it doesn't exist
                $alterQuery = "ALTER TABLE brands ADD COLUMN category_id INT(11) DEFAULT NULL";
                $this->db->write($alterQuery);
            }
        } catch (Exception $e) {
            error_log("Brand category_id column check: " . $e->getMessage());
        }
        
        // Check if user_id column exists
        try {
            $checkQuery = "SHOW COLUMNS FROM brands LIKE 'user_id'";
            $columns = $this->db->read($checkQuery);
            
            if (empty($columns)) {
                // Add user_id column if it doesn't exist
                $alterQuery = "ALTER TABLE brands ADD COLUMN user_id INT(11) DEFAULT NULL";
                $this->db->write($alterQuery);
            }
        } catch (Exception $e) {
            error_log("Brand user_id column check: " . $e->getMessage());
        }
        
        // When creating a new brand, set category_id to NULL so it's available for all categories
        // Check if brand name already exists (if category_id is NULL, brand name must be globally unique)
        if ($this->nameExistsInCategory($name, null)) {
            return ['success' => false, 'message' => 'Brand with this name already exists'];
        }
        
        // Insert brand with NULL category_id to make it available for all categories
        $query = "INSERT INTO brands (brand_name, category_id, user_id) VALUES (?, NULL, ?)";
        return $this->db->write($query, 'si', [$name, $user_id]);
    }

    // Get all brands created by a specific user, organized by category
    public function getAll($user_id = null) {
        try {
            // Check if category_id and user_id columns exist
            $hasCategoryId = false;
            $hasUserId = false;
            
            try {
                $checkQuery = "SHOW COLUMNS FROM brands LIKE 'category_id'";
                $columns = $this->db->read($checkQuery);
                $hasCategoryId = !empty($columns);
            } catch (Exception $e) {
                // Column doesn't exist
            }
            
            try {
                $checkQuery = "SHOW COLUMNS FROM brands LIKE 'user_id'";
                $columns = $this->db->read($checkQuery);
                $hasUserId = !empty($columns);
            } catch (Exception $e) {
                // Column doesn't exist
            }
            
            // Build query based on available columns
            if ($user_id && $hasUserId && $hasCategoryId) {
                // Get brands created by this user OR brands with NULL user_id, organized by category
                $query = "SELECT b.brand_id as id, b.brand_name as name, 
                                b.category_id, 
                                COALESCE(c.cat_name, 'Uncategorized') as category_name
                         FROM brands b 
                         LEFT JOIN categories c ON b.category_id = c.cat_id 
                         WHERE b.user_id = ? OR b.user_id IS NULL 
                         ORDER BY c.cat_name ASC, b.brand_name ASC";
                $result = $this->db->read($query, 'i', [$user_id]);
            } else if ($hasCategoryId) {
                // Get all brands organized by category
                $query = "SELECT b.brand_id as id, b.brand_name as name, 
                                b.category_id, 
                                COALESCE(c.cat_name, 'Uncategorized') as category_name
                         FROM brands b 
                         LEFT JOIN categories c ON b.category_id = c.cat_id 
                         ORDER BY c.cat_name ASC, b.brand_name ASC";
                $result = $this->db->read($query);
            } else {
                // Fallback: get all brands
                $query = "SELECT brand_id as id, brand_name as name FROM brands ORDER BY brand_name ASC";
                $result = $this->db->read($query);
                
                // Add default category_id if column doesn't exist
                if (is_array($result)) {
                    foreach ($result as &$row) {
                        $row['category_id'] = null;
                        $row['category_name'] = 'Uncategorized';
                    }
                }
            }
            
            if (!is_array($result)) {
                return [];
            }
            
            // Format results
            $formatted = [];
            foreach ($result as $row) {
                $category_id = $row['category_id'] ?? null;
                $category_name = $row['category_name'] ?? null;
                
                // If category_id is NULL, brand is available for all categories
                if ($category_id === null) {
                    $category_name = 'All Categories';
                } elseif ($category_name === null || $category_name === 'Uncategorized') {
                    $category_name = 'Uncategorized';
                }
                
                $formatted[] = [
                    'id' => $row['id'] ?? $row['brand_id'] ?? null,
                    'name' => $row['name'] ?? $row['brand_name'] ?? '',
                    'category_id' => $category_id,
                    'category_name' => $category_name
                ];
            }
            
            return $formatted;
        } catch (Exception $e) {
            error_log("Brand getAll error: " . $e->getMessage());
            return [];
        }
    }

    public function getById($id) {
        try {
            // Check if category_id column exists
            $hasCategoryId = false;
            try {
                $checkQuery = "SHOW COLUMNS FROM brands LIKE 'category_id'";
                $columns = $this->db->read($checkQuery);
                $hasCategoryId = !empty($columns);
            } catch (Exception $e) {
                // Column doesn't exist
            }
            
            if ($hasCategoryId) {
                $query = "SELECT b.brand_id as id, b.brand_name as name, 
                                b.category_id, 
                                COALESCE(c.cat_name, 'Uncategorized') as category_name
                         FROM brands b 
                         LEFT JOIN categories c ON b.category_id = c.cat_id 
                         WHERE b.brand_id = ?";
                $result = $this->db->read($query, 'i', [$id]);
                
                if (!empty($result) && $result[0]['category_id'] === null) {
                    // If category_id is NULL, brand is available for all categories
                    $result[0]['category_name'] = 'All Categories';
                }
            } else {
                $query = "SELECT brand_id as id, brand_name as name FROM brands WHERE brand_id = ?";
                $result = $this->db->read($query, 'i', [$id]);
                
                if (!empty($result)) {
                    $result[0]['category_id'] = null;
                    $result[0]['category_name'] = 'All Categories';
                }
            }
            
            return $result[0] ?? null;
        } catch (Exception $e) {
            error_log("Brand getById error: " . $e->getMessage());
            return null;
        }
    }

    public function update($id, $name, $category_id) {
        // Check if brand name already exists (for NULL category_id, check globally)
        if ($this->nameExistsInCategory($name, $category_id, $id)) {
            if ($category_id === null) {
                return ['success' => false, 'message' => 'Brand with this name already exists'];
            } else {
                return ['success' => false, 'message' => 'Brand with this name already exists in this category'];
            }
        }
        
        // Check if category_id column exists
        try {
            $checkQuery = "SHOW COLUMNS FROM brands LIKE 'category_id'";
            $columns = $this->db->read($checkQuery);
            $hasCategoryId = !empty($columns);
        } catch (Exception $e) {
            $hasCategoryId = false;
        }
        
        if ($hasCategoryId && $category_id !== null) {
            // Update both name and category_id if category_id is provided
            $query = "UPDATE brands SET brand_name = ?, category_id = ? WHERE brand_id = ?";
            return $this->db->write($query, 'sii', [$name, $category_id, $id]);
        } else if ($hasCategoryId) {
            // Update only name, preserve existing category_id (which may be NULL for all-category brands)
            $query = "UPDATE brands SET brand_name = ? WHERE brand_id = ?";
            return $this->db->write($query, 'si', [$name, $id]);
        } else {
            // Fallback: update only name if category_id column doesn't exist
            $query = "UPDATE brands SET brand_name = ? WHERE brand_id = ?";
            return $this->db->write($query, 'si', [$name, $id]);
        }
    }

    public function delete($id) {
        $query = "DELETE FROM brands WHERE brand_id = ?";
        return $this->db->write($query, 'i', [$id]);
    }

    // Check if brand name exists in a specific category (for uniqueness requirement)
    // If category_id is NULL, checks if brand name exists globally (brands with NULL category_id)
    public function nameExistsInCategory($name, $category_id, $excludeId = null) {
        try {
            // Check if category_id column exists
            $checkQuery = "SHOW COLUMNS FROM brands LIKE 'category_id'";
            $columns = $this->db->read($checkQuery);
            $hasCategoryId = !empty($columns);
        } catch (Exception $e) {
            $hasCategoryId = false;
        }
        
        if ($hasCategoryId) {
            if ($category_id === null) {
                // If checking for NULL category_id, check if any brand with this name exists with NULL category_id
                // (brands with NULL category_id are globally unique by name)
                $query = "SELECT brand_id FROM brands WHERE brand_name = ? AND category_id IS NULL";
                if ($excludeId) {
                    $query .= " AND brand_id != ?";
                    $result = $this->db->read($query, 'si', [$name, $excludeId]);
                } else {
                    $result = $this->db->read($query, 's', [$name]);
                }
            } else {
                // Check if brand name exists for this specific category OR for all categories (NULL)
                $query = "SELECT brand_id FROM brands WHERE brand_name = ? AND (category_id = ? OR category_id IS NULL)";
                if ($excludeId) {
                    $query .= " AND brand_id != ?";
                    $result = $this->db->read($query, 'sii', [$name, $category_id, $excludeId]);
                } else {
                    $result = $this->db->read($query, 'si', [$name, $category_id]);
                }
            }
        } else {
            // Fallback: just check name if category_id column doesn't exist
            $query = "SELECT brand_id FROM brands WHERE brand_name = ?";
            if ($excludeId) {
                $query .= " AND brand_id != ?";
                $result = $this->db->read($query, 'si', [$name, $excludeId]);
            } else {
                $result = $this->db->read($query, 's', [$name]);
            }
        }
        
        return !empty($result);
    }
    
    // Get all brands for a specific category (for dropdowns)
    // Returns brands that are either assigned to this category OR available for all categories (category_id IS NULL)
    public function getByCategory($category_id) {
        try {
            // Check if category_id column exists
            $checkQuery = "SHOW COLUMNS FROM brands LIKE 'category_id'";
            $columns = $this->db->read($checkQuery);
            $hasCategoryId = !empty($columns);
        } catch (Exception $e) {
            $hasCategoryId = false;
        }
        
        if ($hasCategoryId && $category_id) {
            // Return brands assigned to this category OR brands available for all categories (category_id IS NULL)
            $query = "SELECT brand_id as id, brand_name as name FROM brands WHERE category_id = ? OR category_id IS NULL ORDER BY brand_name ASC";
            $result = $this->db->read($query, 'i', [$category_id]);
        } else {
            // If category_id column doesn't exist or no category specified, return all brands
            $query = "SELECT brand_id as id, brand_name as name FROM brands ORDER BY brand_name ASC";
            $result = $this->db->read($query);
        }
        
        return is_array($result) ? $result : [];
    }
}
?>

