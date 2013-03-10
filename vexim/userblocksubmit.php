<?php
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authuser.php";
  include_once dirname(__FILE__) . "/config/functions.php";
  include_once dirname(__FILE__) . "/config/httpheaders.php";

  if ($_GET['action'] == "delete") {
    $query = "DELETE FROM blocklists WHERE block_id='{$_GET['block_id']}'";
    $result = $db->query($query);
    if (!DB::isError($result)) {
      header ("Location: userchange.php?updated");
    } else {
      header ("Location: userchange.php?failed");
    }
  }

# Finally 'the rest' which is handled by the profile form
  if (preg_match("/^\s*$/",$_POST['blockval'])) { header("Location: userchange.php"); die; }
  $query = "INSERT INTO blocklists (domain_id, user_id, blockhdr, blockval, color) values (
            '{$_SESSION['domain_id']}',
            '{$_SESSION['user_id']}',
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
