<?
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authsite.php";

  if ($_POST[confirm] == "1") {
    $query = "DELETE FROM users WHERE domain_id='$_POST[domain_id]'";
    $query2 = "DELETE FROM domains WHERE domain='$_POST[domain]'";
    $result = $db->query($query);
    $result2 = $db->query($query2);
    if (!DB::isError($result)) {
      header ("Location: site.php?deleted=$_POST[domain]");
      die;
    } else header ("Location: site.php?faildeleted=$_POST[domain]");
      die;
  } else if ($_POST[confirm] == "cancel") {
    header ("Location: site.php?canceldelete=$_POST[domain]");
    die;
  }

  $query = "SELECT COUNT(*) AS count FROM users,domains WHERE domain='$_GET[domain]' AND users.domain_id=domains.domain_id";
  $result = $db->query($query);
  if (DB::isError($result)) { die ($result->getMessage()); }
  $row = $result->fetchRow();
?>
<html>
  <head>
    <title>Virtual Exim: Confirm Delete</title>
    <link rel="stylesheet" href="style.css" type="text/css">
  </head>
    <body>
    <? include dirname(__FILE__) . "/config/header.php"; ?>
    <div id='menu'>
      <a href='site.php'>Manage Domains</a><br>
      <a href='sitepassword.php' title='Change site password'>Site Password</a><br>
      <br><a href='logout.php'>Logout</a><br>
    </div>
    <div id='Content'>
      <form name='domaindelete' method='post' action='sitedelete.php'>
        <table align="center">
	  <tr><td colspan='2'>Please confirm deleting domain <? print $_GET[domain]; ?>:</td></tr>
	  <? if ($_GET[type] != "relay") {
		print "<tr><td colspan='2'>There are currently <b>$row[count]</b> accounts in domain $_GET[domain]</td></tr>";
	     }
	  ?>
	  <tr><td><input name='confirm' type='radio' value='cancel' checked><b> Do Not Delete <? print $_GET[domain]; ?></b></td></tr>
	  <tr><td><input name='confirm' type='radio' value='1'><b> Delete <? print $_GET[domain]; ?></b></td></tr>
	  <tr><td><input name='domain' type='hidden' value='<? print $_GET[domain]; ?>'>
	      <input name='domain_id' type='hidden' value='<? print $_GET[domain_id]; ?>'>
	      <input name='submit' type='submit' value='Continue'></td></tr>
	</table>
      </form>
    </div>
  </body>
</html>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
