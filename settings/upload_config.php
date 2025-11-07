<?php
/**
 * Upload Configuration for Multiple Environments
 * Auto-detects whether running on local XAMPP or school's server
 */

// Detect if we're on the school server (check for public_html in path)
$isSchoolServer = strpos(__DIR__, 'public_html') !== false;

if ($isSchoolServer) {
    // SCHOOL SERVER: uploads folder is INSIDE public_html (your setup)
    // Structure: /home/username/public_html/your-project/uploads/
    define('UPLOADS_BASE_PATH', dirname(__DIR__) . '/uploads');
    define('UPLOADS_WEB_PATH', 'uploads'); // Relative path
    define('IS_SCHOOL_SERVER', true);
    
    // Note: If you later want to move uploads OUTSIDE public_html for security:
    // 1. Create folder: mkdir ~/uploads
    // 2. Set permissions: chmod 775 ~/uploads
    // 3. Uncomment below and configure Apache Alias
    /*
    $uploadsOutside = dirname(dirname(__DIR__)) . '/uploads';
    if (is_dir($uploadsOutside)) {
        define('UPLOADS_BASE_PATH', $uploadsOutside);
        define('UPLOADS_WEB_PATH', '/uploads');
    }
    */
} else {
    // LOCAL XAMPP: uploads folder is inside project directory
    // Structure: /Applications/XAMPP/xamppfiles/htdocs/project/uploads/
    define('UPLOADS_BASE_PATH', dirname(__DIR__) . '/uploads');
    define('UPLOADS_WEB_PATH', 'uploads'); // Relative path
    define('IS_SCHOOL_SERVER', false);
}

// Ensure uploads directory exists
if (!is_dir(UPLOADS_BASE_PATH)) {
    // Try to create it
    @mkdir(UPLOADS_BASE_PATH, 0775, true);
}
?>

