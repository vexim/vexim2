<?php
  include_once dirname(__FILE__) . '/config/variables.php';
  include_once dirname(__FILE__) . '/config/functions.php';
  include_once dirname(__FILE__) . '/config/httpheaders.php';

  # first check if we have sufficient post variables to achieve a successful login... if not the login fails immediately
  if (!isset($_POST['crypt']) || $_POST['crypt']===''
      || !isset($_POST['username']) || $_POST['username']===''
  ){
    header ('Location: index.php?login=failed');
    die;
  }

  # construct the correct sql statement based on who the user is
  if ($_POST['username'] === 'siteadmin') {
    $query = "SELECT crypt,localpart,username,user_id,domain,domains.domain_id,users.admin,users.type,domains.enabled AS domainenabled FROM users,domains
    WHERE localpart='siteadmin'
    AND domain='admin'
    AND username='siteadmin'
    AND users.domain_id = domains.domain_id";
  } else if ($AllowUserLogin) {
    $query = "SELECT crypt,username,user_id,domain,domains.domain_id,users.admin,users.type,domains.enabled AS domainenabled, users.enabled AS userenabled
    FROM users,domains
    WHERE username=:username
    AND users.domain_id = domains.domain_id";
  } else {
    $query = "SELECT crypt,username,user_id,domain,domains.domain_id,users.admin,users.type,domains.enabled AS domainenabled FROM users,domains
    WHERE username=:username
    AND users.domain_id = domains.domain_id
    AND admin=1;";
  }
  $sth = $dbh->prepare($query);
  $success = $sth->execute(array(':username'=>$_POST['username']));
  if(!$success) {
    print_r($sth->errorInfo());
    die();
  }
  if ($sth->rowCount()!=1) {
    header ('Location: index.php?login=failed');
    die();
  }

  $row = $sth->fetch();
  $cryptedpass = crypt_password($_POST['crypt'], $row['crypt']);

//  Some debugging prints. They help when you don't know why auth is failing.
/*  
  print $query. "<br>\n";;
  print $row['username']. "<br>\n";
  print $_POST['username'] . "<br>\n";
  print "Posted crypt: " .$_POST['crypt'] . "<br>\n";
  print $row['crypt'] . "<br>\n";
  print $cryptscheme . "<br>\n";
  print $cryptedpass . "<br>\n";
*/  

  # if they have the wrong password bail out
  if ($cryptedpass !== $row['crypt']) {
    header ('Location: index.php?login=failed');
    die();
  }
  if (($row['userenabled'] === '0')) {
    header ('Location: index.php?userdisabled');
    die();
  }
  if (($row['domainenabled'] === '0')) {
    header ('Location: index.php?domaindisabled');
    die();
  }

  # populate session variables from what was retrieved from the database (NOT what they posted)
  $_SESSION['username'] = $row['username'];
  $_SESSION['domain_id'] = $row['domain_id'];
  $_SESSION['domain'] = $row['domain'];
  $_SESSION['crypt'] = $row['crypt'];
  $_SESSION['user_id'] = $row['user_id'];

  # redirect the user to the correct starting page
  if (($row['admin'] == '1') && ($row['type'] == 'site')) {
    if($_POST['crypt']=="CHANGE") {
      header ('Location: sitepassword.php');
      die();
    }
    header ('Location: site.php');
    die();
  }

  if ($row['admin'] == '1') {
    header ('Location: admin.php');
    die();
  }

  # must be a user, send them to edit their own details
  header ('Location: userchange.php');

?>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
