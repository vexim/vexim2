<?
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authuser.php";
  include_once dirname(__FILE__) . "/config/functions.php";

  if (isset($_POST[avscan])) {$_POST[avscan] = 1;} else {$_POST[avscan] = 0;}
  if (isset($_POST[spamassassin])) {$_POST[spamassassin] = 1;} else {$_POST[spamassassin] = 0;}

  if ($_POST[realname] != "") {
    $query = "UPDATE users SET realname='$_POST[realname]',
		avscan='$_POST[avscan]',
		spamassassin='$_POST[spamassassin]',
		sa_tag='$_POST[sa_tag]',
		sa_refuse='$_POST[sa_refuse]'
		WHERE localpart='" .$_COOKIE[vexim][0]. "'
    		AND domain_id='" .$_COOKIE[vexim][2]. "'";
    $result = $db->query($query);
  } else {
    header ("Location: userchange.php?failrealname");
    die;
  }

# Update the password, if the password was given
  if (validate_password($_POST[clear], $_POST[vclear])) {
    $cryptedpass = crypt($_POST[clear]);
    $query = "UPDATE users SET crypt='$cryptedpass',
		clear='$_POST[clear]'
		WHERE localpart='" .$_COOKIE[vexim][0]. "'
		AND domain_id='" .$_COOKIE[vexim][2]. "'";
    $result = $db->query($query);
    if (!DB::isError($result)) {
      setcookie ("vexim[3]", $cryptedpass, time()+86400);
      header ("Location: userchange.php?updated");
      die;
    } else {
      header ("Location: userchange.php?failed");
    }
  } else {
    header ("Location: userchange.php?badpass");
  }
?>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
