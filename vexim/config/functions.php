<?

  function validate_password($clear,$vclear) {
    return ($clear == $vclear)
      && ($clear != "")
      && ($clear == preg_replace("/[\'\"\`\;]/","",$clear));
  }

  function alias_validate_password($clear,$vclear) {
    return ($clear == $vclear)
      && ($clear == preg_replace("/[\'\"\`\;]/","",$clear));
  }

  function check_user_exists($db,$localpart,$domain_id,$page) {
    $query = "SELECT COUNT(*) AS c FROM users WHERE localpart='$localpart' AND domain_id='$domain_id'";
    $result = $db->query($query);
    $row = $result->fetchRow();
    if ($row[c] != 0) {
      header ("Location: $page?userexists=$localpart");
      die;
    }
  }

?>
