<?php
require_once __DIR__ . '/settings/db_cred.php';
require_once __DIR__ . '/settings/core.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: index.php?error=invalid_email');
        exit;
    }

    $db = new Database();
    $db->connect();
    
    // Check if email already subscribed
    $query = "SELECT id FROM newsletter_subscribers WHERE email = ?";
    $result = $db->read($query, 's', [$email]);
    
    if (!empty($result)) {
        header('Location: index.php?error=already_subscribed');
        exit;
    }
    
    // Add to newsletter subscribers
    $query = "INSERT INTO newsletter_subscribers (email, subscribed_at) VALUES (?, NOW())";
    $result = $db->write($query, 's', [$email]);
    
    if ($result['success']) {
        // In production, send confirmation email here
        // mail($email, "Welcome to Essence", "Thank you for subscribing to our newsletter!");
        
        header('Location: index.php?subscribed=true');
        exit;
    } else {
        header('Location: index.php?error=subscription_failed');
        exit;
    }
} else {
    header('Location: index.php');
    exit;
}
?>
