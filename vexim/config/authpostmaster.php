<?
  include_once dirname(__FILE__) . "/variables.php";

  // Match the crypted password to the database entry
  // and confirm the user is an admin
  $query = "SELECT crypt FROM users WHERE localpart='".$_COOKIE[vexim][0]."' and domain_id='".$_COOKIE[vexim][2]."' AND admin='1';";
  $results = $db->query($query);
  $row = $results->fetchRow();

  // If the localpart isn't in the cookie, of the database
  // password doesn't match the cookie password, reject the
  // user to the login screen
  if (!isset($_COOKIE[vexim][0])) { header ("Location: /?login=failed"); };
  if ($row[crypt] != $_COOKIE[vexim][3]) { header ("Location: /?login=failed"); };
?>
