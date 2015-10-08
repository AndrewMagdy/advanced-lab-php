<?php

class registerController {
  private $db_connection = null;
  public $errors = array();

  private function uploadimg(){
    if(isset($_FILES['userfile']['name'])){
      $uploaddir = '../uploads/';
      $uploadfile = $uploaddir . basename($_FILES['userfile']['name']);
      if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
      } else {
        $this->errors[] = "Image Upload Failed";
      }

    }
    else {
      $this->errors[] = "Image Upload Failed";
    }
  }

  //Taken from https://github.com/panique/php-login-minimal/
  private function registerNewUser()
  {
    if (empty($_POST['first_name'])) {
      $this->errors[] = "Empty first_name";
    }elseif (empty($_POST['last_name'])) {
      $this->errors[] = "Empty last_name";
    } elseif (empty($_POST['user_password_new']) || empty($_POST['user_password_repeat'])) {
      $this->errors[] = "Empty Password";
    } elseif ($_POST['user_password_new'] !== $_POST['user_password_repeat']) {
      $this->errors[] = "Password and password repeat are not the same";
    } elseif (strlen($_POST['user_password_new']) < 6) {
      $this->errors[] = "Password has a minimum length of 6 characters";
    } elseif (strlen($_POST['first_name']) > 64 || strlen($_POST['first_name']) < 2) {
      $this->errors[] = "first_name cannot be shorter than 2 or longer than 64 characters";
    } elseif (!preg_match('/^[a-z\d]{2,64}$/i', $_POST['first_name'])) {
      $this->errors[] = "first_name does not fit the name scheme: only a-Z and numbers are allowed, 2 to 64 characters";
    } elseif (strlen($_POST['last_name']) > 64 || strlen($_POST['last_name']) < 2) {
      $this->errors[] = "last_name cannot be shorter than 2 or longer than 64 characters";
    } elseif (!preg_match('/^[a-z\d]{2,64}$/i', $_POST['last_name'])) {
      $this->errors[] = "last_name does not fit the name scheme: only a-Z and numbers are allowed, 2 to 64 characters";
    } elseif (empty($_POST['user_email'])) {
      $this->errors[] = "Email cannot be empty";
    } elseif (strlen($_POST['user_email']) > 64) {
      $this->errors[] = "Email cannot be longer than 64 characters";
    } elseif (!filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL)) {
      $this->errors[] = "Your email address is not in a valid email format";
    } elseif (!empty($_POST['first_name'])
    && strlen($_POST['first_name']) <= 64
    && strlen($_POST['first_name']) >= 2
    && preg_match('/^[a-z\d]{2,64}$/i', $_POST['first_name'])
    && !empty($_POST['last_name'])
    && strlen($_POST['last_name']) <= 64
    && strlen($_POST['last_name']) >= 2
    && preg_match('/^[a-z\d]{2,64}$/i', $_POST['last_name'])
    && !empty($_POST['user_email'])
    && strlen($_POST['user_email']) <= 64
    && filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL)
    && !empty($_POST['user_password_new'])
    && !empty($_POST['user_password_repeat'])
    && ($_POST['user_password_new'] === $_POST['user_password_repeat'])
    ) {
      // create a database connection
      $this->db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
      // change character set to utf8 and check it
      if (!$this->db_connection->set_charset("utf8")) {
        $this->errors[] = $this->db_connection->error;
      }
      // if no connection errors (= working database connection)
      if (!$this->db_connection->connect_errno) {
        // escaping, additionally removing everything that could be (html/javascript-) code
        $first_name = $this->db_connection->real_escape_string(strip_tags($_POST['first_name'], ENT_QUOTES));
        $last_name = $this->db_connection->real_escape_string(strip_tags($_POST['last_name'], ENT_QUOTES));
        $user_email = $this->db_connection->real_escape_string(strip_tags($_POST['user_email'], ENT_QUOTES));
        $user_password = $_POST['user_password_new'];
        // crypt the user's password with PHP 5.5's password_hash() function, results in a 60 character
        // hash string. the PASSWORD_DEFAULT constant is defined by the PHP 5.5, or if you are using
        // PHP 5.3/5.4, by the password hashing compatibility library
        $user_password_hash = password_hash($user_password, PASSWORD_DEFAULT);
        // check if user or email address already exists
        $sql = "SELECT * FROM users WHERE email = '" . $user_email .  "';";
        $query_check_user_email = $this->db_connection->query($sql);
        if ($query_check_user_email->num_rows == 1) {
          $this->errors[] = "Sorry, that email address is already taken.";
        } else {
          // write new user's data into database
          $sql = "INSERT INTO users (first_name,last_name, password, email)
          VALUES('" . $first_name . "','" . $last_name . "', '" . $user_password_hash . "', '" . $user_email . "');";
          $query_new_user_insert = $this->db_connection->query($sql);
          // if user has been added successfully
          if ($query_new_user_insert) {
            $this->messages[] = "Your account has been created successfully. You can now log in.";
            $this->uploadimg();
          } else {
            $this->errors[] = "Sorry, your registration failed. Please go back and try again.";
          }
        }
      } else {
        $this->errors[] = "Sorry, no database connection.";
      }
    } else {
      $this->errors[] = "An unknown error occurred.";
    }
  }

  private function updateUser()
  {
    if (empty($_POST['first_name'])) {
      $this->errors[] = "Empty 1first_name";
    }elseif (empty($_POST['last_name'])) {
      $this->errors[] = "Empty last_name";
    } elseif (empty($_POST['user_password_new']) || empty($_POST['user_password_repeat'])) {
      $this->errors[] = "Empty Password";
    } elseif ($_POST['user_password_new'] !== $_POST['user_password_repeat']) {
      $this->errors[] = "Password and password repeat are not the same";
    } elseif (strlen($_POST['user_password_new']) < 6) {
      $this->errors[] = "Password has a minimum length of 6 characters";
    } elseif (strlen($_POST['first_name']) > 64 || strlen($_POST['first_name']) < 2) {
      $this->errors[] = "first_name cannot be shorter than 2 or longer than 64 characters";
    } elseif (!preg_match('/^[a-z\d]{2,64}$/i', $_POST['first_name'])) {
      $this->errors[] = "first_name does not fit the name scheme: only a-Z and numbers are allowed, 2 to 64 characters";
    } elseif (strlen($_POST['last_name']) > 64 || strlen($_POST['last_name']) < 2) {
      $this->errors[] = "last_name cannot be shorter than 2 or longer than 64 characters";
    } elseif (!preg_match('/^[a-z\d]{2,64}$/i', $_POST['last_name'])) {
      $this->errors[] = "last_name does not fit the name scheme: only a-Z and numbers are allowed, 2 to 64 characters";
    } elseif (empty($_POST['user_email'])) {
      $this->errors[] = "Email cannot be empty";
    } elseif (strlen($_POST['user_email']) > 64) {
      $this->errors[] = "Email cannot be longer than 64 characters";
    } elseif (!filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL)) {
      $this->errors[] = "Your email address is not in a valid email format";
    } elseif (!empty($_POST['first_name'])
    && strlen($_POST['first_name']) <= 64
    && strlen($_POST['first_name']) >= 2
    && preg_match('/^[a-z\d]{2,64}$/i', $_POST['first_name'])
    && !empty($_POST['last_name'])
    && strlen($_POST['last_name']) <= 64
    && strlen($_POST['last_name']) >= 2
    && preg_match('/^[a-z\d]{2,64}$/i', $_POST['last_name'])
    && !empty($_POST['user_email'])
    && strlen($_POST['user_email']) <= 64
    && filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL)
    && !empty($_POST['user_password_new'])
    && !empty($_POST['user_password_repeat'])
    && ($_POST['user_password_new'] === $_POST['user_password_repeat'])
    ) {
      // create a database connection
      $this->db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
      // change character set to utf8 and check it
      if (!$this->db_connection->set_charset("utf8")) {
        $this->errors[] = $this->db_connection->error;
      }
      // if no connection errors (= working database connection)
      if (!$this->db_connection->connect_errno) {
        // escaping, additionally removing everything that could be (html/javascript-) code
        $first_name = $this->db_connection->real_escape_string(strip_tags($_POST['first_name'], ENT_QUOTES));
        $last_name = $this->db_connection->real_escape_string(strip_tags($_POST['last_name'], ENT_QUOTES));
        $user_email = $this->db_connection->real_escape_string(strip_tags($_POST['user_email'], ENT_QUOTES));
        $user_password = $_POST['user_password_new'];
        // crypt the user's password with PHP 5.5's password_hash() function, results in a 60 character
        // hash string. the PASSWORD_DEFAULT constant is defined by the PHP 5.5, or if you are using
        // PHP 5.3/5.4, by the password hashing compatibility library
        $user_password_hash = password_hash($user_password, PASSWORD_DEFAULT);
        // check if user or email address already exists
        $sql = "SELECT * FROM users WHERE email = '" . $_SESSION['user_email'] .  "';";
        $query_check_user_email = $this->db_connection->query($sql);
        if ($query_check_user_email->num_rows != 1) {
          $this->errors[] = "Sorry, that email address is not taken.";
        } else {
          // write new user's data into database
          $stmt = $this->db_connection->prepare("UPDATE users SET first_name=?,last_name =?,password=? WHERE email =?;");
          $stmt->bind_param("sss", $firstname, $lastname, $email);
          $firstname = "John";
          $lastname = "Doe";
          $email = "john@example.com";
          $stmt->execute();
          $query_new_user_insert = $stmt->execute();
          // if user has been added successfully
          if ($query_new_user_insert) {
            $this->messages[] = "Your account has been created successfully. You can now log in.";
            $this->uploadimg();
          } else {
            $this->errors[] = "Sorry, your registration failed. Please go back and try again.";
          }
        }
      } else {
        $this->errors[] = "Sorry, no database connection.";
      }
    } else {
      $this->errors[] = "An unknown error occurred.";
    }
  }


  public function postRequest() {

    echo (isset($_POST['update']));
    if(isset($_POST['update']))
    {
      $this->updateUser();
    }
    else{
    $this->registerNewUser();
    }

    if(empty($this->errors)){
      $success['message'] ='User Succesfully Registered';
      $response['success'] = $success ;
    }
    else {
      $response['error']= $this->errors;
    }
    return $response;
  }
}
