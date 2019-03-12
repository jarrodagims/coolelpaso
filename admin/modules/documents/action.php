<?php
# Debugging - set to 1
$dev = 0;
$queryLogging = 1;
include_once('../../includes/library.php');

Security::xssProtect();
# Create new instance of module class
$currentModule = new Document();
$module_dir = $currentModule->_moduleDir;
$moduleClassName = $currentModule->_moduleClassName;
$moduleCategoryClassName = $currentModule->_moduleCategoryClassName;
extract($_REQUEST);
// Edit
if($action == 'add'){
	$_POST['category'] = (int) $_POST['category'];
	$moduleClass = new $moduleClassName($_POST);

	// Generate Permalink / Validate
	$moduleClass->setPermalink($moduleClass->generatePermalink($moduleClass->getTitle()))
		->clearMessages()
		->validate()
		->validateUrl(array('url'));
		
	// Declare file class
	$newFile = new File($_FILES['file']);
	
	// Make sure either a document was uploaded or a URL was specified
	if(!strlen(trim($moduleClass->getUrl())) && !isUploaded('file')){
		$moduleClass->addMessage('file',array('type'=>'failure','text'=>'Please upload a document or specify a URL'));
	}
	
	// Failure
	if($moduleClass->hasMessages()){
		$messages = $moduleClass->getMessages();
		echo failure("Your submission contains errors. Please see review them below.", $messages);
		exit;
	}

	// Save record
	$moduleClass->setDate(date('Y-m-d H:i:s'))
				->save($queryLogging);
	if(!$moduleClass->getId()){
		echo failure('Record could not be added.');
		exit;
	}

	// Process document file
	if(isUploaded('file')){
		// Save the file
		$moduleClass->setFile(1)
			->setType($newFile->getMime())
			->setName($newFile->getName())
			->setExtension($newFile->getExtension())
			->save($queryLogging);
		$filePath = $newFile->save($moduleClass->getDbTable().$moduleClass->getId());
		// Validate the save
		if(!is_file($filePath)){
			$moduleClass->addMessage('file',array('type'=>'failure','text'=>'The document failed to upload.'));
			$messages = $moduleClass->getMessages();
			echo failure('There was an error uploading your document.', $messages);
			exit;
		}
		//create file path for display
		$fileUploadPaths[] = '/files/'.$moduleClass->getDbTable().$moduleClass->getId().'.'.$moduleClass->getExtension().'?'.rand(1,1000);

		//create file config for display
		$fileUploadConfigs[] = array("type"=>'pdf',"width"=>$moduleClass->_moduleThumbCanvasX.'px',"height"=>$moduleClass->_moduleThumbCanvasY.'px',"caption"=>$moduleClass->getTitle().'.'.$moduleClass->getExtension(),"url"=>'action.php?action=delete&id='.$moduleClass->getId(),"key"=>$moduleClass->getId(),"extra"=>array("sort"=>$moduleClass->getSortOrder()));

		$content = array('initialPreview' => $fileUploadPaths,'initialPreviewConfig' => $fileUploadConfigs,'append' => true,'category' => $moduleClass->getCategory());
	}
	
	// Process URL override
	if(trim($moduleClass->getUrl())){
		$type = 'text';
		//set extension to web fileinput.js plugin uses the caption extension to determine file icon to use
		$moduleClass->setExtension('web')->save($queryLogging);
		//dertermine if URL is internal or external and set fileinput.js configs accordingly
		if(substr($moduleClass->getUrl(), 0, 1) === '/'){
			$fileUploadPaths[] = 'http://'.$_SERVER['HTTP_HOST'].$moduleClass->getUrl();
		}else{
			$extension = explode('.', $moduleClass->getUrl());
			if($newFile->isAcceptedExtension(end($extension),$child->getFileTypes())){$type = 'html';}
			$fileUploadPaths[] = $moduleClass->getUrl();
		}
		//create file config for display
		$fileUploadConfigs[] = array("type"=>$type,"width"=>$moduleClass->_moduleThumbCanvasX.'px',"height"=>$moduleClass->_moduleThumbCanvasY.'px',"caption"=>$moduleClass->getTitle().'.web',"url"=>'action.php?action=delete&id='.$moduleClass->getId(),"key"=>$moduleClass->getId(),"extra"=>array("sort"=>$moduleClass->getSortOrder()));
		$content = array('initialPreview' => $fileUploadPaths,'initialPreviewConfig' => $fileUploadConfigs,'append' => true,'category' => $moduleClass->getCategory());
	}
	
	// Refresh Data
	$selector = '';
	$moduleClass->addRefreshElement($selector, $content);
	$refreshElements = $moduleClass->getRefreshElements();
	
	// Success
	echo success($moduleClass->getTitle().' added successfully.', $refreshElements, 'refreshData', array($moduleClass->_moduleName,$moduleClass->getTitle(),$moduleClass->getId()));
	exit;
}
if($action == 'edit'){
	$category = (int)$category;
	$moduleClass = new $moduleClassName($id);
	if(!$moduleClass->getId()){
		echo failure('Record not found.');
		exit;
	}
	$moduleClass->setOptions($_POST);
	
	// Generate Permalink
	$moduleClass->setPermalink($moduleClass->generatePermalink($moduleClass->getName(),$moduleClass->getId()))
				->clearMessages()
				->validate()
				->validateUrl(array('url'));
	
	// Declare file class
	$newFile = new File($_FILES['file']);
	$curFile = new File($moduleClass->getDbTable().$moduleClass->getId().'.'.$moduleClass->getExtension());
	
	//preserve extension
	if(strlen(trim($moduleClass->getUrl())) && ($moduleClass->getFile() == '1' || isUploaded('file'))){
		if(isUploaded('file')){
			$moduleClass->setExtensionUrl($newFile->getExtension())
				->setType($newFile->getMime())
				->setName($newFile->getName())
				->save($queryLogging);
		}else{
			$moduleClass->setExtensionUrl($moduleClass->getExtension())->save($queryLogging);
		}
	}
	
	if(!strlen(trim($moduleClass->getUrl())) && $moduleClass->getExtensionUrl()){
		$moduleClass->setExtension($moduleClass->getExtensionUrl())->save($queryLogging);
	}
	
	// Make sure the record has a document, a document was uploaded, or a URL was specified
	// If the document is being deleted make sure there is a URL specified
	$fileOld = '0';
	if($checkbox1){
		$fileOld = $moduleClass->getFile();
		$moduleClass->setFile('0');	
	}
	if(!strlen(trim($moduleClass->getUrl())) && $moduleClass->getFile() != '1' && !isUploaded('file')){
		$moduleClass->addMessage('file',array('type'=>'failure','text'=>'Please upload a document or specify a URL'));
		$moduleClass->setFile($fileOld);
	}

	// Failure
	if($moduleClass->hasMessages()){
		$messages = $moduleClass->getMessages();
		echo failure("Your submission contains errors. Please see review them below.", $messages, 'messages', array($moduleClass->_moduleName,$moduleClass->getTitle(),$moduleClass->getId()));
		exit;
	}
	
	// Check for file deletion
	if($checkbox1){
		$curFile->deleteFile();
		$moduleClass->setFile('0')
					->setType('')
					->setExtension('')->save($queryLogging);
	}
	
	// Save record
	$moduleClass->setDate(date('Y-m-d H:i:s'))
				->save($queryLogging);

	if(isUploaded('file')){
		// Delete current file
		$curFile->deleteFile();
		
		// Set file vars
		$uploadedFile = $_FILES['file']['name'];
		$moduleClass->setFile(1)
			->setType($newFile->getMime())
			->setName($newFile->getName())
			->setExtension($newFile->getExtension())
			->save();
		$filePath = $newFile->save($moduleClass->getDbTable().$moduleClass->getId());
		// Validate the save
		if(!is_file($filePath)){
			$moduleClass->addMessage('file',array('type'=>'failure','text'=>'The document failed to upload.'));
			$messages = $moduleClass->getMessages();
			echo failure('There was an error uploading your document.', $messages, 'messages', array($moduleClass->_moduleName,$moduleClass->getTitle(),$moduleClass->getId()));
			exit;
		}
		// File save success
		$moduleClass->save($queryLogging);
	}

	// Process URL override
	if(trim($moduleClass->getUrl())){
		//set extension to web fileinput.js plugin uses the caption extension to determine file icon to use
		$moduleClass->setExtension('web')->save($queryLogging);
	}
	
	
	// recreate documents array for display
	$children = $moduleClass->fetchAll("WHERE `category`='".$moduleClass->getCategory()."' ","ORDER BY `sort_order`,`id`");
	foreach($children as $child){
			// Process URL override
		if(trim($child->getUrl())){
			$type = 'text';
			//set extension to web fileinput.js plugin uses the caption extension to determine file icon to use
			//dertermine if URL is internal or external and set fileinput.js configs accordingly
			if(substr($child->getUrl(), 0, 1) === '/'){
				$fileUploadPaths[] = 'http://'.$_SERVER['HTTP_HOST'].$child->getUrl();
			}else{
				$extension = explode('.', $child->getUrl());
				if($newFile->isAcceptedExtension(end($extension),$child->getFileTypes())){$type = 'html';}
				$fileUploadPaths[] = $child->getUrl();
			}
			//create file config for display
			$fileUploadConfigs[] = array("type"=>$type,"width"=>$moduleClass->_moduleThumbCanvasX.'px',"height"=>$moduleClass->_moduleThumbCanvasY.'px',"caption"=>$child->getTitle().'.'.$child->getExtension(),"url"=>'action.php?action=delete&id='.$child->getId(),"key"=>$child->getId(),"extra"=>array("sort"=>$child->getSortOrder()));
		}else{
			$fileUploadPaths[] = $GLOBALS['file_path'].$child->getDbTable().$child->getId().'.'.$child->getExtension().'?'.rand(1,1000);
			$fileUploadConfigs[] = array("type"=>"pdf","width"=>$moduleClass->_moduleThumbCanvasX.'px',"height"=>$moduleClass->_moduleThumbCanvasY.'px',"caption"=>$child->getTitle().'.'.$child->getExtension(),"url"=>'action.php?action=delete&id='.$child->getId(),"key"=>$child->getId(),"extra"=>array("sort"=>$child->getSortOrder()));
		}
	}

	//create file config for display
	$content = array('initialPreview' => $fileUploadPaths,'initialPreviewConfig' => $fileUploadConfigs,'append' => false,'category' => $moduleClass->getCategory(),'id' => $moduleClass->getId());

	// Refresh Data
	$selector = '';
	$moduleClass->addRefreshElement($selector, $content);
	$refreshElements = $moduleClass->getRefreshElements();
	
	// Success
	echo success($moduleClass->getTitle().' modified successfully.', $refreshElements, 'refreshData', array($moduleClass->_moduleName,$moduleClass->getTitle(),$moduleClass->getId()));
}

// Delete
if($action == 'delete'){
	$moduleClass = new $moduleClassName($id);
	if(!$moduleClass->getId()){
		echo json_encode(array('failure'=>'Record not found.')); 
		exit;
	}

	// Declare file class
	$curFile = new File($moduleClass->getDbTable().$moduleClass->getId().'.'.$moduleClass->getExtension());
	
	// Delete file
	$curFile->deleteFile();

	// Delete record
	$moduleClass->delete();

	// Success
	
	success($moduleClass->getTitle().' deleted successfully.', '', 'refreshData', array($moduleClass->_moduleName,$moduleClass->getTitle(),$moduleClass->getId()));
	
	echo json_encode(array()); 

}

//sort files
if($action == 'sort'){
	foreach($elements as $key => $val) {
		$moduleClass = new $moduleClassName($val);
		if($moduleClass->getId()){
			$moduleClass->setSortOrder($key);
			$moduleClass->save($queryLogging);
			success('Sort order for '.$moduleClass->getTitle().' changed to '.$moduleClass->getSortOrder().' successfully.', '', 'refreshData', array($moduleClass->_moduleName,$moduleClass->getTitle(),$moduleClass->getId()));
		}
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

	// Uncomment this one to fake upload time
	// usleep(5000);

	// Settings
	$targetDir = $GLOBALS['path'] . "admin" . DIRECTORY_SEPARATOR . "modules" . DIRECTORY_SEPARATOR . $module_dir . DIRECTORY_SEPARATOR . "temp" . DIRECTORY_SEPARATOR . 'category_'.$_POST['category_id'];
	//$targetDir = 'uploads';
	$cleanupTargetDir = true; // Remove old files
	$maxFileAge = 5 * 3600; // Temp file age in seconds

	// Create target dir
	if (!file_exists($targetDir)) {
		@mkdir($targetDir);
	}

	// Get a file name
	$fileName = uniqid("file_");
	$fileUploadPaths = array('');
	$fileUploadConfigs = array('');
	$filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;
	$numUploadedfiles = count($_FILES["file_input_modal_".$category_id]["name"]);
	for($i = 0; $i < $numUploadedfiles; $i++)
	{
		if(is_uploaded_file($_FILES["file_input_modal_".$category_id]["tmp_name"][$i])){

			// Remove old temp files	
			if ($cleanupTargetDir) {
				if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
					echo json_encode(array('error'=>'Failed to open temp directory.')); 
					exit;
				}
				while (($file = readdir($dir)) !== false) {
					$tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;
					// If temp file is current file proceed to the next

					if ($tmpfilePath == "{$filePath}.part") {
						continue;
					}
					// Remove temp file if it is older than the max age and is not the current file
					if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge)) {
						@unlink($tmpfilePath);
					}
				}
				closedir($dir);
			}

			// Open temp file
			if (!$out = @fopen("{$filePath}.part", "ab" )) {
				echo json_encode(array('error'=>'Failed to open output stream.')); 
				exit;
			}
			if (!empty($_FILES)){
				if ($_FILES["file_input_modal_".$category_id]["error"][$i] || !is_uploaded_file($_FILES["file_input_modal_".$category_id]["tmp_name"][$i])) {
					echo json_encode(array('error'=>'Failed to move uploaded file: '.$_FILES["file_input_modal_".$category_id]["tmp_name"][$i])); 
					exit;
				}
				// Read binary input stream and append it to temp file
				if (!$in = @fopen($_FILES["file_input_modal_".$category_id]["tmp_name"][$i], "rb")) {
					echo json_encode(array('error'=>'Failed to open input stream.')); 
					exit;
				}
			}else{	
				if (!$in = @fopen("php://input", "rb")) {
					echo json_encode(array('error'=>'Failed to open input stream.')); 
					exit;
				}
			}
			while ($buff = fread($in, 4096)) {fwrite($out, $buff);}
			@fclose($out);
			@fclose($in);

			// Strip the temp .part suffix off 
			rename("{$filePath}.part", $filePath);
			//

			$moduleClassCategory = new $moduleCategoryClassName($category_id);
			$moduleClass = new $moduleClassName();

			// Use if users cannot rename photos, and every photo is just being set to the name of the Category
			if(!strlen(trim($_POST['photo_name']))){
				$photoName = $moduleClassCategory->getName();	
			}else{
				$photoName = $_POST['photo_name'];	
			}
			// Use if users can rename each photo
			$photoName = $_POST["name"];
			//
			// Declare file class
			$newFile = new File();

			$moduleClass->setCategory($category_id)
				->setType($_FILES["file_input_modal_".$category_id]["type"][$i])
				->setTitle($newFile->cleanFileName($_FILES["file_input_modal_".$category_id]["name"][$i]))
				->setName($newFile->cleanFileName($_FILES["file_input_modal_".$category_id]["name"][$i]))
				->setExtension($newFile->extensionFromMimeType($_FILES["file_input_modal_".$category_id]["type"][$i]))
				->setDate(date('Y-m-d H:i:s'))
				->setPermalink($moduleClass->generatePermalink($moduleClass->getName()))
				->setFile('1')
				->save($queryLogging);
			$newFilePath = $GLOBALS['upload_path'].$moduleClass->getDbTable().$moduleClass->getId().'.'.$moduleClass->getExtension();
			@rename($filePath,$newFilePath);
			
			//extracted text from documents, stores it in database effectivly enabling a site search to return documents based on their content.
			//flew too close to the sun
			//$docObj = new DocumentToText($newFilePath);
			//$moduleClass->setPreviewContent($docObj->convertToText())->save();

			// Remove the temp image
			unlink($filePath);


			//create array of file paths for display
			$fileUploadPaths[] = $GLOBALS['file_path'].$moduleClass->getDbTable().$moduleClass->getId().'.'.$moduleClass->getExtension().'?'.rand(1,1000);

			//create array of file configs for display
			$fileUploadConfigs[] = array("type"=>'pdf',"width"=>$moduleClass->_moduleThumbCanvasX.'px',"height"=>$moduleClass->_moduleThumbCanvasY.'px',"caption"=>$moduleClass->getName().'.'.$moduleClass->getExtension(),"url"=>'action.php?action=delete&id='.$moduleClass->getId(),"key"=>$moduleClass->getId(),"extra"=>array("sort"=>$moduleClass->getSortOrder()));
		}

	}

	// Returns new image previews as a Success response 
	echo json_encode(array('initialPreview' => $fileUploadPaths,'initialPreviewConfig' => $fileUploadConfigs,'append' => true )); 
}
?>