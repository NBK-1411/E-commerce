<?php
require_once __DIR__ . '/../settings/db_cred.php';
require_once __DIR__ . '/../settings/db_class.php';

class Perfume {
    private $db;

    public function __construct() {
        $this->db = new Database();
        $this->db->connect();
    }

    // Helper function to get or create brand and return brand_id
    private function getOrCreateBrand($brand_name) {
        // Check if brand exists
        $query = "SELECT brand_id FROM brands WHERE brand_name = ?";
        $result = $this->db->read($query, 's', [$brand_name]);
        
        if (!empty($result)) {
            return $result[0]['brand_id'];
        }
        
        // Create new brand if it doesn't exist
        $query = "INSERT INTO brands (brand_name) VALUES (?)";
        $result = $this->db->write($query, 's', [$brand_name]);
        if ($result['success']) {
            return $result['insert_id'];
        }
        return null;
    }

    // Match dbforlab.sql schema: products table with product_id, product_title, product_cat, product_brand, etc.
    // Updated to support multiple categories via product_categories junction table
    public function create($name, $brand_name, $category_ids, $price, $stock, $description, $image = null, $notes = null, $badge = null, $keyword = null) {
        // Get or create brand and get brand_id
        // If brand_name is empty, try to create a default brand or use notes as brand
        if (empty($brand_name) || $brand_name === '') {
            $brand_name = 'Generic'; // Default brand
        }
        $brand_id = $this->getOrCreateBrand($brand_name);
        
        if (!$brand_id) {
            return ['success' => false, 'message' => 'Failed to create or find brand'];
        }
        
        // Validate category_ids - can be array or single value
        if (is_array($category_ids)) {
            $category_ids = array_filter($category_ids, function($id) {
                return !empty($id) && $id > 0;
            });
        } else {
            $category_ids = !empty($category_ids) && $category_ids > 0 ? [$category_ids] : [];
        }
        
        if (empty($category_ids)) {
            return ['success' => false, 'message' => 'At least one category is required'];
        }
        
        // Validate that all categories exist in the database
        foreach ($category_ids as $cat_id) {
            $cat_id = (int)$cat_id;
            $checkQuery = "SELECT cat_id FROM categories WHERE cat_id = ?";
            $checkResult = $this->db->read($checkQuery, 'i', [$cat_id]);
            if (empty($checkResult)) {
                return ['success' => false, 'message' => "Category with ID {$cat_id} does not exist. Please select a valid category."];
            }
        }
        
        // Use first category as primary (for backward compatibility with product_cat column)
        $primary_category_id = (int)$category_ids[0];
        
        // Store badge and keyword in product_keywords field
        // Format: badge:{badge},keyword:{keyword} or just keyword:{keyword} or badge:{badge}
        $keywords_parts = [];
        if (!empty($badge)) {
            $keywords_parts[] = 'badge:' . $badge;
        }
        if (!empty($keyword)) {
            $keywords_parts[] = 'keyword:' . $keyword;
        }
        $keywords = implode(',', $keywords_parts);
        
        // Use notes as description if description is empty
        if (empty($description) && !empty($notes)) {
            $description = $notes;
        }
        
        // Insert into products table (matching dbforlab.sql schema)
        // product_keywords can store badge and keyword, product_desc stores description
        $query = "INSERT INTO products (product_title, product_cat, product_brand, product_price, product_desc, product_image, product_keywords) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $result = $this->db->write($query, 'siidsss', [$name, $primary_category_id, $brand_id, $price, $description, $image, $keywords]);
        
        // If product creation succeeded, add categories to junction table
        if ($result['success'] && isset($result['insert_id'])) {
            $product_id = $result['insert_id'];
            
            // Check if product_categories table exists, create it if it doesn't
            $tableCheckQuery = "SHOW TABLES LIKE 'product_categories'";
            $tableExists = $this->db->read($tableCheckQuery);
            
            if (empty($tableExists)) {
                // Create the product_categories junction table
                $createTableQuery = "CREATE TABLE IF NOT EXISTS product_categories (
                    id INT(11) NOT NULL AUTO_INCREMENT,
                    product_id INT(11) NOT NULL,
                    category_id INT(11) NOT NULL,
                    PRIMARY KEY (id),
                    UNIQUE KEY unique_product_category (product_id, category_id),
                    KEY idx_product_id (product_id),
                    KEY idx_category_id (category_id),
                    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
                    FOREIGN KEY (category_id) REFERENCES categories(cat_id) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=latin1";
                $this->db->write($createTableQuery);
                
                // Verify table was created
                $tableExists = $this->db->read($tableCheckQuery);
            }
            
            // Insert into product_categories junction table (supports multiple categories)
            if (!empty($tableExists)) {
            foreach ($category_ids as $cat_id) {
                $cat_id = (int)$cat_id;
                if ($cat_id > 0) {
                    $junctionQuery = "INSERT IGNORE INTO product_categories (product_id, category_id) VALUES (?, ?)";
                    $this->db->write($junctionQuery, 'ii', [$product_id, $cat_id]);
                    }
                }
            }
        }
        
        return $result;
    }

    public function getAll() {
        // Match dbforlab.sql schema: products table joined with categories and brands
        // Updated to include multiple categories from junction table
        $query = "SELECT DISTINCT p.product_id as id, p.product_title as name, 
                         COALESCE(b.brand_name, 'Unknown') as brand, 
                         p.product_cat as category_id, p.product_price as price, 
                         10 as stock, 
                         COALESCE(p.product_desc, '') as description, 
                         COALESCE(p.product_image, '') as image,
                         COALESCE(c.cat_name, 'Uncategorized') as category,
                         COALESCE(p.product_desc, '') as notes,
                         COALESCE(p.product_keywords, '') as keywords
                  FROM products p 
                  LEFT JOIN categories c ON p.product_cat = c.cat_id 
                  LEFT JOIN brands b ON p.product_brand = b.brand_id 
                  ORDER BY p.product_id DESC";
        $result = $this->db->read($query);
        
        // Process results to extract badge and get all categories
        if (is_array($result)) {
            // Check if product_categories table exists
            $tableCheckQuery = "SHOW TABLES LIKE 'product_categories'";
            $tableExists = $this->db->read($tableCheckQuery);
            
            foreach ($result as &$product) {
                // Get all categories for this product from junction table (if it exists)
                if (!empty($tableExists)) {
                $categoryQuery = "SELECT category_id FROM product_categories WHERE product_id = ?";
                $categoryResult = $this->db->read($categoryQuery, 'i', [$product['id']]);
                
                if (is_array($categoryResult) && !empty($categoryResult)) {
                    $category_ids = array_map(function($row) {
                        return $row['category_id'];
                    }, $categoryResult);
                    $product['category_ids'] = $category_ids;
                } else {
                    // Fallback to primary category if junction table is empty
                        $product['category_ids'] = !empty($product['category_id']) ? [$product['category_id']] : [];
                    }
                } else {
                    // If table doesn't exist, use only the primary category
                    $product['category_ids'] = !empty($product['category_id']) ? [$product['category_id']] : [];
                }
                
                // Try to extract badge from product_keywords
                $keywords = $product['keywords'] ?? '';
                $badge = '';
                if (stripos($keywords, 'badge:') !== false) {
                    $parts = explode('badge:', $keywords);
                    if (isset($parts[1])) {
                        $badgeParts = explode(',', trim($parts[1]));
                        $badge = trim($badgeParts[0]);
                    }
                }
                $product['badge'] = $badge;
            }
        }
        
        return is_array($result) ? $result : [];
    }

    public function getById($id) {
        // Match dbforlab.sql schema - updated to get multiple categories
        $query = "SELECT p.product_id as id, p.product_title as name, 
                         COALESCE(b.brand_name, 'Unknown') as brand, 
                         p.product_brand as brand_id,
                         p.product_cat as category_id, p.product_price as price, 
                         10 as stock, 
                         COALESCE(p.product_desc, '') as description, 
                         COALESCE(p.product_image, '') as image,
                         COALESCE(c.cat_name, 'Uncategorized') as category,
                         COALESCE(p.product_desc, '') as notes,
                         COALESCE(p.product_keywords, '') as keywords
                  FROM products p 
                  LEFT JOIN categories c ON p.product_cat = c.cat_id 
                  LEFT JOIN brands b ON p.product_brand = b.brand_id 
                  WHERE p.product_id = ?";
        $result = $this->db->read($query, 'i', [$id]);
        
        if (!empty($result[0])) {
            // Check if product_categories table exists
            $tableCheckQuery = "SHOW TABLES LIKE 'product_categories'";
            $tableExists = $this->db->read($tableCheckQuery);
            
            // Get all categories for this product from junction table (if it exists)
            if (!empty($tableExists)) {
            $categoryQuery = "SELECT category_id FROM product_categories WHERE product_id = ?";
            $categoryResult = $this->db->read($categoryQuery, 'i', [$id]);
            
            if (is_array($categoryResult) && !empty($categoryResult)) {
                $category_ids = array_map(function($row) {
                    return $row['category_id'];
                }, $categoryResult);
                $result[0]['category_ids'] = $category_ids;
            } else {
                // Fallback to primary category if junction table is empty
                    $result[0]['category_ids'] = !empty($result[0]['category_id']) ? [$result[0]['category_id']] : [];
                }
            } else {
                // If table doesn't exist, use only the primary category
                $result[0]['category_ids'] = !empty($result[0]['category_id']) ? [$result[0]['category_id']] : [];
            }
            
            // Extract badge and keyword from keywords
            $keywords = $result[0]['keywords'] ?? '';
            $badge = '';
            $keyword = '';
            
            if (stripos($keywords, 'badge:') !== false) {
                $parts = explode('badge:', $keywords);
                if (isset($parts[1])) {
                    $badgeParts = explode(',', trim($parts[1]));
                    $badge = trim($badgeParts[0]);
                }
            }
            
            if (stripos($keywords, 'keyword:') !== false) {
                $parts = explode('keyword:', $keywords);
                if (isset($parts[1])) {
                    $keywordParts = explode(',', trim($parts[1]));
                    $keyword = trim($keywordParts[0]);
                }
            }
            
            $result[0]['badge'] = $badge;
            $result[0]['keyword'] = $keyword;
        }
        
        return $result[0] ?? null;
    }

    public function getByCategory($category_id) {
        // Match dbforlab.sql schema - updated to use junction table for multiple categories (if it exists)
        // Check if product_categories table exists
        $tableCheckQuery = "SHOW TABLES LIKE 'product_categories'";
        $tableExists = $this->db->read($tableCheckQuery);
        
        if (!empty($tableExists)) {
            // Use junction table to support multiple categories
        $query = "SELECT DISTINCT p.product_id as id, p.product_title as name, 
                         COALESCE(b.brand_name, 'Unknown') as brand, 
                         p.product_cat as category_id, p.product_price as price, 
                         10 as stock, 
                         COALESCE(p.product_desc, '') as description, 
                         COALESCE(p.product_image, '') as image,
                         COALESCE(c.cat_name, 'Uncategorized') as category 
                  FROM products p 
                  LEFT JOIN categories c ON p.product_cat = c.cat_id 
                  LEFT JOIN brands b ON p.product_brand = b.brand_id 
                  LEFT JOIN product_categories pc ON p.product_id = pc.product_id
                  WHERE pc.category_id = ? OR p.product_cat = ?
                  ORDER BY p.product_title ASC";
        return $this->db->read($query, 'ii', [$category_id, $category_id]);
        } else {
            // Fallback to single category (original schema)
            $query = "SELECT DISTINCT p.product_id as id, p.product_title as name, 
                             COALESCE(b.brand_name, 'Unknown') as brand, 
                             p.product_cat as category_id, p.product_price as price, 
                             10 as stock, 
                             COALESCE(p.product_desc, '') as description, 
                             COALESCE(p.product_image, '') as image,
                             COALESCE(c.cat_name, 'Uncategorized') as category 
                      FROM products p 
                      LEFT JOIN categories c ON p.product_cat = c.cat_id 
                      LEFT JOIN brands b ON p.product_brand = b.brand_id 
                      WHERE p.product_cat = ?
                      ORDER BY p.product_title ASC";
            return $this->db->read($query, 'i', [$category_id]);
        }
    }

    // Updated to support multiple categories via product_categories junction table
    public function update($id, $name, $brand_name, $category_ids, $price, $stock, $description, $image = null, $notes = null, $badge = null, $keyword = null) {
        // Get or create brand
        if (empty($brand_name)) {
            $brand_name = 'Generic';
        }
        $brand_id = $this->getOrCreateBrand($brand_name);
        
        if (!$brand_id) {
            return ['success' => false, 'message' => 'Failed to create or find brand'];
        }
        
        // Validate category_ids - can be array or single value
        if (is_array($category_ids)) {
            $category_ids = array_filter($category_ids, function($id) {
                return !empty($id) && $id > 0;
            });
        } else {
            $category_ids = !empty($category_ids) && $category_ids > 0 ? [$category_ids] : [];
        }
        
        if (empty($category_ids)) {
            return ['success' => false, 'message' => 'At least one category is required'];
        }
        
        // Validate that all categories exist in the database
        foreach ($category_ids as $cat_id) {
            $cat_id = (int)$cat_id;
            $checkQuery = "SELECT cat_id FROM categories WHERE cat_id = ?";
            $checkResult = $this->db->read($checkQuery, 'i', [$cat_id]);
            if (empty($checkResult)) {
                return ['success' => false, 'message' => "Category with ID {$cat_id} does not exist. Please select a valid category."];
            }
        }
        
        // Use first category as primary (for backward compatibility with product_cat column)
        $primary_category_id = (int)$category_ids[0];
        
        // Store badge and keyword in product_keywords field
        $keywords_parts = [];
        if (!empty($badge)) {
            $keywords_parts[] = 'badge:' . $badge;
        }
        if (!empty($keyword)) {
            $keywords_parts[] = 'keyword:' . $keyword;
        }
        $keywords = implode(',', $keywords_parts);
        
        // Use notes as description if description is empty
        if (empty($description) && !empty($notes)) {
            $description = $notes;
        }
        
        // Match dbforlab.sql schema: update products table
        if ($image) {
            $query = "UPDATE products SET product_title = ?, product_cat = ?, product_brand = ?, product_price = ?, product_desc = ?, product_image = ?, product_keywords = ? WHERE product_id = ?";
            $result = $this->db->write($query, 'siidsssi', [$name, $primary_category_id, $brand_id, $price, $description, $image, $keywords, $id]);
        } else {
            $query = "UPDATE products SET product_title = ?, product_cat = ?, product_brand = ?, product_price = ?, product_desc = ?, product_keywords = ? WHERE product_id = ?";
            $result = $this->db->write($query, 'siidssi', [$name, $primary_category_id, $brand_id, $price, $description, $keywords, $id]);
        }
        
        // If update succeeded, update the product_categories junction table
        if ($result['success']) {
            // Check if product_categories table exists, create it if it doesn't
            $tableCheckQuery = "SHOW TABLES LIKE 'product_categories'";
            $tableExists = $this->db->read($tableCheckQuery);
            
            if (empty($tableExists)) {
                // Create the product_categories junction table
                $createTableQuery = "CREATE TABLE IF NOT EXISTS product_categories (
                    id INT(11) NOT NULL AUTO_INCREMENT,
                    product_id INT(11) NOT NULL,
                    category_id INT(11) NOT NULL,
                    PRIMARY KEY (id),
                    UNIQUE KEY unique_product_category (product_id, category_id),
                    KEY idx_product_id (product_id),
                    KEY idx_category_id (category_id),
                    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
                    FOREIGN KEY (category_id) REFERENCES categories(cat_id) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=latin1";
                $this->db->write($createTableQuery);
                
                // Verify table was created
                $tableExists = $this->db->read($tableCheckQuery);
            }
            
            // Update the product_categories junction table (supports multiple categories)
            if (!empty($tableExists)) {
                // Delete existing category associations
                $deleteQuery = "DELETE FROM product_categories WHERE product_id = ?";
                $this->db->write($deleteQuery, 'i', [$id]);
                
                // Insert new category associations
                foreach ($category_ids as $cat_id) {
                    $cat_id = (int)$cat_id;
                    if ($cat_id > 0) {
                        $junctionQuery = "INSERT IGNORE INTO product_categories (product_id, category_id) VALUES (?, ?)";
                        $this->db->write($junctionQuery, 'ii', [$id, $cat_id]);
                    }
                }
            }
        }
        
        return $result;
    }

    public function delete($id) {
        // Match dbforlab.sql schema: delete from products table
        $query = "DELETE FROM products WHERE product_id = ?";
        return $this->db->write($query, 'i', [$id]);
    }

    public function getFeatured($limit = 6) {
        // Map to dbforlab.sql structure: products table with product_id, product_title, etc.
        // Featured products are those with "Bestseller", "Featured", "Popular", or "New" badges
        // If not enough products have badges, fill with newest products
        $limit = (int)$limit; // Ensure it's an integer for safety
        
        // First, get products with featured badges (Bestseller, Featured, Popular, New)
        $badgeQuery = "SELECT p.product_id as id, p.product_title as name, 
                         COALESCE(b.brand_name, 'Unknown') as brand, p.product_cat as category_id, 
                         p.product_price as price, 
                         10 as stock, 
                         COALESCE(p.product_desc, '') as description, 
                         COALESCE(p.product_image, '') as image,
                         COALESCE(c.cat_name, 'Uncategorized') as category,
                         COALESCE(p.product_keywords, '') as keywords
                  FROM products p 
                  LEFT JOIN categories c ON p.product_cat = c.cat_id 
                  LEFT JOIN brands b ON p.product_brand = b.brand_id 
                  WHERE p.product_keywords LIKE '%badge:Bestseller%' 
                     OR p.product_keywords LIKE '%badge:Featured%'
                     OR p.product_keywords LIKE '%badge:Popular%'
                     OR p.product_keywords LIKE '%badge:New%'
                     OR p.product_keywords LIKE '%badge:bestseller%'
                     OR p.product_keywords LIKE '%badge:featured%'
                     OR p.product_keywords LIKE '%badge:popular%'
                  ORDER BY p.product_id DESC 
                  LIMIT " . $limit;
        $badgeResults = $this->db->read($badgeQuery);
        
        $featuredProducts = is_array($badgeResults) ? $badgeResults : [];
        $featuredCount = count($featuredProducts);
        
        // If we have enough products with badges, return them
        if ($featuredCount >= $limit) {
            // Extract badge information
            foreach ($featuredProducts as &$product) {
                $keywords = $product['keywords'] ?? '';
                $badge = '';
                if (stripos($keywords, 'badge:') !== false) {
                    $parts = explode('badge:', $keywords);
                    if (isset($parts[1])) {
                        $badgeParts = explode(',', trim($parts[1]));
                        $badge = trim($badgeParts[0]);
                    }
                }
                $product['badge'] = $badge;
                unset($product['keywords']); // Remove keywords from output
            }
            return array_slice($featuredProducts, 0, $limit);
        }
        
        // If not enough products with badges, fill with newest products
        $remainingSlots = $limit - $featuredCount;
        if ($remainingSlots > 0) {
            $newestQuery = "SELECT p.product_id as id, p.product_title as name, 
                         COALESCE(b.brand_name, 'Unknown') as brand, p.product_cat as category_id, 
                         p.product_price as price, 
                         10 as stock, 
                         COALESCE(p.product_desc, '') as description, 
                         COALESCE(p.product_image, '') as image,
                         COALESCE(c.cat_name, 'Uncategorized') as category,
                         COALESCE(p.product_keywords, '') as keywords
                  FROM products p 
                  LEFT JOIN categories c ON p.product_cat = c.cat_id 
                  LEFT JOIN brands b ON p.product_brand = b.brand_id 
                  WHERE (p.product_keywords NOT LIKE '%badge:Bestseller%' 
                     AND p.product_keywords NOT LIKE '%badge:Featured%'
                     AND p.product_keywords NOT LIKE '%badge:Popular%'
                     AND p.product_keywords NOT LIKE '%badge:bestseller%'
                     AND p.product_keywords NOT LIKE '%badge:featured%'
                     AND p.product_keywords NOT LIKE '%badge:popular%')
                     OR p.product_keywords IS NULL
                     OR p.product_keywords = ''
                  ORDER BY p.product_id DESC 
                  LIMIT " . $remainingSlots;
            $newestResults = $this->db->read($newestQuery);
            
            if (is_array($newestResults)) {
                // Extract badge information for newest products
                foreach ($newestResults as &$product) {
                    $keywords = $product['keywords'] ?? '';
                    $badge = '';
                    if (stripos($keywords, 'badge:') !== false) {
                        $parts = explode('badge:', $keywords);
                        if (isset($parts[1])) {
                            $badgeParts = explode(',', trim($parts[1]));
                            $badge = trim($badgeParts[0]);
                        }
                    }
                    $product['badge'] = $badge;
                    unset($product['keywords']); // Remove keywords from output
                }
                $featuredProducts = array_merge($featuredProducts, $newestResults);
            }
        }
        
        // Extract badge information for featured products if not already done
        foreach ($featuredProducts as &$product) {
            if (!isset($product['badge'])) {
                $keywords = $product['keywords'] ?? '';
                $badge = '';
                if (stripos($keywords, 'badge:') !== false) {
                    $parts = explode('badge:', $keywords);
                    if (isset($parts[1])) {
                        $badgeParts = explode(',', trim($parts[1]));
                        $badge = trim($badgeParts[0]);
                    }
                }
                $product['badge'] = $badge;
                unset($product['keywords']);
            }
        }
        
        // Return up to the limit
        return array_slice($featuredProducts, 0, $limit);
    }
}
?>
