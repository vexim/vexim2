<?
  if (isset($_COOKIE['vexim'][2])) {
    $domheaderquery = "SELECT enabled FROM domains WHERE domains.domain_id='" . $_COOKIE['vexim'][2] . "'";
    $domheaderresult = $db->query($domheaderquery);
    $domheaderrow = $domheaderresult->fetchRow();
    $usrheaderquery = "SELECT enabled FROM users WHERE localpart='" . $_COOKIE['vexim'][0] . "' AND domain_id='" . $_COOKIE['vexim'][2] . "'";
    $usrheaderresult = $db->query($usrheaderquery);
    $usrheaderrow = $usrheaderresult->fetchRow();
  }

  print "<div id=\"Header\"><a href=\"http://silverwraith.com/vexim/\" target=\"_blank\">Virtual Exim</a> -- " . $_COOKIE['vexim'][1] . "";
  if (($domheaderrow['enabled'] == "0") || ($domheaderrow['enabled'] == "f")) {
    print "-- domain disabled (please see your administrator)";
  } else if (($usrheaderrow['enabled'] == "0") ||($usrheaderrow['enabled'] == "f")) {
    print "-- account disabled (please see your administrator)";
  }
  print "</div>";
?>
