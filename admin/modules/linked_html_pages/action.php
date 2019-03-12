<?php
# Debugging - set to 1
$dev = 0;

# Includes
include_once('../../includes/library.php');

# Security 
Security::xssProtect();

# Create new instance of module class
$moduleClass = new LinkedHtmlPage();
$moduleClassName = $moduleClass->_moduleClassName;

# Action
$action = $_REQUEST['action'];

# Add
if($action == 'add'){

	# Create new instance of module class
	$moduleClass = new $moduleClassName($_REQUEST);
	
	# Associated Photos
	$associated_photos = serialize($_REQUEST['associated_photos']);
	$moduleClass->setAssociatedPhotos($associated_photos);

	# Generate Permalink / Validate
	$moduleClass->setPermalink($moduleClass->generatePermalink($moduleClass->getName(),'','permalink',$moduleClass->getParentId(),'parent_id'))
		->setPermalinkFull($moduleClass->buildPermalinkFull())
		->setDepth($moduleClass->calculateDepth())
		->clearMessages()
		->validate()
		->validateUrl(array('url','banner_url'));
	
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
	
	# Save
	$moduleClass->save();
	
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
		$moduleClass->setImage($image->getExt())->save();
	}
	
	# Refresh Data
	// If page does not have a parent
	if($moduleClass->getParentId() == 0){
		$selector = ".index-wrapper > ol";
	}
	else{
		$selector = "#menuItem_".$moduleClass->getParentId()." > ol";
	}
	
	
	if($moduleClass->fetchCount("WHERE `id` > 0") == 1){
		$content = $moduleClass->buildSortingStructure();
		$action = 'replace';
	}
	else{
		$content = $moduleClass->buildSortingStructure('all', $moduleClass->getId(), 1, 1, 1);
	}
	
	$moduleClass->addRefreshElement($selector, $content, $action)
				->addRefreshElement(".no_records", "", "delete");
	
	$refreshElements = $moduleClass->getRefreshElements();

	# Success
	echo success('Record added successfully.', $refreshElements);
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
	
	# If Name changed - Permalinks need to be updated
	$updatePermalinks = 0;
	if($moduleClass->getName() != $_POST['name']){
		$updatePermalinks = 1;
	}
	
	# Set Options for module class
	$moduleClass->setOptions($_POST);
	
	# Associated Photos
	$associated_photos = serialize($_POST['associated_photos']);
	$moduleClass->setAssociatedPhotos($associated_photos);
	
	# Generate Permalink / Validate
	$moduleClass->setPermalink($moduleClass->generatePermalink($moduleClass->getName(),$moduleClass->getId(),'permalink',$moduleClass->getParentId(),'parent_id'))
		->clearMessages()
		->validate()
		->validateUrl(array('url','banner_url'));
	
	# Check allowed image types
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
	
	# Delete image if checked
	if(strlen($_POST['checkbox1'])){
		$image->deleteImage($moduleClass->getDbTable(),$moduleClass->getId(),$moduleClass->getImage());
		$image->deleteImage($moduleClass->getDbTable(),$moduleClass->getId().'_t',$moduleClass->getImage());
		$moduleClass->setImage('0');
		$moduleClass->save();
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
		$moduleClass->save();
	}
	
	
	# Save
	$moduleClass->setDepth($moduleClass->calculateDepth())
		->setPermalinkFull($moduleClass->buildPermalinkFull())	
		->save();
	
	# Update Child Permalinks
	$moduleClass->updateChildPermalinks();
	
	# If Name changed - Permalinks need to be updated
	if($updatePermalinks){
		# Refresh Data
		# Update current page sorting container
		$selector = "#menuItem_".$moduleClass->getId()." > .menuDiv";
		$content = $moduleClass->toHtml('default_list');
		$moduleClass->addRefreshElement($selector, $content, 'replace');
		
		# Update children tree structure
		$selector = "#menuItem_".$moduleClass->getId()." > ol";
		$content = $moduleClass->buildSortingStructure('all', $moduleClass->getId());
		$moduleClass->addRefreshElement($selector, $content, 'replace');
	}
	else{
		# Refresh Data
		$selector = "#menuItem_".$moduleClass->getId()." > .menuDiv .itemTitle";
		$content = process($moduleClass->getName());
		$moduleClass->addRefreshElement($selector, $content);
	}
	
	$refreshElements = $moduleClass->getRefreshElements();
	
	# Success
	echo success('Record modified successfully.', $refreshElements);
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

	# Check for child records
	if($moduleClass->fetchCount("WHERE `parent_id` = ".$moduleClass->getId())){
		echo failure('Sub-Pages still belong to this page.');
		exit;
	}
	
	# Delete Images
	$image = new Image();
	$image->deleteImage($moduleClass->getDbTable(),$moduleClass->getId(),$moduleClass->getImage());
	$image->deleteImage($moduleClass->getDbTable(),$moduleClass->getId().'_t',$moduleClass->getImage());
	
	# Delete
	$moduleClass->delete();
	
	# Refresh Data
	$selector = "#menuItem_".$_POST['id'];
	
	$moduleClass->addRefreshElement($selector);
	
	if(!$moduleClass->fetchCount("WHERE `id` > 0")){
		$moduleClass->addRefreshElement(".index-wrapper", "<div class='no_records'><i class='fa fa-times-circle'></i>No records available.</div>", "add");
	}
	
	$refreshElements = $moduleClass->getRefreshElements();
	
	# Success
	echo success('Record modified successfully.', $refreshElements);
	exit;	
}
# Active
if($action == 'active'){
	
	# Create new instance of module class
	$moduleClass = new $moduleClassName($_REQUEST['id']);
	
	# If the Record does not exist
	if(!$moduleClass->getId()){
		echo failure('Record not found.');
		exit;
	}

	# Switch active status
	if($moduleClass->getActive() == 1){
		$moduleClass->setActive(0);
	}else{
		$moduleClass->setActive(1);
	}

	# Save
	$moduleClass->save();
	
	# Success
	echo success('Record modified successfully.');
	exit;
}
# Locked
if($action == 'locked'){
	
	# Create new instance of module class
	$moduleClass = new $moduleClassName($_REQUEST['id']);
	
	# If the Record does not exist
	if(!$moduleClass->getId()){
		echo failure('Record not found.');
		exit;
	}

	# Switch locked status
	if($moduleClass->getLocked() == 1){
		$moduleClass->setLocked(0);
	}else{
		$moduleClass->setLocked(1);
	}

	# Save
	$moduleClass->save();
	
	# Refresh Data
	$selector = "#menuItem_".$moduleClass->getId()." > .menuDiv";
	$content = $moduleClass->toHtml('default_list');
	
	$moduleClass->addRefreshElement($selector, $content, "replace");
	
	$refreshElements = $moduleClass->getRefreshElements();
	
	# Success
	echo success('Record modified successfully.', $refreshElements);
	exit;
}
?>