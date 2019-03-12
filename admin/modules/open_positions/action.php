<?php
# Debugging - set to 1
$dev = 0;
$queryLogging = 1;
# Includes
include_once('../../includes/library.php');

# Security 
Security::xssProtect();

# Create new instance of module class
$moduleClass = new OpenPosition();
$moduleClassName = $moduleClass->_moduleClassName;

# Action
$action = $_REQUEST['action'];

# Add
if($action == 'add'){
	
	# Create new instance of module class
	$moduleClass = new $moduleClassName($_REQUEST);
	if(!strlen(trim($_POST['remove_date']))){
		$moduleClass->setRemoveDate('0000-00-00');
	}

	# Declare file class
	$newFile = new File($_FILES['pdf']);
	$newFile->setDirectory($GLOBALS['path'].$moduleClass->getFilePath());

	# Generate Permalink / Validate
	$moduleClass->clearMessages()
				->validate();

	# Make sure the file is a PDF file
	$fileCheck = $newFile->checkFileTypes();
	if(strlen($fileCheck)){
		$moduleClass->addMessage('pdf',array('type'=>'failure','text'=>$fileCheck));
	}
	
	# Failure	
	if($moduleClass->hasMessages()){
		$messages = $moduleClass->getMessages();
		echo failure("Your submission contains errors. Please see review them below.", $messages);
		exit;
	}
	
	# Save the record
	$moduleClass->save($queryLogging);
	
	# mySQL INSERT Failure
	if(!$moduleClass->getId()){
		echo failure('Record could not be added.');
		exit;
	}

	# Process PDF File
	if(isUploaded('pdf')){
		$newFile->save($moduleClass->getDbTable().$moduleClass->getId());
		$moduleClass->setPdf('1');
		$moduleClass->save($queryLogging);
	}

	# Refresh Data
	$selector = "ol.sortable";
	$content = $moduleClass->toHtml('default-list');
	
	$moduleClass->addRefreshElement($selector, $content)
						->addRefreshElement(".no_records", "", "delete");
	
	$refreshElements = $moduleClass->getRefreshElements();

	# Success
	echo success($moduleClass->getTitle().' added successfully.', $refreshElements, 'refreshData', array($moduleClass->_moduleName,$moduleClass->getTitle(),$moduleClass->getId()));
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
	if(!strlen(trim($_POST['remove_date']))){
		$moduleClass->setRemoveDate('0000-00-00 00:00:00');
	}

	# Declare file class
	$newFile = new File($_FILES['pdf']);
	$newFile->setDirectory($GLOBALS['path'].$moduleClass->getFilePath());
	$curFile = new File($moduleClass->getDbTable().$moduleClass->getId().'.pdf');
	$curFile->setDirectory($GLOBALS['path'].$moduleClass->getFilePath());
	$curFile->load($moduleClass->getDbTable().$moduleClass->getId().'.pdf');
	
	# Set Options for module class
	$moduleClass->setOptions($_POST);

	# Generate Permalink
	$moduleClass->clearMessages()
				->validate();

	# Check file is a PDF file
	$fileCheck = $newFile->checkFileTypes();
	if(strlen($fileCheck)){
		$moduleClass->addMessage('pdf',array('type'=>'failure','text'=>$fileCheck));
	}
	
	# Failure
	if($moduleClass->hasMessages()){
		$messages = $moduleClass->getMessages();
		echo failure("Your submission contains errors. Please see review them below.", $messages, 'messages', array($moduleClass->_moduleName,$moduleClass->getTitle(),$moduleClass->getId()));
		exit;
	}

	# Save record
	$moduleClass->save($queryLogging);

	# Delete PDF if checked
	if(strlen($_POST['checkbox2'])){
		$curFile->deleteFile();
		$moduleClass->setPdf('0');
		$moduleClass->save($queryLogging);
	}

	# Process PDF File
	if(isUploaded('pdf')){
		$curFile->deleteFile();
		$newFile->save($moduleClass->getDbTable().$moduleClass->getId());
		$moduleClass->setPdf('1');
		$moduleClass->save($queryLogging);
	}

	# Refresh Data
	$selector = "#".$moduleClassName."_".$moduleClass->getId()." .itemTitle";
	$content = strip_tags(process($moduleClass->getTitle()));
	
	$moduleClass->addRefreshElement($selector, $content);
	
	$refreshElements = $moduleClass->getRefreshElements();
	
	# Success
	echo success($moduleClass->getTitle().' modified successfully.', $refreshElements, 'refreshData', array($moduleClass->_moduleName,$moduleClass->getTitle(),$moduleClass->getId()));
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

	# Declare file and image class
	$curFile = new File($moduleClass->getDbTable().$moduleClass->getId().'.pdf');
	$curFile->setDirectory($GLOBALS['path'].$moduleClass->getFilePath());
	$curFile->load($moduleClass->getDbTable().$moduleClass->getId().'.pdf');
	
	# Delete PDF
	$curFile->deleteFile();
	
	# Delete the record
	$moduleClass->delete();
	
	# Refresh Data
	$selector = "#menuItem_".$moduleClass->getId();
	
	$moduleClass->addRefreshElement($selector);
	
	if(!$moduleClass->fetchCount("WHERE `id` > 0")){
		$moduleClass->addRefreshElement(".index-wrapper", "<div class='no_records'><i class='fa fa-times-circle'></i>No records available.</div>", "add");
	}
	
	$refreshElements = $moduleClass->getRefreshElements();
	
	# Success
	echo success($moduleClass->getTitle().' deleted successfully.', $refreshElements, 'refreshData', array($moduleClass->_moduleName,$moduleClass->getTitle(),$moduleClass->getId()));
	exit;
}
?>