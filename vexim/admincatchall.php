<?php
  include_once dirname(__FILE__) . '/config/variables.php';
  include_once dirname(__FILE__) . '/config/authpostmaster.php';
  include_once dirname(__FILE__) . '/config/functions.php';
  include_once dirname(__FILE__) . '/config/httpheaders.php';
  $query = "SELECT smtp FROM users WHERE user_id=:user_id 
			AND domain_id=:domain_id AND type='catch'";
  $sth = $dbh->prepare($query);
  $sth->execute(array(':user_id'=>$_GET['user_id'], ':domain_id'=>$_SESSION['domain_id']));
  if ($sth->rowCount()) {
    $row = $sth->fetch();
  }
?>
<html>
  <head>
    <title><?php echo _('Virtual Exim') . ': ' . _('Manage Users'); ?></title>
    <link rel="stylesheet" href="style.css" type="text/css">
  </head>
  <body>
    <?php include dirname(__FILE__) . '/config/header.php'; ?>
    <div id="Menu">
      <a href="adminalias.php"><?php echo _('Manage Aliases'); ?></a><br>
      <a href="admin.php"><?php echo _('Main Menu'); ?></a><br>
      <br><a href="logout.php"><?php echo _('Logout'); ?></a><br>
    </div>
    <div id="Forms">
	<?php 
		# ensure this page can only be used to view/edit the catchall that already exist for the domain of the admin account
		if (!$sth->rowCount()) {			
			echo '<table align="center"><tr><td>';
			printf(_("Invalid catchall userid '%s' for domain '%s'"), htmlentities($_GET['user_id']), htmlentities($_SESSION['domain']));
			echo '</td></tr></table>';
		}else{	
	?>
	<form name="admincatchall" method="post" action="admincatchallsubmit.php">
        <table align="center">
          <tr>
            <td><?php echo _('Alias Name'); ?>:</td>
            <td><?php echo _('Catchall'); ?></td>
          </tr>
          <tr>
            <td><?php echo _('Forward email addressed to'); ?>:</td>
            <td>*@<?php echo $_SESSION['domain']; ?></td>
          </tr>
          <tr>
            <td><?php echo _('Forward the email to'); ?>:</td>
            <td>
              <input name="smtp" type="text" value="<?php echo $row['smtp']; ?>"
                class="textfield"><br>
              <?php echo _('Must be a full e-mail address'); ?>!
            </td>
          </tr>
          <tr>
            <td>
              <input name="user_id" type="hidden"
                value="<?php print $_GET['user_id']; ?>" class="textfield">
            </td>
            <td>
              <input name="submit" type="submit"
                value="<?php echo _('Submit'); ?>">
            </td>
          </tr>
        </table>
      </form>
		<?php 		
			# end of block shown for editing the domains catchall
		}  
		?>  
    </div>
  </body>
</html>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
