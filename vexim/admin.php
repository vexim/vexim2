<?
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authpostmaster.php";
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
	<tr><td><a href="adminuser.php">Add, delete and manage POP/IMAP accounts</a></td></tr>
	<tr><td><a href="adminalias.php">Add, delete and manage aliases, forwards and a Catchall</a></td></tr>
	<tr><td><a href="adminfail.php">Add, delete and manage :fail:'s</a></td></tr>
	  <?
	    if ($mailmanroot != "") {
      	      print '<tr><td><a href="adminlists.php">Manage mailing lists</a></td></tr>';
	    }
	  ?>
	<tr><td style="padding-top:1em"><a href="logout.php">logout</a></td></tr>
      </table>
    </div>
  </body>
</html>

<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->

