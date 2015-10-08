<?php

class Request {
    public $controller;
    public $method;

  //Get the class name of our controller
  //Example /user/?id=1 => user
  //and also the http request method (get or post)

  public function __construct() {
      $this->controller = explode('/', $_SERVER['REQUEST_URI'])[2];
      $this->method = $_SERVER['REQUEST_METHOD'];
  }
}
