<?php
/**
 * Database Configuration
 * 
 * This file automatically detects if you're on localhost or production server
 * and uses the appropriate database credentials.
 * 
 * Production credentials are set as defaults (for live server).
 * Local credentials are used when running on localhost.
 */

// Detect if we're running on localhost
function is_localhost() {
    // Check various ways to detect localhost
    $hostname = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
    $server_name = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '';
    $remote_addr = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
    
    // Check if it's localhost, 127.0.0.1, or local IP
    $is_local = (
        $hostname === 'localhost' ||
        $hostname === '127.0.0.1' ||
        $hostname === 'localhost:8080' ||
        $hostname === 'localhost:8000' ||
        strpos($hostname, 'localhost') !== false ||
        $server_name === 'localhost' ||
        $server_name === '127.0.0.1' ||
        $remote_addr === '127.0.0.1' ||
        $remote_addr === '::1' ||
        // Check if running on XAMPP (common localhost paths)
        (isset($_SERVER['DOCUMENT_ROOT']) && (
            strpos($_SERVER['DOCUMENT_ROOT'], 'xampp') !== false ||
            strpos($_SERVER['DOCUMENT_ROOT'], 'htdocs') !== false ||
            strpos($_SERVER['DOCUMENT_ROOT'], 'www') === false // XAMPP usually doesn't have 'www' in path
        ))
    );
    
    return $is_local;
}

// PRODUCTION SERVER CREDENTIALS (default - for live server)
// Update these with your actual production database credentials
$production_config = [
    'DB_HOST' => 'localhost',
    'DB_USER' => 'nana.hayford',  // Update this with your cPanel database username
    'DB_PASS' => 'cogitoergosam',  // Update this with your cPanel database password
    'DB_NAME' => 'ecoomerce_2025A_nana_hayford'  // Your production database name
];

// LOCAL DEVELOPMENT CREDENTIALS (for XAMPP/localhost)
$local_config = [
    'DB_HOST' => 'localhost',
    'DB_USER' => 'root',
    'DB_PASS' => '',
    'DB_NAME' => 'dbforlab'
];

// Use local config if on localhost, otherwise use production config
$config = is_localhost() ? $local_config : $production_config;

// Define constants
define('DB_HOST', $config['DB_HOST']);
define('DB_USER', $config['DB_USER']);
define('DB_PASS', $config['DB_PASS']);
define('DB_NAME', $config['DB_NAME']);
?>
