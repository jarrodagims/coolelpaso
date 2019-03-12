<?php
# Debugging - set to 1
$dev = 0;
$queryLogging = 1;
# Includes
include_once('../../includes/library.php');

# Security 
Security::xssProtect();

# Create new instance of module class
$moduleClass = new HtmlPage();
$moduleClassName = $moduleClass->_moduleClassName;

# Action
$action = $_REQUEST['action'];

# Add
if($action == 'add'){

	# Create new instance of module class
	$moduleClass = new $moduleClassName($_REQUEST);

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
	
	$imageCheck = $image->checkImageType('image_two',$image->getImageTypes());
	if(strlen($imageCheck)){
		$moduleClass->addMessage('image_two',array('type'=>'failure','text'=>$imageCheck));
	}
	
	$imageCheck = $image->checkImageType('image_three',$image->getImageTypes());
	if(strlen($imageCheck)){
		$moduleClass->addMessage('image_three',array('type'=>'failure','text'=>$imageCheck));
	}
	
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
	
	if(isUploaded('image_two')){
		$image = new Image($_FILES['image_two']['tmp_name']);
		$image->canvas($moduleClass->_moduleImageProductCanvasX,$moduleClass->_moduleImageProductCanvasY);
		$image->save($moduleClass->getDbTable().$moduleClass->getId().'_two.'.$image->getExt());
		$moduleClass->setImageTwo($image->getExt())->save($queryLogging);
	}
	
	if(isUploaded('image_three')){
		$image = new Image($_FILES['image_three']['tmp_name']);
		$image->canvas($moduleClass->_moduleImageProductCanvasX,$moduleClass->_moduleImageProductCanvasY);
		$image->save($moduleClass->getDbTable().$moduleClass->getId().'_three.'.$image->getExt());
		$moduleClass->setImageThree($image->getExt())->save($queryLogging);
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
	
	# If Name changed - Permalinks need to be updated
	$updatePermalinks = 0;
	if($moduleClass->getName() != $_POST['name']){
		$updatePermalinks = 1;
	}
	
	# Set Options for module class
	$moduleClass->setOptions($_POST);
	
	# Generate Permalink / Validate
	$moduleClass->setPermalink($moduleClass->generatePermalink($moduleClass->getName(),$moduleClass->getId(),'permalink',$moduleClass->getParentId(),'parent_id'))
		->clearMessages()
		->validate()
		->validateUrl(array('url','banner_url'));
	
	// Check allowed image types
	$image = new Image();
	$imageCheck = $image->checkImageType('image',$image->getImageTypes());
	if(strlen($imageCheck)){
		$moduleClass->addMessage('image',array('type'=>'failure','text'=>$imageCheck));
	}
	
	$imageCheck = $image->checkImageType('image_two',$image->getImageTypes());
	if(strlen($imageCheck)){
		$moduleClass->addMessage('image_two',array('type'=>'failure','text'=>$imageCheck));
	}
	
	$imageCheck = $image->checkImageType('image_three',$image->getImageTypes());
	if(strlen($imageCheck)){
		$moduleClass->addMessage('image_three',array('type'=>'failure','text'=>$imageCheck));
	}
	
	# Failure
	if($moduleClass->hasMessages()){
		$messages = $moduleClass->getMessages();
		echo failure("Your submission contains errors. Please see review them below.", $messages, 'messages', array($moduleClass->_moduleName,$moduleClass->getName(),$moduleClass->getId()));
		exit;
	}
	
	# Delete image if checked
	if($_POST['checkbox1'] != '0'){
		$image->deleteImage($moduleClass->getDbTable(),$moduleClass->getId(),$moduleClass->getImage());
		$image->deleteImage($moduleClass->getDbTable(),$moduleClass->getId().'_t',$moduleClass->getImage());
		$moduleClass->setImage('0');
		$moduleClass->save($queryLogging);
	}
	
	if($_POST['checkbox2'] != '0'){
		$image->deleteImage($moduleClass->getDbTable(),$moduleClass->getId().'_t',$moduleClass->getImageTwo());
		$moduleClass->setImageTwo('0');
		$moduleClass->save($queryLogging);
	}
	
	if($_POST['checkbox3'] != '0'){
		$image->deleteImage($moduleClass->getDbTable(),$moduleClass->getId().'_t',$moduleClass->getImageThree());
		$moduleClass->setImageThree('0');
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
	
	
	
	if(isUploaded('image_two')){
		$image->deleteImage($moduleClass->getDbTable(),$moduleClass->getId().'_two',$moduleClass->getImageTwo());
		$image = new Image($_FILES['image_two']['tmp_name']);
		$image->canvas($moduleClass->_moduleImageProductCanvasX,$moduleClass->_moduleImageProductCanvasY);
		$image->save($moduleClass->getDbTable().$moduleClass->getId().'_two.'.$image->getExt());
		$moduleClass->setImageTwo($image->getExt())->save($queryLogging);
	}
	
	if(isUploaded('image_three')){
		$image->deleteImage($moduleClass->getDbTable(),$moduleClass->getId().'_three',$moduleClass->getImageThree());
		$image = new Image($_FILES['image_three']['tmp_name']);
		$image->canvas($moduleClass->_moduleImageProductCanvasX,$moduleClass->_moduleImageProductCanvasY);
		$image->save($moduleClass->getDbTable().$moduleClass->getId().'_three.'.$image->getExt());
		$moduleClass->setImageThree($image->getExt())->save($queryLogging);
	}
	
	
	
	# Save
	$moduleClass->setDepth($moduleClass->calculateDepth())
		->setPermalinkFull($moduleClass->buildPermalinkFull())	
		->save($queryLogging);
	
	# Update Child Permalinks
	$moduleClass->updateChildPermalinks();
	
	# If Name changed - Permalinks need to be updated
	if($updatePermalinks){
		# Refresh Data
		$selector = "#menuItem_".$moduleClass->getId();
		$content = $moduleClass->buildSortingStructure('all', $moduleClass->getId(), 1, 2);
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

	# Check for child records
	if($moduleClass->fetchCount("WHERE `parent_id` = ".$moduleClass->getId())){
		echo failure('Sub-Pages still belong to this page.', '', 'messages', array($moduleClass->_moduleName,$moduleClass->getName(),$moduleClass->getId()));
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
	echo success($moduleClass->getName().' deleted successfully.', $refreshElements, 'refreshData', array($moduleClass->_moduleName,$moduleClass->getName(),$moduleClass->getId()));
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
		$message = $moduleClass->getName().' deactivated successfully.';
	}else{
		$moduleClass->setActive(1);
		$message = $moduleClass->getName().' activated successfully.';
	}

	# Save
	$moduleClass->save($queryLogging);
	
	# Success
	echo success($message, '', 'refreshData', array($moduleClass->_moduleName,$moduleClass->getName(),$moduleClass->getId()));
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
		$message = $moduleClass->getName().' unlocked successfully.';
	}else{
		$moduleClass->setLocked(1);
		$message = $moduleClass->getName().' locked successfully.';
	}

	# Save
	$moduleClass->save($queryLogging);
	
	# Refresh Data
	$selector = "#menuItem_".$moduleClass->getId()." > .menuDiv";
	$content = $moduleClass->toHtml('default_list');
	
	$moduleClass->addRefreshElement($selector, $content, "replace");
	
	$refreshElements = $moduleClass->getRefreshElements();
	
	# Success
	echo success($message, $refreshElements, 'refreshData', array($moduleClass->_moduleName,$moduleClass->getName(),$moduleClass->getId()));
	exit;
}
if($action == 'super_admin'){
	
	# Create new instance of module class
	$moduleClass = new $moduleClassName($_REQUEST['id']);
	
	# If the Record does not exist
	if(!$moduleClass->getId()){
		echo failure('Record not found.');
		exit;
	}

	# Switch locked status
	if($moduleClass->getSuperAdmin() == 1){
		$moduleClass->setSuperAdmin(0);
		$message = 'Super admin successfully disabled for '.$moduleClass->getName();
	}else{
		$moduleClass->setSuperAdmin(1);
		$message = 'Super admin successfully enabled for '.$moduleClass->getName();
	}

	# Save
	$moduleClass->save($queryLogging);
	
	# Refresh Data
	$selector = "#menuItem_".$moduleClass->getId()." > .menuDiv";
	$content = $moduleClass->toHtml('default_list');
	
	$moduleClass->addRefreshElement($selector, $content, "replace");
	
	$refreshElements = $moduleClass->getRefreshElements();
	
	# Success
	echo success($message, $refreshElements, 'refreshData', array($moduleClass->_moduleName,$moduleClass->getName(),$moduleClass->getId()));
	exit;
}
?>