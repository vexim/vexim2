<?
  include_once dirname(__FILE__) . "/config/variables.php";
?>
<html>
  <head>
    <title>Virtual Exim</title>
    <link rel="stylesheet" href="style.css" type="text/css">
  </head>
  <body onLoad="document.login.username.focus()">
    <div id="Header"><a href='http://<? print $veximurl; ?>' target="_blank">VExim</a></div>
    <div id="Centered">
    <form style="margin-top:3em;" name="login" method="post" action="login.php">
      <table valign="center" align="center">
         <tr>
          <td>Username:<input name="localpart" type="text" class="textfield">&nbsp;@&nbsp;</td>
	  <td><select name='domain' class="textfield">
	    <option value="">
	    <?
              $sql = ('SELECT DISTINCT domain FROM domains WHERE domain!="admin" AND domain != "" ORDER BY domain');
              $result = $db->query($sql);
	      if (DB::isError($result)) {
	         die ($result->getMessage());
	      }                             
              while ($row = $result->fetchRow()) {
                print "\t<option value=\"@" . $row[domain] . '">' . $row[domain] . '</option>' . "\n";
              }
	    ?>
	    </select>
          </td>
        </tr>
        <tr>
          <td>Password:<input name="password" type="password" class="textfield"></td>
        </tr>
        <tr>
          <td colspan="3" style="text-align:center;padding-top:1em">
	    <input name="submit" type="submit" value="submit" class="longbutton">
	  </td>
        </tr>
      </table>
    </form>
    </div>
    <? if ($_GET[login] == "failed") { print "<div id='status'>Login Failed</div>"; } ?>
  </body>
</html>

<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
