<?
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/httpheaders.php";

  if ($_POST['localpart'] == "siteadmin") {
    $query = "SELECT crypt,localpart FROM users,domains WHERE localpart='siteadmin'
    			AND domain='admin' and users.domain_id = domains.domain_id";
  } else if ($AllowUserLogin) {
    $query = "SELECT crypt,localpart FROM users,domains WHERE localpart='{$_POST['localpart']}'
    			AND users.domain_id = domains.domain_id AND domains.domain='{$_POST['domain']}';";
  } else {
    $query = "SELECT crypt,localpart FROM users,domains WHERE localpart='{$_POST['localpart']}'
    			AND users.domain_id = domains.domain_id AND domains.domain='{$_POST['domain']}' AND admin=1;";
}
  $result = $db->query($query);
  if (DB::isError($result)) { die ($result->getMessage()); }
  $row = $result->fetchRow();

  if ($cryptscheme == "des") {
    $salt = substr("{$row['crypt']}", 0, 2);
  } else if ($cryptscheme == "md5") {
    $salt = substr("{$row['crypt']}", 0, 12);
  }
  $cryptedpass = crypt("{$_POST['crypt']}",$salt);

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

  if ($cryptedpass == $row['crypt']) {
    if ($_POST['localpart'] == "siteadmin") {
      $query = "SELECT user_id,domains.domain_id,users.admin,users.type FROM
      			users,domains WHERE localpart='siteadmin' AND domain='admin' AND
			users.domain_id = domains.domain_id";
    } else {
      $query = "SELECT user_id,domain,users.domain_id,admin,users.type,domains.enabled AS de FROM users,domains WHERE
      			localpart='{$_POST['localpart']}' AND users.domain_id = domains.domain_id
			AND domains.domain='{$_POST['domain']}'";
    }
    $result = $db->query($query);
    if ($result->numRows()) { $row = $result->fetchRow(); }
    setcookie ("vexim[0]", $_POST['localpart'], time()+86400);
    setcookie ("vexim[1]", $_POST['domain'], time()+86400);
    setcookie ("vexim[2]", $row['domain_id'], time()+86400);
    setcookie ("vexim[3]", $cryptedpass, time()+86400);
    setcookie ("vexim[4]", $row['user_id'], time()+86400);

    $_SESSION['localpart'] = $_POST['localpart'];
    $_SESSION['domain'] = $_POST['domain'];
    $_SESSION['domain_id'] = $row['domain_id'];
    $_SESSION['crypt'] = $cryptedpass;
    $_SESSION['user_id'] = $row['user_id'];

    if (($row['admin'] == "1") && ($row['type'] == "site")) {
      header ("Location: site.php");
    } else if ($row['admin'] == "1") {
      header ("Location: admin.php");
    } else if (($row['de'] == "0") && ($row['admin'] != "1")) {
      header ("Location: index.php?domaindisabled");
    } else {
      header ("Location: userchange.php");
    }
  } else {
  header ("Location: index.php?login=failed");
}

?>

<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
