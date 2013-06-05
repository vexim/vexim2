<?php
  include_once dirname(__FILE__) . '/config/variables.php';
  include_once dirname(__FILE__) . '/config/authpostmaster.php';
  include_once dirname(__FILE__) . '/config/functions.php';
  include_once dirname(__FILE__) . '/config/httpheaders.php';

  check_user_exists(
    $db,$_POST['localpart'],$_SESSION['domain_id'],'adminfail.php'
  );

  if (preg_match("/['@%!\/\| ']/",$_POST['localpart'])) {
    header("Location: adminfail.php?badname={$_POST['localpart']}");
    die;
  }

  $query = "INSERT INTO users (localpart, username, domain_id, smtp, pop,
    uid, gid, type, realname) SELECT '{$_POST['localpart']}',
      '{$_POST['localpart']}@{$_SESSION['domain']}',
      '{$_SESSION['domain_id']}',
      ':fail:',
      ':fail:',
       uid,
       gid,
      'fail',
      'Fail' FROM domains WHERE domain_id='{$_SESSION['domain_id']}'";
  $result = $db->query($query);
  if (!DB::isError($result)) {
    header ("Location: adminfail.php?added={$_POST['localpart']}");
  } else {
    header ("Location: adminfail.php?failadded={$_POST['localpart']}");
  }
?>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
