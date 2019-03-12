<?php
# Debugging - set to 1
$dev = 0;
$queryLogging = 1;
# Includes
include_once('../../includes/library.php');

# Security 
Security::xssProtect();

# Create new instance of module class
$moduleClass = new Faq();
$moduleClassName = $moduleClass->_moduleClassName;

# Create new instance of module category class
$moduleCategoryClassName = $moduleClass->_moduleCategoryClassName;


# Action
$action = $_REQUEST['action'];

# Add 
if($action == 'add'){
	$moduleCategoryClass = new $moduleCategoryClassName($_REQUEST);
	# Generate Permalink & Validate
	$moduleCategoryClass->setPermalink($moduleCategoryClass->generatePermalink($moduleCategoryClass->getName(),$moduleCategoryClass->getId()))
						->clearMessages()
						->validate();
		
	# Failure
	if($moduleCategoryClass->hasMessages()){
		$messages = $moduleClass->getMessages();
		echo failure("Your submission contains errors. Please see review them below.", $messages);
		exit;
	}
	
	# Save
	$moduleCategoryClass->save($queryLogging);
	
	# mySQL INSERT Failure
	if(!$moduleCategoryClass->getId()){
		echo failure('Record could not be added.');
		exit;
	}
	
	# Refresh Data
	$selector = ".ui-sortable";
	$content = $moduleCategoryClass->buildSortingStructure('all', 0, 1, 1,$moduleCategoryClass->getId());
	
	$moduleCategoryClass->addRefreshElement($selector, $content)
						->addRefreshElement(".no_records", "", "delete");
	
	$refreshElements = $moduleCategoryClass->getRefreshElements();
	
	# Success
	
	echo success($moduleCategoryClass->getName().' added successfully.', $refreshElements, 'refreshData', array($moduleClass->_moduleCategoryClassName,$moduleCategoryClass->getName(),$moduleCategoryClass->getId()));
	exit;
}
# Edit 
if($action == 'edit'){
	
	# Check to see if record exists
	if(!$moduleCategoryClass->getId()){
		echo failure('Record not found.');
		exit;
	}
	
	# Set Options for module class
	$moduleCategoryClass->setOptions($_POST);
	
	# Generate Permalink & Validate
	$moduleCategoryClass->setPermalink($moduleCategoryClass->generatePermalink($moduleCategoryClass->getName()))
						->clearMessages()
						->validate();
	
	# Failure
	if($moduleCategoryClass->hasMessages()){
		$messages = $moduleClass->getMessages();
		echo failure("Your submission contains errors. Please see review them below.", $messages, 'messages', array($moduleClass->_moduleCategoryClassName,$moduleCategoryClass->getName(),$moduleCategoryClass->getId()));
		exit;
	}
	
	# Save
	$moduleCategoryClass->save($queryLogging);
	
	# Refresh Data
	$selector = "#menuItem_".$moduleCategoryClass->getId()." > .menuDiv .itemTitle";
	$content = $moduleCategoryClass->getName();
	
	$moduleCategoryClass->addRefreshElement($selector, $content);
	
	$refreshElements = $moduleCategoryClass->getRefreshElements();
	
	# Success
	echo success($moduleCategoryClass->getName().' modified successfully.', $refreshElements, 'refreshData', array($moduleClass->_moduleCategoryClassName,$moduleCategoryClass->getName(),$moduleCategoryClass->getId()));
	exit;
}
# Delete 
if($action == 'delete'){
	$moduleCategoryClass = new $moduleCategoryClassName($_REQUEST['id']);
	# Check to see if record exists
	if(!$moduleCategoryClass->getId()){
		echo failure('Record not found.');
		exit;
	}

	# Check for linked records
	$moduleClass = new $moduleClassName();
	if($moduleClass->fetchCount("WHERE `category` = ".$moduleCategoryClass->getId())){
		echo failure('Records still belong to '.$moduleCategoryClass->getName().'.', '', 'messages', array($moduleClass->_moduleCategoryClassName,$moduleCategoryClass->getName(),$moduleCategoryClass->getId()));
		exit;
	}
	
	# Delete
	$moduleCategoryClass->delete();

	# Refresh Data
	$selector = "#menuItem_".$_POST['id'];
	
	$moduleCategoryClass->addRefreshElement($selector);
	
	if(!$moduleCategoryClass->fetchCount("WHERE `id` > 0")){
		$moduleCategoryClass->addRefreshElement(".index-wrapper", "<div class='no_records'><i class='fa fa-times-circle'></i>No records available.</div>", "add");
	}
	
	$refreshElements = $moduleCategoryClass->getRefreshElements();
	
	# Success
	echo success($moduleCategoryClass->getName().' deleted successfully.', $refreshElements, 'refreshData', array($moduleClass->_moduleCategoryClassName,$moduleCategoryClass->getName(),$moduleCategoryClass->getId()));
	exit;
}
?>