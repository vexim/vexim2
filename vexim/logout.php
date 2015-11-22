<?php
    include_once dirname(__FILE__) . '/config/httpheaders.php';
    include_once dirname(__FILE__) . "/config/functions.php";
    invalidate_session();
    header ('Location: index.php');
?>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
