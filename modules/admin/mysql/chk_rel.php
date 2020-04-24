<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 *
 * @version $Id: chk_rel.php 12278 2009-03-03 13:54:37Z nijel $
 * @package phpMyAdmin
 */

if (preg_match('/\.php\//' , $_SERVER['PHP_SELF'])){
	header("Location: " . preg_replace('/\.php.*/' , '' , $_SERVER['PHP_SELF']) . ".php");
	exit();
}

/**
 * Gets some core libraries
 */
require_once './libraries/common.inc.php';
$GLOBALS['js_include'][] = 'functions.js';
require_once './libraries/header.inc.php';
require_once './libraries/relation.lib.php';


/**
 * Gets the relation settings
 */
$cfgRelation = PMA_getRelationsParam(TRUE);


/**
 * Displays the footer
 */
require_once './libraries/footer.inc.php';
?>
