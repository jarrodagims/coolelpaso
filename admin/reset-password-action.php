<?php
$dev = 0;

# Debugging
if($dev){
	ini_set('display_errors','1');
	ini_set("error_reporting", E_ALL);
}

# Includes
include_once("includes/library.php");
$_SESSION['SKIP_MODULE_VERIFY'] = true;
include_once("modules/manage_admins/includes/library.php");

# Security
Security::xssProtect();

extract($_POST);

$manageAdmin = new ManageAdmin($_POST);

if(!strlen(trim($id))){
	header("Location: /admin");
	exit;	
}else{
	# Verify account credentials
	$md5_existing_pass = md5($manageAdmin->_modulePasswordSalt.$manageAdmin->getOldPassword());
	$manageAdmins = $manageAdmin->fetchAll("WHERE `id` = '".$id."' AND `password` = '".$md5_existing_pass."'");
	
	if(!sizeof($manageAdmins)){
		$_SESSION['LOGIN_SUCCESSFUL'] = 0;
		$_SESSION['LOGIN_OUTCOME'] = failure("Your password cannot be reset at this time. Please try again later.");
		header('Location: '.$_SERVER['HTTP_REFERER']);
		exit;	
	}else{
		$manageAdmin = $manageAdmins[0];
		
		# Get the current password for comparison
		if(md5($manageAdmin->_modulePasswordSalt.$password) == $manageAdmin->getPassword()){
			$_SESSION['LOGIN_SUCCESSFUL'] = 0;
			$_SESSION['LOGIN_OUTCOME'] = failure("You must choose a new password.");
			header('Location: '.$_SERVER['HTTP_REFERER']);
			exit;		
		}
	}
	
	# Validate
	$manageAdmin->setPassword($_POST['password'])
		->setConfirmPassword($_POST['confirm_password'])
		->clearMessages()
		->validatePassword();
	
	if($manageAdmin->hasMessages()){
		$_SESSION['LOGIN_SUCCESSFUL'] = 0;
		$_SESSION['LOGIN_OUTCOME'] = failure("Your submission contains errors.");
		header('Location: '.$_SERVER['HTTP_REFERER']);
		exit;
	}
	
	# Update the password
	$manageAdmin->setUpdatePassword(true)
		->setResetPassword(0)
		->setLocked(0)
		->setFailedAttempts(0)
		->save();
	
	$_SESSION['LOGIN_SUCCESSFUL'] = 1;
	$_SESSION['LOGIN_OUTCOME'] = success("Password updated. Please login.");
	header('Location: index.php');
	exit;
}
?>