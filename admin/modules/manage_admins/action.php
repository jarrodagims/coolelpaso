<?php
# Debugging - set to 1
$dev = 0;
$queryLogging = 1;
# Includes
include_once('../../includes/library.php');

# Security 
Security::xssProtect();

# Create new instance of module class
$moduleClass = new ManageAdmin();
$moduleClassName = $moduleClass->_moduleClassName;

# Action
$action = $_REQUEST['action'];

# Add
if($action == 'add'){

	# Create new instance of module class
	$moduleClass = new $moduleClassName($_REQUEST);
	
	# Validate
	$moduleClass->clearMessages()
		->validate()
		->validatePassword();
	
	# Failure
	if($moduleClass->hasMessages()){
		$messages = $moduleClass->getMessages();
		echo failure("Your submission contains errors. Please see review them below.", $messages);
		exit;
	}
	
	# Determine if user has full access
	# Get the number of active modules in the admin
	$numberOfActiveModules = new Module();
	$numberOfActiveModules = $numberOfActiveModules->fetchCount("WHERE `enabled` = '1'");
	
	# Add 3 for webtraffic, postmarketer, and support
	$numberOfActiveModules = $numberOfActiveModules + 3; 
	
	# Process Access Levels
	if(count($_POST['access_levels']) >= $numberOfActiveModules){
		$access_levels = 'All';
	}
	else{
		$access_levels = serialize($_POST['access_levels']);
	}
	
	# Set Access Levels
	$moduleClass->setAccessLevels($access_levels);
	
	# Set Date for Password to expire
	$moduleClass->setPasswordExpires(date('Y-m-d',strtotime('Today +'.$moduleClass->_modulePasswordDaysExpires.' days')));
	
	# Save
	$moduleClass->save($queryLogging);
	
	# Insert Failure
	if(!$moduleClass->getId()){
		echo failure('Record could not be added.');
		exit;
	}
	
	# Refresh Data
	$selector = 'table.table tbody';
	$content = $moduleClass->toHtml('row');
	
	$moduleClass->addRefreshElement($selector, $content);
	
	$refreshElements = $moduleClass->getRefreshElements();

	# Success
	echo success($moduleClass->getName().' added successfully.', $refreshElements, 'refreshData', array($moduleClass->_moduleName,$moduleClass->getName(),$moduleClass->getId()));
	exit;
}

# Edit
if($action == 'edit'){

	# Create new instance of module class
	$moduleClass = new $moduleClassName($_REQUEST['id']);
	
	# If the Record does not exist
	if(!$moduleClass->getId()){
		failure('Record not found.');
		exit;
	}
	
	# Set Options for module class
	$_POST['reset_password'] = (int)$_POST['reset_password'];
	
	if(!strlen(trim($_POST['password']))){
		unset($_POST['password']);
	}
	
	$moduleClass->setOptions($_POST);	

	# Validate
	$moduleClass->clearMessages()
				->validate();
	
	if(isset($_POST['password'])){
		$moduleClass->validatePassword();
	}
	
	# Failure
	if($moduleClass->hasMessages()){
		$messages = $moduleClass->getMessages();
		echo failure("Your submission contains errors. Please see review them below.", $messages, 'messages', array($moduleClass->_moduleName,$moduleClass->getName(),$moduleClass->getId()));
		exit;
	}
	
	
	$moduleClass->setLocked(0)
				->setFailedAttempts(0);
	
	# Determine if user has full access
	# Get the number of active modules in the admin
	$numberOfActiveModules = new Module();
	$numberOfActiveModules = $numberOfActiveModules->fetchCount("WHERE `enabled` = '1'");
	
	# Add 3 for webtraffic, postmarketer, and support
	$numberOfActiveModules = $numberOfActiveModules + 3; 
	
	# Process Access Levels
	if(count($_POST['access_levels']) >= $numberOfActiveModules){
		$access_levels = 'All';
	}
	else{
		$access_levels = serialize($_POST['access_levels']);
	}
	
	# Set Access Levels
	$moduleClass->setAccessLevels($access_levels);		

	# Set Password
	if(strlen(trim($_POST['password']))){
		$moduleClass->setUpdatePassword(true);	
	}
	
	# Save
	$moduleClass->save($queryLogging);
	
	# Refresh Data
	$selector = "#".$moduleClassName."_".$moduleClass->getId()." td.name";
	$content = $moduleClass->getName();
	
	$moduleClass->addRefreshElement($selector, $content);
	
	$refreshElements = $moduleClass->getRefreshElements();
	
	# Success
	echo success($moduleClass->getName().' modified successfully.', $refreshElements, 'refreshData', array($moduleClass->_moduleName,$moduleClass->getName(),$moduleClass->getId()));
	exit;
}

# Delete
if($action == 'delete'){

	# Create new instance of module class
	$moduleClass = new $moduleClassName($_REQUEST['id']);
	
	# If the Record does not exist
	if(!$moduleClass->getId()){
		echo failure('Record not found.');
		exit;
	}
	
	# Delete
	$moduleClass->delete();
	
	# Refresh Data
	$selector = "#".$moduleClassName."_".$_POST['id'];
	
	$moduleClass->addRefreshElement($selector);
	
	$refreshElements = $moduleClass->getRefreshElements();
	
	# Success
	echo success($moduleClass->getName().' deleted successfully.', $refreshElements, 'refreshData', array($moduleClass->_moduleName,$moduleClass->getName(),$moduleClass->getId()));
	exit;
}
?>