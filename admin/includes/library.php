<?php

set_time_limit(0);
/**
 * library.php - Code Library
 *
 * This file includes all library files.
 * session_public.php - Is not included as it is for the Public sessions
 * @filesource
 */

/**
 */
//$dev=1;
# Debugging 
if($dev){
	$messages = $_POST;
	$messages = json_encode($messages);
	echo $messages;
	
	ini_set('display_errors','1');
	ini_set("error_reporting", E_ALL);
}

include_once("globals.php");
include_once("classes.php");
if(stristr($_SERVER['REQUEST_URI'],'/admin/')){
	include_once("session.php");
}else{
	session_start();
	include_once("seo.php");
}
//include_once("ftp.php");
include_once("menu.php");
include_once("misc_functions.php");
//include_once("postmarketer.php");
include_once("status.php");

?>