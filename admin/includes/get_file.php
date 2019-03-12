<?php
# Debugging - set to 1
$dev = 0;

include_once("library.php");
if(!$_SESSION['session_logged_in'] == true){
	header("Location: http://".$_SERVER['HTTP_HOST']);
	exit;
}
Security::xssProtect();
// extract get variables
extract($_GET);

$id = (int) $_GET['id'];
$moduleClassName = $_GET['class'];

$moduleClass = new $moduleClassName($id);
if(get_class($moduleClass)=='Document' && trim($moduleClass->getUrl())){
	header('Location: '.$moduleClass->getUrl());
	exit;
}
if(!$moduleClass->getId()){
	header('Location: '.$_SERVER['HTTP_REFERER']);
	exit;
}
if(method_exists($moduleClass, 'getExtension')){
	$extension = $moduleClass->getExtension();
}elseif($ext){
	$extension = $ext;
}else{
	$extension = 'pdf';
}
$filePath = $GLOBALS['upload_path'].$moduleClass->getDbTable().$moduleClass->getId().'.'.$extension;
if(!is_file($filePath)){
	header('Location: '.$_SERVER['HTTP_REFERER']);
	exit;
}
if(method_exists($moduleClass, 'getType')){
	$type = $moduleClass->getType();
}else{
	$type = 'application/pdf';
}
if(method_exists($moduleClass, 'getName')){
	$name = $moduleClass->getName();
}else{
	$name = $moduleClassName;
}
// Output the file
header('Content-type: '.$type);
header('Cache-Control: public');
header('Content-Disposition: attachment; filename="'.$name.'.'.$extension.'"');
readfile($filePath);
exit;
?>