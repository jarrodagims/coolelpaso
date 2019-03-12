<?php
# Debugging - set to 1
$dev = 0;
$queryLogging = 1;
# Includes
include_once('../../includes/library.php');

# Security 
Security::xssProtect();

# Create new instance of module class
$moduleClass = new Testimonial();
$moduleClassName = $moduleClass->_moduleClassName;

# Action
$action = $_REQUEST['action'];

# Add
if($action == 'add'){
	
	# Create new instance of module class
	$moduleClass = new $moduleClassName($_REQUEST);

	# Generate Permalink / Validate
	$moduleClass->clearMessages()
				->validate();
	
	// Check allowed image types
	$image = new Image();
	$imageCheck = $image->checkImageType('image',$image->getImageTypes());
	if(strlen($imageCheck)){
		$moduleClass->addMessage('image',array('type'=>'failure','text'=>$imageCheck));
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
		$image->canvas($moduleClass->_moduleImageCanvasX,$moduleClass->_moduleImageCanvasY);
		$image->save($moduleClass->getDbTable().$moduleClass->getId().'.'.$image->getExt());
		
		$image = new Image($_FILES['image']['tmp_name']);
		$image->canvas($moduleClass->_moduleThumbCanvasX,$moduleClass->_moduleThumbCanvasY);
		$image->save($moduleClass->getDbTable().$moduleClass->getId().'_t.'.$image->getExt());
		$moduleClass->setImage($image->getExt())->save($queryLogging);
	}

	# Refresh Data
	$selector = "ol.sortable";
	$content = $moduleClass->toHtml('default-list');
	
	$moduleClass->addRefreshElement($selector, $content)
						->addRefreshElement(".no_records", "", "delete");
	
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

	# Generate Permalink
	$moduleClass->clearMessages()
				->validate();

	// Check allowed image types
	$image = new Image();
	$imageCheck = $image->checkImageType('image',$image->getImageTypes());
	if(strlen($imageCheck)){
		$moduleClass->addMessage('image',array('type'=>'failure','text'=>$imageCheck));
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
	if($_POST['checkbox1'] != '0'){
		$image->deleteImage($moduleClass->getDbTable(),$moduleClass->getId(),$moduleClass->getImage());
		$image->deleteImage($moduleClass->getDbTable(),$moduleClass->getId().'_t',$moduleClass->getImage());
		$moduleClass->setImage('0');
		$moduleClass->save($queryLogging);
	}
	
	# Process Image
	if(isUploaded('image')){
		$image->deleteImage($moduleClass->getDbTable(),$moduleClass->getId(),$moduleClass->getImage());
		$image->deleteImage($moduleClass->getDbTable(),$moduleClass->getId().'_t',$moduleClass->getImage());
		
		$image = new Image($_FILES['image']['tmp_name']);
		$image->canvas($moduleClass->_moduleImageCanvasX,$moduleClass->_moduleImageCanvasY);
		$image->save($moduleClass->getDbTable().$moduleClass->getId().'.'.$image->getExt());
		
		$image = new Image($_FILES['image']['tmp_name']);
		$image->canvas($moduleClass->_moduleThumbCanvasX,$moduleClass->_moduleThumbCanvasY);
		$image->save($moduleClass->getDbTable().$moduleClass->getId().'_t.'.$image->getExt());
		
		$moduleClass->setImage($image->getExt());
		$moduleClass->save($queryLogging);
	}

	# Refresh Data
	$selector = "#".$moduleClassName."_".$moduleClass->getId()." .itemTitle";
	$content = strip_tags(process($moduleClass->getName()));
	
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

	# Delete images
	$image = new Image();
	$image->deleteImage($moduleClass->getDbTable(),$moduleClass->getId(),$moduleClass->getImage());
	$image->deleteImage($moduleClass->getDbTable(),$moduleClass->getId().'_t',$moduleClass->getImage());
	
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
	echo success($moduleClass->getName().' deleted successfully.', $refreshElements, 'refreshData', array($moduleClass->_moduleName,$moduleClass->getName(),$moduleClass->getId()));
	exit;
}
# Active
if($action == 'active'){
	
	# Create new instance of module class
	$moduleClass = new $moduleClassName($_REQUEST['id']);
	
	# If Item does not exist
	if(!$moduleClass->getId()){
		echo failure('Record not found.');
		exit;
	}

	# Remove from active
	if($moduleClass->getActive() == 1){
		$moduleClass->setActive('0');
		$message = $moduleClass->getName().' disabled successfully.';
	}
	# Add to active
	else{
		$moduleClass->setActive('1');
		$message = $moduleClass->getName().' activated successfully.';
	}
	
	# Save
	$moduleClass->save($queryLogging);
	
	# Success
	echo success($message, $refreshElements, 'refreshData', array($moduleClass->_moduleName,$moduleClass->getName(),$moduleClass->getId()));
	exit;	
}
?>