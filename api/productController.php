<?php

class productController {

  private $db_connection = null;

  public function getRequest() {
    $this->db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    // change character set to utf8 and check it
    if (!$this->db_connection->set_charset("utf8")) {
      $this->errors[] = $this->db_connection->error;
    }

    if(isset($_GET['id'])) {
      if (!$this->db_connection->connect_errno) {
        $id = $this->db_connection->real_escape_string($_GET['id']);
        $sql = "SELECT  * FROM products WHERE id = '" . $id . "';";
        $result = $this->db_connection->query($sql);
        $response['success'] =  $result->fetch_object();
        return $response ;
      }
    }
    else {
      if (!$this->db_connection->connect_errno) {
        $sql = "SELECT  *FROM products;";
        $result = $this->db_connection->query($sql);
        while($r = mysqli_fetch_assoc($result)) {
          $rows[] = $r;
        }
        $response['success'] = $rows;
        return $response ;
      }
    }
  }
}
