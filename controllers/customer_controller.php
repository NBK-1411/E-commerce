<?php
// File: auth_lab_dbforlab/controllers/customer_controller.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../classes/customer_class.php';

/** REGISTER
 * Expects keys: name, email, password, country, city, contact
 * Returns [bool, string]
 */
function register_customer_ctr(array $p): array {
  $name    = trim($p['name'] ?? '');
  $email   = strtolower(trim($p['email'] ?? ''));
  $pass    = (string)($p['password'] ?? '');
  $country = trim($p['country'] ?? '');
  $city    = trim($p['city'] ?? '');
  $contact = trim($p['contact'] ?? '');
  $role    = isset($p['role']) ? (int)$p['role'] : 2;

  if ($name==='' || $email==='' || $pass==='' || $country==='' || $city==='' || $contact==='') {
    return [false, 'All fields are required'];
  }
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return [false, 'Invalid email'];
  if (strlen($pass) < 8) return [false, 'Password must be at least 8 characters'];

  $m = new Customer();
  if ($m->email_exists($email)) return [false, 'Email already exists'];

  $hash = password_hash($pass, PASSWORD_DEFAULT);
  $ok = $m->add($name,$email,$hash,$country,$city,$contact,$role,null);
  return $ok ? [true, 'Registered'] : [false, 'Registration failed'];
}

/** LOGIN
 * Returns [bool, array|string]
 *  - on success: ['id'=>int,'role'=>int,'message'=>'Logged in']
 *  - on failure: 'reason'
 */
function login_customer_ctr(string $email, string $password): array {
  $email = strtolower(trim($email));
  if ($email==='' || $password==='') return [false, 'Email and password are required'];

  $m = new Customer();
  $row = $m->get_by_email($email);
  if (!$row) return [false, 'Invalid email or password'];

  $hash = $row['customer_pass'] ?? null;
  if (!$hash || !password_verify($password, $hash)) return [false, 'Invalid email or password'];

  if (session_status() === PHP_SESSION_NONE) { session_start(); }
  session_regenerate_id(true);

  $id   = (int)($row['customer_id'] ?? 0);
  $role = (int)($row['user_role'] ?? 2);
  $name = (string)($row['customer_name'] ?? '');
  $mail = (string)($row['customer_email'] ?? '');

  $_SESSION['customer_id'] = $id;
  $_SESSION['customer'] = [
    'id'    => $id,
    'name'  => $name,
    'email' => $mail,
    'role'  => $role, // numeric, 1 = admin
  ];

  return [true, ['id'=>$id, 'role'=>$role, 'message'=>'Logged in']];
}
?>
