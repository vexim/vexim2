<?
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authsite.php";
  include_once dirname(__FILE__) . "/config/functions.php";

  if (validate_password($_POST['clear'], $_POST['vclear'])) {
    $crypted = crypt($_POST['clear']);
    $query = "UPDATE users SET crypt='$crypted',
		clear='{$_POST['clear']}' WHERE localpart='siteadmin'";
    $result = $db->query($query);
    if (!DB::isError($result)) {
      setcookie ("vexim[3]", $crypted, time()+86400);
      header ("Location: site.php?sitepass=success");
      die;
    } else {
      header ("Location: site.php?sitepass=fail");
      die;
    }
  } else {
    header ("Location: site.php?badpass=siteadmin");
  }
?>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
