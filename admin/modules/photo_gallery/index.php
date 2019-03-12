<?php
# Debugging - set to 1
$dev = 0;


if($dev){
	ini_set('display_errors','1');
	ini_set("error_reporting", E_ALL);
}

# Include Libraries
include_once('../../includes/library.php');

# Security
Security::xssProtect();

# Current Module
$currentModule = new Photo();
$moduleClassName = $currentModule->_moduleClassName;
$moduleCategoryClassName = $currentModule->_moduleCategoryClassName;
# Check if module is installed 
$currentModule->verifyInstallation();

# Verify Access
$currentModule->verifyAccess();

# Get Classes
$module = new Module($moduleCategoryClassName);

# Content
ob_start();
$GLOBALS['CSS'] .= $currentModule->buildCss();
echo $module->toHtml();
$GLOBALS['JAVASCRIPT'] .= $currentModule->buildAdminListJavascript();

echo $module->toHtml('modal');
$content = ob_get_clean();

# Page title 
$GLOBALS['page_title'] = $currentModule->_moduleName;

# Include Template
include_once('../../template.php');
?>