<?
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authpostmaster.php";
  include_once dirname(__FILE__) . "/config/functions.php";

  $domquery = "SELECT (count(users.user_id) < domains.max_accounts)
  		OR (domains.max_accounts=0) AS allowed FROM users,domain
		WHERE users.domain_id=domains.domain_id
		AND domains.domain_id={$_COOKIE['vexim'][2]}
		AND users.type='local'	GROUP BY domains.max_accounts";
  $domresult = $db->query($domquery);
  if (!DB::isError($domresult)) {
    $domrow = $domresult->fetchRow();
    if (!$domrow['allowed']) {
	header ("Location: adminuser.php?maxaccounts=true");
    }
  }

  # Fix the boolean values
  $query = "SELECT uid,gid,quotas FROM domains WHERE domain_id={$_COOKIE['vexim'][2]}";
  $result = $db->query($query);
  if ($result->numRows()) { $row = $result->fetchRow(); }
  if (isset($_POST['admin'])) {$_POST['admin'] = 1;} else {$_POST['admin'] = 0;}
  if (isset($_POST['on_avscan'])) {$_POST['on_avscan'] = 1;} else {$_POST['on_avscan'] = 0;}
  if (isset($_POST['on_piped'])) {$_POST['on_piped'] = 1;} else {$_POST['on_piped'] = 0;}
  if (isset($_POST['on_spamassassin'])) {$_POST['on_spamassassin'] = 1;} else {$_POST['on_spamassassin'] = 0;}
  if (isset($_POST['enabled'])) {$_POST['enabled'] = 1;} else {$_POST['enabled'] = 0;}
  if (!isset($_POST['uid'])) {$_POST['uid'] = $row['uid'];}
  if (!isset($_POST['gid'])) {$_POST['gid'] = $row['gid'];}
  if (!isset($_POST['quota'])) {$_POST['quota'] = $row['quotas'];}
  if ($row['quotas'] != "0") {
    if (($_POST['quota'] > $row['quotas']) || ($_POST['quota'] == "0")) { 
      header ("Location: adminuser.php?quotahigh={$row['quotas']}");
      die; 
    }
  }

  check_user_exists($db,$_POST['localpart'],$_COOKIE['vexim'][2],'adminuser.php');

  if ($_POST['realname'] == "") {
    header("Location: adminuser.php?blankname=yes");
    die;
  }

  if (preg_match("/['@%!\/\| ']/",$_POST['localpart']))  {
  	header("Location: adminuser.php?badname={$_POST['localpart']}");
  	die;
  }

  $query = "SELECT maildir FROM domains WHERE domain_id ={$_COOKIE['vexim'][2]}";
  $result = $db->query($query);
  if ($result->numRows()) { $row = $result->fetchRow(); }
  if (($_POST['on_piped'] == 1) && ($_POST['smtp'] != "")) {
    $smtphomepath = $_POST['smtp'];
    $pophomepath = "{$row['maildir']}/{$_POST['localpart']}";
    $_POST['type'] = "piped";
  } else {
    $smtphomepath = "{$row['maildir']}/{$_POST['localpart']}/Maildir";
    $pophomepath = "{$row['maildir']}/{$_POST['localpart']}";
    $_POST['type'] = "local";
  }

  if (validate_password($_POST['clear'], $_POST['vclear'])) {
    $query = "INSERT INTO users (localpart, username, domain_id, crypt, clear, smtp, pop, uid, gid, realname, type, admin, on_avscan, on_piped, on_spamassassin, sa_tag, sa_refuse, maxmsgsize, enabled, quota)
      VALUES ('{$_POST['localpart']}',
	'{$_POST['localpart']}@{$_COOKIE['vexim'][1]}',
	{$_COOKIE['vexim'][2]},
	'" . crypt($_POST['clear'],$salt) . "',
	'{$_POST['clear']}',
	'{$smtphomepath}',
	'{$pophomepath}',
	{$_POST['uid']},
	{$_POST['gid']},
	'{$_POST['realname']}',
	'{$_POST['type']}',
	{$_POST['admin']},
	{$_POST['on_avscan']},
	{$_POST['on_piped']},
	{$_POST['on_spamassassin']},
	" . ((isset($_POST['sa_tag'] )) ? $_POST['sa_tag']  : 0) . ",
	" .((isset($_POST['sa_refuse'] )) ? $_POST['sa_refuse']  : 0) . ",
	{$_POST['maxmsgsize']},
	{$_POST['enabled']},
	{$_POST['quota']})";
    $result = $db->query($query);
    if (!DB::isError($result)) { header ("Location: adminuser.php?added={$_POST['localpart']}");
      $query = "SELECT localpart,domain FROM users,domains WHERE domain_id={$_COOKIE['vexim'][2]}' AND users.type='admin'";
      $result = $db->query($query);
      $row =  $result->fetchRow();
      mail("{$_POST['localpart']}@{$_COOKIE['vexim'][1]}", "Welcome {$_POST['realname']}!",  $welcome_message, "From: {$_COOKIE['vexim'][0]}@{$_COOKIE['vexim'][1]}\r\n");
      die; }
    else { header ("Location: adminuser.php?failadded={$_POST['localpart']}"); die; } }
  else { header ("Location: adminuser.php?badpass={$_POST['localpart']}"); die; }
?>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
