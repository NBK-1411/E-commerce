<?php
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/../controllers/customer_controller.php';
$email=$_POST['email']??''; $password=$_POST['password']??'';
list($ok,$msg)=login_customer_ctr($email,$password);
echo json_encode($ok?['status'=>'success','message'=>'Logged in']:['status'=>'error','message'=>$msg]);
?>