<?php

class historyController {
  private $db_connection = null;

  public function isUserLoggedIn()
  {
    session_start();
    if (isset($_SESSION['user_login_status']) AND $_SESSION['user_login_status'] == 1) {
      return true;
    }
    // default return
    return false;
  }

  private function getHistory()
  {
    $this->db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    // change character set to utf8 and check it
    if (!$this->db_connection->set_charset("utf8")) {
      $this->errors[] = $this->db_connection->error;
    }
    if (!$this->db_connection->connect_errno) {
      $sql = "SELECT  * FROM history where user_id ='". $_SESSION['user_email'] ."' ;";
      $result = $this->db_connection->query($sql);
      while($r = mysqli_fetch_assoc($result)) {
        $rows[] = $r;
      }
      if (isset($rows)){
      $response['success'] = $rows;
      return $response ;
      }
      else {
        $response['success'] = [];
        return $response ;
      }
    }
  }
  public function getRequest() {

    if($this->isUserLoggedIn()){
      $response = $this->getHistory();

    }
    else {
      $error['message'] = 'user not logged in';
      $response['error'] =$error;
    }
    return $response;
  }

}
