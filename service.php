<?php
require("config/config.php");

//load the required classes
require("classes/route.php");
require("classes/basecontroller.php");  
require("classes/view.php");
require("classes/loader.php");

require 'models/database.php';
require 'models/user.php';
require 'models/company.php';
require 'models/data.php';
require 'models/data_maintenance.php';
require 'models/categoryKPI.php';


//error_reporting (0);
mb_internal_encoding('UTF-8');

date_default_timezone_set("Asia/Tokyo");

ob_start();
session_start();

try {
	
	//merge data input to $_POST
	$_POST = array_merge($_POST, (array)json_decode(file_get_contents("php://input")));

	$route = new Route(array( 'json' => true));

	//Check login
	$currentUser = User::getCurrentUser();
	if ($currentUser == null && $route->getControllerName() != 'authenticate') {

	    $res = array('success' => false,
	        'code' => ERR_NOT_YET_LOGIN,
	        'message' => _('エラーが発生しました。再度ログインをお願い致します。'),
	    );

	    echo json_encode($res);

	    return;
	}


	$loader = new Loader($route); //create the loader object
	$controller = $loader->createController(); //creates the requested controller object based on the 'controller' URL value
	$controller->executeAction(); //execute the requested controller's requested method based on the 'action' URL value. Controller methods output a View.
} catch (Exception $e) {
	 $res = array('success' => false,
        'code' => ERR_SYSTEM,
        'message' => __('System error'),
    );

    echo json_encode($res);

    return;
}

?>