<?
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authsite.php";
?>
<html>
  <head>
    <title>Virtual Exim: Manage Domains</title>
    <link rel="stylesheet" href="style.css" type="text/css">
  </head>
  <body onLoad="document.siteadd.domain.focus()">
  <? include dirname(__FILE__) . "/config/header.php"; ?>
  <div id='menu'>
      <a href="site.php">Manage Domains</a><br>
      <a href='sitepassword.php' title='Change site password'>Site Password</a><br>
      <br><a href="logout.php">Logout</a><br>
  </div>
  <div id='Forms'>
    <form name="siteadd" method="post" action="siteaddsubmit.php">
      <table align="center">
	<tr><td>Domain:</td><td><input name="domain" type="text" class="textfield"></td>
	    <td>The name of the new domain you are adding</td></tr>
	<? if ($_GET[type] == "local") {
	   print "
	     <tr><td>Domain Admin:</td><td><input name=\"localpart\" type=\"text\" value=\"postmaster\" class=\"textfield\"></td>
		 <td>The username of the domain's administrator account</td></tr>
	     <tr><td>Password:</td><td><input name=\"clear\" type=\"password\" class=\"textfield\"></td></tr>
	     <tr><td>Verify Password:</td><td><input name=\"vclear\" type=\"password\" class=\"textfield\"></td></tr>
	     <tr><td>System UID:</td><td><input name=\"uid\" type=\"text\" class=\"textfield\" value=\"$uid\"></td></tr>
	     <tr><td>System GID:</td><td><input name=\"gid\" type=\"text\" class=\"textfield\" value=\"$gid\"></td></tr>
	     <tr><td>Domain Mail directory:</td>
	         <td><input name=\"maildir\" type=\"text\" class=\"textfield\" value=\"$mailroot\"></td>
		 <td>Create the domain directory below this top-level mailstore</td></tr>
	     <tr><td>Max mailbox quota (0 for disabled):</td>
	         <td><input name=\"quotas\" type=\"text\" class=\"textfield\" value=\"0\"></td></tr>
	     <tr><td>Spamassassin warn score:</td>
	         <td><input name=\"sa_tag\" type=\"text\" class=\"textfield\" value=\"$sa_tag\"></td>
		 <td>The score at which to mark mail as \"Spam\" but still deliver</td></tr>
	     <tr><td>Spamassassin refuse score:</td>
	         <td><input name=\"sa_refuse\" type=\"text\" class=\"textfield\" value=\"$sa_refuse\"></td>
		 <td>The score at which to refuse potentially spam mail and not deliver</td></tr>
	     <tr><td>Spamassassin enabled?</td><td><input name=\"spamassassin\" type=\"checkbox\" class=\"textfield\"></td></tr>
	     <tr><td>Anti Virus enabled?</td><td><input name=\"avscan\" type=\"checkbox\" class=\"textfield\"></td></tr>
	     <tr><td>Domain enabled?</td><td><input name=\"enabled\" type=\"checkbox\" class=\"textfield\" checked></td></tr>
	     <tr><td></td>";
	   }
	?>
	    <td><input name="type" type="hidden" value="<? print $_GET[type]; ?>">
		<input name="admin" type="hidden" value="1">
		<input name="submit" type="submit" value="Submit"></td>
	</tr>
      </table>
    </form>
    </div>
  </body>
</html>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
