<?
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authpostmaster.php";
  include_once dirname(__FILE__) . "/config/functions.php";
  include_once dirname(__FILE__) . "/config/httpheaders.php";
?>
<html>
  <head>
    <title>Virtual Exim</title>
    <link rel="stylesheet" href="style.css" type="text/css">
  </head>
  <body>
    <? include dirname(__FILE__) . "/config/header.php"; ?>
    <div id="Centered">
      <table align="center">
	<tr><td><a href="adminuser.php"><? echo _("Add, delete and manage POP/IMAP accounts"); ?></a></td></tr>
	<tr><td><a href="adminalias.php"><? echo _("Add, delete and manage aliases, forwards and a Catchall"); ?></a></td></tr>
	<tr><td><a href="admingroup.php"><?=_("Add, delete and manage groups")?></a></td></tr>
	<tr><td><a href="adminfail.php"><? echo _("Add, delete and manage :fail:'s"); ?></a></td></tr>
	  <?
	    if ($mailmanroot != "") {
      	      print '<tr><td><a href="adminlists.php">' . _("Manage mailing lists") . '</a></td></tr>';
	    }
	  ?>
	<tr><td style="padding-top:1em"><a href="logout.php"><? echo _("Logout"); ?></a></td></tr>
      </table>
    </div>
  </body>
</html>

<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->

