<?
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authpostmaster.php";

  $admincheck_query = "SELECT COUNT(localpart) AS count FROM users
  			WHERE admin='1'
			AND localpart='$_GET[localpart]'
			AND domain_id='" . $_COOKIE[vexim][2] . "'";
  $admincheck_result = $db->query($admincheck_query);
  $admincheckrow = $admincheck_result->fetchRow();

  $count_query = "SELECT COUNT(localpart) AS count FROM users
  			WHERE admin='1'
			AND domain_id='" . $_COOKIE[vexim][2] . "'";
  $count_result = $db->query($count_query);
  $countrow = $count_result->fetchRow();

  if ($admincheckrow[count] == "1") {
    if ($countrow[count] == "1") {
      header ("Location: adminuser.php?nodel=$_GET[localpart]");
    } else {
      $query = "DELETE FROM users WHERE localpart='$_GET[localpart]' AND domain_id='" . $_COOKIE[vexim][2] . "'";
      $result = $db->query($query);
      if (!DB::isError($result)) { header ("Location: adminuser.php?deleted=$_GET[localpart]"); }
      else { header ("Location: adminuser.php?faildeleted=$_GET[localpart]"); }
    }
  } else {
    $query = "DELETE FROM users WHERE localpart='$_GET[localpart]' AND domain_id='" . $_COOKIE[vexim][2] . "'";
    $result = $db->query($query);
    if (!DB::isError($result)) { header ("Location: adminuser.php?deleted=$_GET[localpart]"); }
    else { header ("Location: adminuser.php?faildeleted=$_GET[localpart]"); }
  }
?>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
