<?php

abstract class BaseController {
    
    protected $route; //Route
    protected $urlValues;
    protected $view;
	protected $data = array(
            'success' => true,
            'message' => "",
        );
    
    public function __construct($route, $urlValues) {
        $this->route = $route;
        $this->urlValues = $urlValues;
                
        //establish the view object
        $this->view = new View($route);
    }
        
    //executes the requested method
    public function executeAction() {
        return $this->{$this->route->getActionName()}();
    }
	
	public function render($template = 'base'){
		$this->view->output($this->data, $template);
	}

    public function redirectTo($url){
        /* Redirect browser */
        header("Location: $url");
        /* Make sure that code below does not get executed when we redirect. */
        exit;
    }
	
	public function addError($error){
        $this->data['success'] = false;
        if ($this->data['message'] != "") {
            $this->data['message'] = $this->data['message'] . ", " . $error; 
        }
        else{
            $this->data['message'] = $error;
        }
    }

    public function addMessage($mess){
        if (isset($this->data['message']) && $this->data['message'] !="") {
            $this->data['message'] = $this->data['message'] . ", " . $mess; 
        }
        else{
            $this->data['message'] = $mess;
        } 
    }

    public function setData($name,$val) {
        $this->data[$name] = $val;
    }
    
    //returns the requested property value
    public function getData($name) {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        } else {
            return null;
        }
    }
}

?>
