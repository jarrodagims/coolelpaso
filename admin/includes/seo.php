<?php

// These 2 functions manage the sites seo, providing key words and descriptions for front facing site pages (read bottom of this file).

function setSEO(){
	if(strlen(trim($GLOBALS['site_id']))){
		// Get the Website default values
		$database = new Database($GLOBALS['pdo_seo_db']);
		$database->query('SELECT * FROM `clients_websites` WHERE `site_id` = :Id');
		$database->bind(':Id', $GLOBALS['site_id']);
		$result = $database->single();
		if($database->rowCount()){
			$GLOBALS['SEO_NAME'] = $result['seo_name'];
			$GLOBALS['SEO_TITLE_DESCRIPTION'] = $result['seo_title_description'];
			$GLOBALS['SEO_KEYWORDS'] = $result['seo_keywords'];
			$GLOBALS['SEO_DESCRIPTION'] = $result['seo_description'];
			$seo_title_description_propagate = $result['seo_title_description_propagate'];
			$seo_keywords_propagate = $result['seo_keywords_propagate'];
			$seo_description_propagate = $result['seo_description_propagate'];
		}
		$page_portion = substr($GLOBALS['rewrite_url'],0,strpos($GLOBALS['rewrite_url'],"/",1));
		// Get Page specific values
		$database->query('SELECT * FROM `websites_pages` WHERE `websites_id` = :Site AND (`url`= :Url OR `url`= :Portion)');
		$database->bind(':Site', $result['id']);
		$database->bind(':Url', $GLOBALS['rewrite_url']);
		$database->bind(':Portion', $page_portion);
		$row = $database->single();
		/**
		* Set SEO accordingly
		* 
		* If page is specified: Use set values if specified, or propagated values
		* If page is not specified: Use propagation values if indicated.
		*/
		if($database->rowCount()){
			if(strlen(trim($row['seo_title']))){
				$GLOBALS['SEO_TITLE'] = $row['seo_title'];
			}
			if(!$seo_title_description_propagate || strlen(trim($row['seo_title_description']))){
				$GLOBALS['SEO_TITLE_DESCRIPTION'] = $row['seo_title_description'];
			}
			if(!$seo_keywords_propagate || strlen(trim($row['seo_keywords']))){
				$GLOBALS['SEO_KEYWORDS'] = $row['seo_keywords'];
			}
			if(!$seo_description_propagate || strlen(trim($row['seo_description']))){
				$GLOBALS['SEO_DESCRIPTION'] = $row['seo_description'];
			}
		}else{
			// Sub page has no match so pull over propagated values
			$GLOBALS['SEO_TITLE'] = '';
			if(!$seo_title_description_propagate){
				$GLOBALS['SEO_TITLE_DESCRIPTION'] = '';
			}
			if(!$seo_keywords_propagate){
				$GLOBALS['SEO_KEYWORDS'] = '';
			}
			if(!$seo_description_propagate){
				$GLOBALS['SEO_DESCRIPTION'] = '';
			}
		}
	}
}
function setSEOXML(){
	if(strlen(trim($GLOBALS['site_id']))){
		$xml= new DOMDocument();
		$xml->load($GLOBALS['path'].'xml/seo'.$GLOBALS['site_id'].'.xml');
		// Get Website Settings
		$website = $xml->getElementsByTagName('Website');
		$website =  $website->item(0);
		$website_settings = $website->attributes;
		foreach($website_settings as $attribute){
			$GLOBALS['SEO_PAGES']['Website'][$attribute->name] = base64_decode($attribute->value);
		}
		// Get Individual Page Settings
		$pages = $xml->getElementsByTagName('Page');
		foreach($pages as $page){
			$url = $page->textContent;
			$page_settings = $page->attributes;
			foreach($page_settings as $attribute){
				$GLOBALS['SEO_PAGES'][$url][$attribute->name] = base64_decode($attribute->value);
			}
		}
		// Get the Website default values
		$GLOBALS['SEO_NAME'] = $GLOBALS['SEO_PAGES']['Website']['seo_name'];
		$GLOBALS['SEO_TITLE_DESCRIPTION'] = $GLOBALS['SEO_PAGES']['Website']['seo_title_description'];
		$GLOBALS['SEO_KEYWORDS'] = $GLOBALS['SEO_PAGES']['Website']['seo_keywords'];
		$GLOBALS['SEO_DESCRIPTION'] = $GLOBALS['SEO_PAGES']['Website']['seo_description'];
		$seo_title_description_propagate = $GLOBALS['SEO_PAGES']['Website']['seo_title_description_propagate'];
		$seo_keywords_propagate = $GLOBALS['SEO_PAGES']['Website']['seo_keywords_propagate'];
		$seo_description_propagate = $GLOBALS['SEO_PAGES']['Website']['seo_description_propagate'];
		// Check if there is a Page match
		$page_portion = substr($GLOBALS['rewrite_url'],0,strpos($GLOBALS['rewrite_url'],"/",1));		
		if(array_key_exists($GLOBALS['rewrite_url'],$GLOBALS['SEO_PAGES'])){
			$match = $GLOBALS['SEO_PAGES'][$GLOBALS['rewrite_url']];
		}elseif(array_key_exists($page_portion,$GLOBALS['SEO_PAGES'])){
			$match = $GLOBALS['SEO_PAGES'][$page_portion];
		}else{
			$match = '';
		}
		/**
		 * Set SEO accordingly
		 * 
		 * If page is specified: Use set values if specified, or propagated values
		 * If page is not specified: Use propagation values if indicated.
		 */
		if(is_array($match)){
			if(strlen(trim($match['seo_title']))){
				$GLOBALS['SEO_TITLE'] = $match['seo_title'];
			}
			if(!$seo_title_description_propagate || strlen(trim($match['seo_title_description']))){
				$GLOBALS['SEO_TITLE_DESCRIPTION'] = $match['seo_title_description'];
			}
			if(!$seo_keywords_propagate || strlen(trim($match['seo_keywords']))){
				$GLOBALS['SEO_KEYWORDS'] = $match['seo_keywords'];
			}
			if(!$seo_description_propagate || strlen(trim($match['seo_description']))){
				$GLOBALS['SEO_DESCRIPTION'] = $match['seo_description'];
			}
		}else{
			// Sub page has no match so pull over propagated values
			$GLOBALS['SEO_TITLE'] = '';
			if(!$seo_title_description_propagate){
				$GLOBALS['SEO_TITLE_DESCRIPTION'] = '';
			}
			if(!$seo_keywords_propagate){
				$GLOBALS['SEO_KEYWORDS'] = '';
			}
			if(!$seo_description_propagate){
				$GLOBALS['SEO_DESCRIPTION'] = '';
			}
		}
	}
}

// IMPORTANT:  

// setSEOXML is used when the sstg seo system is able to write the xml file to the xml folder on this site.
// setSEO is used for remote sites where xml cant be writen, comment them out accordingly.

setSEOXML();
//setSEO();


?>