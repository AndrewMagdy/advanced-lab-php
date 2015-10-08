<?php

class loginController {
  private $db_connection = null;
  public $errors = array();
  //Taken from https://github.com/panique/php-login-minimal/
  private function dologinWithPostData()
    {
        session_start();
        // check login form contents
        if (empty($_POST['user_email'])) {
            $this->errors[] = "user_email field was empty.";
        } elseif (empty($_POST['user_password'])) {
            $this->errors[] = "Password field was empty.";
        } elseif (!empty($_POST['user_email']) && !empty($_POST['user_password'])) {
            // create a database connection, using the constants from config/db.php (which we loaded in index.php)
            $this->db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            // change character set to utf8 and check it
            if (!$this->db_connection->set_charset("utf8")) {
                $this->errors[] = $this->db_connection->error;
            }
            // if no connection errors (= working database connection)
            if (!$this->db_connection->connect_errno) {
                // escape the POST stuff
                $user_email = $this->db_connection->real_escape_string($_POST['user_email']);
                // database query, getting all the info of the selected user (allows login via email address in the
                // username field)
                $sql = "SELECT  email,first_name,password
                        FROM users
                        WHERE email = '" . $user_email . "';";
                $result_of_login_check = $this->db_connection->query($sql);
                // if this user exists
                if ($result_of_login_check->num_rows == 1) {
                    // get result row (as an object)
                    $result_row = $result_of_login_check->fetch_object();
                    // using PHP 5.5's password_verify() function to check if the provided password fits
                    // the hash of that user's password
                    if (password_verify($_POST['user_password'], $result_row->password)) {
                        // write user data into PHP SESSION (a file on your server)
                        $_SESSION['user_first_name'] = $result_row->first_name;
                        $_SESSION['user_email'] = $result_row->email;
                        $_SESSION['user_login_status'] = 1;
                    } else {
                        $this->errors[] = "Wrong password. Try again.";
                    }
                } else {
                    $this->errors[] = "This user does not exist.";
                }
            } else {
                $this->errors[] = "Database connection problem.";
            }
        }
    }

  public function postRequest() {

    $this->dologinWithPostData();
    if(empty($this->errors)){
      $success['message'] ='User Succesfully logged in';
      $response['success'] = $success ;
    }
    else {
      $error['message'] = $this->errors;;
      $response['error'] = $error;
    }
    return $response;
  }
}
