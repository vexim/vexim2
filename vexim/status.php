<?
	if (isset($_GET['deleted'])) {
	  print "<div id='status'>User '{$_GET['deleted']}' has been successfully deleted</div>\n";
	} else if (isset($_GET['nodel'])) {
	  print "<div id='status'>User '{$_GET['nodel']}' cannot be deleted or have admin rights removed.<br>\n";
	  print "It is the last admin account. Create another admin account first.</div>\n";
	 } else if (isset($_GET['added'])) {
	  print "<div id='status'>User '{$_GET['added']}' has been successfully added</div>\n";
	} else if (isset($_GET['updated'])) {
	  print "<div id='status'>User '{$_GET['updated']}' has been successfully updated</div>\n";
	} else if (isset($_GET['faildeleted'])) {
	  print "<div id='status'>User '{$_GET['faildeleted']}' was not deleted</div>\n";
	} else if (isset($_GET['failadded'])) {
	  print "<div id='status'>User '{$_GET['failadded']}' could not be added</div>\n";
	} else if (isset($_GET['failupdated'])) {
	  print "<div id='status'>User '{$_GET['failupdated']}' could not be updated</div>\n";
	} else if (isset($_GET['canceldelete'])) {
	  print "<div id='status'>User '{$_GET['badname']}' contains invalid characters</div>\n";
	} else if (isset($_GET['userexists'])) {
	  print "<div id='status'>The account could not be added as the name {$_GET['userexists']} is already in use</div>\n";
	} else if (isset($_GET['blankname'])) {
	  print "<div id='status'>You can not specify a blank realname.</div>\n";
	} else if (isset($_GET['badpass'])) {
	  print "<div id='status'>Account {$_GET['badpass']} could not be added.<br>\n";
	  print "Your passwords were blank, do not match, or contain illegal characters: ' \" ` or ;</div>\n";
	} else if (isset($_GET['badaliaspass'])) {
	  print "<div id='status'>Account {$_GET['badaliaspass']} could not be added.<br>\n";
	  print "Your passwords do not match, or contain illegal characters: ' \" ` or ;</div>\n";
	} else if (isset($_GET['maxaccounts'])) {
	  print "<div id='status'>Account Limit Reached.<br>\n";
	  print "Your domain account limit has been reached.</div>\n";
	} else if (isset($_GET['quotahigh'])) {
	  print "<div id='status'>The quota you specified was too high.<br>\n";
	  print "The maximum quota you can specify is: {$_GET['quotahigh']} MB\n";
	} else if (isset($_GET['domaindisabled'])) {
	  print "<div id='status'>This domain is currently disabled.<br>\n";
	  print "Please see yout administrator.\n";
	}

?>
