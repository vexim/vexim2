<?
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/httpheaders.php";
?>
<html>
  <head>
    <title>Virtual Exim</title>
    <link rel="stylesheet" href="style.css" type="text/css">
  </head>
  <body onLoad="document.login.localpart.focus()">
    <? include dirname(__FILE__) . "/config/header.php"; ?>
    <div id="Centered">
    <form style="margin-top:3em;" name="login" method="post" action="login.php">
      <table valign="center" align="center">
	 <tr>
	  <td><? echo _("Username"); ?>:<td><input name="localpart" type="text" class="textfield">&nbsp;@&nbsp;</td>
	  <td>
	    <? if ($domaininput == "dropdown") {
		print "<select name=\"domain\" class=\"textfield\">\n";
		print "<option value=\"\">\n";
		  $query = "SELECT domain FROM domains WHERE type='local' AND domain!='admin' ORDER BY domain";
		  $result = $db->query($query);
		  if ($result->numRows()) {
		    while ($row = $result->fetchRow()) {
		      print "\t<option value=\"" . $row['domain'] . '">' . $row['domain'] . '</option>' . "\n";
		    }
		  }
		print "</select>\n";
	      } else if ($domaininput == "textbox") {
		print "<input type=\"text\" name=\"domain\" class=\"textfield\"> (domain name)\n";
	      }
	    ?>
	  </td>
	</tr>
	<tr>
	  <td><? echo _("Password"); ?>:<td><input name="crypt" type="password" class="textfield"></td>
	</tr>
	<tr>
	  <td colspan="3" style="text-align:center;padding-top:1em"><input name="submit" type="submit" value="<? echo _("Submit"); ?>" class="longbutton"></td>
	</tr>
      </table>
    </form>
    </div>
    <? if ($_GET['login'] == "failed") { print "<div id='status'>" . _("Login failed") . "</div>"; } ?>
  </body>
</html>

<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
