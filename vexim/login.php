<?php
  include_once dirname(__FILE__) . '/config/variables.php';
  include_once dirname(__FILE__) . '/config/functions.php';
  include_once dirname(__FILE__) . '/config/httpheaders.php';

	# first check if we have sufficient post variables to achieve a successful login... if not the login fails immediately
	if (!isset($_POST['crypt']) || $_POST['crypt']==''
		|| !isset($_POST['localpart']) || $_POST['localpart']==''
		|| !isset($_POST['domain'])
	){
    header ('Location: index.php?login=failed');
    die;
  }

    $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'];
    # when using a PROXY instance you'll need the following
    #$apache_headers = apache_request_headers();
    #if (array_key_exists( 'X-Forwarded-For', $apache_headers ) && filter_var( $apache_headers['X-Forwarded-For'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
    #    $_SESSION['user_ip'] = $apache_headers['X-Forwarded-For']; 
    #} else {
    #    $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'];
    #}
    
    $allowed = check_ip_logins($dbh, $_SESSION['user_ip']);
    if ($allowed == FALSE) {
        header ('Location: index.php?login=failed');
    die;
    }
    
	# construct the correct sql statement based on who the user is
  if ($_POST['localpart'] == 'siteadmin') {
		$query = "SELECT crypt,localpart,user_id,domain,domains.domain_id,users.admin,users.type,domains.enabled AS domainenabled FROM users,domains
      WHERE localpart='siteadmin'
      AND domain='admin'
      AND username='siteadmin'
      AND users.domain_id = domains.domain_id";
  } else if ($AllowUserLogin) {
		$query = "SELECT crypt,localpart,user_id,domain,domains.domain_id,users.admin,users.type,domains.enabled AS domainenabled FROM users,domains
      WHERE localpart=:localpart
      AND users.domain_id = domains.domain_id
      AND domains.domain=:domain;";
  } else {
		$query = "SELECT crypt,localpart,user_id,domain,domains.domain_id,users.admin,users.type,domains.enabled AS domainenabled FROM users,domains
      WHERE localpart=:localpart
      AND users.domain_id = domains.domain_id
      AND domains.domain=:domain
      AND admin=1;";
  }
  $sth = $dbh->prepare($query);
  $success = $sth->execute(array(':localpart'=>$_POST['localpart'], ':domain'=>$_POST['domain']));
  if(!$success) {
    print_r($sth->errorInfo());
    die();
  }

  if ($sth->rowCount()!=1) {
    insert_failed_login($dbh, $_SESSION['user_ip']);
    header ('Location: index.php?login=failed');
    die();
  }

  $row = $sth->fetch();
	
  $cryptedpass = crypt_password($_POST['crypt'], $row['crypt']);

//  Some debugging prints. They help when you don't know why auth is failing.
  /*
  print $query. "<br>\n";;
  print $row['localpart']. "<br>\n";
  print $_POST['localpart'] . "<br>\n";
  print $_POST['domain'] . "<br>\n";
  print "Posted crypt: " .$_POST['crypt'] . "<br>\n";
  print $row['crypt'] . "<br>\n";
  print $cryptscheme . "<br>\n";
  print $cryptedpass . "<br>\n";
  */

	# if they have the wrong password bail out
	if ($cryptedpass != $row['crypt']) {
        insert_failed_login($dbh, $_SESSION['user_ip']);
		header ('Location: index.php?login=failed');
		die();
	}

    cleanup_logins($dbh, $_SESSION['user_ip']);

	# populate session variables from what was retrieved from the database (NOT what they posted)
    $_SESSION['localpart'] = $row['localpart'];
    $_SESSION['domain'] = $row['domain'];
    $_SESSION['domain_id'] = $row['domain_id'];
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
	if (($row['domainenabled'] == '0')) {
		header ('Location: index.php?domaindisabled');
		die();
}
	
	# must be a user, send them to edit their own details
	header ('Location: userchange.php');
	
?>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
