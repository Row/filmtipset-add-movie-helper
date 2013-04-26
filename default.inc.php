<?php
set_time_limit(0);

/**
 * This will remove slashes if present by bad php.ini
 */
function stripslashes_array($data) {
   if (is_array($data)){
	   foreach ($data as $key => $value){
           $data[$key] = stripslashes_array($value);
       }
       return $data;
   }else{
       return stripslashes($data);
   }
}
if (get_magic_quotes_gpc()) {
	$_POST = stripslashes_array($_POST);
	$_GET = stripslashes_array($_GET);
}

/**
 * Autoload classes
 */
function __autoload($class_name) 
{
    require_once 'class/' . $class_name . '.class.php';
}

if(!date_default_timezone_set('Europe/Stockholm')) {
	die('Failed to set timezone');
}
