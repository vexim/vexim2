<?
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authpostmaster.php";
?>
<html>
  <head>
    <title>Virtual Exim: Manage Users</title>
    <link rel="stylesheet" href="style.css" type="text/css">
  </head>
  <body>
    <? include dirname(__FILE__) . "/config/header.php"; ?>
    <div id="menu">
      <a href="adminaliasadd.php">Add Alias</a></br>
      <? $query = "SELECT * FROM users,domains WHERE domains.domain='" .$_COOKIE[vexim][1]. "' AND
      			users.domain_id=domains.domain_id AND users.type='catch'";
	 $result = $db->query($query);
	 $row = $result->numRows();
	 if ($row == 0) { print "<a href=\"admincatchalladd.php\">Add Catchall</a></br>"; }
	 #if ($row[type] == "catch") { $catchall = "exists"; }
	 #if ($catchall != "exists") { print "<a href=\"admincatchalladd.php\">Add Catchall</a></br>"; }
	 ?>
      <a href="admin.php">Main Menu</a><br>
      <br><a href="logout.php">Logout</a><br>
    </div>
    <div id="Content">
    <table align="center">
      <tr><th>&nbsp;</th><th>Alias</th><th>Target address</th><th>Forwards to..</th><th>Admin</th></tr>
      <?
	$query = "SELECT localpart,smtp,realname,users.type,admin FROM users,domains WHERE
			domains.domain='" .$_COOKIE[vexim][1]. "'
			AND domains.domain_id=users.domain_id
			AND (users.type='alias' OR users.type='catch') ORDER BY localpart;";
	$result = $db->query($query);
	while ($row = $result->fetchRow()) {
	  print "<tr><td align=\"center\"><a href=\"adminaliasdelete.php?localpart="
	  		. $row[localpart] . "\"><img style='border:0;width:10px;height:16px'
	  		src=\"images/trashcan.gif\" title=\"Delete alias "
			. $row[localpart] . "\"></a></td>\n";
	  print "<td>";
	  if ($row[type] == "catch") {
	    print "<a href=\"admincatchall.php\">" . $row[realname] . "</a></td>\n";
	    $catchall = "exists";
	  } else {
	    print "<a href=\"adminaliaschange.php?localpart=" . $row[localpart] . "\">" . $row[realname] . "</a></td>\n";
	  }
	  print "<td>" . $row[localpart] . "</td>\n";
	  print "<td>" . $row[smtp] . "</td>\n";
          print "\t<td class='check'>";
            if ($row[admin] == 1) print "<img style='border:0;width:13px;height:12px' src='images/check.gif' title='" . $row[realname] . " is an administrator'>";
	  print "</tr>\n";
	}
      ?>
      <tr><td colspan="4" style="padding-top:1em"><b>Note:</b>You can only have one catchall per domain.<br>
      It will catch and forward all email, that does not get delivered to a specific mailbox.</td></tr>
    </table>
    </div>
    <? 
      include "status.php";
    ?>
  </body>
</html>

<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
