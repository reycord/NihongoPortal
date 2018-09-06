<?php

define('ERR_BADURL_CD', 2000);

define('ERR_NOT_YET_LOGIN', 2001);

//authenticate
define('ERR_LOGIN_MISSING_PARAMS_CD', 3001);
define('ERR_LOGIN_USER_NOT_EXIST_CD', 3002);

//question
define('ERR_QUESTION_NOT_EXIST_CD', 4001);
define('ERR_QUESTION_XML_FILE', 4002);
define('ERR_QUESTION_IMPORT', 4003);

//answer
define('ERR_ANSWER_USER_NOT_EXIST_CD', 4101);
define('ERR_ANSWER_MISSING_PARAMS_CD', 4102);

//system error
define('ERR_SYSTEM', 9999);


date_default_timezone_set("Asia/Tokyo");

?>