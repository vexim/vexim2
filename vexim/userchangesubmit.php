<?
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authuser.php";
  include_once dirname(__FILE__) . "/config/functions.php";

  if (isset($_POST[on_avscan])) {$_POST[on_avscan] = 1;} else {$_POST[on_avscan] = 0;}
  if (isset($_POST[on_spamassassin])) {$_POST[on_spamassassin] = 1;} else {$_POST[on_spamassassin] = 0;}
  if (isset($_POST[on_vacation])) {$_POST[on_vacation] = 1;} else {$_POST[on_vacation] = 0;}
  if (isset($_POST[on_forward])) {$_POST[on_forward] = 1;} else {$_POST[on_forward] = 0;}

  if ($_POST[realname] != "") {
    $query = "UPDATE users SET realname='$_POST[realname]',
		WHERE localpart='" .$_COOKIE[vexim][0]. "'
    		AND domain_id='" .$_COOKIE[vexim][2]. "'";
    $result = $db->query($query);
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
      header ("Location: userchange.php?badpass");
    }
  }

# Finally 'the rest' which is handled by the profile form
    $query = "UPDATE users SET on_avscan='$_POST[on_avscan]',
		on_spamassassin='$_POST[on_spamassassin]',
		sa_refuse='$_POST[sa_refuse]',
		on_vacation='$_POST[on_vacation]',
		vacation='$_POST[vacation]',
		on_forward='$_POST[on_forward]',
		forward='$_POST[forward]'
		WHERE localpart='" .$_COOKIE[vexim][0]. "'
    		AND domain_id='" .$_COOKIE[vexim][2]. "'";
    $result = $db->query($query);
    if (!DB::isError($result)) {
      header ("Location: userchange.php?updated");
    } else {
      header ("Location: userchange.php?failed");
    }

  header ("Location: userchange.php?updated");
?>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
