<?php

class Loader {
    
    private $route;
    
    //store the URL request values on object creation
    public function __construct($route) {
        $this->urlValues = $_GET;
        $this->route = $route;
    }
                  
    //factory method which establishes the requested controller as an object
    public function createController() {
        //check our requested controller's class file exists and require it if so
        if (file_exists("controllers/" . $this->route->getControllerName() . ".php")) {
            require("controllers/" . $this->route->getControllerName() . ".php");
            $controllerClass = ucfirst(strtolower($this->route->getControllerName())) . "Controller";
            if (class_exists($controllerClass)) {
                $parents = class_parents($controllerClass);
                
                //does the class inherit from the BaseController class?
                if (in_array("BaseController",$parents)) {   
                    //does the requested class contain the requested action as a method?
                    if (method_exists($controllerClass,$this->route->getActionName()))
                    {
                        return new $controllerClass($this->route,$this->urlValues);
                    }
                }
            }
        }

        require("controllers/error.php");
        $this->route->setControllerName("error");
        $this->route->setActionName("badurl");

        return new ErrorController($this->route,$this->urlValues);
    }
}

?>
