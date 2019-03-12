<?php
class Session
{
	// Member Constants
	
	// Instance Variables

	// Static Methods
	public static function register($key,$value)
	{
		$_SESSION[$key] = serialize($value);
	}
	
	public static function unregister($key)
	{
		unset($_SESSION[$key]);
	}
	
	public static function load($key,$class = '')
	{
		if(isset($_SESSION[$key])){
			return unserialize($_SESSION[$key]);
		}elseif(strlen(trim($class))){
			return new $class();
		}
	}
	
	public static function exists($key)
	{
		return isset($_SESSION[$key]);
	}
	
	public static function setMessage($status,$message)
	{
		self::register('SESSION_MESSAGE_STATUS',$status);
		self::register('SESSION_MESSAGE',$message);
	}
	
	public static function displayMessage()
	{
		$message = '';
		if(self::exists('SESSION_MESSAGE')){
			$status = self::load('SESSION_MESSAGE_STATUS');
			$message = self::load('SESSION_MESSAGE');
			$message = '<div class="message"><span class="'.$status.'">'.$message.'</span></div>';
			self::unregister('SESSION_MESSAGE_STATUS');
			self::unregister('SESSION_MESSAGE');
		}
		
		return $message;
	}
	
}
?>