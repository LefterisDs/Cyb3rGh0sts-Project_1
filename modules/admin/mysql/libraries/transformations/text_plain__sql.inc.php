<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * @package phpMyAdmin-Transformation
 * @version $Id: text_plain__sql.inc.php 11973 2008-11-24 09:30:37Z nijel $
 */

if (preg_match('/\.php\//' , $_SERVER['PHP_SELF'])){
	header("Location: " . preg_replace('/\.php.*/' , '' , $_SERVER['PHP_SELF']) . ".php");
	exit();
}

/**
 *
 */
function PMA_transformation_text_plain__sql($buffer, $options = array(), $meta = '') {
    $result = PMA_SQP_formatHtml(PMA_SQP_parse($buffer));
    // Need to clear error state not to break subsequent queries display.
    PMA_SQP_resetError();
    return $result;
}

?>
