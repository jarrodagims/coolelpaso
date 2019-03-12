<?php
# Debugging - set to 1
$dev = 0;
$queryLogging = 1;
include_once('../../includes/library.php');
Security::xssProtect();
# Create new instance of module class
$moduleClass = new Document();
$moduleClassName = $moduleClass->_moduleClassName;
$moduleCategoryClassName = $moduleClass->_moduleCategoryClassName;
extract($_REQUEST);
// Add
if($action == 'add'){
	$moduleCategoryClass = new $moduleCategoryClassName($_POST);

	// Generate Permalink / Validate
	$moduleCategoryClass->setPermalink($moduleCategoryClass->generatePermalink($moduleCategoryClass->getName()))
		->clearMessages()
		->validate();

	// Failure
	$messages = $moduleCategoryClass->getMessages();
	if($moduleCategoryClass->hasMessages()){
		echo failure("Your submission contains errors. Please see review them below.", $messages);
		exit;
	}
	
	// Save
	$moduleCategoryClass->save($queryLogging);
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
	echo success($moduleCategoryClass->getName().' added successfully.', $refreshElements, 'refreshData', array($moduleCategoryClass->_moduleCategoryName,$moduleCategoryClass->getName(),$moduleCategoryClass->getId()));
	exit;
}
// Edit
if($action == 'edit'){
	$moduleCategoryClass = new $moduleCategoryClassName($id);
	if(!$moduleCategoryClass->getId()){
		echo failure('Record not found.');
		exit;
	}
	$moduleCategoryClass->setOptions($_POST);

	// Generate Permalink / Validate
	$moduleCategoryClass->setPermalink($moduleCategoryClass->generatePermalink($moduleCategoryClass->getName(),$moduleCategoryClass->getId()))
		->clearMessages()
		->validate();
	
	// Failure
	if($moduleCategoryClass->hasMessages()){
		$messages = $moduleCategoryClass->getMessages();
		echo failure("Your submission contains errors. Please see review them below.", $messages, 'messages', array($moduleCategoryClass->_moduleCategoryName,$moduleCategoryClass->getName(),$moduleCategoryClass->getId()));
		exit;
	}
	
	// Save
	$moduleCategoryClass->save($queryLogging);

	// Refresh Data
	$selector = "#menuItem_".$moduleCategoryClass->getId()." .itemTitle";
	$content = $moduleCategoryClass->getName();
	$moduleCategoryClass->addRefreshElement($selector, $content);
	$refreshElements = $moduleCategoryClass->getRefreshElements();
	
	// Success
	echo success($moduleCategoryClass->getName().' modified successfully.', $refreshElements, 'refreshData', array($moduleCategoryClass->_moduleCategoryName,$moduleCategoryClass->getName(),$moduleCategoryClass->getId()));
	exit;
}
// Delete
if($action == 'delete'){
	// Check for record
	$moduleCategoryClass = new $moduleCategoryClassName($id);
	if(!$moduleCategoryClass->getId()){
		echo failure('Record not found.');
		exit;
	}

	// Check for linked records
	$moduleClass = new $moduleClassName();
	if($moduleClass->fetchCount("WHERE `category` = ".$moduleCategoryClass->getId())){
		echo failure('There are still documents in '.$moduleCategoryClass->getName().'.', '', 'messages', array($moduleCategoryClass->_moduleCategoryName,$moduleCategoryClass->getName(),$moduleCategoryClass->getId()));
		exit;
	}
	
	// Delete
	$moduleCategoryClass->delete();
	// Success
	# Refresh Data
	$selector = "#menuItem_".$_POST['id'];
	$moduleCategoryClass->addRefreshElement($selector);
	if(!$moduleCategoryClass->fetchCount("WHERE `id` > 0")){
		$moduleCategoryClass->addRefreshElement(".index-wrapper", "<div class='no_records'><i class='fa fa-times-circle'></i>No records available.</div>", "add");
	}
	$refreshElements = $moduleCategoryClass->getRefreshElements();
	# Success
	echo success($moduleCategoryClass->getName().' deleted successfully.', $refreshElements, 'refreshData', array($moduleCategoryClass->_moduleCategoryName,$moduleCategoryClass->getName(),$moduleCategoryClass->getId()));
}

?>