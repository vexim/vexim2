<?
  $headerquery = "SELECT users.enabled AS ue, domains.enabled AS de FROM users,domains WHERE domains.domain_id='" . $_COOKIE[vexim][2] . "'";
  $headerresult = $db->query($headerquery);
  $headerrow = $headerresult->fetchRow();

  print "<div id=\"Header\"><a href=\"http://silverwraith.com/vexim/\" target=\"_blank\">Virtual Exim</a> -- " . $_COOKIE[vexim][1] . "";
  if ($headerrow[de] == "0") {
    print "-- domain disabled (please see your administrator)";
  } else if ($headerrow[ue] == "0") {
    print "-- account disabled (please see your administrator)";
  }
  print "</div>";
?>
