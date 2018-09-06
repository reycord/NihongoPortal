<?php 
class Route {

    private $controllerName;
    private $actionName;

    public $option = array(
            'controllerKey' => 'c',
            'actionKey' => 'a',
            'defaultControllerName' => 'home',
            'defaultActionName' => 'index',
            'json' => false,
        );

    public function __construct($opt= array()){
        $this->option = array_merge($this->option, $opt);

        $this->setControllerName($this->option['defaultControllerName']);
        if($this->option['json'] == false && isset($_GET[$this->option['controllerKey']])) {
            $this->setControllerName(strtolower($_GET[$this->option['controllerKey']]));
        }
        else if($this->option['json'] == true && ($_POST[$this->option['controllerKey']])) {
            $this->setControllerName(strtolower($_POST[$this->option['controllerKey']]));
        }

        $this->setActionName($this->option['defaultActionName']);
        if($this->option['json'] == false && isset($_GET[$this->option['actionKey']])) {
            $this->setActionName(strtolower($_GET[$this->option['actionKey']]));
        }
        else if($this->option['json'] == true && isset($_POST[$this->option['actionKey']])) {
            $this->setActionName(strtolower($_POST[$this->option['actionKey']]));
        }

    }

    public function setControllerName($value){
        $this->controllerName = $value;
    }

    public function getControllerName(){
        return $this->controllerName;
    }

    public function setActionName($value){
        if ($this->option['json'] ==  true) {
            $value = $value . "_json";
        }

        $this->actionName = $value;
    }

    public function getActionName(){
        return $this->actionName;
    }

    public function url($controller, $action ="", $params = array()) {
        $controller = urlencode($controller);
        $action = urlencode($action);
        $url = "index.php?" . $this->option['controllerKey'] . "=$controller";
        
        if ($action != "") {
            $url = $url . "&". $this->option['actionKey'] ."=$action";    
        }

        foreach ($params as $key => $value) {
            $value = urlencode($value);
            $url = $url . "&$key=$value";
        }
        return $url;
    }
}

?>