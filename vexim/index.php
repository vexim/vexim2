<?
  include_once dirname(__FILE__) . "/config/variables.php";
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
          <td>Username:<td><input name="localpart" type="text" class="textfield">&nbsp;@&nbsp;</td>
	  <td>
	    <? if ($domaininput == "dropdown") {
	        print "<select name=\"domain\" class=\"textfield\">\n";
	        print "<option value=\"\">\n";
                  $query = "SELECT domain FROM domains WHERE domain!='admin' ORDER BY domain";
	          $result = $db->query($query);
	          if (DB::isError($result)) { die ($result->getMessage()); }
                  while ($row = $result->fetchRow()) {
                    print "\t<option value=\"" . $row[domain] . '">' . $row[domain] . '</option>' . "\n";
                  }
	        print "</select>\n";
	      } else if ($domaininput == "textbox") {
	        print "<input type=\"text\" name=\"domain\" class=\"textfield\"> (domain name)\n";
	      }
	    ?>
          </td>
        </tr>
        <tr>
          <td>Password:<td><input name="crypt" type="password" class="textfield"></td>
        </tr>
        <tr>
          <td colspan="3" style="text-align:center;padding-top:1em"><input name="submit" type="submit" value="submit" class="longbutton"></td>
        </tr>
      </table>
    </form>
    </div>
    <? if ($_GET[login] == "failed") { print "<div id='status'>Login Failed</div>"; } ?>
  </body>
</html>

<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
