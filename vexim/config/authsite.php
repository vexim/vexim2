<?php
	include_once dirname(__FILE__) . '/variables.php';
	include_once dirname(__FILE__) . '/functions.php';
	include_once dirname(__FILE__) . '/httpheaders.php';
	
	# some session debugging code
	#print_r($_SESSION);
	
	# confirm we have the necessary session variables
	if (!isset($_SESSION['user_id'])
		|| !isset($_SESSION['domain_id'])
		|| !isset($_SESSION['crypt'])
		)		
	{
		header ("Location: index.php?login=failed"); 
		die(); 
	}
	
	if ($_SESSION['username'] != "siteadmin"){ 
		header ("Location: index.php?login=failed"); 
		die(); 
	}
	
	# Match the session details to the siteadmin account
	$query = "SELECT crypt,domain FROM users,domains 
		WHERE users.user_id=:user_id AND users.domain_id=:domain_id
		AND localpart='siteadmin' AND domain='admin' AND users.domain_id=domains.domain_id;";
    $sth = $dbh->prepare($query);
    $success = $sth->execute(array(':user_id'=>$_SESSION['user_id'], ':domain_id'=>$_SESSION['domain_id']));
    if(!$success || ($sth->rowCount()!=1)) {
		header ("Location: index.php?login=failed"); 
		die(); 
    }

    $row = $sth->fetch();
	
	# confirm the crypted password in the session matches the crypted password in the database for the siteadmin
	if ($row['crypt'] !== $_SESSION['crypt']) {
		header ("Location: index.php?login=failed"); 
		die(); 
	}
	
?>
