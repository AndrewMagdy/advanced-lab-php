<?php

class userController {
  public function isUserLoggedIn()
   {
        session_start();
       if (isset($_SESSION['user_login_status']) AND $_SESSION['user_login_status'] == 1) {
           return true;
       }
       // default return
       return false;
   }

  public function getRequest() {

    if($this->isUserLoggedIn()){
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
