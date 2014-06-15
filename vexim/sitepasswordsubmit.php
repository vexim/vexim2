<?php
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authsite.php";
  include_once dirname(__FILE__) . "/config/functions.php";
  include_once dirname(__FILE__) . "/config/httpheaders.php";

  if (validate_password($_POST['clear'], $_POST['vclear'])) {
    $cryptedpassword = crypt_password($_POST['clear']);
      $query = "UPDATE users SET crypt=:crypt";
      if($saveclearpw==1) $query .= ", clear=:clear";
      $query .= " WHERE localpart='siteadmin' AND domain_id='1'";
    $sth = $dbh->prepare($query);
      if($saveclearpw==1) {
          $success = $sth->execute(array(':crypt'=>$cryptedpassword, ':clear'=>$_POST['clear']));
      } else {
          $success = $sth->execute(array(':crypt'=>$cryptedpassword));
      }
        if ($success) {
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
