<?php
class View {    
    
    protected $viewFile;
    protected $route;
    

    
    //establish view location on object creation
    public function __construct($route) {

        $this->route = $route;
        $this->viewFile = "views/" . $route->getControllerName() . "/" . $route->getActionName() . ".php";

        $this->commonInit();
    }

    private function commonInit(){

    }

               
    //output the view
    public function output($data, $template = "base") {

        //print_r($this->data);
        
        $templateFile = "views/".$template.".php";
        
        if (file_exists($this->viewFile)) {
            if ($template) {
                //include the full template
                if (file_exists($templateFile)) {
                    require($templateFile);
                } else {
                    require("views/error/badtemplate.php");
                }
            } else {
                //we're not using a template view so just output the method's view directly
                require($this->viewFile);
            }
        } else {
            require("views/error/badview.php");
        }
        
    }

    public function getLink($controllerName, $actionName=""){
        return array(
                "href" => $this->url($controllerName),
                "active" => $this->route->getControllerName() == $controllerName,
            );
    }

    public function url($controller, $action = "", $params = array()){
        return $this->route->url($controller, $action, $params);
    }
}

?>