<?

  /* SQL Database login information */
  require_once "DB.php";
  $sqlserver = "localhost:/tmp/mysql.sock";
  $sqltype = "mysql";
  $sqldb = "vexim";
  $sqluser = "vexim";
  $sqlpass = "CHANGE";
  $dsn = "$sqltype://$sqluser:$sqlpass@$sqlhost/$sqldb";
  $db = DB::connect($dsn);
  if (DB::isError($db)) { die ($db->getMessage()); }
  $db->setFetchMode(DB_FETCHMODE_ASSOC);
 
  /* URL to your Virtual Exim install */
  $veximurl = "vexim2.silverwraith.com";

  /* Set to either "des" or "md5" depending on your crypt() libraries */
  $cryptscheme = "md5";

  /* Default password to use for things like Aliases. */
  $emptypass = "NOPASSWORD"; 

  /* The location of your mailstore. Make sure the exim user owns it! */
  $mailroot = "/usr/local/mail/";

  /* path to Mailman */
  $mailmanroot = "http://www.EXAMPLE.com/mailman";

  /* Welcome message, sent to new POP/IMAP accounts */
  $welcome_message = "Welcome, $_POST[realname] !\n\nYour new E-mail account is all ready for you.\n\n"
                     . "Here are some settings you might find useful:\n\n"
		     . "Username: $_POST[username]@" . $_COOKIE[vexim][2] ."\n"
		     . "POP3 server: mail." . $_COOKIE[vexim][2] . "\n"
		     . "SMTP server: mail." . $_COOKIE[vexim][2] . "\n";

?>
