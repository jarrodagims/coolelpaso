<?php
/**
 * ftp.php - FTP functions
 *
 * This file contains functions for FTP actions
 *
 * @filesource
 */
function ftpPut($source,$destination = '',$ftp_host='',$ftp_username='',$ftp_password='')
{
	$destination = (strlen(trim($destination))) ? $destination : $source;
	$ftp_host = (strlen(trim($ftp_host))) ? $ftp_host : $GLOBALS['ftp_host'];
	$ftp_username = (strlen(trim($ftp_username))) ? $ftp_username : $GLOBALS['ftp_username'];
	$ftp_password = (strlen(trim($ftp_password))) ? $ftp_password : $GLOBALS['ftp_password'];
	$ftp = ftp_connect($ftp_host);
	$login = ftp_login($ftp, $ftp_username, $ftp_password);
	$upload = ftp_put($ftp, $destination, $source,FTP_BINARY); 
	ftp_close($ftp);
}
function ftpDelete($file, $ftp_host='',$ftp_username='',$ftp_password='')
{
	$ftp_host = (strlen(trim($ftp_host))) ? $ftp_host : $GLOBALS['ftp_host'];
	$ftp_username = (strlen(trim($ftp_username))) ? $ftp_username : $GLOBALS['ftp_username'];
	$ftp_password = (strlen(trim($ftp_password))) ? $ftp_password : $GLOBALS['ftp_password'];
	$ftp = ftp_connect($ftp_host);
	$login = ftp_login($ftp, $ftp_username, $ftp_password);
	$upload = ftp_delete($ftp, "project/intranet/files/".$file); 
	ftp_close($ftp);
}
?>