<?
  /* SQL Database login information */
  require_once "DB.php";

  $sqlserver = "unix+localhost";
  $sqltype = "mysql";
  $sqldb = "vexim";
  $sqluser = "vexim";
  $sqlpass = "CHANGE";
  $dsn = "$sqltype://$sqluser:$sqlpass@$sqlserver/$sqldb";
  $db = DB::connect($dsn);
  if (DB::isError($db)) { die ($db->getMessage()); }
  $db->setFetchMode(DB_FETCHMODE_ASSOC); 

  /* Setting this to 0 if only admins should be allowed to login */
  $AllowUserLogin = 1;

  /* Set to either "des" or "md5" depending on your crypt() libraries */
  $cryptscheme = "md5";

  /* Choose the type of domain name input for the index page. It should 
     either be 'dropdown' or 'textbox'. Dropdown uses a dropdown style
     menu with <select> and <option>. Textbox presents a blank line for
     the user to type their domain name one. Textbox might be prefered
     if you have a large number of domains, or don't want to reveal the
     names of sites which you host */
  $domaininput = "dropdown";

  /* The UID's and GID's control the default UID and GID for new domains
     and if postmasters can define their own */
  $uid = "90";
  $gid = "90";
  $postmasteruidgid = "yes";

  /* The location of your mailstore for new domains.
     Make sure the exim user owns it! */
  $mailroot = "/usr/local/mail/";

  /* path to Mailman */
  $mailmanroot = "http://www.EXAMPLE.com/mailman";

  /* sa_tag is the default value to offer when we create new domains for SpamAssassin tagging
     sa_refuse is the default value to offer when we create new domains for SpamAssassin dropping */
  $sa_tag = "2";
  $sa_refuse = "5";

  /* Welcome message, sent to new POP/IMAP accounts */
  $welcome_message = "Welcome, $_POST[realname] !\n\nYour new E-mail account is all ready for you.\n\n"
                     . "Here are some settings you might find useful:\n\n"
		     . "Username: $_POST[localpart]@" . $_COOKIE[vexim][2] ."\n"
		     . "POP3 server: mail." . $_COOKIE[vexim][2] . "\n"
		     . "SMTP server: mail." . $_COOKIE[vexim][2] . "\n";
?>
