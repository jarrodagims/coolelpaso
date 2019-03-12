<?php
# Debugging - set to 1
$dev = 0;
$queryLogging = 1;
# Includes
include_once('../../includes/library.php');

# Security 
Security::xssProtect();

# Create new instance of module class
$moduleClass = new ContactUs();
$moduleClassName = $moduleClass->_moduleClassName;

# Action
$action = $_REQUEST['action'];

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
# Active
if($action == 'archived'){
	
	# Create new instance of module class
	$moduleClass = new $moduleClassName($_REQUEST['id']);
	
	# If the Record does not exist
	if(!$moduleClass->getId()){
		echo failure('Record not found.');
		exit;
	}

	# Switch active status
	if($moduleClass->getArchived() == 1){
		$moduleClass->setArchived(0);
		$message = $moduleClass->getName().' un-archived successfully.';
	}else{
		$moduleClass->setArchived(1);
		$message = $moduleClass->getName().' archived successfully.';
	}
	

	# Save
	$moduleClass->save($queryLogging);
	
	# Refresh Data
	$selector = "#".$moduleClassName."_".$moduleClass->getId();
	
	$moduleClass->addRefreshElement($selector, "", "delete");
	
	$refreshElements = $moduleClass->getRefreshElements();
	
	# Success
	echo success($message, $refreshElements, 'refreshData', array($moduleClass->_moduleName,$moduleClass->getName(),$moduleClass->getId()));
	exit;
}

?>