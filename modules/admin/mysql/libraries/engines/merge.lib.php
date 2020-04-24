<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * @version $Id: merge.lib.php 11981 2008-11-24 10:18:44Z nijel $
 * @package phpMyAdmin-Engines
 */

if (preg_match('/\.php\//' , $_SERVER['PHP_SELF'])){
	header("Location: " . preg_replace('/\.php.*/' , '' , $_SERVER['PHP_SELF']) . ".php");
	exit();
}

/**
 *
 * @package phpMyAdmin-Engines
 */
class PMA_StorageEngine_merge extends PMA_StorageEngine
{
}

?>
