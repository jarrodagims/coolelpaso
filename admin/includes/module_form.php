<?php
$dev = 0;

# Debugging
if($dev){
	ini_set('display_errors','1');
	ini_set("error_reporting", E_ALL);
}

# Includes
include("library.php");

if(!$_SESSION['session_logged_in'] == true){
	header("Location: http://".$_SERVER['HTTP_HOST']);
	exit;
}

extract($_REQUEST);

# Security
Security::xssProtect();

# Add
if($action == 'add'){
	// Create the record instance
	if(Session::exists($moduleSessionFormName)){
		// The form was rejected after submission
		$moduleClass = Session::load($moduleSessionFormName);
		Session::unregister($moduleSessionFormName);
	}
	if(empty($moduleClass) || !($moduleClass instanceof $module)){
		// Create a new record
		$moduleClass = new $module();
		// Set module specific variables needed to add record before display
		$moduleClass->adminNewRecord($category);

		if(isset($parent_id)){
			$moduleClass->adminNewRecord($parent_id);
		}
	}

	// Content
	echo $moduleClass->toHtml('admin_add');

	// JavaScript
	if(method_exists($moduleClass, "adminAddJavascriptAction")){
		echo $moduleClass->toHtml('admin_add_javascript');
	}
}

# Edit
if($action == 'edit'){
	// Create the record instance
	if(Session::exists($moduleSessionFormName)){
		// The form was rejected after submission
		$moduleClass = Session::load($moduleSessionFormName);
		Session::unregister($moduleSessionFormName);
	}
	if(empty($moduleClass) || !($moduleClass instanceof $module)){
		// Fetch the record
		$moduleClass = new $module(intval($id));
		if(!$moduleClass->getId()){
			echo 'Record not found.';
		}
	}
	// Content
	echo $moduleClass->toHtml('admin_edit');
	// JavaScript
	if(method_exists($moduleClass, "adminEditJavascriptAction")){
		echo $moduleClass->toHtml('admin_edit_javascript');
	}
}

# View
if($action == 'view'){
	$moduleClass = new $module(intval($id));

	// Content
	echo $moduleClass->toHtml('admin_view');
}

# Confirm
if($action == 'confirm'){
	// generic confirm dialog
	$moduleClass = new $module(intval($id));
	echo $moduleClass->toHtml($function);
}
if($action && isset($GLOBALS['MODAL_CSS'])){echo $GLOBALS['MODAL_CSS'];}
if($action && isset($GLOBALS['MODAL_JAVASCRIPT'])){echo $GLOBALS['MODAL_JAVASCRIPT'];}
?>
