<?
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authsite.php";
  include_once dirname(__FILE__) . "/config/functions.php";

  if (isset($_POST[avscan])) {$_POST[avscan] = 1;} else {$_POST[avscan] = 0;}
  if (isset($_POST[spamassassin])) {$_POST[spamassassin] = 1;} else {$_POST[spamassassin] = 0;}
  if (isset($_POST[enabled])) {$_POST[enabled] = 1;} else {$_POST[enabled] = 0;}
  if (isset($_POST[pipe])) {$_POST[pipe] = 1;} else {$_POST[pipe] = 0;}
  if ($_POST[max_accounts] == '') {$_POST[max_accounts] = 'NULL';}
  if (isset($_POST[clear])) {
    if (validate_password($_POST[clear], $_POST[vclear])) {
      $query = "UPDATE users SET crypt='".crypt($_POST[clear])."',
   		clear='$_POST[clear]'
		WHERE localpart='$_POST[localpart]' AND
		domain_id='$_POST[domain_id]'";
      $result = $db->query($query);
      if (!DB::isError($result)) {
        header ("Location: site.php?updated=$_POST[domain]");
	die;
      } else {
        header ("Location: site.php?failupdated=$_POST[domain]");
	die;
      }
    } else {
      header ("Location: site.php?badpass=$_POST[domain]");
      die;
    }
  } 

  if (isset($_POST[uid])) {
    $query = "UPDATE domains SET uid='$_POST[uid]',
    		gid='$_POST[gid]',
		avscan='$_POST[avscan]',
		maxmsgsize='$_POST[maxmsgsize]',
		pipe='$_POST[pipe]',
		max_accounts=$_POST[max_accounts],
		quotas='$_POST[quotas]',
		sa_tag='$_POST[sa_tag]',
		sa_refuse='$_POST[sa_refuse]',
		spamassassin='$_POST[spamassassin]',
		enabled='$_POST[enabled]' WHERE domain='$_POST[domain]'";
    $result = $db->query($query);
    if (!DB::isError($result)) {
      header ("Location: site.php?updated=$_POST[domain]");
      die; 
    } else {
      header ("Location: site.php?failupdated=$_POST[domain]");
      die;
    }
  }

  if (isset($_POST[sadisable])) {
    $query = "UPDATE users SET on_spamassassin='0' WHERE domain_id=$_POST[domain_id]";
    $result = $db->query($query);
    if (DB::isError($result)) { $result->getMessage(); }
    header ("Location: site.php?updated=$_POST[domain]");
    die;
  }

  if (isset($_POST[avdisable])) {
    $query = "UPDATE users SET on_avscan='0' WHERE domain_id=$_POST[domain_id]";
    $result = $db->query($query);
    if (DB::isError($result)) { $result->getMessage(); }
    header ("Location: site.php?updated=$_POST[domain]");
    die;
  }

# Just-in-case catchall
header ("Location: site.php?failupdated=$_POST[domain]");
?>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
