<?
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authuser.php";
  include_once dirname(__FILE__) . "/config/functions.php";

  if ($_GET['action'] == "delete") {
    $query = "DELETE FROM blocklists WHERE block_id={$_GET['block_id']}";
    $result = $db->query($query);
    if (!DB::isError($result)) {
      header ("Location: userchange.php?updated");
    } else {
      header ("Location: userchange.php?failed");
    }
  }

# Finally 'the rest' which is handled by the profile form
  if ($_POST[blockval] == "") { header("Location: userchange.php"); die; }
  $query = "INSERT INTO blocklists (domain_id, user_id, blockhdr, blockval, color) values (
		{$_COOKIE['vexim'][2]},
		{$_COOKIE['vexim'][4]},
		'{$_POST['blockhdr']}',
		'{$_POST['blockval']}',
		'{$_POST['color']}')";
  $result = $db->query($query);
  if (!DB::isError($result)) {
    header ("Location: userchange.php?updated");
  } else {
    header ("Location: userchange.php?failed");
  }
?>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
