<?
  include_once dirname(__FILE__) . "/httpheaders.php";
  include_once dirname(__FILE__) . "/variables.php";
  $query = "SELECT user_id,localpart,crypt,domain_id FROM users WHERE localpart='".$_SESSION['localpart']."'
  		AND domain_id='".$_SESSION['localpart']."';";
  $result = $db->query($query);
  $row = $result->fetchRow();

  // If the localpart isn't in the cookie, of the database
  // password doesn't match the cookie password, reject the
  // user to the login screen
  if ($row['localpart'] != $_SESSION['localpart']) { header ("Location: index.php?login=failed"); };
  if ($row['crypt'] != $_SESSION['crypt']) { header ("Location: index.php?login=failed"); };
  if ($row['user_id'] != $_SESSION['user_id']) {header ("Location: index.php?login=failed"); };

?>
