<?
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authpostmaster.php";
  include_once dirname(__FILE__) . "/config/functions.php";

  # Fix the boolean values
  $query = "SELECT uid,gid,quotas FROM domains WHERE domain_id ='" .$_COOKIE[vexim][2]. "'";
  $result = $db->query($query);
  $row = $result->fetchRow();
  if (isset($_POST[admin])) {$_POST[admin] = 1;} else {$_POST[admin] = 0;}
  if (isset($_POST[avscan])) {$_POST[avscan] = 1;} else {$_POST[avscan] = 0;}
  if (isset($_POST[spamassassin])) {$_POST[spamassassin] = 1;} else {$_POST[spamassassin] = 0;}
  if (isset($_POST[enabled])) {$_POST[enabled] = 1;} else {$_POST[enabled] = 0;}
  if (!isset($_POST[uid])) {$_POST[uid] = $row[uid];}
  if (!isset($_POST[gid])) {$_POST[gid] = $row[gid];}
  if (!isset($_POST[quota])) {$_POST[quota] = $row[quotas];}
  if (!isset($_POST[sa_tag])) {$_POST[sa_tag] = "0";}
  if (!isset($_POST[sa_refuse])) {$_POST[sa_refuse] = "0";}
  if ($row[quotas] != "0") {
    if (($_POST[quota] > $row[quotas]) || ($_POST[quota] == "0")) {
      header ("Location: adminuser.php?quotahigh=$row[quotas]"); die;
    }
  }

  # Big code block, to make sure we're not de-admining the last admin
  $query = "SELECT COUNT(admin) AS count FROM users
                        WHERE admin='1'
                        AND domain_id='" . $_COOKIE[vexim][2] . "'";
  $result = $db->query($query);
  $row = $result->fetchRow();
  if ($row[count] == "1") {
    $query = "SELECT admin FROM users WHERE localpart='$_POST[localpart]' AND domain_id='" . $_COOKIE[vexim][2] . "'";
    $result = $db->query($query);
    $row = $result->fetchRow();
    if (($row[admin] == "1") && ($_POST[admin] == "0")) {
      header ("Location: adminuser.php?nodel=$_POST[localpart]");
      die;
    }
  }

  # Set the apporpriate maildirs
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

  # Update the password, if the password was given
  if (validate_password($_POST[clear], $_POST[vclear])) {
    $cryptedpassword = crypt($_POST[clear]);
    $query = "UPDATE users SET crypt='$cryptedpassword',
		clear='$_POST[clear]'
		WHERE localpart='$_POST[localpart]' AND domain_id='" .$_COOKIE[vexim][2]. "'";
    $result = $db->query($query);
    if ((!DB::isError($result)) && ($_POST[localpart] == $_COOKIE[vexim][0])) { setcookie ("vexim[3]", $cryptedpassword, time()+86400); }
    else { header ("Location: adminuser.php?failupdated=$_POST[localpart]"); }
  } else if ($_POST[clear] != $_POST[vclear]) {
    header ("Location: adminuser.php?badpass=$_POST[localpart]");
  }

  $query = "UPDATE users SET uid='$_POST[uid]',
    gid='$_POST[gid]',
    realname='$_POST[realname]',
    admin='$_POST[admin]',
    avscan='$_POST[avscan]',
    spamassassin='$_POST[spamassassin]',
    sa_tag='$_POST[sa_tag]',
    sa_refuse='$_POST[sa_refuse]',
    enabled='$_POST[enabled]',
    quota='$_POST[quota]'
    WHERE localpart='$_POST[localpart]' AND domain_id='".$_COOKIE[vexim][2]."'";
  $result = $db->query($query);
  if (!DB::isError($result)) { header ("Location: adminuser.php?updated=$_POST[localpart]"); }
  else { header ("Location: adminuser.php?failupdated=$_POST[localpart]"); }
?>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
