<?php
// File: auth_lab_dbforlab/actions/login_action.php
// NOTE: AJAX endpoint—always returns JSON; frontend will redirect after parsing.
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../controllers/customer_controller.php';

$email    = isset($_POST['email']) ? trim((string)$_POST['email']) : '';
$password = isset($_POST['password']) ? (string)$_POST['password'] : '';

[$ok, $payload] = login_customer_ctr($email, $password);

if ($ok) {
  // $payload should be ['id'=>int,'role'=>int,'message'=>'Logged in']
  echo json_encode(['success' => true, 'data' => $payload], JSON_UNESCAPED_UNICODE);
} else {
  echo json_encode(['success' => false, 'message' => (string)$payload], JSON_UNESCAPED_UNICODE);
}
