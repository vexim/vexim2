<?

  /* SQL Database login information */
  $sqlserver = "localhost:/tmp/mysql.sock";
  $sqldb = "vexim";
  $sqluser = "exim";
  $sqlpassword = "CHANGE";
  $dsn = "mysql://$sqluser:$sqlpass@$sqlhost/$sqldb";
  $db = DB::connect($dsn);
  if (DB::isError($db)) {
     die ($db->getMessage());
  }
 
  /* The Real name of the Catchall accounts */
  $catchallrealname = "Catchall";

  /* Set to either "des" or "md5" depending on your crypt() libraries */
  $cryptscheme = "md5";

  /* Default password to use for things like Aliases. */
  $emptypass = "NOPASSWORD"; 

  /* Group ID to which Exim changes by default when delivering mail or creating directories */
  $gid = "90";

  /* The location of your mailstore. Make sure the exim user owns it! */
  $mailroot = "/usr/local/mail/";

  /* path to Mailman */
  $mailmanroot = "http://www.EXAMPLE.com/mailman";

  /* User ID to which Exim changes by default when delivering mail or creating directories */
  $uid = "90";

  /* Setting this to 0 if only admins should be allowed to login */
  $AllowUserLogin = 1;

  /* Welcome message, sent to new POP/IMAP accounts */
  $welcome_message = "Welcome, $_POST[realname] !\n\nYour new E-mail account is all ready for you.\n\n"
                     . "Here are some settings you might find useful:\n\n"
		     . "Username: $_POST[username]@" . $_COOKIE[vexim][2] ."\n"
		     . "POP3 server: mail." . $_COOKIE[vexim][2] . "\n"
		     . "SMTP server: mail." . $_COOKIE[vexim][2] . "\n";
  $dbtype = "pg";

?>
