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

	$validateAsSiteadmin =
		(    isset($siteadminManageDomains)
		  && $siteadminManageDomains
		  && 'siteadmin' === $_SESSION['username']
		  && isset($_SESSION['siteadmin_domain_id'])
		);
	$domain_id = $validateAsSiteadmin ? $_SESSION['siteadmin_domain_id'] : $_SESSION['domain_id'];

	# Match the session details to an admin account the domain of the postmaster
	$query = "SELECT `crypt` FROM users WHERE `user_id`=:user_id AND `domain_id`=:domain_id AND `admin`=1";
    $sth = $dbh->prepare($query);
    $success = $sth->execute(array(':user_id'=>$_SESSION['user_id'], ':domain_id'=>$domain_id));
    if(!$success || ($sth->rowCount()!=1)) {
      header ("Location: index.php?login=failed");
      die();
    }
	$row = $sth->fetch();

	# confirm the crypted password in the session matches the crypted password in the database for the user
	if ($row['crypt'] != $_SESSION['crypt']) {
		header ("Location: index.php?login=failed");
		die();
	}

	if (isset($siteadminManageDomains)
	    && $siteadminManageDomains
	    && isset($_SESSION['username'])
	    && 'siteadmin' === $_SESSION['username']
	    && isset($_GET['manage_domain_id'])
	    )
	{
		$query = "SELECT `domain` FROM domains WHERE `domain_id`=:domain_id";
		$sth = $dbh->prepare($query);
		$success = $sth->execute(array(':domain_id'=>$_GET['manage_domain_id']));
		if(!$success || ($sth->rowCount()!=1)) {
			header ("Location: index.php?login=failed");
			die();
		}
		$row = $sth->fetch();
		$_SESSION['domain_id'] = $_GET['manage_domain_id'];
		$_SESSION['domain'] = $row['domain'];
	}
