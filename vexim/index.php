<?php
  require_once dirname(__FILE__) . '/config/variables.php';
  require_once dirname(__FILE__) . '/config/functions.php';
  require_once dirname(__FILE__) . '/config/httpheaders.php';
?>
<html>
  <head>
    <title><?php echo _('Virtual Exim'); ?></title>
    <link rel="stylesheet" href="style.css" type="text/css">
  </head>
  <body onLoad="document.login.localpart.focus()">
    <?php include dirname(__FILE__) . '/config/header.php'; ?>
    <div id="Centered">
      <form style="margin-top:3em;" name="login" method="post" action="login.php">
        <table align="center">
          <tr>
            <td><?php echo _('Username'); ?>:<td><input name="localpart" type="text" class="textfield">&nbsp;@&nbsp;</td>
            <td>
              <?php
                $domain = preg_replace ("/^mail\./", "", $_SERVER["SERVER_NAME"]);
                if ($domaininput == 'dropdown') {
                  $query = "SELECT domain FROM domains WHERE type='local' AND domain!='admin' ORDER BY domain";
                  $result = $db->query($query);
              ?>
                  <select name="domain" class="textfield">
                  <option value=''>
              <?php
                    if ($result->numRows()) {
                      while ($row = $result->fetchRow()) {
                        print "<option value='{$row['domain']}'>{$row['domain']}"
                        . '</option>';
                      }
                    }
                  print '</select>';
                } else if ($domaininput == 'textbox') {
                  print '<input type="text" name="domain" class="textfield"> (domain name)';
                } else if ($domaininput == 'static') {
                  print $domain
                    . '<input type="hidden" name="domain" value='
                    . $domain
                    . '>';
                }
              ?>
            </td>
          </tr>
          <tr>
            <td><?php echo _("Password"); ?>:</td>
            <td><input name="crypt" type="password" class="textfield"></td>
          </tr>
          <tr>
            <td colspan="3" style="text-align:center;padding-top:1em">
              <input name="submit" type="submit"
                value="<?php echo _("Submit"); ?>" class="longbutton">
            </td>
          </tr>
        </table>
      </form>
    </div>
    <?php
      if (isset($_GET['login']) && ($_GET['login'] == "failed")) {
        print "<div id='status'>" . _("Login failed") . "</div>";
      }
    ?>
  </body>
</html>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
