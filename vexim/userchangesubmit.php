<?
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authuser.php";
  include_once dirname(__FILE__) . "/config/functions.php";
  include_once dirname(__FILE__) . "/config/httpheaders.php";
  if (isset($_POST['on_avscan'])) {$_POST['on_avscan'] = 1;} else {$_POST['on_avscan'] = 0;}
  if (isset($_POST['on_spamassassin'])) {$_POST['on_spamassassin'] = 1;} else {$_POST['on_spamassassin'] = 0;}
  if (isset($_POST['on_vacation'])) {$_POST['on_vacation'] = 1;} else {$_POST['on_vacation'] = 0;}
  if (isset($_POST['on_forward'])) {$_POST['on_forward'] = 1;} else {$_POST['on_forward'] = 0;}

  if ($_POST['realname'] != "") {
    $query = "UPDATE users SET realname='{$_POST['realname']}'
		WHERE user_id={$_SESSION['user_id']}";
    $result = $db->query($query);
  }

# Update the password, if the password was given
  if (validate_password($_POST['clear'], $_POST['vclear'])) {
    $cryptedpassword = crypt($_POST['clear']);
    $query = "UPDATE users SET crypt='$cryptedpassword',
		clear='{$_POST['clear']}'
		WHERE user_id={$_SESSION['user_id']}";
    $result = $db->query($query);
    if (!DB::isError($result)) {
      $_SESSION['crypt'] = $cryptedpassword;
      header ("Location: userchange.php?updated");
      die;
    } else {
      header ("Location: userchange.php?badpass");
      die;
    }
    header ("Location: userchange.php?badpass");
    die;
  }

# Finally 'the rest' which is handled by the profile form
    $query = "UPDATE users SET on_avscan='{$_POST['on_avscan']}',
		on_spamassassin={$_POST['on_spamassassin']},
		sa_tag={$_POST['sa_tag']},
		sa_refuse={$_POST['sa_refuse']},
		on_vacation={$_POST['on_vacation']},
		vacation='{$_POST['vacation']}',
		on_forward={$_POST['on_forward']},
		forward='{$_POST['forward']}',
		maxmsgsize={$_POST['maxmsgsize']}
		WHERE user_id={$_SESSION['user_id']}";
    $result = $db->query($query);
    if (!DB::isError($result)) {
      header ("Location: userchange.php?updated");
      die;
    } else {
      header ("Location: userchange.php?failed");
      die;
    }

  header ("Location: userchange.php?updated");
?>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
