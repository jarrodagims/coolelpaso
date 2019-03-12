<?php
//ini_set('display_errors','1');
//ini_set("error_reporting", E_ALL);
include_once('admin/includes/library.php');

Security::validateHttpRequest('get');
Security::xssProtect();

// Extract and parse the pages from the request
extract($_GET);
$route = array_reverse(explode("/",$id));

$pageExists = false;
// Document handler
if($route[1] == 'documents' && $route[0]){
	include_once('document.php');
}

// Load HTML Page
if(sizeof($route)){
	$x =0;
	while($x <= (sizeof($route)+1)){
		$htmlPage = new HtmlPage($id);
		if($htmlPage->getActive() && $htmlPage->exists($id)){
			$pageExists = true;
			if($x > 0 && !$htmlPage->getCustomPage()){
				header("Location: /not-found");
				exit;
			}else{
				$GLOBALS['CONTENT'] .= $htmlPage->toHtml();
				
				// Check Custom Pages
				if($htmlPage->getCustomPage()){include_once($htmlPage->getCustomPage());}
			}
			$x=999;
		}else{
			unset($route[$x]);
			$id = implode('/',array_reverse($route));
		}
		$x++;
	} 
	if(!$pageExists){
		header("Location: /not-found");
	}
}else{
	header("Location: /not-found");
	exit;
}

// Setup Page
$root = $htmlPage->getRoot();
$GLOBALS['PAGE_SECTION'] = $root->getPermalink();
$GLOBALS['PAGE_TITLE'] = $htmlPage->getName();
$GLOBALS['BANNER'] = $htmlPage->buildImageSrc();
if(!$GLOBALS['BREADCRUMB']){
	$GLOBALS['BREADCRUMB'] = $htmlPage->buildBreadcrumb();
}
if(!$GLOBALS['SIDE_NAVIGATION']){
	$GLOBALS['SIDE_NAVIGATION'] = $htmlPage->toHtml('side-nav');
}
if(!strlen(trim(($GLOBALS['SIDE_NAVIGATION'])))){
	$GLOBALS['FULL_PAGE'] = true;
}
$GLOBALS['SEO_TITLE'] = $GLOBALS['PAGE_TITLE'];
include_once('template.php');
?>