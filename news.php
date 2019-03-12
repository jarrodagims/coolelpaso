<?php 
$extendedCrumbs = array();
ob_start(); 
// if url is base linked html page
if($route[0] == $htmlPage->getPermalink()){
	$newsCategory = new NewsCategory('general');
	$articles = $newsCategory->getNews();
	foreach($articles as $article){
		echo $article->toHtml('listing');
	}
	$pageTitle = $htmlPage->getName();
}else{
	//if url is module item
	$article = new News($route[0]);
	if($article->getId()){
		echo $article->toHtml();
		$pageTitle = $article->getHeadline();
		$extendedCrumbs = array($article->getPermalink() => $article->getHeadline());
	}else{
		//if no module item exists in url
		header("Location: /not-found");
	}
}

$GLOBALS['CONTENT'] = ob_get_clean();

// set page title to current page
$htmlPage->setName($pageTitle);
// add extendedCrumbs to breadCrumbs
$GLOBALS['BREADCRUMB'] = $htmlPage->buildBreadcrumb(true,true,$extendedCrumbs);
?>