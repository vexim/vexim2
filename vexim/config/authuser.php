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
		header ("Location: index.php?login=failed&1");
		die();
	}

	# Match the session details to an admin account the domain of the postmaster
	$query = "SELECT `crypt` FROM users WHERE `user_id`=:user_id AND `domain_id`=:domain_id";
    $sth = $dbh->prepare($query);
    $success = $sth->execute(array(':user_id'=>$_SESSION['user_id'], ':domain_id'=>$_SESSION['domain_id']));
    if(!$success || !$sth->rowCount()) {
		header ("Location: index.php?login=failed&2");
		die();
	}
	$row = $sth->fetch();

    # confirm the crypted password in the session matches the crypted password in the database for the user
	if ($row['crypt'] !== $_SESSION['crypt']) {
		header ("Location: index.php?login=failed&3");
		die();
	}

?>
