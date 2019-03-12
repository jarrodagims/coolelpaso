<?php
// postmarketer.php
// Note: PM3 API expects and returns data in windows-1252 character encoding
function generateNewsletter($customer_id = "",$newsletter = "",$article = "")
{
	
	$pm_url = "http://dev-pm3.stantonstreethosting.com";
	
	if(!strlen($customer_id)){
		return false;
	}
	if(!strlen($newsletter)){
		// Get the newsletter content from PM3
		$ch = curl_init($pm_url."/api/archive.sstg");	
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "id=".$customer_id);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		$str = curl_exec($ch);
		//$str = iconv('windows-1252','UTF-8',$str);
		$str = iconv('windows-1251','utf-8',str_replace('href="/newsletter.sstg','href="'.$pm_url.'/newsletter.sstg',$str));
		curl_close($ch);
	}else{
		// Get the newsletter content from PM3
		$ch = curl_init($pm_url."/api/newsletter.sstg");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "newsletter=".$newsletter."&article=".$article);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		$str = curl_exec($ch);
		//$str = iconv('windows-1252','UTF-8',$str);
		$str = iconv('windows-1251','utf-8',str_replace('href="/newsletter.sstg','href="http://pm3.stantonstreetgroup.com/newsletter.sstg',$str));
		curl_close($ch);
	}
	return $str;
}
/**
 * function sendNewsBlast()
 *
 * This function will send information to create a newsletter blast to the PM3 API
 * @param array An associative aray of fields relative to a PM3 newsletter
 * @param string Local absolute path to image file associated with record
 * @param string Local absolute path to document file associated with record
 * @return string ID of newsletter for cancelation
 * @internal Use: 
 * 	$field_array = array(
 *	"customer_id" => $GLOBALS['pm3_client_id'],
 *	"recipient_list" => $recipient_list,
 *	"subject" => $title,
 *	"template" => "main_template",
 *	"send_date" => $post_date,
 *	"send_time" => $post_time,
 *	"article" => array(
 *		"headline" => $title,
 *		"lead" => $description,
 *		"url" => "vintage.stantonstreetgroup.com?id=$id",
 *		"url_label" => "Visit our Website",
 *		"image" => $image = (isset($file)) ? '1' : '0',
 *		"pdf" => $pdf = (isset($document)) ? '1' : '0',
 *		"pdf_label" => "Download More Info on this Special",
 *		"large_headline" => '1'
 *		)
 *	);
 */
function sendNewsBlast($field_array = '', $image = '', $document = '')
{
	global $newsblast_use_ftp;
	// FTP Image
	if(strlen($image)){
		list($name,$ext) = explode(".", $image);
		$image = $name."_t.".$ext;
		list(,$image_name) = explode('/files/', $image);
		if($newsblast_use_ftp){
			ftpPut($image, "/postmarketer3/newsletters/".$GLOBALS['postmarketer_folder']."/files/".$image_name, $GLOBALS['pm_ftp_host'], $GLOBALS['pm_ftp_username'], $GLOBALS['pm_ftp_password']);
		}else{
			copy($image, "E:/Inetpub/docroot/postmarketer3/newsletters/".$GLOBALS['postmarketer_folder']."/files/".$image_name);
		}
		$image_name = "&image_name=".$image_name;
	}
	// FTP Document
	if(strlen($document)){
		list(,$document_name) = explode('/files/', $document);
		if($newsblast_use_ftp){
			ftpPut($document, "/postmarketer3/newsletters/".$GLOBALS['postmarketer_folder']."/files/".$document_name, $GLOBALS['pm_ftp_host'], $GLOBALS['pm_ftp_username'], $GLOBALS['pm_ftp_password']);
		}else{
			copy($document, "E:/Inetpub/docroot/postmarketer3/newsletters/".$GLOBALS['postmarketer_folder']."/files/".$document_name);
		}
		$document_name = "&pdf_name=".$document_name;
	}
	// encode array for post submission
	$field_str = base64_encode(serialize($field_array));
	// CURL area
	$ch = curl_init("http://pm3.stantonstreetgroup.com/api/postmarketer.sstg");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "action=news_blast&path=".urlencode($GLOBALS['postmarketer_folder'])."&field_array=$field_str".$image_name.$document_name);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	$str = curl_exec($ch);
	$str = iconv('windows-1252','UTF-8',$str);
	curl_close($ch);
	return $str;
}
/**
 * function getRecipientLists()
 *
 * This function will fetch recipient lists from the PM3 API
 * @param string PM3 Client ID (See globals.php $GLOBALS['pm3_client_id'])
 * @return html Returns a drop-down list of the subscriber lists
 * @internal Use: $recipient_drop = getRecipientLists($GLOBALS['pm3_client_id']);
 */
function getRecipientLists($client_id)
{
	// CURL area
	$ch = curl_init("http://pm3.stantonstreetgroup.com/api/postmarketer.sstg");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "action=get_lists&customer_id=$client_id");
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	$str = curl_exec($ch);
	$str = iconv('windows-1252','UTF-8',$str);
	curl_close($ch);
	// Create arrays for drop list
	$list_labels = array("All Recipients");
	$list_values = array("");
	// Append recipient lists if available
	if($str != "0"){
		$list_array = unserialize(base64_decode($str));
		foreach($list_array as $key => $value){
			$list_values[] = $key;
			$list_labels[] = $value;
		}
	}
	// Make droplist
	$recipient_drop = FormComponent::dropDownList("recipient_list",$list_values,$list_labels,"","recipient_list");
	return $recipient_drop;	
}
/**
 * function getNewsletterTemplates()
 *
 * This function will fetch newsletter templates from the PM3 API
 * @param string PM3 Client ID (See globals.php $GLOBALS['pm3_client_id'])
 * @return html Returns a drop-down list of the newsletter templates
 * @internal Use: $template_drop = getNewsletterTemplates($GLOBALS['pm3_client_id']);
 */
function getNewsletterTemplates($client_id)
{
	// CURL area
	$ch = curl_init("http://pm3.stantonstreetgroup.com/api/postmarketer.sstg");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "action=get_templates&customer_id=$client_id");
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	$str = curl_exec($ch);
	$str = iconv('windows-1252','UTF-8',$str);
	curl_close($ch);
	// Append recipient lists if available
	if($str != "0"){
		$list = unserialize(base64_decode($str));
	}
	return $list;	
}
/**
 * function cancelNewsBlast()
 *
 * This function will fetch newsletter templates from the PM3 API
 * @param string PM3 Newsletter ID
 * @return void
 */
function cancelNewsBlast($newsletter_id)
{
	// CURL area
	$ch = curl_init("http://pm3.stantonstreetgroup.com/api/postmarketer.sstg");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "action=cancel_news_blast&id=$newsletter_id");
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	$str = curl_exec($ch);
	$str = iconv('windows-1252','UTF-8',$str);
	curl_close($ch);
	return;
}
/**
 * function subscribe()
 *
 * This function will add a recipient through the PM3 API
 * @param string The e-mail address of the recipient
 * @param string The name of the recipient
 * @param integer The ID of a PM3 recipient sub-list
 * @param string The source of subscription
 * @return string var_dump of return of API action
 */
function subscribe($recipient_email,$recipient_name,$list_id="",$source = "Website")
{
 	// Check for $GLOBALS['pm3_client_id']
	if(!isset($GLOBALS['pm3_client_id'])){
		return;
	}else{
		$data = array(
		'action' => 'subscribe',
		'customer_id' => $GLOBALS['pm3_client_id'],
		'recipient_email' => $recipient_email,
		'recipient_name' => iconv('UTF-8','windows-1252',$recipient_name),
		'list_id' => $list_id,
		'source' => $source);
		$query_str = http_build_query($data);
		// CURL area
		$ch = curl_init("http://pm3.stantonstreetgroup.com/api/postmarketer.sstg");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $query_str);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		$str = curl_exec($ch);
		$str = iconv('windows-1252','UTF-8',$str);
		curl_close($ch);
		return $str;
	}
}
?>