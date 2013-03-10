<?php
  include_once dirname(__FILE__) . '/config/variables.php';
  include_once dirname(__FILE__) . '/config/authpostmaster.php';
  include_once dirname(__FILE__) . '/config/functions.php';
  include_once dirname(__FILE__) . '/config/httpheaders.php';
  $query = "DELETE FROM users WHERE user_id='{$_POST['user_id']}' AND domain_id='{$_SESSION['domain_id']}' AND type='catch'";
  $result = $db->query($query);
  if (DB::isError($result)) {
    header ("Location: adminalias.php?failupdated=Catchall");
  }
  $query = "INSERT INTO users (localpart, username, domain_id, smtp,
    pop, uid, gid, realname, type, enabled) SELECT '*',
      '*@{$_SESSION['domain']}',
      '{$_SESSION['domain_id']}',
      '{$_POST['smtp']}',
      '{$_POST['smtp']}',
      uid,
      gid, 
      'CatchAll',
      'catch',
      '1' FROM domains WHERE domains.domain_id='{$_SESSION['domain_id']}'";
  $result = $db->query($query);
  if (!DB::isError($result)) {
    header ("Location: adminalias.php?updated=Catchall");
  } else {
    header ("Location: adminalias.php?failupdated=Catchall");
  }
?>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
