<?php
require_once __DIR__ . '/db_cred.php';

class DB {
  public $conn;

  function __construct(){
    $this->conn = mysqli_connect(SERVER, USERNAME, PASSWD, DATABASE);
    if (!$this->conn) die('DB connection failed: '.mysqli_connect_error());
    mysqli_set_charset($this->conn,'utf8mb4');
  }

  function write($sql,$params=[],$types=''){
    $stmt = mysqli_prepare($this->conn,$sql);
    if(!$stmt) return [false,mysqli_error($this->conn)];
    if($params){
      if($types===''){
        for ($i=0; $i<count($params); $i++) { $types .= is_int($params[$i])?'i':'s'; }
      }
      mysqli_stmt_bind_param($stmt,$types,...$params); // ← fixed (PHP splat)
    }
    if(!mysqli_stmt_execute($stmt)) return [false,mysqli_stmt_error($stmt)];
    $id = mysqli_insert_id($this->conn);
    mysqli_stmt_close($stmt);
    return [true,$id];
  }

  function read($sql,$params=[],$types=''){
    $stmt = mysqli_prepare($this->conn,$sql);
    if(!$stmt) return [false,mysqli_error($this->conn)];
    if($params){
      if($types===''){
        for ($i=0; $i<count($params); $i++) { $types .= is_int($params[$i])?'i':'s'; }
      }
      mysqli_stmt_bind_param($stmt,$types,...$params); // ← fixed (PHP splat)
    }
    if(!mysqli_stmt_execute($stmt)) return [false,mysqli_stmt_error($stmt)];
    $res = mysqli_stmt_get_result($stmt);
    $rows = [];
    if($res){ while($r=mysqli_fetch_assoc($res)) $rows[]=$r; }
    mysqli_stmt_close($stmt);
    return [true,$rows];
  }
}
?>
