<?php
# Debugging - set to 1
$dev = 0;
$queryLogging = 1;
# Includes
include_once('../../includes/library.php');

# Security
Security::xssProtect();

# Create new instance of module class
$moduleClass = new Event();
$moduleClassName = $moduleClass->_moduleClassName;
$moduleCategoryClassName = $moduleClass->_moduleCategoryClassName;

# Action
$action = $_REQUEST['action'];

# Add
if($action == 'add'){

	# Create new instance of module class
	$moduleClass = new $moduleClassName($_REQUEST);

	# Ensure Category Id is an integer
	$_POST['category'] = (int) $_POST['category'];

	# Create new instance of module category class
	$moduleCategoryClass = new $moduleCategoryClassName($moduleClass->getCategory());

	# Declare file and image class
	$image = new Image();
	$newFile = new File($_FILES['pdf']);
	$newFile->setDirectory($GLOBALS['path'].$moduleClass->getFilePath());

	# Generate Permalink / Validate
	$moduleClass->setPermalink($moduleClass->generatePermalink($moduleClass->getName()))
		->clearMessages()
		->validate()
		->validateUrl(array('url'));

	// Check allowed image types
	$imageCheck = $image->checkImageType('image',$image->getImageTypes());
	if(strlen($imageCheck)){
		$moduleClass->addMessage('image',array('type'=>'failure','text'=>$imageCheck));
	}

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

	# Process Image
	if(isUploaded('image')){
		$image = new Image($_FILES['image']['tmp_name']);
		$image->setDirectory($GLOBALS['path'].$moduleClass->getFilePath());
		$image->canvas($moduleClass->_moduleImageCanvasX,$moduleClass->_moduleImageCanvasY);
		$image->save($moduleClass->getDbTable().$moduleClass->getId().'.'.$image->getExt());

		$image = new Image($_FILES['image']['tmp_name']);
		$image->setDirectory($GLOBALS['path'].$moduleClass->getFilePath());
		$image->canvas($moduleClass->_moduleThumbCanvasX,$moduleClass->_moduleThumbCanvasY);
		$image->save($moduleClass->getDbTable().$moduleClass->getId().'_t.'.$image->getExt());
		$moduleClass->setImage($image->getExt())->save($queryLogging);
	}

	# Process PDF File
	if(isUploaded('pdf')){
		$newFile->save($moduleClass->getDbTable().$moduleClass->getId());
		$moduleClass->setPdf('1');
		$moduleClass->save($queryLogging);
	}

	# Refresh Data
	$selector = "#menuItem_".$moduleClass->getCategory()." > ol";
	$content = $moduleCategoryClass->buildChild($moduleClass, "2", $moduleClass->_moduleClassName);

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
		echo failure('Record not found.');
		exit;
	}

	# Set Options for module class
	$moduleClass->setOptions($_POST);

	# Declare file and image class
	$image = new Image();
	$newFile = new File($_FILES['pdf']);
	$newFile->setDirectory($GLOBALS['path'].$moduleClass->getFilePath());
	$curFile = new File($GLOBALS['path'].$moduleClass->getFilePath().$moduleClass->getDbTable().$moduleClass->getId().'.pdf');

	# Generate Permalink
	$moduleClass->setPermalink($moduleClass->generatePermalink($moduleClass->getName(),$moduleClass->getId()))
		->clearMessages()
		->validate()
		->validateUrl(array('url'));

	# Check allowed image types
	$imageCheck = $image->checkImageType('image',$image->getImageTypes());
	if(strlen($imageCheck)){
		$moduleClass->addMessage('image',array('type'=>'failure','text'=>$imageCheck));
	}

	# Check file is a PDF file
	$fileCheck = $newFile->checkFileTypes();
	if(strlen($fileCheck)){
		$moduleClass->addMessage('pdf',array('type'=>'failure','text'=>$fileCheck));
	}

	# Failure
	if($moduleClass->hasMessages()){
		$messages = $moduleClass->getMessages();
		echo failure("Your submission contains errors. Please see review them below.", $messages, 'messages', array($moduleClass->_moduleName,$moduleClass->getName(),$moduleClass->getId()));
		exit;
	}

	# Save record
	$moduleClass->save($queryLogging);

	# Delete image if checked
	if(strlen($_POST['checkbox1'])){
		$image->setDirectory($GLOBALS['path'].$moduleClass->getFilePath());
		$image->deleteImage($moduleClass->getDbTable(),$moduleClass->getId(),$moduleClass->getImage());
		$image->deleteImage($moduleClass->getDbTable(),$moduleClass->getId().'_t',$moduleClass->getImage());
		$moduleClass->setImage('0');
		$moduleClass->save($queryLogging);
	}

	# Delete PDF if checked
	if(strlen($_POST['checkbox2'])){
		$curFile->deleteFile();
		$moduleClass->setPdf('0');
		$moduleClass->save($queryLogging);
	}

	# Process Image
	if(isUploaded('image')){
		$image->setDirectory($GLOBALS['path'].$moduleClass->getFilePath());
		$image->deleteImage($moduleClass->getDbTable(),$moduleClass->getId(),$moduleClass->getImage());
		$image->deleteImage($moduleClass->getDbTable(),$moduleClass->getId().'_t',$moduleClass->getImage());

		$image = new Image($_FILES['image']['tmp_name']);
		$image->setDirectory($GLOBALS['path'].$moduleClass->getFilePath());
		$image->canvas($moduleClass->_moduleImageCanvasX,$moduleClass->_moduleImageCanvasY);
		$image->save($moduleClass->getDbTable().$moduleClass->getId().'.'.$image->getExt());

		$image = new Image($_FILES['image']['tmp_name']);
		$image->setDirectory($GLOBALS['path'].$moduleClass->getFilePath());
		$image->canvas($moduleClass->_moduleThumbCanvasX,$moduleClass->_moduleThumbCanvasY);
		$image->save($moduleClass->getDbTable().$moduleClass->getId().'_t.'.$image->getExt());

		$moduleClass->setImage($image->getExt());
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
	$content = '<strong>'.strip_tags(process($moduleClass->getName())).'</strong> - <em>'.strip_tags($moduleClass->formatDate($moduleClass->getStartDate(), "F j, Y")).'</em>';

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

	$categoryId = $moduleClass->getCategory();

	# If the Record does not exist
	if(!$moduleClass->getId()){
		echo failure('Record not found.');
		exit;
	}

	# Declare file and image class
	$curFile = new File($moduleClass->getDbTable().$moduleClass->getId().'.pdf');

	# Delete images
	$image = new Image();
	$image->setDirectory($GLOBALS['path'].$moduleClass->getFilePath());
	$image->deleteImage($moduleClass->getDbTable(),$moduleClass->getId(),$moduleClass->getImage());
	$image->deleteImage($moduleClass->getDbTable(),$moduleClass->getId().'_t',$moduleClass->getImage());

	# Delete PDF
	$curFile->deleteFile();
	# Delete the record
	$moduleClass->delete();

	# Refresh Data
	$selector = "#menuItem_".$categoryId."\\>".$moduleClass->getId();

	$moduleClass->addRefreshElement($selector);

	$refreshElements = $moduleClass->getRefreshElements();

	# Success
	echo success($moduleClass->getName().' deleted successfully.', $refreshElements, 'refreshData', array($moduleClass->_moduleName,$moduleClass->getName(),$moduleClass->getId()));
	exit;
}
# Active
if($action == 'active'){

	# Create new instance of module class
	$moduleClass = new $moduleClassName($_REQUEST['id']);

	# Create new instance of module category class
	$moduleCategoryClass = new $moduleCategoryClassName($moduleClass->getCategory());

	# If Item does not exist
	if(!$moduleClass->getId()){
		echo failure('Record not found.');
		exit;
	}

	# Remove from active
	if($moduleClass->getActive() == 1){
		$moduleClass->setActive('0');
		$message = $moduleClass->getName().' deactivated successfully.';
	}
	# Add to active
	else{
		$moduleClass->setActive('1');
		$message = $moduleClass->getName().' activated successfully.';
	}

	# Save
	$moduleClass->save($queryLogging);

	# Refresh Data
	$selector = "#menuItem_".$moduleCategoryClass->getId()."\\>".$moduleClass->getId();
	$content = $moduleCategoryClass->buildChild($moduleClass, "2", $moduleClass->_moduleClassName);

	$moduleClass->addRefreshElement($selector, $content, "edit");

	$refreshElements = $moduleClass->getRefreshElements();

	# Success
	echo success($message, $refreshElements, 'refreshData', array($moduleClass->_moduleName,$moduleClass->getName(),$moduleClass->getId()));
	exit;
}
?>
