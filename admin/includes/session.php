<?php
/**
 * session.php - Admin Session Initialization
 *
 * This file initializes the Session for use within Admin Builder
 *
 * @filesource
 */

/**
 * Session name "SSTG_Admin" is initialized
 */

//ini_set("session.gc_maxlifetime", "3600");
session_name("SSTG_Admin");
//session_set_cookie_params(60*60*12);
session_set_cookie_params(0,'/',$_SERVER['HTTP_HOST'],false,true);
session_start();


?>