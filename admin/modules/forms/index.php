<?php

# Debugging - set to 1
$dev = 0;

# Include Libraries
include_once('../../includes/library.php');

# Security
Security::xssProtect();

# Current Module
$currentModule = new Form();

# Check if module is installed 
$currentModule->verifyInstallation();

# Verify Access
$currentModule->verifyAccess();

# Create instance of Module Class for this module
$module = new Module($currentModule->_moduleClassName);

# Content
ob_start();

# Get module content
echo $module->toHtml();

# Create blank modal 
echo $module->toHtml('modal');

$content .= ob_get_clean();

# Page title 
$GLOBALS['page_title'] = $currentModule->_moduleName;

# Include Template
include_once('../../template.php');
?>