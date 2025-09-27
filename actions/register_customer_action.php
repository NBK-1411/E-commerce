<?php
// File: auth_lab_dbforlab/actions/register_customer_action.php
// NOTE: AJAX endpoint—always returns JSON; frontend will redirect.
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../controllers/customer_controller.php';

try {
  $ct = $_SERVER['CONTENT_TYPE'] ?? '';
  if (is_string($ct) && stripos($ct, 'application/json') !== false) {
    $raw = file_get_contents('php://input');
    $in  = json_decode($raw, true) ?: [];
  } else {
    $in = $_POST;
  }

  $payload = [
    'name'     => trim($in['full_name'] ?? $in['customer_name'] ?? $in['name'] ?? ''),
    'email'    => trim($in['email'] ?? ''),
    'password' => (string)($in['password'] ?? ''),
    'country'  => trim($in['country'] ?? ''),
    'city'     => trim($in['city'] ?? ''),
    'contact'  => trim($in['contact'] ?? $in['contact_number'] ?? $in['phone'] ?? ''),
  ];

  [$ok, $msg] = register_customer_ctr($payload);
  echo json_encode([
    'success' => $ok,
    'message' => $msg,
  ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
  echo json_encode(['success' => false, 'message' => 'Server error during registration']);
}
