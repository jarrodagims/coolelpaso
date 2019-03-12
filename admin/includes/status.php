<?php
/**
 * status.php - Status message display handling
 *
 * This file contains functions for displaying status message within Admin Builder
 *
 * @filesource
 */

/**
 * function success()
 *
 * This function outputs a success message
 * @param string The message to be displayed
 * @param array of refreshData 
 * @param string key used to access refreshData
 * @return string Returns the formatted success message
 * @internal This function should only be used within the Admin system
 */
function success($str, $refreshData = array(), $key="refreshData", $moduleInfo = array('Not Specified','Not Specified','Not Specified')){
	
	$status = status($str, "success");
	
	// If refreshData array is not empty
	if(!empty($refreshData)){
		$refreshData = array($key=>$refreshData);
		$status = array_merge($status, $refreshData);
	}
	$activityLog = new ActivityLog();
	$server = ($_SESSION['session_logged_in'] == 1 ? '' : serialize($_SERVER));
	$ip = ($_SESSION['session_logged_in'] == 1 ? '' : $_SERVER["REMOTE_ADDR"]);
	$country = ($_SESSION['session_logged_in'] == 1 ? '' : $_SESSION['login_country']);
	$login_status = ($_SESSION['session_logged_in'] == 1 ? '' : $_SESSION['login_status']);
	$activityLog->setDate(date('Y-m-d H:i:s'))
				->setUser($_SESSION['session_fullname'])
				->setIp($ip)
				->setCountry($country)
				->setCountryStatus($login_status)
				->setUserAgent($server)
				->setQueryExecuted($_SESSION['last_query'])
				->setActivity('<b>Module:</b> '.$moduleInfo[0].'<br /><b>Title:</b> '.$moduleInfo[1].'<br /><b>ID:</b> '.$moduleInfo[2].'<br /><b>Action:</b> '.$str)
				->save();
	unset($_SESSION['last_query']); 
	unset($_SESSION['login_country']); 
	unset($_SESSION['login_status']); 
	return json_encode($status);
}

/**
 * function failure()
 *
 * This function outputs a failure message
 * @param string The message to be displayed
 * @param array of messages 
 * @param string key used to access messages
 * @return string Returns the formatted failure message
 * @internal This function should only be used within the Admin system
 */
function failure($str, $messages = array(), $key="messages", $moduleInfo = array('Not Specified','Not Specified','Not Specified'))
{
	$status = status($str,"failure");
	
	// If messages array is not empty
	if(!empty($messages)){
		$messages = array($key=>$messages);
		$status = array_merge($status, $messages);	
	}
	if(!$_SESSION['INSTALLING_ADMIN']){
		$activityLog = new ActivityLog();
		$server = ($_SESSION['session_logged_in'] == 1 ? '' : serialize($_SERVER));
		$ip = ($_SESSION['session_logged_in'] == 1 ? '' : $_SERVER["REMOTE_ADDR"]);
		$country = ($_SESSION['session_logged_in'] == 1 ? '' : $_SESSION['login_country']);
		$login_status = ($_SESSION['session_logged_in'] == 1 ? '' : $_SESSION['login_status']);
		$activityLog->setDate(date('Y-m-d H:i:s'))
					->setUser($_SESSION['session_fullname'])
					->setIp($ip)
					->setCountry($country)
					->setCountryStatus($login_status)
					->setUserAgent($server)
					->setQueryExecuted($_SESSION['last_query'])
					->setActivity('<b>Module:</b> '.$moduleInfo[0].'<br /><b>Title:</b> '.$moduleInfo[1].'<br /><b>ID:</b> '.$moduleInfo[2].'<br /><b>Action:</b> '.$str)
					->save();
		unset($_SESSION['last_query']);
		unset($_SESSION['login_country']); 
		unset($_SESSION['login_status']); 
	}
	return json_encode($status);
}
/**
 * function info()
 *
 * This function outputs an info message
 * @param string The message to be displayed
 * @return string Returns the formatted info message
 * @internal This function should only be used within the Admin system
 */
function info($str)
{
	$status = status($str,"info");
	return json_encode($status);
}
/**
 * function warning()
 *
 * This function outputs an warning message
 * @param string The message to be displayed
 * @return string Returns the formatted info message
 * @internal This function should only be used within the Admin system
 */
function warning($str)
{
	$status = status($str,"warning");
	return json_encode($status);
}
/**
 * function status()
 *
 * This function outputs a status message
 * @param string The message to be displayed
 * @param string The type of message, to determine output formatting
 * @return string Returns the formatted status message
 * @internal This function should only be used within the Admin system
 */
function status($str,$type)
{
	switch($type){
		case 'failure':
			$type = 'danger';
			$title = 'Failure';
			break;
		case 'success':
			$type = 'success';
			$title = 'Success';
			break;
		case 'info':
			$type = 'info';
			$title = 'Information';
			break;
		case 'warning':
			$type = 'warning';
			$title = 'Warning';
		default:
			$type = 'info';	
	}
	
	$outcome = array("formOutcome" => array(
											"status" => $type, 
											"title" => $title,
											"message" => $str
										   )
					);
	return $outcome;
	
	/*$icon = '';
	$alert_type = '';
	switch($type){
		case 'failure':
			$icon = '<i class="fa fa-times"></i>';
			$alert_type = 'danger';
			break;
		case 'success':
			$icon = '<i class="fa fa-check"></i>';
			$alert_type = 'success';
			break;
		case 'info':
			$icon = '<i class="fa fa-info-circle"></i>';
			$alert_type = 'info';
			break;
		default:
			$icon = '';	
	}
	
	ob_start(); ?>
    <div class="alert alert-<?php echo $alert_type; ?>" role="alert">
    	<?php echo $icon; ?>
    	<strong><?php echo ucWords($type); ?></strong><?php echo ' ' . $str; ?>
   	</div>
    <?php
	$status = ob_get_clean();
	$_SESSION['session_status'] = $status;
	$_SESSION['session_status_set'] = true;
	return;*/
}
?>