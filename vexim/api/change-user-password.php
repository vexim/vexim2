<?php
/*
  Endpoint for external interface like roundcube to change the users password
*/

include_once dirname(__FILE__) . '/config/variables.php';
include_once dirname(__FILE__) . '/config/functions.php';

if(!$AllowUserLogin) {
    http_response_code(403);
    die("user login is forbidden");
}

# first check if we have sufficient post variables to achieve a successful login... if not the login fails immediately
if (!isset($_POST['user']) || $_POST['user']==='' 
    || !isset($_POST['curpass']) || $_POST['curpass']===''
    || !isset($_POST['newpass']) || $_POST['newpass']===''){
    http_response_code(400);
    die("invalid arguments");
}

# sql statement based on username
$query = "SELECT u.user_id,u.username,u.crypt
FROM users as u JOIN domains as d ON d.domain_id = u.domain_id
WHERE u.username=:username
AND u.enabled = 1
AND d.enabled = 1
AND u.type IN ('local', 'piped')";
$sth = $dbh->prepare($query);
$success = $sth->execute(array(':username'=>$_POST['user']));
if(!$success) {
  http_response_code(500);
  if(ini_get('display_errors')) {
    print_r($sth->errorInfo());
  }
  die('internal server error');
}
if ($sth->rowCount()!=1) {
  http_response_code(403);
  die('invalid user or password');
}

$row = $sth->fetch();
$cryptedpass = crypt_password($_POST['curpass'], $row['crypt']);

if ($cryptedpass !== $row['crypt']) {
   http_response_code(403);
   die('invalid user or password');
}

if (!password_strengthcheck($_POST['newpass'])) {
    http_response_code(422);
    die("week password");
}

$cryptedpass = crypt_password($_POST['newpass']);

$query = "UPDATE users SET crypt=:crypt WHERE user_id=:user_id";
$sth = $dbh->prepare($query);
$success = $sth->execute(array(':crypt'=>$cryptedpass, ':user_id'=>$row['user_id']));
if (!$success) {
  http_response_code(500);
  if(ini_get('display_errors')) {
    print_r($sth->errorInfo());
  }
  die('internal server error');
}
