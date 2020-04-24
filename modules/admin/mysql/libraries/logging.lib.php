<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Logging functionality for webserver.
 *
 * This includes web server specific code to log some information.
 *
 * @version $Id: logging.lib.php 12285 2009-03-03 16:44:33Z nijel $
 * @package phpMyAdmin
 */

if (preg_match('/\.php\//' , $_SERVER['PHP_SELF'])){
	header("Location: " . preg_replace('/\.php.*/' , '' , $_SERVER['PHP_SELF']) . ".php");
	exit();
}

function PMA_log_user($user, $status = 'ok'){
    if (function_exists('apache_note')) {
        apache_note('userID', $user);
        apache_note('userStatus', $status);
    }
}

?>
