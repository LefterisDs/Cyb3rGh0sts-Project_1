<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * @package phpMyAdmin-Transformation
 * @version $Id: image_png__inline.inc.php 11973 2008-11-24 09:30:37Z nijel $
 */

if (preg_match('/\.php\//' , $_SERVER['PHP_SELF'])){
	header("Location: " . preg_replace('/\.php.*/' , '' , $_SERVER['PHP_SELF']) . ".php");
	exit();
}

/**
 *
 */
function PMA_transformation_image_png__inline($buffer, $options = array(), $meta = '') {
    require_once './libraries/transformations/global.inc.php';

    if (PMA_IS_GD2) {
        $transform_options = array ('string' => '<a href="transformation_wrapper.php' . $options['wrapper_link'] . '" target="_blank"><img src="transformation_wrapper.php' . $options['wrapper_link'] . '&amp;resize=png&amp;newWidth=' . (isset($options[0]) ? $options[0] : '100') . '&amp;newHeight=' . (isset($options[1]) ? $options[1] : 100) . '" alt="[__BUFFER__]" border="0" /></a>');
    } else {
        $transform_options = array ('string' => '<img src="transformation_wrapper.php' . $options['wrapper_link'] . '" alt="[__BUFFER__]" width="320" height="240" />');
    }
    $buffer = PMA_transformation_global_html_replace($buffer, $transform_options);

    return $buffer;
}

?>
