<?
  include_once dirname(__FILE__) . "/variables.php";

  // Match the crypted password to the database entry
  // and confirm the user is a the siteadmin
  if ($_COOKIE[vexim][0] != "siteadmin") { header ("Location: index.php?login=failed"); };
  $query = "SELECT crypt,domain FROM users,domains WHERE localpart='siteadmin' AND domain='admin' AND
  		users.domain_id=domains.domain_id;";
  $results = $db->query($query);
  $row = $results->fetchRow();

  // If the cookie password doesn't math the user password
  // reject them to the login screen
  if ($row[crypt] != $_COOKIE[vexim][3]) { header ("Location: index.php?login=failed"); };
?>
