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
$moduleCategoryClassName = $moduleClass->_moduleCategoryClassName;

# Action
$action = $_REQUEST['action'];

# Add
if($action == 'add'){
	
	# Ensure Category Id is an integer
	$_POST['category'] = (int) $_POST['category'];
	
	# Create new instance of module class
	$moduleClass = new $moduleClassName($_REQUEST);
	
	# Create new instance of module category class
	$moduleCategoryClass = new $moduleCategoryClassName($moduleClass->getCategory());

	# Generate Permalink & Validate
	$moduleClass->setPermalink($moduleClass->generatePermalink(generateBlurb($moduleClass->getQuestion(),20)))
		->clearMessages()
		->validate();
	
	# Failure
	if($moduleClass->hasMessages()){
		$messages = $moduleClass->getMessages();
		echo failure("Your submission contains errors. Please see review them below.", $messages);
		exit;
	}
	
	# Save
	$moduleClass->save($queryLogging);
	
	# mySQL INSERT Failure
	if(!$moduleClass->getId()){
		echo failure('Record could not be added.');
		exit;
	}
	
	# Refresh Data
	$selector = "#menuItem_".$moduleClass->getCategory()." > ol";
	$content = $moduleCategoryClass->buildChild($moduleClass, "2", $moduleClass->_moduleClassName);
	
	$moduleClass->addRefreshElement($selector, $content);
	
	$refreshElements = $moduleClass->getRefreshElements();

	# Success
	echo success(generateBlurb($moduleClass->getQuestion(),20).' added successfully.', $refreshElements, 'refreshData', array($moduleClass->_moduleName,generateBlurb($moduleClass->getQuestion(),20),$moduleClass->getId()));
	exit;
}

# Edit
if($action == 'edit'){
	
	# Create new instance of module class
	$moduleClass = new $moduleClassName($_REQUEST['id']);
	
	# If the Record does not exist
	if(!$moduleClass->getId()){
		echo failure('Record not found.');
		exit;
	}
	
	# Set Options for module class
	$moduleClass->setOptions($_POST);

	# Generate Permalink & Validate
	$moduleClass->setPermalink($moduleClass->generatePermalink(generateBlurb($moduleClass->getQuestion(),20),$moduleClass->getId()))
				->clearMessages()
				->validate();
	
	# Failure
	if($moduleClass->hasMessages()){
		$messages = $moduleClass->getMessages();
		echo failure("Your submission contains errors. Please see review them below.", $messages, 'messages', array($moduleClass->_moduleName,generateBlurb($moduleClass->getQuestion(),20),$moduleClass->getId()));
		exit;
	}
	
	# Save
	$moduleClass->save($queryLogging);
	
	# Refresh Data
	$selector = "#".$moduleClassName."_".$moduleClass->getId()." span.question";
	$content = strip_tags($moduleClass->getQuestion());
	
	$moduleClass->addRefreshElement($selector, $content);
	
	$refreshElements = $moduleClass->getRefreshElements();

	# Success
	echo success(generateBlurb($moduleClass->getQuestion(),20).' modified successfully.', $refreshElements, 'refreshData', array($moduleClass->_moduleName,generateBlurb($moduleClass->getQuestion(),20),$moduleClass->getId()));
	exit;
}

# Delete
if($action == 'delete'){
	
	$moduleClass = new $moduleClassName($_REQUEST['id']);
	
	$categoryId = $moduleClass->getCategory();
	
	# If the Record does not exist
	if(!$moduleClass->getId()){
		echo failure('Record not found.');
		exit;
	}

	# Delete
	$moduleClass->delete();
	
	# Refresh Data
	$selector = "#menuItem_".$categoryId."\\>".$moduleClass->getId();
	
	$moduleClass->addRefreshElement($selector);
	
	$refreshElements = $moduleClass->getRefreshElements();
	
	# Success
	echo success(generateBlurb($moduleClass->getQuestion(),20).' deleted successfully.', $refreshElements, 'refreshData', array($moduleClass->_moduleName,generateBlurb($moduleClass->getQuestion(),20),$moduleClass->getId()));
	exit;
}
?>