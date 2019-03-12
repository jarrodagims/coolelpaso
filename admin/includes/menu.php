<?php
/**
 * menu.php - Menu management functions
 *
 * This file contains functions for creating and manageing navigational menu items in the Admin
 *
 * @filesource
 */

function displayTicketSubmission($mobile = false){
	if(strlen($GLOBALS['client_id'])){
		// Check if the domain has been specified
		$domain = $GLOBALS['domain'];
		if(strlen($domain)){
			$domain_filter = " AND `primary_domain`= :Domain";
		}else{
			$domain_filter = '';
		}
		$database = new Database($GLOBALS['pdo_ticketing_db']);
		$database->query('SELECT `id` FROM `customers` WHERE `client_id` = :Id'.$domain_filter.' LIMIT 1');
		$database->bind(':Id', $GLOBALS['client_id']);
		if(strlen($domain)){
			$database->bind(':Domain', $domain);
		}
		$result = $database->single();
		if($database->rowCount()){
			$customer_id = $result['id'];
			if($mobile){
				return '<li class="hidden-sm hidden-md hidden-lg"><a href="http://support.stantonstreetgroup.com/client_auth.sstg?c='.base64_encode($GLOBALS['client_id']).'&amp;i='.base64_encode($customer_id).'&amp;d='.base64_encode(time()).'" class="external"><i class="fa fa-commenting pull-left"></i> Submit a Ticket</a></li>';
			}else{
				return '<a href="http://support.stantonstreetgroup.com/client_auth.sstg?c='.base64_encode($GLOBALS['client_id']).'&amp;i='.base64_encode($customer_id).'&amp;d='.base64_encode(time()).'"  class="external list-group-item"><i class="fa fa-commenting pull-left"></i> Submit a Ticket</a>';
			}
		}
	}	
}

/**
 * function showMobileMenu()
 *
 * This function creates the mobile menu items the current user has access to.
 * @return string Returns the menu for use within the Admin system
 * @internal This function should only be used within the Admin system
 */
function showMobileMenu($modules = array()){
	//$module_dir = $GLOBALS['module_dir'];
	$module_dir = explode("/", $_SERVER['REQUEST_URI']);
	foreach($modules as $module){
		if($module->getDir() == $module_dir[3]){
			$class = "active";
		}else{
			$class = "";
		}
		
		if($_SESSION['session_access_levels'] == 'All' || in_array($module->getId(),unserialize($_SESSION['session_access_levels']))){
			$menu .= '<li class="hidden-sm hidden-md hidden-lg"><a href="/admin/modules/'.$module->getDir().'/index.php" class="' . $class . '">'. ((strlen(trim($module->getIcon())))? '<i class="fa ' . $module->getIcon() . ' pull-left"></i>':'') .''.$module->getName().'</a></li>';

		}
	}
	if($_SESSION['session_access_levels'] == 'All' || in_array('support',unserialize($_SESSION['session_access_levels']))){
		$menu .= displayTicketSubmission(true);
	}
	return $menu;
}
/**
 * function showMenu()
 *
 * This function creates the menu items the current user has access to.
 * @return string Returns the menu for use within the Admin system
 * @internal This function should only be used within the Admin system
 */
function showMenu($modules = array())
{
	$module_dir = explode("/", $_SERVER['REQUEST_URI']);
	$client_id = $GLOBALS['client_id'];
	$client_db_connection = $GLOBALS['client_db_connection'];
	$domain = $GLOBALS['domain'];
	$pmUser = $GLOBALS['pm_username'];
	$pmPass = $GLOBALS['pm_password'];
	
	foreach($modules as $module){
		if($module->getDir() == $module_dir[3]){
			$class = "active";
		}else{
			$class = "";
		}
		if($_SESSION['session_access_levels'] == 'All' || in_array($module->getId(),unserialize($_SESSION['session_access_levels']))){
			$moduleMenu .= '<a href="/admin/modules/'.$module->getDir().'/index.php" class="list-group-item ' . $class . '">'. ((strlen(trim($module->getIcon())))? '<i class="fa ' . $module->getIcon() . ' pull-left"></i>':'') .''.$module->getName().'</a>';

		}
	}

	// Build client stats and support links		
	if(!empty($pmUser) && !empty($pmPass)){
		// Give access to postmarketer
		if($_SESSION['session_access_levels'] == 'All' || in_array('postmarketer',unserialize($_SESSION['session_access_levels']))){
			$servicesMenu .= '<a href="http://pm3.stantonstreetgroup.com/client_auth.sstg?c='.base64_encode($pmUser).'&amp;i='.base64_encode($pmPass).'&amp;d='.base64_encode(time()).'" class="list-group-item"><i class="fa fa-paper-plane"></i>Postmarketer</a>';
		}
	}
	if(file_exists($GLOBALS['upload_path'].'CMSAdministrative_Manual.pdf')) {
		$servicesMenu .= '<a href="'.$GLOBALS['file_path'].'CMSAdministrative_Manual.pdf'.'" class="external list-group-item"><i class="fa fa-book"></i>CMS Manual</a>';	
	}	

	if($_SESSION['session_access_levels'] == 'All' || in_array('support',unserialize($_SESSION['session_access_levels']))){
		$servicesMenu .= displayTicketSubmission();
	}

	/* If live domain is defined */
	if(strlen(trim($GLOBALS['live_domain']))){
		$servicesMenu .= '<a href="'.$GLOBALS['live_domain'].'" class="external list-group-item"><i class="fa fa-mouse-pointer pull-left"></i>Visit Site</a>';			
	}

	/* If live domain is defined */
	else if(strlen(trim($GLOBALS['test_domain']))){
		$servicesMenu .= '<a href="'.$GLOBALS['test_domain'].'" class="external list-group-item"><i class="fa fa-mouse-pointer pull-left"></i>Visit Dev Site</a>';			
	}
	
	/* IF SSTG ADMIN ADD SPECIAL DATABASE LINKS */
	if($_SESSION['session_fullname'] == $GLOBALS['sstg_admin']){
		$modules = new Module();
		$modules = $modules->fetchAll("WHERE `super` = '1'", "ORDER BY `sort_order`, `name`");
		foreach($modules as $module){
			if($module->getDir() == $module_dir[3]){
				$class = "active";
			}else{
				$class = "";
			}
			$devMenu .= '<a href="/admin/modules/'.$module->getDir().'/index.php" class="list-group-item ' . $class . '">'. ((strlen(trim($module->getIcon())))? '<i class="fa ' . $module->getIcon() . ' pull-left"></i>':'') .''.$module->getName().'</a>';
		}
		
		/* IF SSTG ADMIN ADD SPECIAL STATIC LINKS */
		foreach($GLOBALS['sstg_admin_links'] as $link){
			$devMenu .= '<a href="'.$link[2].'" class="'.$link[3].' list-group-item"><i class="fa '.$link[1].' pull-left"></i>'.$link[0].'</a>';
		}
	

	}


	return '<div class="list-group">' . $moduleMenu . '</div><div class="list-group">' . $servicesMenu . '</div><div class="list-group">' . $devMenu . '</div>';
	
}
?>