<?
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authpostmaster.php";
  include_once dirname(__FILE__) . "/config/functions.php";
  include_once dirname(__FILE__) . "/config/httpheaders.php";

# Fix the boolean values
if (isset($_POST['admin'])) {$_POST['admin'] = 1;} else {$_POST['admin'] = 0;}
if (isset($_POST['on_avscan'])) {$_POST['on_avscan'] = 1;} else {$_POST['on_avscan'] = 0;}
if (isset($_POST['on_spamassassin'])) {$_POST['on_spamassassin'] = 1;} else {$_POST['on_spamassassin'] = 0;}
if (isset($_POST['enabled'])) {$_POST['enabled'] = 1;} else {$_POST['enabled'] = 0;}

# Update the password, if the password was given
  if (validate_password($_POST['password'], $_POST['vpassword'])) {
    $cryptedpassword = crypt($_POST['password']);
    $query = "UPDATE users SET crypt='{$cryptedpassword}', clear='{$_POST['crypt']}'
	      WHERE user_id={$_POST['user_id']}";
    $result = $db->query($query);
    if (!DB::isError($result)) {
      if ($_POST['localpart'] == $_SESSION['localpart']) { setcookie ("vexim[3]", $cryptedpassword, time()+86400); }
    } else {
      header ("Location: adminalias.php?failed");
    }
  } else {
    header ("Location: adminalias.php?badpass");
  }

  $aliasto = preg_replace("/[', ']+/", ", ", $_POST['target']);
  $query = "UPDATE users SET localpart='{$_POST['localpart']}',
    username='{$_POST['localpart']}@{$_SESSION['domain']}',
    smtp='$aliasto',
    pop='$aliasto',
    realname='{$_POST['realname']}',
    admin={$_POST['admin']},
    on_avscan={$_POST['on_avscan']},
    on_spamassassin={$_POST['on_spamassassin']},
    enabled={$_POST['enabled']}
    WHERE user_id={$_POST['user_id']}";
  $result = $db->query($query);
  if (!DB::isError($result)) { header ("Location: adminalias.php?updated={$_POST['localpart']}"); }
  else { print $query;header ("Location: adminalias.php?failupdated={$_POST['localpart']}"); }
?>
