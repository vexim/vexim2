<?
    include_once dirname(__FILE__) . "/config/httpheaders.php";
    setcookie ("vexim[0]", $_POST['localpart'], time()-86400);
    setcookie ("vexim[1]", $cookierow['domain'], time()-86400);
    setcookie ("vexim[2]", $cookierow['domain_id'], time()-86400);
    setcookie ("vexim[3]", $cryptedpass, time()-86400);
    $_SESSION = array();
    session_destroy();
    header ("Location: index.php");
?>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
