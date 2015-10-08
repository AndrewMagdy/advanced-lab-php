<?php
class logoutController {
  private function doLogout(){
        session_start();
        // delete the session of the user
        $_SESSION = array();
        session_destroy();
    }

    public function postRequest() {
      $this->doLogout();
      $success['message'] = "You have been logged out.";
      $response['success'] =$success;
      return $response;
    }

    public function getRequest() {
      $this->doLogout();
      $success['message'] = "You have been logged out.";
      $response['success'] =$success;
      return $response;
    }

}
