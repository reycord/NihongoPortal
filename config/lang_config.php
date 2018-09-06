<?php
global $g_lang;
global $g_messageList;
$g_messageList = array();
function getMessageById()
{
    $args = func_get_args();
    $key = array_shift($args);
    
	global $g_lang;
    if(isset($g_lang[$key])){
        if(isset($args) && count($args) > 0 && !empty($args) && $args[0] != null){
            return vsprintf($g_lang[$key], $args);
        }else{
            return $g_lang[$key];
        }
    }else{
        return $key;
    }
}
require_once 'config/lang/message.php';


?>