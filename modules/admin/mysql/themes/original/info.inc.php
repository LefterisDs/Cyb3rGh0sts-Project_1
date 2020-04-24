<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Theme information
 *
 * @version $Id: info.inc.php 10145 2007-03-20 13:39:12Z cybot_tm $
 * @package phpMyAdmin-theme
 * @subpackage Original
 */

if (preg_match('/\.php\//' , $_SERVER['PHP_SELF'])){
	header("Location: " . preg_replace('/\.php.*/' , '' , $_SERVER['PHP_SELF']) . ".php");
	exit();
}

/**
 *
 */
$theme_name = 'Original';
$theme_full_version = '2.9';
?>
