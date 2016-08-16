<?php
  include_once dirname(__FILE__) . '/config/variables.php';
  include_once dirname(__FILE__) . '/config/authpostmaster.php';
  include_once dirname(__FILE__) . '/config/functions.php';
  include_once dirname(__FILE__) . '/config/httpheaders.php';

  # confirm that the postmaster is updating an alias they are permitted to change before going further  
  $query = "SELECT localpart,realname,smtp,on_spamassassin,sa_tag,sa_refuse,spam_drop,
    admin,enabled FROM users 
	WHERE user_id=:user_id AND domain_id=:domain_id AND type='alias'";
  $sth = $dbh->prepare($query);
  $sth->execute(array(':user_id'=>$_POST['user_id'], ':domain_id'=>$_SESSION['domain_id']));
  if (!$sth->rowCount()) {
	  header ("Location: adminalias.php?failupdated={$_POST['localpart']}");
	  die();  
  }
  
  # Fix the boolean values
  if (isset($_POST['admin'])) {
    $_POST['admin'] = 1;
  } else {
    $_POST['admin'] = 0;
  }
  if (isset($_POST['enabled'])) {
    $_POST['enabled'] = 1;
  } else {
    $_POST['enabled'] = 0;
  }
  $query = "SELECT avscan,spamassassin from domains
    WHERE domain_id=:domain_id";
  $sth = $dbh->prepare($query);
  $sth->execute(array(':domain_id'=>$_SESSION['domain_id']));
  $row = $sth->fetch();
  if ((isset($_POST['on_avscan'])) && ($row['avscan'] == 1)) {
    $_POST['on_avscan'] = 1;
  } else {
    $_POST['on_avscan'] = 0;
  }
  if ((isset($_POST['on_spamassassin'])) && ($row['spamassassin'] == 1)) {
    $_POST['on_spamassassin'] = 1;
  } else {
    $_POST['on_spamassassin'] = 0;
  }

  # Update the password, if the password was given
  if(isset($_POST['password']) && $_POST['password']!=='' ){
	if (validate_password($_POST['password'], $_POST['vpassword'])) {
          if (!password_strengthcheck($_POST['password'])) {  
            header ("Location: adminalias.php?weakpass={$_POST['localpart']}");
            die;
          }
		$cryptedpassword = crypt_password($_POST['password']);
		$query = "UPDATE users SET crypt=:crypt WHERE user_id=:user_id AND domain_id=:domain_id AND type='alias'";
          $sth = $dbh->prepare($query);
          $success = $sth->execute(array(':crypt'=>$cryptedpassword, ':user_id'=>$_POST['user_id'], ':domain_id'=>$_SESSION['domain_id']));
        
		if ($success) {
			if ($_POST['localpart'] == $_SESSION['localpart']) {
				$_SESSION['crypt'] = $cryptedpassword;
			}
		} else {
		  header ('Location: adminalias.php?failedupdated=' . $_POST['localpart']);
		  die();
		}
	} else {
		header ('Location: adminalias.php?badaliaspass');
		die();
	}
  }

  # update the actual alias in the users table
  $forwardto=explode(",",$_POST['target']);
  for($i=0; $i<count($forwardto); $i++){
    $forwardto[$i]=trim($forwardto[$i]);
    if(!filter_var($forwardto[$i], FILTER_VALIDATE_EMAIL)) {
      header ("Location: adminalias.php?invalidforward=".htmlentities($forwardto[$i]));
      die;
    }
  }
  $aliasto = implode(",",$forwardto);
  $query = "UPDATE users SET localpart=:localpart,
    username=:username, smtp=:smtp, pop=:pop,
    realname=:realname, admin=:admin, on_avscan=:on_avscan,
    on_spamassassin=:on_spamassassin, sa_tag=:sa_tag, sa_refuse=:sa_refuse,
    spam_drop=:spam_drop,enabled=:enabled
    WHERE user_id=:user_id
	AND domain_id=:domain_id AND type='alias'";
  $sth = $dbh->prepare($query);
  $success = $sth->execute(array(
    ':localpart'=>$_POST['localpart'],
    ':username'=>$_POST['localpart'].'@'.$_SESSION['domain'],
    ':smtp'=>$aliasto,
    ':pop'=>$aliasto,
    ':realname'=>$_POST['realname'],
    ':admin'=>$_POST['admin'],
    ':on_avscan'=>$_POST['on_avscan'],
    ':on_spamassassin'=>$_POST['on_spamassassin'],
    ':sa_tag'=>(isset($_POST['sa_tag']) ? $_POST['sa_tag'] : $sa_tag),
    ':sa_refuse'=>(isset($_POST['sa_refuse']) ? $_POST['sa_refuse'] : $sa_refuse),
    ':spam_drop'=>(isset($_POST['spam_drop']) ? $_POST['spam_drop'] : 0),
    ':enabled'=>$_POST['enabled'],
    ':user_id'=>$_POST['user_id'],
    ':domain_id'=>$_SESSION['domain_id']
    ));
  if ($success) {
    header ("Location: adminalias.php?updated={$_POST['localpart']}");
  } else {
    header ("Location: adminalias.php?failupdated={$_POST['localpart']}");
  }
?>
