<?php
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authsite.php";
  include_once dirname(__FILE__) . "/config/functions.php";
  include_once dirname(__FILE__) . "/config/httpheaders.php";

  if (validate_password($_POST['clear'], $_POST['vclear'])) {
    $cryptedpassword = crypt_password($_POST['clear']);
    $query = "UPDATE users SET crypt='$cryptedpassword',
		clear='{$_POST['clear']}' WHERE localpart='siteadmin' AND domain_id='1'";
    $result = $db->query($query);
    if (!DB::isError($result)) {
      $_SESSION['crypt'] = $cryptedpassword;
      header ("Location: site.php?sitepass=success");
      die;
    } else {
      header ("Location: site.php?sitepass=fail");
      die;
    }
  } else {
    header ("Location: site.php?badpass=siteadmin");
  }
?>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
