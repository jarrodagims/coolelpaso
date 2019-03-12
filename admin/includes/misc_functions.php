<?php
/**
 * misc_functions.php - Miscellaneous functions
 *
 * This file contains functions for a variety of helpful tasks.
 *
 * @filesource
 */

/**
 * function hex2rgb()
 * *used in Image.php class
 * This function returns the rgb value of a hex code
 * @return array Returns rgb values
 */
function hex2rgb($hex){
   $hex = str_replace("#", "", $hex);
   if(strlen($hex) == 3){
      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
   }else{
      $r = hexdec(substr($hex,0,2));
      $g = hexdec(substr($hex,2,2));
      $b = hexdec(substr($hex,4,2));
   }
   $rgb = array($r, $g, $b);
   return $rgb;
}

/**
 * function currentDate()
 *
 * This function returns the current date
 * @return string Returns the current date as yyyy-mm-dd
 */
function currentDate(){
	return date('Y')."-".date('m')."-".date('d');
}

/**
 * function currentTime()
 *
 * This function returns the current time
 * @return string Returns the current time as hh:mm:ss
 */
function currentTime(){
	return date('H').":".date('i').":00";
}

/**
 * function process()
 *
 * This function formats pre-filled form data for proper display
 * @param string The value to format
 * @return string Returns the entered value properly formatted for HTML display
 */
function process($str){
	return htmlspecialchars($str,ENT_QUOTES,'UTF-8');
}

function detectSpam($str,$duration = 30,$threshold = 9){
	if(strlen(strip_tags(trim($str))) != strlen(trim($str))){
		return true;
	}
	if($duration <= $threshold || ($duration > $threshold && (substr_count($str,'http://') >= 2 || substr_count($str,'https://') >= 2))){
		return true;	
	}
	return false;
}

/**
 * function isUploaded()
 *
 * This function checks if the specified file has been uploaded via the $_FILES array
 * @param string Name of file in the $_FILES[<name>][] array
 * @return bool - true if the file is uploaded, - false otherwise
 */
function isUploaded($file)
{
	if(is_uploaded_file($_FILES[$file]['tmp_name'])){
		return true;
	}else{
		return false;
	}
}

/**
 * function generateBlurb(){
 *
 * This function will generate a readable blurb of text
 *
 */
function generateBlurb($text,$length = 150,$char = " ")
{
	if(strlen(trim($text)) <= $length){
		$blurb = $text;
	}else{
		$blurb = substr($text,0,$length);
		// Truncate to last occurrence of $char within $length.  If not found default to " " (space)
		$char_position = strrpos($blurb,$char);
		if(!$char_position){
			$char = " ";
			$char_position = strrpos($blurb,$char);
		}
		$blurb = substr($blurb,0,$char_position).'...';
	}
	return $blurb;
}

/**
 * function printDate()
 *
 * This function formats the inputted $date 
 * @param date The date to be formatted
 * @param string The date format template as used by the PHP date() function
 * @return string Returns the formatted date as January 1, 1970 by default, returns by the specified format otherwise
 */
function printDate($date, $format="F j, Y")
{
	list($year,$month,$day) = explode("-",$date);
	$timestamp = mktime(0,0,0,$month,$day,$year);
	return date($format,$timestamp);
}
/**
 * function printDateAndTime()
 *
 * This function formats the inputted $date_time
 * @param datetime The date and time to be formatted
 * @return string Returns the formatted date as January 1, 1970 - 12:01 AM
 */
function printDateAndTime($date_time)
{	
	$timestamp = strtotime($date_time);
	return date("F j, Y - g:i a ",$timestamp);
}


?>