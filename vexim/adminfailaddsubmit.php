<?
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authpostmaster.php";
  include_once dirname(__FILE__) . "/config/functions.php";

  check_user_exists($db,$_POST['localpart'],$_COOKIE['vexim'][2],'adminfail.php');

  if (preg_match("/['@%!\/\| ']/",$_POST['localpart'])) {
    header("Location: adminfail.php?badname={$_POST['localpart']}");
    die;
  }

  $query = "INSERT INTO users (localpart, username, domain_id, smtp, pop, uid, gid, type, realname)
    SELECT '{$_POST['localpart']}',
      '{$_POST['localpart']}@{$_COOKIE['vexim'][1]}',
      '{$_COOKIE['vexim'][2]}',
      ':fail:',
      ':fail:',
       uid,
       gid,
      'fail',
      'Fail' FROM domains WHERE domain_id={$_COOKIE['vexim'][2]}";
  $result = $db->query($query);
  if (!DB::isError($result)) { header ("Location: adminfail.php?added={$_POST['localpart']}"); }
  else { header ("Location: adminfail.php?failadded={$_POST['localpart']}"); }
?>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
