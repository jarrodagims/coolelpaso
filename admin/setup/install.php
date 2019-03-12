<?php
$dev = 0;

# Debugging 
if($dev){
	ini_set('display_errors','1');
	ini_set("error_reporting", E_ALL);
}



$color = $_POST['button_color'];
if(!preg_match('/^#?([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})$/i', $color, $parts)){
	$out = ""; 
	for($i = 1; $i <= 3; $i++){
		$parts[$i] = hexdec($parts[$i]);
		$parts[$i] = round($parts[$i] * 97/100);
		$out .= str_pad(dechex($parts[$i]), 2, '0', STR_PAD_LEFT);
	}
}

$str=file_get_contents('../includes/globals.php');
$str=str_replace("#236171", $_POST['primary_color'],$str);
$str=str_replace("#36707E", $_POST['button_color'],$str);
$str=str_replace("#2E606C", "#".$out,$str);
file_put_contents('../includes/globals.php', $str);




# Includes 
include_once("../includes/library.php");

# Security
Security::validateHttpRequest('post');

# If the admin is already installed, throw an error.
$manageAdmin = new ManageAdmin();
if($manageAdmin->isInstalled()){
	$_SESSION['LOGIN_SUCCESSFUL'] = 0;
	$_SESSION['LOGIN_OUTCOME'] = failure("Administration system already installed.");
	header("Location: /admin/");
	exit;
}

# If the passwords do not match, throw an error.
if($_POST['password'] != $_POST['confirm_password']){
	$_SESSION['LOGIN_SUCCESSFUL'] = 0;
	$_SESSION['LOGIN_OUTCOME'] = failure("Passwords do not match.");
	unset($_POST['password'],$_POST['confirm_password']);
	$_SESSION['SESSION_REJECTED_FORM'] = $_POST;
	header("Location: index.php");
	exit;
}

# Validate
$manageAdmin->setPassword($_POST['password'])
	->setConfirmPassword($_POST['confirm_password'])
	->clearMessages()
	->validatePassword();

if($manageAdmin->hasMessages()){
	$_SESSION['LOGIN_SUCCESSFUL'] = 0;
	$_SESSION['LOGIN_OUTCOME'] = failure("Password does not meet requirements.");
	unset($_POST['password'],$_POST['confirm_password']);
	$_SESSION['SESSION_REJECTED_FORM'] = $_POST;
	header("Location: index.php");
	exit;
}

# Create the modules table
$module = new Module();
$module->install();
$module->register('','1','1');

// Install the Manage Admins module
extract($_POST);
$manageAdmin = new ManageAdmin();
$manageAdmin->setName($name);
$manageAdmin->install($username,$password);

// install the rest of the modules
$files = array_diff(scandir('../classes/database/'), array('.', '..'));
foreach($files as $file){
	$class = preg_replace('/\\.[^.\\s]{3,4}$/', '', $file);
	$test = new $class();
	if(method_exists($class, 'install')){
		$obj = new $class();
		$obj->setupInstallation();
	}
}

$_SESSION['LOGIN_SUCCESSFUL'] = 0;
$_SESSION['LOGIN_OUTCOME'] = success("Administration system installed successfully.");
unset($_SESSION['INSTALLING_ADMIN']);
header("Location: /admin/");
exit;
?>