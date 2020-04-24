<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * phpinfo() wrapper to allow displaying only when configured to do so.
 * @version $Id: phpinfo.php 11986 2008-11-24 11:05:40Z nijel $
 * @package phpMyAdmin
 */

if (preg_match('/\.php\//' , $_SERVER['PHP_SELF'])){
	header("Location: " . preg_replace('/\.php.*/' , '' , $_SERVER['PHP_SELF']) . ".php");
	exit();
}

/**
 * @ignore
 */
define('PMA_MINIMUM_COMMON', true);
/**
 * Gets core libraries and defines some variables
 */
require_once './libraries/common.inc.php';


/**
 * Displays PHP information
 */
if ($GLOBALS['cfg']['ShowPhpInfo']) {
    phpinfo();
}
?>
