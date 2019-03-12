<?php
/**
 * globals.php - Global Variables
 *
 * This file initializes global system variables
 *
 * @filesource
 */


# Database Information
/***************************************************************************/
// @global string $GLOBALS['host']
$GLOBALS['host'] = 'mariadb-157.wc2.phx1.stabletransit.com';

// @global string $GLOBALS['database']
$GLOBALS['database'] = '453466_coolelpaso';

// @global string $GLOBALS['username']
$GLOBALS['username'] = '453466_coolelpa';

// @global string $GLOBALS['password']
$GLOBALS['password'] = 'Sstg2564!';

// @global string $GLOBALS['server']
$GLOBALS['server'] = 'mariadb-157.wc2';

// @global string $GLOBALS['pdo']
$GLOBALS['pdo'] = array($GLOBALS['host'],$GLOBALS['username'],$GLOBALS['password'],$GLOBALS['database']);

// determine if site is coming from external host outside liquid web.
if($GLOBALS['host'] == '127.0.0.1'){
	$clientHostname = '207.246.242.127';
}else{
	$clientHostname = 'mariadb-124.wc2.phx1.stabletransit.com';
}

// @global string $GLOBALS['pdo_ticketing_db']
$GLOBALS['pdo_ticketing_db'] = array($clientHostname,'453466_clientdb','sstg_customers','453466_clientdb');

// @global string $GLOBALS['pdo_seo_db']
$GLOBALS['pdo_seo_db'] = array('windows.stantonstreetgroup.com','sstg_admin_user','sstg1336','sstg_admin');


# Interface Colors
/***************************************************************************/
// @global string $GLOBALS['primary_color']
$GLOBALS['primary_color'] = '#557345';

// @global string $GLOBALS['button_color']
$GLOBALS['button_color'] = '#834d00';

// @global string $GLOBALS['button_color_hover']
$GLOBALS['button_color_hover'] = '#834d00';


# Client Information
/***************************************************************************/
// @global string $GLOBALS['client_id']
$GLOBALS['client_id'] = '2564';

// @global string $GLOBALS['pm_username']
$GLOBALS['pm_username'] = '';

// @global string $GLOBALS['pm_password']
$GLOBALS['pm_password'] = '';

// @global string $GLOBALS['site_id']
$GLOBALS['site_id'] = '1153';

// @global string $GLOBALS['domain'] For Web Stats Login: Leave blank unless client_id is associated with more than one domain
$GLOBALS['domain'] = '';

// @global string $GLOBALS['site_name']
$GLOBALS['site_name'] = "Stanton Street Admin Development";

// @global string $GLOBALS['email_domain']
$GLOBALS['email_domain'] = "@stantonstreet.com";

// @global string $GLOBALS['live_domain']
$GLOBALS['LIVE_DOMAIN'] = "www.coolelpaso.com";

// @global string $GLOBALS['test_domain']
$GLOBALS['test_domain'] = "http://www.stantonstreet.com/";


# Postmarketer Information
/***************************************************************************/
// @global string $GLOBALS['pm3_client_id']
$GLOBALS['pm3_client_id'] = '0';

// @global string $GLOBALS['postmarketer_folder']
$GLOBALS['postmarketer_folder'] = '';

// Postmarketer FTP Info
$GLOBALS['pm_ftp_host'] = 'winsec.stantonstreetgroup.com';
$GLOBALS['pm_ftp_username'] = 'postmarketer';
$GLOBALS['pm_ftp_password'] = 'postmarketer1336';

# FTP Information
/***************************************************************************/
$GLOBALS['ftp_host'] = '';
$GLOBALS['ftp_username'] = '';
$GLOBALS['ftp_password'] = '';

# Google Maps
/***************************************************************************/
// @global string $GLOBALS['google_map_api_key']
$GLOBALS['google_map_api_key'] = '';


# Recaptcha
/***************************************************************************/
// @global string $GLOBALS['recaptcha_site_key']
// @global string $GLOBALS['recaptcha_secret_key']
$GLOBALS['recaptcha_site_key'] = '';
$GLOBALS['recaptcha_secret_key'] = '';

# Path Information
/***************************************************************************/
/**
 * - $_SERVER['DOCUMENT_ROOT']."/"
 * @global string $GLOBALS['path']
 * @internal Uses $_SERVER['PATH_TRANSLATED'] if $_SERVER['DOCUMNET_ROOT'] is blank.
 * Assumes /admin folder is on the web root
 * "\" is replaced with "/" for compatibility on both linux and windows servers
 */
$GLOBALS['path'] = $_SERVER['DOCUMENT_ROOT'];
// DOCUMENT_ROOT is empty on some servers
if(!strlen($GLOBALS['path'])){
	$GLOBALS['path'] = str_replace($_SERVER['SCRIPT_NAME'],"",eregi_replace("[\]+","/",$_SERVER['PATH_TRANSLATED'])).'/';
}else{
	$GLOBALS['path'] = $_SERVER['DOCUMENT_ROOT']."/";
}

// @global string $GLOBALS['upload_path']
$GLOBALS['upload_path'] = $GLOBALS['path']."files/";

// @global string $GLOBALS['file_path']
$GLOBALS['file_path'] = "/files/";


# Page Request URI / Rewrite URL consolidation
/***************************************************************************/
/**
 * - $_SERVER['HTTP_X_REWRITE_URL']
 * @global string $GLOBALS['rewrite_url']
 * @internal Uses $_SERVER['REQUEST_URI'] if $_SERVER['HTTP_X_REWRITE_URL'] is blank.
 * for compatibility on both linux and windows servers
 */
if(isset($_SERVER['HTTP_X_REWRITE_URL'])) {
	$GLOBALS['rewrite_url'] = $_SERVER['HTTP_X_REWRITE_URL'];
}

// HTTP_X_REWRITE_URL is empty on some servers
if(empty($GLOBALS['rewrite_url'])){
	$GLOBALS['rewrite_url'] = $_SERVER['REQUEST_URI'];
}


# Admin Information
/***************************************************************************/
// @global string $GLOBALS['admin_title']
$GLOBALS['admin_title'] = "Stanton Street Website Administration";

// @global string $GLOBALS['template_title']
$GLOBALS['template_title'] = "Website Administration";

// @global string $GLOBALS['help_contact']
$GLOBALS['help_contact'] = "administrator@stantonstreetgroup.com";

// @global string $GLOBALS['help_subject']
$GLOBALS['help_subject'] =  "Help Request";

// @global string $GLOBALS['help_desk_link']
$GLOBALS['help_desk_link'] =  "http://support.stantonstreetgroup.com/";

// @global string $GLOBALS['tiny_mce']
$GLOBALS['tiny_mce'] = true;

// SSTG Admin Links
$GLOBALS['sstg_admin'] = 'Stanton Street Administrator';
$GLOBALS['sstg_admin_links'] = array();

array_push($GLOBALS['sstg_admin_links'], 
	$link = array('Database', 'fa-database', 'https://mysql.dfw1-2.websitesettings.com/index.php?server='.$GLOBALS['server'].'&pma_username='.$GLOBALS['username'].'&pma_password='.$GLOBALS['password'],'external'));

?>