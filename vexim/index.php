<?php
  // This is Vexim 2.3+git

  require_once dirname(__FILE__) . '/config/variables.php';
  require_once dirname(__FILE__) . '/config/functions.php';
  require_once dirname(__FILE__) . '/config/httpheaders.php';
?>
<html>
  <head>
    <title><?php echo _('Virtual Exim'); ?></title>
    <link rel="stylesheet" href="style.css" type="text/css">
    <meta name="robots" content="noindex, nofollow">
  </head>
  <body onLoad="document.login.localpart.focus()">
    <?php include dirname(__FILE__) . '/config/header.php'; ?>
    <div id="Centered">
      <form style="margin-top:3em;" name="login" method="post" action="login.php">
        <table align="center">
          <tr>
            <td><?php echo _('Username'); ?>:</td>
            <td><input name="username" type="text" class="textfield">
            <?php
            if($domainguess===1) echo '@'.preg_replace ("/^(".$domainguess_lefttrim.")\./", "", $_SERVER["SERVER_NAME"]);
            ?>
            </td>
          </tr>
          <tr>
            <td><?php echo _("Password"); ?>:</td>
            <td><input name="crypt" type="password" class="textfield"></td>
          </tr>
          <tr>
            <td colspan="2" style="text-align:center;padding-top:1em">
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
