<?php
require_once __DIR__ . '/db_cred.php';

class DB {
  public $conn;

  function __construct(){
    $this->conn = mysqli_connect(SERVER, USERNAME, PASSWD, DATABASE);
    if (!$this->conn) {
      die('DB connection failed: ' . mysqli_connect_error());
    }
    mysqli_set_charset($this->conn, 'utf8mb4');
  }

  // SELECT
  function read($sql, $params = [], $types = ''){
    $stmt = mysqli_prepare($this->conn, $sql);
    if(!$stmt){ return [false, mysqli_error($this->conn)]; }

    if($params){
      if($types === ''){
        $t = '';
        foreach($params as $p){
          $t .= is_int($p) ? 'i' : (is_float($p) ? 'd' : 's');
        }
        $types = $t;
      }
      mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    if(!mysqli_stmt_execute($stmt)){
      $err = mysqli_stmt_error($stmt);
      mysqli_stmt_close($stmt);
      return [false, $err];
    }

    $result = mysqli_stmt_get_result($stmt);
    $rows = [];
    if($result){
      while($r = mysqli_fetch_assoc($result)){ $rows[] = $r; }
      mysqli_free_result($result);
    }
    mysqli_stmt_close($stmt);
    return [true, $rows];
  }

  // INSERT / UPDATE / DELETE
  function write($sql, $params = [], $types = ''){
    $stmt = mysqli_prepare($this->conn, $sql);
    if(!$stmt){ return false; }

    if($params){
      if($types === ''){
        $t = '';
        foreach($params as $p){
          $t .= is_int($p) ? 'i' : (is_float($p) ? 'd' : 's');
        }
        $types = $t;
      }
      mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return (bool)$ok;
  }
}
?>
