<?
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authpostmaster.php";
  include_once dirname(__FILE__) . "/config/functions.php";

  # Fix the boolean values
  $query = "SELECT uid,gid,quotas FROM domains WHERE domain_id ='$_COOKIE[vexim][2]'";
  $result = $db->query($query);
  $row = $result->fetchRow();
  if (isset($_POST[admin])) {$_POST[admin] = 1;} else {$_POST[admin] = 0;}
  if (isset($_POST[avscan])) {$_POST[avscan] = 1;} else {$_POST[avscan] = 0;}
  if (isset($_POST[pipe])) {$_POST[pipe] = 1;} else {$_POST[pipe] = 0;}
  if (isset($_POST[spamassassin])) {$_POST[spamassassin] = 1;} else {$_POST[spamassassin] = 0;}
  if (isset($_POST[enabled])) {$_POST[enabled] = 1;} else {$_POST[enabled] = 0;}
  if (!isset($_POST[uid])) {$_POST[uid] = $row[uid];}
  if (!isset($_POST[gid])) {$_POST[gid] = $row[gid];}
  if (!isset($_POST[quota])) {$_POST[quota] = $row[quotas];}
  if ($row[quotas] != "0") {
    if (($_POST[quota] > $row[quotas]) || ($_POST[quota] == "0")) { 
      header ("Location: adminuser.php?quotahigh=$row[quotas]"); die; 
    }
  }

  check_user_exists($db,$_POST[localpart],$_COOKIE[vexim][2],'adminuser.php');

  if (preg_match("/[@%!\/\| ]/",$_POST[localpart])) {
  	header("Location: adminuser.php?badname=$_POST[localpart]");
  	die;
  }

  $query = "SELECT maildir FROM domains WHERE domain_id ='" .$_COOKIE[vexim][2]. "'";
  $result = $db->query($query);
  $row = $result->fetchRow();
  if (($_POST[pipe] == 1) && ($_POST[smtp] != "")) {
    $smtphomepath = $_POST[smtp];
    $pophomepath = "$row[maildir]/$_POST[localpart]";
    $_POST[type] = "piped";
  } else {
    $smtphomepath = "$row[maildir]/$_POST[localpart]/Maildir";
    $pophomepath = "$row[maildir]/$_POST[localpart]";
  }

  if (validate_password($_POST[clear], $_POST[vclear])) {
    $query = "INSERT INTO users (localpart, domain_id, crypt, clear, smtp, pop, uid, gid, realname, type, admin, avscan, spamassassin, enabled, quota)
      VALUES ('$_POST[localpart]',
        '" . $_COOKIE[vexim][2] . "',
        '" . crypt($_POST[clear],$salt) . "',
        '$_POST[clear]',
        '$smtphomepath',
        '$pophomepath',
        '$_POST[uid]',
        '$_POST[gid]',
        '$_POST[realname]',
        'local',
        '$_POST[admin]',
        '$_POST[avscan]',
        '$_POST[spamassassin]',
        '$_POST[enabled]',
	'$_POST[quota]')";
    $result = $db->query($query);
    if (!DB::isError($result)) { header ("Location: adminuser.php?added=$_POST[localpart]"); }
    else header ("Location: adminuser.php?failadded=$_POST[localpart]"); }
  else { header ("Location: adminuser.php?badpass=$_POST[localpart]"); die; }

  $query = "SELECT localpart,domain FROM users,domains WHERE domain_id='" . $_COOKIE[vexim][2] . "' AND users.type='admin'";
  $result = $db->query($query);
  $row =  $result->fetchRow();
  mail("$_POST[localpart]@" . $_COOKIE[vexim][1], "Welcome $_POST[realname]!",  $welcome_message, "From: " . $_COOKIE[vexim][0] . "@". $_COOKIE[vexim][1] . "\r\n");
?>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
