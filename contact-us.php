<?php
$htmlPage = new HtmlPage('contact-us');
$contactUs = new ContactUs();
ob_start();?>
<div class="col-md-12">
<?php echo $contactUs->toHtml('form');?>
</div>
<?php $GLOBALS['CONTENT'] = ob_get_clean();
$GLOBALS['PAGE_SECTION'] = $htmlPage->getPermalink();
$GLOBALS['PAGE_TITLE'] = $htmlPage->getName();
$GLOBALS['BANNER'] = $htmlPage->buildImageSrc();
$GLOBALS['SIDE_NAVIGATION'] = $htmlPage->toHtml('sub-navigation');
$GLOBALS['SEO_TITLE'] = $GLOBALS['PAGE_TITLE'];
if(!strlen(trim(($GLOBALS['SIDE_NAVIGATION'])))){
	$GLOBALS['FULL_PAGE'] = true;
}
include_once('template.php');
?>