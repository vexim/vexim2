<?php
  // Harden the session cookie before the session starts: not readable from
  // JavaScript, flagged Secure on HTTPS requests, and not sent on cross-site
  // requests (a basic CSRF mitigation).
  session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'httponly' => true,
    'samesite' => 'Strict',
    'secure'   => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
  ]);
  session_start();
  header("Cache-control: private"); // IE6 hack to back forms + BACK button work
  header("Content-Type: text/html; charset=utf-8");
  header("X-Content-Type-Options: nosniff");
  header("X-Frame-Options: SAMEORIGIN");
  if (isset($CSPenabled) && $CSPenabled === true)  header("Content-Security-Policy: default-src 'self';");
?>
