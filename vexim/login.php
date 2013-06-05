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

	# construct the correct sql statement based on who the user is
  if ($_POST['localpart'] == 'siteadmin') {
		$query = "SELECT crypt,localpart,user_id,domain,domains.domain_id,users.admin,users.type,domains.enabled AS domainenabled FROM users,domains
      WHERE localpart='siteadmin'
      AND domain='admin'
      AND username='siteadmin'
      AND users.domain_id = domains.domain_id";
  } else if ($AllowUserLogin) {
		$query = "SELECT crypt,localpart,user_id,domain,domains.domain_id,users.admin,users.type,domains.enabled AS domainenabled FROM users,domains
      WHERE localpart='{$_POST['localpart']}'
      AND users.domain_id = domains.domain_id
      AND domains.domain='{$_POST['domain']}';";
  } else {
		$query = "SELECT crypt,localpart,user_id,domain,domains.domain_id,users.admin,users.type,domains.enabled AS domainenabled FROM users,domains
      WHERE localpart='{$_POST['localpart']}'
      AND users.domain_id = domains.domain_id
      AND domains.domain='{$_POST['domain']}'
      AND admin=1;";
  }
  $result = $db->query($query);
  if (DB::isError($result)) {
    die ($result->getMessage());
  }
	if ($result->numRows()!=1 ) {
		header ('Location: index.php?login=failed'); 
		die(); 
	}
  $row = $result->fetchRow();
	if (DB::isError($result)) {
		die ($result->getMessage());
	}
	
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
		header ('Location: index.php?login=failed');
		die();
	}

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
