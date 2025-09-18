<?php
require_once __DIR__ . '/../settings/db_class.php';
class Customer {
  private $db;
  function __construct(){ $this->db = new DB(); }
  function add($name,$email,$hash,$country,$city,$contact,$role=2,$image=null){
    $sql = "INSERT INTO customer (customer_name,customer_email,customer_pass,customer_country,customer_city,customer_contact,customer_image,user_role)
            VALUES (?,?,?,?,?,?,?,?)";
    return $this->db->write($sql, [$name,$email,$hash,$country,$city,$contact,$image,$role], "sssssssi");
  }
  function get_by_email($email){
    $res = $this->db->read("SELECT * FROM customer WHERE customer_email=?", [$email], "s");
    return $res[0] ? ($res[1][0] ?? null) : null;
  }
}
?>