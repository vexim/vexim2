<?
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authpostmaster.php";
  include_once dirname(__FILE__) . "/config/functions.php";

  # Fix the boolean values
  if (isset($_POST[admin])) {$_POST[admin] = 1;} else {$_POST[admin] = 0;}
  if (isset($_POST[avscan])) {$_POST[avscan] = 1;} else {$_POST[avscan] = 0;}
  if (isset($_POST[spamassassin])) {$_POST[spamassassin] = 1;} else {$_POST[spamassassin] = 0;}
  if (isset($_POST[enabled])) {$_POST[enabled] = 1;} else {$_POST[enabled] = 0;}

  check_user_exists($db,$_POST[localpart],$_COOKIE[vexim][2],'adminalias.php');

  $aliasto = preg_replace("/[, ]+/", ", ", $_POST[smtp]);
  if (alias_validate_password($_POST[clear], $_POST[vclear])) {
    $query = "INSERT INTO users
      (localpart, username, domain_id, crypt, clear, smtp, pop, uid, gid, realname, type, admin, avscan,
	spamassassin, enabled)
      SELECT '$_POST[localpart]',
	'" . $_POST[localpart] . "@". $_COOKIE[vexim][1] . "',
	'" . $_COOKIE[vexim][2] . "',
	'" . crypt($_POST[clear],$salt) . "',
	'$_POST[clear]',
	'$aliasto',
	'$aliasto',
	uid,
	gid,
	'$_POST[realname]',
	'alias',
	'$_POST[admin]',
	'$_POST[avscan]',
	'$_POST[spamassassin]',
	'$_POST[enabled]' from domains WHERE domains.domain_id='" . $_COOKIE[vexim][2] . "'";
    $result = $db->query($query);
    if (!DB::isError($result)) { header ("Location: adminalias.php?added=$_POST[localpart]"); }
    else { header ("Location: adminalias.php?failadded=$_POST[localpart]"); }
  } else { header ("Location: adminalias.php?badaliaspass=$_POST[localpart]"); } 
?>

<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
