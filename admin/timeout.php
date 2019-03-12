<?php 
include_once("includes/session.php");

$_SESSION['session_timeout'] = true;

header('Location: /admin/logout.php');

?>