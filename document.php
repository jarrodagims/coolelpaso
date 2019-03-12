<?php
include_once('admin/includes/library.php');
// called from html pages
$document = new Document($pages[0]);
if(!$document->getId()){
	header('Location: /');
	exit;
}

$filePath = $GLOBALS['upload_path'].$document->getDbTable().$document->getId().'.'.$document->getExtension();
if(!is_file($filePath)){
	header('Location: /');
	exit;
}

// Output the file
header('Content-type: '.$document->getType());
header('Cache-Control: public');
header('Content-Disposition: attachment; filename="'.$document->getName().'.'.$document->getExtension().'"');
readfile($filePath);
exit;
?>