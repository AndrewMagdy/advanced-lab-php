<?php
  require 'request.php';
  require 'userController.php';
  require 'loginController.php';
  require 'logoutController.php';
  require 'registerController.php';
  require 'mediaController.php' ;
  require 'view.php';
  require 'productController.php';
  require 'buyController.php';
  require 'historyController.php';

  require_once("db.php");

  $request = new Request();

  //Get the Controller name and the method (get or post)
  $controllerName = strtolower($request->controller).'Controller';
  $methodName = strtolower($request->method).'Request';
    //Make sure class exist
  if (class_exists($controllerName)) {

    //Intitalize the controller
    $controller = new $controllerName();

    //Make sure method exist
    if (method_exists($controller, $methodName)) {
        //call the method and send the respone to be rendered
        $response = $controller->$methodName();
        $view = new View();
        $view->renderJson($response);
    }
  }
