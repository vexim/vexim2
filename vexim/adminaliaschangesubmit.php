<?
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authpostmaster.php";
  include_once dirname(__FILE__) . "/config/functions.php";

# Fix the boolean values
if (isset($_POST[admin])) {$_POST[admin] = 1;} else {$_POST[admin] = 0;}
if (isset($_POST[avscan])) {$_POST[avscan] = 1;} else {$_POST[avscan] = 0;}
if (isset($_POST[spamassassin])) {$_POST[spamassassin] = 1;} else {$_POST[spamassassin] = 0;}
if (isset($_POST[enabled])) {$_POST[enabled] = 1;} else {$_POST[enabled] = 0;}

# Update the password, if the password was given
  if (validate_password($_POST[password], $_POST[vpassword])) {
    $cryptedpassword = crypt($_POST[password]);
    $query = "UPDATE users SET crypt='$cryptedpassword',
      clear='$_POST[crypt]' WHERE localpart='$_POST[localpart]'
      AND domain_id='" .  $_COOKIE[vexim][2] . "'";
    $result = $db->query($query);
    if (!DB::isError($result)) {
      if ($_POST[localpart] == $_COOKIE[vexim][0]) { setcookie ("vexim[3]", $cryptedpassword, time()+86400); }
    } else {
      header ("Location: adminalias.php?failed");
    }
  } else {
    header ("Location: adminalias.php?badpass");
  }

  $aliasto = preg_replace("/[, ]+/", ", ", $_POST[target]);
  $query = "UPDATE users SET localpart='$_POST[localpart]',
    smtp='$aliasto',
    pop='$aliasto',
    realname='$_POST[realname]',
    admin='$_POST[admin]',
    avscan='$_POST[avscan]',
    spamassassin='$_POST[spamassassin]',
    enabled='$_POST[enabled]'
    WHERE localpart='$_POST[origlocalpart]' AND domain_id='" . $_COOKIE[vexim][2] . "'";
  $result = $db->query($query);
  if (!DB::isError($result)) { header ("Location: adminalias.php?updated=$_POST[localpart]"); }
  else { header ("Location: adminalias.php?failupdated=$_POST[localpart]"); }
?>
