<?
  include_once dirname(__FILE__) . "/variables.php";
  $query = "SELECT user_id,localpart,crypt,domain_id FROM users WHERE localpart='".$_COOKIE['vexim'][0]."'
  		AND domain_id='".$_COOKIE['vexim'][2]."';";
  $result = $db->query($query);
  $row = $result->fetchRow();

  // If the localpart isn't in the cookie, of the database
  // password doesn't match the cookie password, reject the
  // user to the login screen
  if ($row['localpart'] != $_COOKIE['vexim'][0]) { header ("Location: index.php?login=failed"); };
  if ($row['crypt'] != $_COOKIE['vexim'][3]) { header ("Location: index.php?login=failed"); };
  if ($row['user_id'] != $_COOKIE['vexim'][4]) {header ("Location: index.php?login=failed"); };

?>
