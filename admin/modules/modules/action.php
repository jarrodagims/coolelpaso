<?php
# Debugging - set to 1
$dev = 0;

# Includes
include_once('../../includes/library.php');

# Security 
Security::xssProtect();

# Create new instance of module class
$moduleClass = new CmsModule();
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
	
	# Failure	
	if($moduleClass->hasMessages()){
		$messages = $moduleClass->getMessages();
		echo failure("Your submission contains errors. Please see review them below.", $messages);
		exit;
	}
	
	# Save the record
	$moduleClass->save();
	
	# mySQL INSERT Failure
	if(!$moduleClass->getId()){
		echo failure('Record could not be added.');
		exit;
	}

	# Refresh Data
	$selector = "ol.sortable";
	$content = $moduleClass->toHtml('default-list');
	
	$moduleClass->addRefreshElement($selector, $content)
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
	
	# Set Options for module class
	$moduleClass->setOptions($_POST);

	# Generate Permalink
	$moduleClass->clearMessages()
				->validate();
	
	# Failure
	if($moduleClass->hasMessages()){
		$messages = $moduleClass->getMessages();
		echo failure("Your submission contains errors. Please see review them below.", $messages);
		exit;
	}

	# Save record
	$moduleClass->save();

	# Refresh Data
	$selector = "#".$moduleClassName."_".$moduleClass->getId()." .itemTitle";
	$content = strip_tags(process($moduleClass->getName()));
	
	$moduleClass->addRefreshElement($selector, $content);
	
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
	echo success('Record deleted successfully.', $refreshElements);
	exit;
}
# Active
if($action == 'enabled'){
	
	# Create new instance of module class
	$moduleClass = new $moduleClassName($_REQUEST['id']);
	
	# If Item does not exist
	if(!$moduleClass->getId()){
		echo failure('Record not found.');
		exit;
	}

	# Remove from active
	if($moduleClass->getEnabled() == 1){
		$moduleClass->setEnabled('0');
	}
	# Add to active
	else{
		$moduleClass->setEnabled('1');
	}
	
	# Save
	$moduleClass->save();
	
	# Success
	echo success('Record modified successfully.', $refreshElements);
	exit;	
}
?>