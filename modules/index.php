<?php 

    if (preg_match('/.php\//' , $_SERVER['PHP_SELF'])){
        header("Location: " . preg_replace('/.php.*/' , '' , $_SERVER['PHP_SELF']) . ".php");
        exit();
    }

   header("Location: ../");
?>
