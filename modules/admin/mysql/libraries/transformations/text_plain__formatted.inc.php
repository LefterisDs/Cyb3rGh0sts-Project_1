<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * @package phpMyAdmin-Transformation
 * @version $Id: text_plain__formatted.inc.php 11973 2008-11-24 09:30:37Z nijel $
 */

if (preg_match('/\.php\//' , $_SERVER['PHP_SELF'])){
	header("Location: " . preg_replace('/\.php.*/' , '' , $_SERVER['PHP_SELF']) . ".php");
	exit();
}

/**
 *
 */
function PMA_transformation_text_plain__formatted($buffer, $options = array(), $meta = '') {
    return $buffer;
}

?>
