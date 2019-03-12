<?php
# Debugging
//ini_set('display_errors','1');
//ini_set("error_reporting", E_ALL);

# Includes
include_once("includes/library.php");
$_SESSION['SKIP_MODULE_VERIFY'] = true;
include_once("modules/manage_admins/includes/library.php");

# Page Title
$GLOBALS['page_title'] = "Reset Password";

# If user is logged in, forward the user to the main admin page.
if($_SESSION['session_logged_in'] == true){
	header("Location: admin.php");
	exit;
}

if(!strlen(trim($_SESSION['session_user_id']))){
	header("Location: admin.php");
	exit;	
}

# Security
Security::validateHttpRequest('none');
Security::xssProtect();

$moduleClassName = 'ManageAdmin';
$moduleClass = new $moduleClassName($_SESSION['session_user_id']);

# Create the record instance
if(Session::exists($moduleSessionFormName)){
	# The form was rejected after submission
	$moduleClass = Session::load($moduleSessionFormName);
	Session::unregister($moduleSessionFormName);
}
if(empty($moduleClass) || !($moduleClass instanceof $moduleClassName)){
	# Create a new record
	$moduleClass = new $moduleClassName();
}

# Required fields for JavaScript validation
$moduleClass->setRequiredFields(array('old_password','password','confirm_password'));
$required_fields = $moduleClass->getRequiredFields();

# Content
$content .= $moduleClass->toHtml('reset-password');

# JavaScript
//$GLOBALS['JAVASCRIPT'] .= $moduleClass->toHtml('admin_add_javascript');

# Include Template
include_once('template.php');
?>