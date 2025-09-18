<?php
require_once __DIR__ . '/../classes/customer_class.php';
function register_customer_ctr($payload){
  foreach(['name','email','password','country','city','contact'] as $f){ if(empty($payload[$f])) return [false,"Missing field: $f"]; }
  $name=trim($payload['name']); $email=strtolower(trim($payload['email']));
  $password=$payload['password']; $country=trim($payload['country']); $city=trim($payload['city']); $contact=trim($payload['contact']);
  $role=isset($payload['role'])?(int)$payload['role']:2; $image=$payload['image'] ?? null;
  if(!filter_var($email, FILTER_VALIDATE_EMAIL)) return [false,"Invalid email"];
  if(strlen($password)<8) return [false,"Password must be at least 8 characters"];
  if(!preg_match('/^[0-9+\-\s]{7,20}$/',$contact)) return [false,"Invalid contact number"];
  $m=new Customer(); if($m->get_by_email($email)) return [false,"Email already exists"];
  $hash=password_hash($password,PASSWORD_DEFAULT);
  $res=$m->add($name,$email,$hash,$country,$city,$contact,$role,$image);
  return $res[0] ? [true,$res[1]] : [false,"Registration failed"];
}
function login_customer_ctr($email,$password){
  $m=new Customer(); $row=$m->get_by_email(strtolower(trim($email)));
  if(!$row) return [false,"Invalid email or password"];
  if(!password_verify($password,$row['customer_pass'])) return [false,"Invalid email or password"];
  $_SESSION['customer_id']=$row['customer_id'];
  $_SESSION['customer']=['id'=>$row['customer_id'],'name'=>$row['customer_name'],'email'=>$row['customer_email'],'role'=>$row['user_role']];
  return [true,$row['customer_id']];
}
?>