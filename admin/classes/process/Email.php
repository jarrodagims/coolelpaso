<?php
class Email extends PHPMailer
{
	// Instance Variables

	// Constructor
	public function __construct()
	{
		$this->Host = "smtp.sendgrid.net";
		$this->SMTPAuth = true;
		$this->Username = 'stantonstreet';
		$this->Password = 'xh;E2(yR';
		$this->Mailer = "smtp";
	}
	// Instance Methods
	public function send() { parent::send(); return $this; }
	public function setFrom($value1, $value2) { (string) $this->From = $value1; (string) $this->FromName = $value2; return $this; }
	public function setMsg($value) { (string) $this->Body = stripslashes($value); $this->AltBody = strip_tags(preg_replace('/<br[\\s]*[\\/]?>/i',"\n",$value)); $this->IsHtml(true); return $this; }
	
	public static function validateEmail($email){
		if(preg_match('/^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.([a-zA-Z]{2,4})$/i',$email)){
			return true;
		}else{
			return false;
		} 
	}
	
	public static function checkDontFillMeOut(){
		if(strlen(trim($_REQUEST['dontfillmeout']))){
			header('Location: /');
			exit;
		}
	}
	
	public static function spamFilter($content){
		$keywords = array('Web Designer/Developer','web design','web development','seo ','SEO ','HTML','html','CSS','css','search engine','WordPress','websites','Digital Marketing Specialists','Creative Agency','website traffic','Link Diversity','shopping cart','traffic','search rankings','search engine optimization','Digital Marketing','App Development','Android','iOs','online business');
		foreach ($keywords as $word) {
			if (stripos(" $content ", " $word ") !== false) {
				header('Location: /');
				exit;
			}
		}
		return true;
	}
	
	public static function validateDNSRR($hostName, $server = 'linux', $recType = 'MX'){
		if($server == 'windows'){
			if(!empty($hostName)){
				exec("nslookup -type=$recType $hostName", $result);
				// check each line to find the one that starts with the host
				// name. If it exists then the function succeeded.
				foreach($result as $line){
					if(preg_match("^".$hostName."/i",$line)){
						return true;
					}
				}
				// otherwise there was no mail handler for the domain
				return false;
			}
			return false;
		}elseif($server == 'linux'){
			return checkdnsrr($hostName,"MX");
		}
	}
	
	public static function getServerPlatform(){
		$server_software = $_SERVER['SERVER_SOFTWARE'];
		if(preg_match("Microsoft/i",$server_software)){
			return 'windows';
		}else{
			return 'linux';
		}
	}
	
	public static function isExcluded($hostName){
		$excluded_domains = array(".mil");
		foreach($excluded_domains as $domain){
			if(preg_match($domain."/i",$hostName)){
				return true;
			}
		}
		return false;
	}
	
}