<?
  // Strictly two aren't alone functions, but they are functions of sorts and we call it every
  // page to prevent tainted data expoits
  foreach ($_GET as $getkey => $getval) {
    $_GET[$getkey] = preg_replace('/[\'";$]/','',$getval);
  }

  foreach ($_POST as $postkey => $postval) {
    $_POST[$postkey] = preg_replace('/[\'";$]/','',$postval);
  }

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
    if ($row['c'] != 0) {
      header ("Location: $page?userexists=$localpart");
      die;
    }
  }

  function alpha_menu($flag) {
    global $letter;	// needs to be available to the parent
    $letter = $_GET['LETTER'];
    if ($letter == '') $letter = 'ALL'; // Which letter to start the menu lists all
    if ($letter == 'ALL') $letter = '';
    if ($flag) {
      print "\n<p class='alpha'><a href='" . $_SERVER['PHP_SELF'] . "?LETTER=ALL' class='alpha'>ALL</a>&nbsp;&nbsp; ";
      // loops through the alphabet. For international alphabets, replace the string in the proper order
      foreach (preg_split('//', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', -1, PREG_SPLIT_NO_EMPTY) as $i) {
      	print "<a href='" . $_SERVER['PHP_SELF'] . "?LETTER=$i' class='alpha'>$i</a>&nbsp; ";
      }
    print "</p>\n";
    }
  }
?>
