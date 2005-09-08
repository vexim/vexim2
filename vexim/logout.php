<?php
    include_once dirname(__FILE__) . '/config/httpheaders.php';
    $_SESSION = array();
    header ('Location: index.php');
    session_destroy();
?>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
