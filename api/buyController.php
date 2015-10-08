<?php

class buyController {
  private $db_connection = null;
  public $errors = array();

  public function isUserLoggedIn()
  {
    session_start();
    if (isset($_SESSION['user_login_status']) AND $_SESSION['user_login_status'] == 1) {
      return true;
    }
    // default return
    return false;
  }
  private function buy(){
    if(isset($_GET['id']) && isset($_GET['qty']))
    {

      $this->db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
      // change character set to utf8 and check it
      if (!$this->db_connection->set_charset("utf8")) {
        $this->errors[] = $this->db_connection->error;
      }
      // if no connection errors (= working database connection)
      if (!$this->db_connection->connect_errno) {
        $id = $this->db_connection->real_escape_string($_GET['id']);
        $qty = $this->db_connection->real_escape_string($_GET['qty']);


        $sql = "SELECT  *
        FROM products
        WHERE id = '" . $id . "';";
        $result = $this->db_connection->query($sql);
        if ($result->num_rows == 1) {
          $result_row = $result->fetch_object();
          $quantity = $result_row->quantity;
          $price = $result_row->price;
          if($quantity >=$qty )
          {
            $quantity -= $qty;// $num;
            $sql = "UPDATE products SET quantity='". $quantity."' WHERE id='". $id ."';";
            $result = $this->db_connection->query($sql);

            if ($result) {
              //success
              $price *= $qty;
              $created_date = date("Y-m-d H:i:s");
              $sql = "INSERT INTO history (user_id,product_id, price, quantity,date)
              VALUES('" . $_SESSION['user_email'] . "','" . $id . "', '" . $price . "', '" . $qty . "', '" . $created_date . "');";
              $result = $this->db_connection->query($sql);
              if($result)
              {

              }


            }
          }
        }


      }
    }
  }
  public function getRequest() {

    if($this->isUserLoggedIn()){
      $this->buy();
      $success['user'] = $_SESSION['user_first_name'];
      $response['success'] =$success;
    }
    else {
      $error['message'] = 'user not logged in';
      $response['error'] =$error;
    }

    return $response;
  }
}
