<?php
# Debugging - set to 1
$dev = 0;
$queryLogging = 1;
# Includes
include_once('../../includes/library.php');

# Security
Security::xssProtect();

# Create new instance of module class
$moduleClass = new HomepageBanner($_REQUEST);
$moduleClassName = $moduleClass->_moduleClassName;
extract($_REQUEST);

// Add
if($action == 'add'){

	$moduleClass = new $moduleClassName($_POST);

	$moduleClass->clearMessages()
				->validate()
				->validateUrl(array('url'));

	// Check allowed image types
	$image = new Image();
	$imageCheck = $image->checkImageType('image', $image->getImageTypes());
	if(strlen($imageCheck)){
		$moduleClass->addMessage('image', array('type'=>'failure', 'text'=>$imageCheck));
	}

	// Failure
	$messages = $moduleClass->getMessages();
	if($moduleClass->hasMessages()){
		echo failure("Your submission contains errors. Please see review them below.", $messages);
		exit;
	}

	// Save record
	$moduleClass->save($queryLogging);

	// Process Image
	if(isUploaded('image')){

		$image = new Image($_FILES['image']['tmp_name']);
		$image->setDirectory($GLOBALS['path'].$moduleClass->getFilePath());
		$image->canvas($moduleClass->_moduleThumbCanvasX,$moduleClass->_moduleThumbCanvasY, FALSE);
		$image->save($moduleClass->getDbTable().$moduleClass->getId().'_t.'.$image->getExt(),90);
		$moduleClass->setExtension($image->getExt())->save($queryLogging);

		$image = new Image($_FILES['image']['tmp_name']);
		$image->setDirectory($GLOBALS['path'].$moduleClass->getFilePath());
		$image->canvas($moduleClass->_moduleImageCanvasX,$moduleClass->_moduleImageCanvasY,FALSE,'#fff');
		$image->save($moduleClass->getDbTable().$moduleClass->getId().'.'.$image->getExt(),90);
		$moduleClass->setExtension($image->getExt())->save($queryLogging);

	}

	// Refresh Data
	$selector = ".index-wrapper";
	$content = $moduleClass->toHtml('module-index');
	$moduleClass->addRefreshElement($selector, $content, "replace");
	$refreshElements = $moduleClass->getRefreshElements();

	// Success
	echo success($moduleClass->getName().' added successfully.', $refreshElements, 'refreshData', array($moduleClass->_moduleName,$moduleClass->getName(),$moduleClass->getId()));
}

// Edit
if($action == 'edit'){
	if(!$moduleClass->getId()){
		echo failure('Record not found.');
		exit;
	}
	$moduleClass = new $moduleClassName($id);

	$moduleClass->setOptions($_POST);

	$moduleClass->clearMessages()
				->validate()
				->validateUrl(array('url'));

	// Check allowed image types
	$image = new Image();
	$imageCheck = $image->checkImageType('image',$image->getImageTypes());
	if(strlen($imageCheck)){
		$moduleClass->addMessage('image',array('type'=>'failure','text'=>$imageCheck));
	}

	// Failure
	$messages = $moduleClass->getMessages();
	if($moduleClass->hasMessages()){
		echo failure("Your submission contains errors. Please see review them below.", $messages, 'messages', array($moduleClass->_moduleName,$moduleClass->getName(),$moduleClass->getId()));
		exit;
	}

	// Save record
	$moduleClass->save($queryLogging);

	// Process Image
	if(isUploaded('image')){

		$image->deleteImage($moduleClass->getDbTable(),$moduleClass->getId(),$moduleClass->getExtension());
		$image->deleteImage($moduleClass->getDbTable(),$moduleClass->getId().'_t',$moduleClass->getExtension());

		$image = new Image($_FILES['image']['tmp_name']);
		$image->setDirectory($GLOBALS['path'].$moduleClass->getFilePath());
		$image->canvas($moduleClass->_moduleThumbCanvasX,$moduleClass->_moduleThumbCanvasY, FALSE);
		$image->save($moduleClass->getDbTable().$moduleClass->getId().'_t.'.$image->getExt(),90);
		$moduleClass->setExtension($image->getExt())->save($queryLogging);

		$image = new Image($_FILES['image']['tmp_name']);
		$image->setDirectory($GLOBALS['path'].$moduleClass->getFilePath());
		$image->canvas($moduleClass->_moduleImageCanvasX,$moduleClass->_moduleImageCanvasY, FALSE,'#fff');
		$image->save($moduleClass->getDbTable().$moduleClass->getId().'.'.$image->getExt(),90);
		$moduleClass->setExtension($image->getExt())->save($queryLogging);

	}

	// Refresh Data
	$selector = '';
	$content = array("id"=>$moduleClass->getId(),"caption"=>$moduleClass->getName());
	$moduleClass->addRefreshElement($selector, $content);
	$refreshElements = $moduleClass->getRefreshElements();

	// Success
	echo success($moduleClass->getName().' modified successfully.', $refreshElements, 'refreshData', array($moduleClass->_moduleName,$moduleClass->getName(),$moduleClass->getId()));
}

// Delete
if($action == 'delete'){

	$moduleClass = new $moduleClassName($_REQUEST['id']);

	if(!$moduleClass->getId()){
		echo json_encode(array('failure'=>'Record not found.'));
		exit;
	}
	

	// Delete images
	$image = new Image();
	$image->setDirectory($GLOBALS['path'].$moduleClass->getFilePath());
	$image->deleteImage($moduleClass->getDbTable(),$moduleClass->getId(),$moduleClass->getExtension());
	$image->deleteImage($moduleClass->getDbTable(),$moduleClass->getId().'_t',$moduleClass->getExtension());

	// Delete
	$moduleClass->delete();

	// Success
	success($moduleClass->getName().' deleted successfully.', '', 'refreshData', array($moduleClass->_moduleName,$moduleClass->getName(),$moduleClass->getId()));
	echo json_encode(array());
	exit;
}

//sort files
if($action == 'sort'){
	foreach($elements as $key => $val) {
		$moduleClass = new $moduleClassName($val);
		if($moduleClass->getId()){
			$moduleClass->setSortOrder($key);
			$moduleClass->save($queryLogging);
			success('Sort order for '.$moduleClass->getName().' changed to '.$moduleClass->getSortOrder().' successfully.', '', 'refreshData', array($moduleClass->_moduleName,$moduleClass->getName(),$moduleClass->getId()));
		}
	}
}

//toggle active
if($action == 'toggle'){
	$id = str_replace("toggle_", "", $id);
	$moduleClass = new $moduleClassName($id);
	if($moduleClass->getId()){
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
		$moduleClass->save($queryLogging);
		# Success
		echo success($message, $refreshElements, 'refreshData', array($moduleClass->_moduleName,$moduleClass->getName(),$moduleClass->getId()));
		exit;
	}
}

//bulk upload files
if($action == 'upload'){

	// Make sure file is not cached (as it happens for example on iOS devices)
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");

	// 5 minutes execution time
	@set_time_limit(5 * 60);

	// set temp directory
	$targetDir = $GLOBALS['path'] . "admin" . DIRECTORY_SEPARATOR . "modules" . DIRECTORY_SEPARATOR . $moduleClass->_moduleDir . DIRECTORY_SEPARATOR . "temp";

	// Create target dir
	if (!file_exists($targetDir)) {
		@mkdir($targetDir);
	}

	$fileName = uniqid("file_"); //define file name
	$fileUploadPaths = array(''); //define upload path array
	$fileUploadConfigs = array(''); //define image info array
	$filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;
	$numUploadedfiles = count($_FILES["file_input_modal"]);
	for($i = 0; $i < $numUploadedfiles; $i++)
	{
		if(is_uploaded_file($_FILES["file_input_modal"]["tmp_name"][$i])){
			// Remove old temp files
			if(!is_dir($targetDir) || !$dir = opendir($targetDir)){
				echo json_encode(array('error'=>'Failed to open temp directory.'));
				exit;
			}
			while(($file = readdir($dir)) !== false){
				$tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;
				// If temp file is current file proceed to the next

				if($tmpfilePath == "{$filePath}.part"){
					continue;
				}
				// Remove temp file if it is older than the max age and is not the current file
				if(preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - 5 * 3600)){
					@unlink($tmpfilePath);
				}
			}
			closedir($dir);

			// Open temp file
			if(!$out = @fopen("{$filePath}.part", "ab" )){
				echo json_encode(array('error'=>'Failed to open output stream.'));
				exit;
			}
			if(!empty($_FILES)){
				if($_FILES["file_input_modal"]["error"][$i] || !is_uploaded_file($_FILES["file_input_modal"]["tmp_name"][$i])){
					echo json_encode(array('error'=>'Failed to move uploaded file: '.$_FILES["file_input_modal"]["tmp_name"][$i]));
					exit;
				}
				// Read binary input stream and append it to temp file
				if(!$in = @fopen($_FILES["file_input_modal"]["tmp_name"][$i], "rb")){
					echo json_encode(array('error'=>'Failed to open input stream.'));
					exit;
				}
			}else{
				if(!$in = @fopen("php://input", "rb")){
					echo json_encode(array('error'=>'Failed to open input stream.'));
					exit;
				}
			}
			// move file in pieces from server $_FILES folder to $targetDir folder
			while($buff = fread($in, 4096)){
				fwrite($out, $buff);
			}
			@fclose($out);
			@fclose($in);

			// Strip the temp .part suffix off
			rename("{$filePath}.part", $filePath);

			// Use if users can rename each photo
			$photoName = $name.' ';
			$image = new Image($filePath);
			$moduleClass->setExtension($image->getExt())
						->save($queryLogging);
			$moduleClass->setName($photoName.' '.$moduleClass->getId())->save($queryLogging);

			// process image thumb
			$image = new Image($filePath);
			$image->setDirectory($GLOBALS['path'].$moduleClass->getFilePath());
			$image->canvas($moduleClass->_moduleThumbCanvasX,$moduleClass->_moduleThumbCanvasY, FALSE);
			$image->save($moduleClass->getDbTable().$moduleClass->getId().'_t.'.$image->getExt(),90);

			// process main image
			$image = new Image($filePath);
			$image->setDirectory($GLOBALS['path'].$moduleClass->getFilePath());
			$image->canvas($moduleClass->_moduleImageCanvasX,$moduleClass->_moduleImageCanvasY, FALSE,'#fff');
			$image->save($moduleClass->getDbTable().$moduleClass->getId().'.'.$image->getExt(),90);

			// Remove the temp image
			unlink($filePath);

			//create array of file paths for display
			$fileUploadPaths[] = DIRECTORY_SEPARATOR.$moduleClass->getFilePath().$moduleClass->getDbTable().$moduleClass->getId().'_t.'.$moduleClass->getExtension().'?'.rand(1,1000);

			//create array of file configs for display
			$fileUploadConfigs[] = array("width"=>$moduleClass->_moduleThumbCanvasX.'px',"height"=>$moduleClass->_moduleThumbCanvasY.'px',"caption"=>$moduleClass->getName(),"url"=>'action.php?action=delete&id='.$moduleClass->getId(),"key"=>$moduleClass->getId(),"extra"=>array("sort"=>$moduleClass->getSortOrder()));
		}

	}
	// Returns new image previews as a Success response to fileinput.js
	echo json_encode(array('initialPreview' => $fileUploadPaths,'initialPreviewConfig' => $fileUploadConfigs,'append' => true ));
}

?>
