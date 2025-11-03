<?php
/**
 * Script to assign images to specific categories
 * Run this once to populate category images
 */

require_once __DIR__ . '/../settings/db_cred.php';
require_once __DIR__ . '/../settings/db_class.php';

// Map categories to images based on user requirements
$categoryImages = [
    'Luxury' => 'public/luxury-perfume-bottles-on-elegant-marble-surface-w.jpg',
    'Unisex Fragrances' => 'public/modern-unisex-perfume-bottles-minimalist-design.jpg',
    'Men\'s Fragrances' => 'public/sophisticated-men-s-cologne-bottles-with-woody-ele.jpg',
    'Women\'s Fragrances' => 'public/elegant-women-s-perfume-bottles-with-floral-elemen.jpg'
];

try {
    $db = new Database();
    $db->connect();
    
    // Check if category_image column exists
    $checkQuery = "SHOW COLUMNS FROM categories LIKE 'category_image'";
    $columns = $db->read($checkQuery);
    
    if (empty($columns)) {
        echo "Adding category_image column to categories table...\n";
        $alterQuery = "ALTER TABLE categories ADD COLUMN category_image VARCHAR(255) DEFAULT NULL";
        $result = $db->write($alterQuery);
        if (!$result['success']) {
            echo "Warning: Failed to add category_image column\n";
        }
    }
    
    echo "Assigning images to categories...\n";
    $updated = 0;
    
    foreach ($categoryImages as $categoryName => $imagePath) {
        // Check if category exists
        $checkQuery = "SELECT cat_id FROM categories WHERE cat_name = ?";
        $existing = $db->read($checkQuery, 's', [$categoryName]);
        
        if (!empty($existing)) {
            // Update category with image
            $updateQuery = "UPDATE categories SET category_image = ? WHERE cat_name = ?";
            $result = $db->write($updateQuery, 'ss', [$imagePath, $categoryName]);
            
            if ($result['success']) {
                echo "✓ Assigned image to: {$categoryName}\n";
                $updated++;
            } else {
                echo "✗ Failed to assign image to: {$categoryName} - {$result['message']}\n";
            }
        } else {
            echo "- Category not found: {$categoryName}\n";
        }
    }
    
    echo "\nSummary:\n";
    echo "  Updated: {$updated}\n";
    echo "  Total mappings: " . count($categoryImages) . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

