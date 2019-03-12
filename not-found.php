<?php

header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found"); 
include_once('admin/includes/library.php');
$htmlPage = new HtmlPage('not-found');
ob_start(); ?>

<h2>We're sorry, the page you requested could not be found.</h2>
<p>The requested page has been moved or no longer exists.</p>


<?php $GLOBALS['CONTENT'] = ob_get_clean();
$GLOBALS['PAGE_SECTION'] = $htmlPage->getPermalink();
$GLOBALS['PAGE_TITLE'] = $htmlPage->getName();
$GLOBALS['BANNER'] = $htmlPage->buildImageSrc();
//$GLOBALS['SIDE_NAVIGATION'] = $htmlPage->toHtml('sub-navigation');
$GLOBALS['SEO_TITLE'] = $GLOBALS['PAGE_TITLE'];
if(!strlen(trim(($GLOBALS['SIDE_NAVIGATION'])))){
	$GLOBALS['FULL_PAGE'] = true;
}
include_once('template.php');
?>