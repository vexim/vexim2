<?
	session_start();
	header("Cache-control: private");	
	if (!isset($_SESSION['count'])) {
	    $_SESSION['count'] = 0;
	} else {
	    $_SESSION['count']++;
	}
//	setcookie ('PHPSESSID', $PHPSESSID, 0);

	$_SESSION[sqlserver] = "localhost";
	$_SESSION[database] = "vexim2";
	$_SESSION[sqluser] = "vexim";
	$_SESSION[sqlpasswd] = "";
	$_SESSION[vsqlpasswd] = "";
	$_SESSION[sqltype] = "mysql";
	header("Location:  setup1.php");
?>
