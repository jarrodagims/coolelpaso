<?php
//ini_set('display_errors','1');
//ini_set("error_reporting", E_ALL);
ob_start();
extract($_GET);
$route = array_reverse(explode("/",$id));
$extendedSideNav = array();



// if url is base linked html page
if($route[0] == $htmlPage->getPermalink()){
	$faqCategory = new FaqCategory();
	$faqCategories = $faqCategory->fetchAll();
	foreach($faqCategories as $faqCategory){
		$extendedSideNav[$faqCategory->getPermalink()] = $faqCategory->getName();
	}
	foreach($faqCategories as $faqCategory){ ?>
		<h2 class="category-title" data-aos="fade-right"><?php echo $faqCategory->getName(); ?></h2>
		<a name="<?php echo $faqCategory->getPermalink(); ?>"></a>
		<?php $faq = new Faq();
			$faqs = $faq->fetchAll("WHERE `category` = '".$faqCategory->getId()."'");
			foreach($faqs as $faq){ echo $faq->toHtml(); }
	 }
	
	 $pageTitle = $htmlPage->getName();
}else{
	//if url is module category
	$faqCategory = new FaqCategory($route[0]);
	if($faqCategory->getId()){
		$pageTitle = $faqCategory->getName();
	}else{
		header("Location: /not-found");
	}
}
$GLOBALS['CONTENT'] = ob_get_clean();

// set page title to current page
$htmlPage->setName($pageTitle);
$GLOBALS['PAGE_SECTION'] = $htmlPage->getPermalink();
$GLOBALS['PAGE_TITLE'] = $htmlPage->getName();
$GLOBALS['BANNER'] = $htmlPage->buildImageSrc();
//$GLOBALS['SIDE_NAVIGATION'] = $htmlPage->sideNavAction($extendedSideNav);
?>