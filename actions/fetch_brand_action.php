<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ob_start(); // Start output buffering

require_once __DIR__ . '/../settings/db_cred.php';
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/brand_controller.php';

// Check admin without redirecting (require_admin redirects on failure)
if (!is_admin()) {
    ob_end_clean(); // Clear any output
    header('Content-Type: application/json');
    json_response(false, 'Unauthorized: Admin access required', []);
    exit;
}

ob_end_clean(); // Clear any output before JSON response
header('Content-Type: application/json');

try {
    // Lab requirement: Display brands created by the logged-in user only, organized by category
    $user = get_current_customer();
    $user_id = $user['customer_id'] ?? null;

    if (!$user_id) {
        json_response(false, 'User not logged in', []);
        exit;
    }

    $controller = new BrandController();
    $brands = $controller->getAll($user_id);

    json_response(true, 'Brands fetched', $brands);
} catch (Exception $e) {
    error_log("Error in fetch_brand_action.php: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    json_response(false, 'Error loading brands: ' . $e->getMessage(), []);
} catch (Error $e) {
    error_log("Fatal error in fetch_brand_action.php: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    json_response(false, 'Fatal error: ' . $e->getMessage(), []);
}
?>

