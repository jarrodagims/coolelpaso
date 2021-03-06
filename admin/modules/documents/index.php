<?php
# Debugging - set to 1
$dev = 0;

# Include Libraries
include_once('../../includes/library.php');

# Security
Security::xssProtect();

# Current Module
$currentModule = new Document();
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

# CSS Styles
$GLOBALS['CSS'] .= $currentModule->buildCss();

# Get module content
echo $module->toHtml();

# JavaScript
$GLOBALS['JAVASCRIPT'] .= $currentModule->buildAdminListJavascript();

# Create blank modal
echo $module->toHtml('modal');

$content .= ob_get_clean();

# Page title 
$GLOBALS['page_title'] = $currentModule->_moduleName;

# Include Template
include_once('../../template.php');
?>