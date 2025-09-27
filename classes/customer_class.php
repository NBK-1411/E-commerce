<?php
// File: auth_lab_dbforlab/classes/customer_class.php
require_once __DIR__ . '/../settings/db_class.php';

class Customer {
  private $db;
  public function __construct(){ $this->db = new DB(); }

  /** Return first row for this email or null */
  public function get_by_email(string $email): ?array {
    list($ok, $rows) = $this->db->read(
      "SELECT * FROM customer WHERE customer_email = ? LIMIT 1",
      [$email], "s"
    );
    if ($ok && $rows) return $rows[0];
    return null;
  }

  /** Does any row exist for this email? */
  public function email_exists(string $email): bool {
    list($ok, $rows) = $this->db->read(
      "SELECT 1 FROM customer WHERE customer_email = ? LIMIT 1",
      [$email], "s"
    );
    return $ok && !empty($rows);
  }

  /**
   * Create a customer row (1=admin, 2=user). $image can be null.
   */
  public function add(
    string $name,
    string $email,
    string $password_hash,
    string $country,
    string $city,
    string $contact,
    int $role = 2,
    ?string $image = null
  ): bool {
    $sql = "INSERT INTO customer (
              customer_name, customer_email, customer_pass,
              customer_country, customer_city, customer_contact,
              customer_image, user_role
            ) VALUES (?,?,?,?,?,?,?,?)";

    return $this->db->write(
      $sql,
      [$name, $email, $password_hash, $country, $city, $contact, $image, $role],
      "sssssssi"
    );
  }
}
?>
