<?php
include_once("includes/library.php");

$timeout = $_SESSION['session_timeout'];

setcookie(session_name(), '', time()-42000, '/');
session_destroy();

if($timeout){
	session_start();
	$_SESSION['session_timeout'] = true;
}

$_SESSION['LOGIN_SUCCESSFUL'] = 0;
$_SESSION['LOGIN_OUTCOME'] = success("Logout successful.");
header("Location: index.php");
exit;
?>