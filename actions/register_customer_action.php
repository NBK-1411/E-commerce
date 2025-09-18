<?php
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/../controllers/customer_controller.php';
list($ok,$msg)=register_customer_ctr($_POST);
echo json_encode($ok?['status'=>'success','message'=>'Registered']:['status'=>'error','message'=>$msg]);
?>