<?php

require("config/config.php");


//load the required classes
require "classes/route.php";
require "classes/loader.php";
require "classes/basecontroller.php";
require "classes/view.php";

require "controllers/kpi.php";

require 'models/database.php';
require 'models/user.php';
require 'models/data.php';
require 'models/data_maintenance.php';
require 'models/company.php';
require 'models/categoryKPI.php';
require 'models/excel.php';

// ---LANGUAGE I18N ---
//require_once('helpers/gettext/gettext.inc');

//define("DEFAULT_LOCALE", "ja");

// error 500
function handleException(Exception $ex) {
	$log_path = __DIR__ . DIRECTORY_SEPARATOR . "log" . DIRECTORY_SEPARATOR . "error.log";
	$messageError = PHP_EOL . $ex->getMessage() . PHP_EOL . $ex->getTraceAsString(). PHP_EOL. PHP_EOL;
	 error_log($messageError, 3, $log_path );
  ob_end_clean(); # try to purge content sent so far
  header('HTTP/1.1 500 Internal Server Error');
  // echo "Uncaught exception class=" . get_class($ex) . " message=" . $ex->getMessage() . " line=" . $ex->getLine() . "file=" . $ex->getFile();
}

set_exception_handler('handleException');

// error handler function

//xdebug_disable();
function myErrorHandler($errno, $errstr, $errfile, $errline)
{
	$log_path = __DIR__ . DIRECTORY_SEPARATOR . "log" . DIRECTORY_SEPARATOR . "error.log";
	$date = "[" . date("Y/m/d H:i:s") ."] ";
	$message = "";
    if (!(error_reporting() & $errno)) {
        // This error code is not included in error_reporting
        return;
    }

    switch ($errno) {
    case E_USER_ERROR:
        $message .= "<b>My ERROR</b> [$errno] $errstr<br />\n";
        $message .= "  Fatal error on line $errline in file $errfile";
        $message .= ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
        $message .= "Aborting...<br />\n";
        exit(1);
        break;

    case E_USER_WARNING:
        $message .= "<b>My WARNING</b> [$errno] $errstr<br />\n";
        $message .= "  Fatal error on line $errline in file $errfile";
        $message .= ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
        break;

    case E_USER_NOTICE:
        $message .= "<b>My NOTICE</b> [$errno] $errstr<br />\n";
        $message .= "  Fatal error on line $errline in file $errfile";
        $message .= ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
        break;

    default:
        $message .= "<b>Unknown error type</b> [$errno] $errstr<br />\n";
        $message .= "  Fatal error on line $errline in file $errfile";
        $message .= ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
        break;
    }
		error_log($date . $message, 3, $log_path );

    /* Don't execute PHP internal error handler */
    return true;
	
}


// set to the user defined error handler

$old_error_handler = set_error_handler("myErrorHandler");

error_reporting (E_ALL ^ E_NOTICE);
mb_internal_encoding('UTF-8');

ob_start();
session_start();

$route = new Route();

//Check login

$currentUser = User::getCurrentUser();
if ($currentUser == null && $route->getControllerName() != "authenticate") {
    $redirectTo = $route->url("authenticate","login",array('location' => $_SERVER["REQUEST_URI"]));
    header("Location: $redirectTo");
    exit();
}

$maintain_controler_arr = array("authenticate","company","department","user","category","employee","inquirymanager","release","termsmanager");
$admin_controler_arr = array("authenticate","home","list","kpiregistration","kpiresult","importexport","department","user","employee","inquiry","versioninfo","terms");
$member_controler_arr = array("authenticate","home","list","kpiregistration","kpiresult","importexport","user","employee","inquiry","versioninfo","terms");

$controler = $route->getControllerName();

if (User::getCurrentUser()->maintenance_flag == true){
    
    if(!in_array($route->getControllerName(), $maintain_controler_arr)){
        $controler = "company";
    }
}
elseif (User::getCurrentUser()->admin_flag == 1){
    
    if(!in_array($route->getControllerName(), $admin_controler_arr)){
        $controler = "home";
    }
}

else{
    $controler_arr = array("authenticate","home","list","kpiregistration","kpiresult","importexport","inquiry","versioninfo","terms");
    if(!in_array($controler, $member_controler_arr)){
        $controler = "home";
    }
}

if ($controler != $route->getControllerName()){
    $redirectTo = $route->url($controler);
    header("Location: $redirectTo");
    exit();
}

$loader = new Loader($route); //create the loader object
$controller = $loader->createController(); //creates the requested controller object based on the 'controller' URL value
$controller->executeAction(); //execute the requested controller's requested method based on the 'action' URL value. Controller methods output a View.

?>