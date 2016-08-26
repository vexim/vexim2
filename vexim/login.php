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

  if($domainguess === 1 && $_POST['username']!=='siteadmin') $_POST['username'].='@'.preg_replace ("/^(".$domainguess_lefttrim.")\./", "", $_SERVER["SERVER_NAME"]);

  # sql statement based on username
  $query = "SELECT users.crypt,users.username,users.user_id,users.localpart,domains.domain,domains.domain_id,users.admin,users.type,
  domains.enabled AS domainenabled, users.enabled AS userenabled
  FROM users,domains
  WHERE username=:username
  AND users.domain_id = domains.domain_id";
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
  if($row['username']!=='siteadmin') {
    if (($row['userenabled'] === '0')) {
      header ('Location: index.php?userdisabled');
      die();
    }
    if (($row['domainenabled'] === '0')) {
      header ('Location: index.php?domaindisabled');
      die();
    }
  }

  # populate session variables from what was retrieved from the database (NOT what they posted)
  $_SESSION['username'] = $row['username'];
  $_SESSION['localpart'] = $row['localpart'];
  $_SESSION['domain_id'] = $row['domain_id'];
  $_SESSION['domain'] = $row['domain'];
  $_SESSION['crypt'] = $row['crypt'];
  $_SESSION['user_id'] = $row['user_id'];

  # redirect the user to the correct starting page
  if (($row['admin'] == '1') && ($row['type'] == 'site')) {
    header ('Location: site.php');
    die();
  }

  if ($row['admin'] == '1') {
    header ('Location: admin.php');
    die();
  }

  # must be a user, send them to edit their own details, if User-Login is permitted
  if($AllowUserLogin===1) {
    header ('Location: userchange.php');
    die();
  }
  header ('Location: index.php?login=disabled');
?>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
