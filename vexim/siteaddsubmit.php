<?
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authsite.php";
  include_once dirname(__FILE__) . "/config/functions.php";
  include_once dirname(__FILE__) . "/config/httpheaders.php";

  if (isset($_POST['avscan'])) {$_POST['avscan'] = 1;} else {$_POST['avscan'] = 0;}
  if (isset($_POST['spamassassin'])) {$_POST['spamassassin'] = 1;} else {$_POST['spamassassin'] = 0;}
  if (isset($_POST['enabled'])) {$_POST['enabled'] = 1;} else {$_POST['enabled'] = 0;}
  if (isset($_POST['pipe'])) {$_POST['pipe'] = 1;} else {$_POST['pipe'] = 0;}
  if ($_POST['type'] == ("relay"||"alias")) {$_POST['clear'] = $_POST['vclear'] = "BLANK";}
  if ($_POST['max_accounts'] == '') {$_POST['max_accounts'] = '0';}

  $smtphomepath = $mailroot . $_POST['domain'] . "/" . $_POST['localpart'] . "/Maildir";
  $pophomepath = $mailroot . $_POST['domain'] . "/" . $_POST['localpart'];
//Gah. Transactions!! -- GCBirzan
  if ((validate_password($_POST['clear'], $_POST['vclear'])) && ($_POST['type'] != "alias")) {
    $query = "INSERT INTO domains (domain, spamassassin, sa_tag, sa_refuse, avscan, max_accounts, quotas, maildir, pipe, enabled," .
    ((isset($_POST['uid'])) ? "uid," : "") . ((isset($_POST['gid'])) ? "gid," : "") ." type, maxmsgsize)
    VALUES ('" . $_POST['domain'] . "',
    {$_POST['spamassassin']},
    " . ((isset($_POST['sa_tag'])) ? $_POST['sa_tag']  : 0) . ",
    " . ((isset($_POST['sa_refuse'])) ? $_POST['sa_refuse']  : 0) . ",
    {$_POST['avscan']},
    {$_POST['max_accounts']},
    " . ((isset($_POST['quotas'])) ? $_POST['quotas']  : 0) . ",
    '{$_POST['maildir']}{$_POST['domain']}',
    {$_POST['pipe']},
    {$_POST['enabled']},
    " . ((isset($_POST['uid'])) ? $_POST['uid'] . "," : "" ) . "
    " . ((isset($_POST['gid'])) ? $_POST['gid'] . "," : "" ) . "
    '{$_POST['type']}',
    " . ((isset($_POST['maxmsgsize'] )) ? $_POST['maxmsgsize']  : 0) . ")";
    $domresult = $db->query($query);
    if (!DB::isError($domresult)) {
      if ($_POST['type'] == "local") {
	$query = "INSERT INTO users (domain_id, localpart, username, clear, crypt, uid, gid, smtp, pop, realname, type, admin)
		  SELECT domain_id, '" . $_POST['localpart'] . "',
		  '{$_POST['localpart']}@{$_POST['domain']}',
		  '{$_POST['clear']}',
		  '". crypt($_POST['clear'],$salt) . "',
		  {$_POST['uid']}, {$_POST['gid']},
		  '{$smtphomepath}', '{$pophomepath}',
		  'Domain Admin', 'local', 1 FROM domains
		  WHERE domains.domain = '{$_POST['domain']}'";
// Is using indexes worth setting the domain_id by hand? -- GCBirzan
	$usrresult = $db->query($query);
	if (DB::isError($usrresult)) {
	  header ("Location: site.php?failaddedusrerr={$_POST['domain']}");
	} else {
	  header ("Location: site.php?added={$_POST['domain']}&type={$_POST['type']}");
	}
      } else {
      	header ("Location: site.php?added={$_POST['domain']}&type={$_POST['type']}");
      }
    } else {
      header ("Location: site.php?failaddeddomerr={$_POST['domain']}");
    }
  } else if ($_POST['type'] == "alias") {
    $idquery = "SELECT domain_id FROM domains WHERE domain = '{$_POST['aliasdest']}'";
    $idresult = $db->query($idquery);
    if (!DB::isError($result)) {
      $idrow = $idresult->fetchRow();
    } else {
      header ("Location: site.php?baddestdom={$_POST['domain']}");
      die;
    }
    $query = "INSERT INTO domainalias (domain_id, alias) values ('{$idrow['domain_id']}', '{$_POST['domain']}')";
    $result = $db->query($query);
    if (DB::isError($result)) {
      header ("Location: site.php?failaddeddomerr={$_POST['domain']}");
    } else {
      header ("Location: site.php?added={$_POST['domain']}&type={$_POST['type']}");
    }
  } else {
    header ("Location: site.php?failaddedpassmismatch={$_POST['domain']}");
  }

?>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
