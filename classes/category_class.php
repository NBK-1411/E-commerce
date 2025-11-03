<?php
require_once __DIR__ . '/../settings/db_cred.php';
require_once __DIR__ . '/../settings/db_class.php';

class Category {
    private $db;

    public function __construct() {
        try {
        $this->db = new Database();
        $this->db->connect();
        } catch (Exception $e) {
            error_log("Category class database connection error: " . $e->getMessage());
            throw $e;
        }
    }

    // Create category with user_id tracking (Lab requirement: track who created each category)
    public function create($name, $user_id) {
        // Map to dbforlab.sql structure: categories table with cat_id and cat_name
        // Note: categories table doesn't have user_id, so we'll add it or use existing structure
        // For now, check if user_id column exists, if not, add it dynamically or use default
        // Since dbforlab.sql doesn't have user_id, we'll need to alter table or work around it
        // For Lab requirement, we'll add user_id tracking
        
        // First check if user_id column exists in categories table
        try {
            $checkQuery = "SHOW COLUMNS FROM categories LIKE 'user_id'";
            $columns = $this->db->read($checkQuery);
            
            if (empty($columns)) {
                // Add user_id column if it doesn't exist
                $alterQuery = "ALTER TABLE categories ADD COLUMN user_id INT(11) DEFAULT NULL";
                $result = $this->db->write($alterQuery);
                // If alter fails, continue anyway (column might already exist or table doesn't support it)
            }
        } catch (Exception $e) {
            // Column might already exist or there's an issue - continue anyway
            error_log("Category user_id column check: " . $e->getMessage());
        }
        
        $query = "INSERT INTO categories (cat_name, user_id) VALUES (?, ?)";
        return $this->db->write($query, 'si', [$name, $user_id]);
    }

    // Get all categories created by a specific user (Lab requirement)
    public function getAll($user_id = null) {
        try {
            // First, try to get all categories without user_id filter
            // This is more reliable and handles cases where user_id column doesn't exist
            // Check if category_image column exists
            $hasImageColumn = false;
            try {
                $checkQuery = "SHOW COLUMNS FROM categories LIKE 'category_image'";
                $columns = $this->db->read($checkQuery);
                $hasImageColumn = !empty($columns);
            } catch (Exception $e) {
                $hasImageColumn = false;
            }
            
            if ($hasImageColumn) {
                $query = "SELECT cat_id as id, cat_name as name, category_image as image FROM categories ORDER BY cat_name ASC";
            } else {
                $query = "SELECT cat_id as id, cat_name as name, NULL as image FROM categories ORDER BY cat_name ASC";
            }
            $result = $this->db->read($query);
            
            // If that works and we have a user_id, try filtering (but don't fail if it doesn't work)
            if ($user_id && is_array($result) && !empty($result)) {
                try {
                    // Try to filter by user_id if column exists
                    $filteredQuery = "SELECT cat_id as id, cat_name as name FROM categories WHERE user_id = ? OR user_id IS NULL ORDER BY cat_name ASC";
                    $filteredResult = $this->db->read($filteredQuery, 'i', [$user_id]);
                    if (is_array($filteredResult)) {
                        $result = $filteredResult;
                    }
                } catch (Exception $e) {
                    // user_id column doesn't exist or query failed - use all categories
                    error_log("Category getAll user_id filter failed (expected if column doesn't exist): " . $e->getMessage());
                    // Continue with unfiltered result
                }
            }
            
            // Return empty array if query fails (no categories table or empty)
            if (!is_array($result)) {
                error_log("Category getAll: Result is not an array");
                return [];
            }
            
            // Ensure we return the right format
            $formatted = [];
            foreach ($result as $row) {
                // Handle both possible column name formats
                $id = $row['id'] ?? $row['cat_id'] ?? null;
                $name = $row['name'] ?? $row['cat_name'] ?? '';
                
                if ($id !== null && $name !== '') {
                    $formatted[] = [
                        'id' => $id,
                        'name' => $name,
                        'image' => $row['image'] ?? $row['category_image'] ?? null
                    ];
                }
            }
            
            return $formatted;
        } catch (Exception $e) {
            error_log("Category getAll error: " . $e->getMessage());
            error_log("Category getAll stack trace: " . $e->getTraceAsString());
            // Return empty array on error instead of crashing
            return [];
        }
    }

    public function getById($id) {
        // Map to dbforlab.sql structure: use cat_id and cat_name
        $query = "SELECT cat_id as id, cat_name as name FROM categories WHERE cat_id = ?";
        $result = $this->db->read($query, 'i', [$id]);
        return $result[0] ?? null;
    }

    public function update($id, $name) {
        // Map to dbforlab.sql structure: use cat_id and cat_name
        $query = "UPDATE categories SET cat_name = ? WHERE cat_id = ?";
        return $this->db->write($query, 'si', [$name, $id]);
    }

    public function delete($id) {
        // Map to dbforlab.sql structure: use cat_id
        $query = "DELETE FROM categories WHERE cat_id = ?";
        return $this->db->write($query, 'i', [$id]);
    }

    public function nameExists($name, $excludeId = null) {
        // Map to dbforlab.sql structure: use cat_name and cat_id
        $query = "SELECT cat_id FROM categories WHERE cat_name = ?";
        if ($excludeId) {
            $query .= " AND cat_id != ?";
            $result = $this->db->read($query, 'si', [$name, $excludeId]);
        } else {
            $result = $this->db->read($query, 's', [$name]);
        }
        return !empty($result);
    }
}
?>
