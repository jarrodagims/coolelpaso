<?php
class Security
{
	// Member Constants
	
	// Instance Variables

	// Static Methods
	public static function validateHttpRequest($valid_type = 'all',$redirect = '/')
	{
		$valid_type = strtolower($valid_type);
		switch($valid_type){
			case 'all':
			break;
			
			case 'get':
				if(sizeof($_POST)){
					header("Location: ".$redirect);
					exit;	
				}
			break;
			
			case 'post':
				if(sizeof($_GET)){
					header("Location: ".$redirect);
					exit;	
				}
			break;
			
			case 'none':
				if(sizeof($_GET) || sizeof($_POST)){
					header("Location: ".$redirect);
					exit;
				}
			break;
		}
	}
	
	public static function xssProtect()
	{
		if(urldecode($_SERVER['QUERY_STRING']) != strip_tags(urldecode($_SERVER['QUERY_STRING'])) 
		|| (urldecode($_SERVER['QUERY_STRING']) != strip_tags(html_entity_decode($_SERVER['QUERY_STRING']))) ){
			header("Location: /");
			exit;
		}
	}
	
	public static function xssProtectRequest()
	{
		foreach($_REQUEST as $key => $value){
			if(strip_tags($value) != $value){
				header("Location: /");
				exit;
			}
		}
	}
}
?>