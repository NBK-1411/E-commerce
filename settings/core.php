<?php
/**
 * Core session & authorization helpers.
 * Works with your existing session structure:
 *   $_SESSION['customer_id'] = <int>
 *   $_SESSION['customer'] = ['id'=>..., 'name'=>..., 'email'=>..., 'role'=><int|string>]
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start(); // start session once, everywhere
}

/**
 * Returns true if a user session exists.
 */
function is_logged_in(): bool {
    return isset($_SESSION['customer_id']) && (int)$_SESSION['customer_id'] > 0;
}

/**
 * Returns true if the current user has admin privileges.
 * Your convention: user_role = 1 → admin; everything else → regular.
 * Also tolerant of string roles like "admin".
 */
function is_admin(): bool {
    $role = $_SESSION['customer']['role'] ?? 2; // default to regular user
    if (is_numeric($role)) {
        return ((int)$role) === 1;
    }
    $s = strtolower((string)$role);
    return in_array($s, ['admin', 'administrator', 'superadmin'], true);
}

/* ----------------- Optional convenience guards ----------------- */

/**
 * Require the user to be logged in for this page; otherwise redirect.
 */
function require_login(string $loginPath = '../public/login.php'): void {
    if (!is_logged_in()) {
        header("Location: {$loginPath}");
        exit;
    }
}

/**
 * Require the user to be an admin for this page; otherwise redirect.
 */
function require_admin(string $loginPath = '../public/login.php'): void {
    if (!is_logged_in() || !is_admin()) {
        header("Location: {$loginPath}");
        exit;
    }
}
