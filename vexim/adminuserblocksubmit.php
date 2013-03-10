<?php
  include_once dirname(__FILE__) . '/config/variables.php';
  include_once dirname(__FILE__) . '/config/authuser.php';
  include_once dirname(__FILE__) . '/config/functions.php';
  include_once dirname(__FILE__) . '/config/httpheaders.php';

  if ($_GET[action] == 'delete') {
    $query = "DELETE FROM blocklists WHERE block_id='{$_GET['block_id']}'
			AND domain_id='{$_SESSION['domain_id']}' AND user_id='{$_GET['user_id']}'";
    $result = $db->query($query);
    if (!DB::isError($result)) {
      header ("Location: adminuser.php?updated={$_GET['localpart']}");
      die;
    } else {
      header ("Location: adminuser.php?failed={$_GET['localpart']}");
      die;
    }
  }

# Finally 'the rest' which is handled by the profile form
  if (preg_match("/^\s*$/",$_POST['blockval'])) {
    header("Location: adminuser.php");
    die;
  }
  $query = "INSERT INTO blocklists
    (domain_id, user_id, blockhdr, blockval, color) VALUES (
    '{$_SESSION['domain_id']}',
    '{$_POST['user_id']}',
    '{$_POST['blockhdr']}',
    '{$_POST['blockval']}',
    '{$_POST['color']}')";
  $result = $db->query($query);
  if (!DB::isError($result)) {
    header ("Location: adminuser.php?updated={$_POST['localpart']}");
    die;
  } else {
    header ("Location: adminuser.php?failed={$_POST['localpart']}");
  }
?>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
