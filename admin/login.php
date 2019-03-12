<?php
$dev = 0;

# Debugging
if($dev){
	ini_set('display_errors','1');
	ini_set("error_reporting", E_ALL);
}

# Countries allowed to access the admin section
$allowedCountries = array('United States','Mexico');

# Includes
include_once("includes/library.php");
Security::validateHttpRequest('post');
Security::xssProtectRequest();
# Extract request variables
extract($_POST);

$manageAdmin = new ManageAdmin($_POST);

$status = '';
$country = '';
$activityLog = new ActivityLog();
$results = $activityLog->fetchAll("WHERE `ip` = '".$_SERVER["REMOTE_ADDR"]."' AND `country_status` = 'Allowed' LIMIT 1");
foreach($results as $result){
	$status = $result->getCountryStatus();
	$country = $result->getCountry();
}
	if($status != 'Allowed'){
		$country = $activityLog->ip_info($_SERVER["REMOTE_ADDR"], "Country");
		if(!isset($country)){	
			$mail = new Email();
			$mail->AddAddress('mason@stantonstreet.com','Mason Sales');
			$mail->SetFrom('donotreply'.$GLOBALS['email_domain'],$GLOBALS['site_name']);
			$mail->Subject = $GLOBALS['site_name'].' - Country not returning for login verification';
			$mail->setMsg('the ip_info function in the ActivtyLog.php class is not returning the country. Probable cause: www.geoplugin.net is unreachable.');
			$mail->send();
		}
	}

	$_SESSION['login_country'] = $country;
	if(isset($country)){
		if(in_array($country,$allowedCountries)){
			$_SESSION['login_status'] = 'Allowed';
		}else{
			$_SESSION['login_status'] = 'Blocked';
			$_SESSION['LOGIN_SUCCESSFUL'] = 0;
			$_SESSION['session_fullname'] = $POST['username'];
			$_SESSION['LOGIN_OUTCOME'] = failure('Please login from an authorized location.');
			header("Location: index.php");
			exit;
		}
	}


if($manageAdmin = $manageAdmin->adminLogIn()){
	
	if($manageAdmin->getLocked() && !$manageAdmin->getResetPassword()){
		$_SESSION['LOGIN_SUCCESSFUL'] = 0;
		$_SESSION['LOGIN_OUTCOME'] = failure("Account is locked. Please contact system administrator.");
		header("Location: index.php");
		exit;
	}

	if($manageAdmin->getId() != '1' && ($manageAdmin->getResetPassword() || (str_replace("-","",$manageAdmin->getPasswordExpires()) < date('Ymd') && $manageAdmin->getPasswordExpires() != '0000-00-00' && $manageAdmin->getPasswordExpires() != ''))){
		$_SESSION['session_user_id'] = $manageAdmin->getId();
		$_SESSION['session_access_levels'] = '';
		$_SESSION['session_logged_in'] = false;

		$_SESSION['LOGIN_SUCCESSFUL'] = 0;
		$_SESSION['LOGIN_OUTCOME'] = info("Your password must be reset.");

		header("Location: /admin/reset-password.php");
		exit;
	}

	$_SESSION['session_user_id'] = $manageAdmin->getId();
	$_SESSION['session_fullname'] = $manageAdmin->getName();
	$_SESSION['session_access_levels'] = $manageAdmin->getAccessLevels();
	$_SESSION['LOGIN_OUTCOME'] = success("Login successful.");
	$_SESSION['session_logged_in'] = true;
	//
	$manageAdmin->logLogin();
	//
	$_SESSION['LOGIN_SUCCESSFUL'] = 1;
	if($_SESSION['session_referer']){
		header("Location: ".$_SESSION['session_referer']);
	}else{
		header("Location: admin.php");
	}
	exit;
}else{
	$manageAdmin = new ManageAdmin($_POST);
	$_SESSION['session_user_id'] = $manageAdmin->getId();
	$_SESSION['session_fullname'] = $manageAdmin->getName();
	if($manageAdmin = $manageAdmin->getByUsername()){
		$manageAdmin->logLogin('failure');
	}

	$_SESSION['LOGIN_SUCCESSFUL'] = 0;
	$_SESSION['LOGIN_OUTCOME'] = failure("Username and password incorrect.");
	header("Location: index.php");
	exit;
}
?>
