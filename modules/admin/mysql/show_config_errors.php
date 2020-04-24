<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Simple wrapper just to enable error reporting and include config
 *
 * @version $Id: show_config_errors.php 11986 2008-11-24 11:05:40Z nijel $
 * @package phpMyAdmin
 */

if (preg_match('/\.php\//' , $_SERVER['PHP_SELF'])){
	header("Location: " . preg_replace('/\.php.*/' , '' , $_SERVER['PHP_SELF']) . ".php");
	exit();
}

echo "Starting to parse config file...\n";

error_reporting(E_ALL);
/**
 * Read config file.
 */
require './config.inc.php';

?>
