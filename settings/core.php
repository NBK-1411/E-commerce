<?php
session_start();
function is_logged_in(){ return isset($_SESSION['customer_id']); }
function current_user(){ return $_SESSION['customer'] ?? null; }
function require_login(){ if(!is_logged_in()){ header('Location: /public/login.php'); exit; } }
?>