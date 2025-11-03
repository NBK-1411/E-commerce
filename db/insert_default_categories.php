<?php
/**
 * Script to insert default perfume shop categories
 * Run this once to populate the categories table with appropriate categories
 */

require_once __DIR__ . '/../settings/db_cred.php';
require_once __DIR__ . '/../settings/db_class.php';

// Categories appropriate for a perfume shop
$categories = [
    'Men\'s Fragrances',
    'Women\'s Fragrances',
    'Unisex Fragrances',
    'Eau de Parfum',
    'Eau de Toilette',
    'Eau de Cologne',
    'Luxury',
    'Designer',
    'Niche',
    'Floral',
    'Oriental',
    'Woody',
    'Fresh',
    'Citrus',
    'Spicy',
    'Aromatic'
];

try {
    $db = new Database();
    $db->connect();
    
    // Check if user_id column exists
    $checkQuery = "SHOW COLUMNS FROM categories LIKE 'user_id'";
    $columns = $db->read($checkQuery);
    $hasUserIdColumn = !empty($columns);
    
    // If user_id column doesn't exist, add it
    if (!$hasUserIdColumn) {
        echo "Adding user_id column to categories table...\n";
        $alterQuery = "ALTER TABLE categories ADD COLUMN user_id INT(11) DEFAULT NULL";
        $db->write($alterQuery);
    }
    
    echo "Inserting default categories...\n";
    $inserted = 0;
    $skipped = 0;
    
    foreach ($categories as $categoryName) {
        // Check if category already exists
        $checkQuery = "SELECT cat_id FROM categories WHERE cat_name = ?";
        $existing = $db->read($checkQuery, 's', [$categoryName]);
        
        if (empty($existing)) {
            // Insert category (without user_id for default categories)
            $insertQuery = "INSERT INTO categories (cat_name, user_id) VALUES (?, NULL)";
            $result = $db->write($insertQuery, 's', [$categoryName]);
            
            if ($result['success']) {
                echo "✓ Inserted: {$categoryName}\n";
                $inserted++;
            } else {
                echo "✗ Failed to insert: {$categoryName} - {$result['message']}\n";
            }
        } else {
            echo "- Already exists: {$categoryName}\n";
            $skipped++;
        }
    }
    
    echo "\n";
    echo "Summary:\n";
    echo "  Inserted: {$inserted}\n";
    echo "  Skipped (already exist): {$skipped}\n";
    echo "  Total categories: " . count($categories) . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

