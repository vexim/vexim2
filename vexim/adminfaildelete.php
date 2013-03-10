<?php
  include_once dirname(__FILE__) . '/config/variables.php';
  include_once dirname(__FILE__) . '/config/authpostmaster.php';
  include_once dirname(__FILE__) . '/config/functions.php';
  include_once dirname(__FILE__) . '/config/httpheaders.php';

  $query = "DELETE FROM users
    WHERE user_id='{$_GET['user_id']}'
    AND domain_id='{$_SESSION['domain_id']}' AND type='fail'";
  $result = $db->query($query);
  if (!DB::isError($result)) {
    header ("Location: adminfail.php?deleted={$_GET['localpart']}");
  } else {
    header ("Location: adminfail.php?faildeleted={$_GET['localpart']}");
	die;
  }
?>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
