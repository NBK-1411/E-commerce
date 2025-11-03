<?php
/**
 * Migration script to add product_categories junction table
 * This allows products to belong to multiple categories
 */

require_once __DIR__ . '/../settings/db_cred.php';
require_once __DIR__ . '/../settings/db_class.php';

try {
    $db = new Database();
    $db->connect();
    
    echo "Creating product_categories junction table...\n";
    
    // Create the junction table
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
    
    $result = $db->write($createTableQuery);
    
    if ($result['success']) {
        echo "✓ product_categories table created successfully\n";
    } else {
        echo "✗ Failed to create product_categories table: " . $result['message'] . "\n";
        exit(1);
    }
    
    // Migrate existing data from products.product_cat to product_categories
    echo "\nMigrating existing product-category relationships...\n";
    
    $migrateQuery = "INSERT IGNORE INTO product_categories (product_id, category_id)
                     SELECT product_id, product_cat 
                     FROM products 
                     WHERE product_cat IS NOT NULL AND product_cat > 0";
    
    $migrateResult = $db->write($migrateQuery);
    
    if ($migrateResult['success']) {
        echo "✓ Existing product-category relationships migrated\n";
    } else {
        echo "⚠ Warning: Migration of existing data may have failed: " . $migrateResult['message'] . "\n";
    }
    
    echo "\nMigration completed successfully!\n";
    echo "Products can now belong to multiple categories.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}

