<?
    include_once dirname(__FILE__) . "/config/httpheaders.php";
    $_SESSION = array();
    session_destroy();
    header ("Location: index.php");
?>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
