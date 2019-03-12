<?php
class Sanitize 
{
	public static function digitsOnly($value)
	{
		return preg_replace("/[^\d]/","",$value);	
	}
	public static function isDigitsOnly($value)
	{
		return (bool) !preg_match("/[^\d]/",$value);
	}
	public static function truncate($value,$length)
	{
		return substr($value,0,$length);	
	}
	public static function formatDate($value,$format = 'Y-m-d')
	{
		return date($format,strtotime($value));
	}
	public static function isValidEmail($value){
		if(preg_match('/^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.([a-zA-Z]{2,4})$/i',$value)){
			return true;
		}else{
			return false;
		} 
	}
	public static function isValidPhone($value){
		if(preg_match("/^(?:(?:\((?=\d{3}\)))?(\d{3})(?:(?<=\(\d{3})\))?[\s.\/-]?)?(\d{3})[\s\.\/-]?(\d{4})\s?(?:(?:(?:(?:e|x|ex|ext)?\.?\:?|extension\:?)\s?)(?=\d+)(\d+))?$/i", trim($value))){
			return true;
		}else{
			return false;
			
		}
	}
}
?>