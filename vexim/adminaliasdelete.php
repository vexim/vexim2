<?
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authpostmaster.php";

  $query = "DELETE FROM users WHERE user_id={$_GET['user_id']}";
  $result = $db->query($query);
  if (!DB::isError($result)) { header ("Location: adminalias.php?deleted={$_GET['localpart']}"); }
  else { header ("Location: adminalias.php?faildeleted={$_GET['localpart']}"); }
?>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
