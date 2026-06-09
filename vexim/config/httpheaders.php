<?php
  session_start();
  require_once dirname(__FILE__) . '/csrf.php';
  # Reject any state-changing POST that does not carry a valid CSRF token.
  # GET delete actions call csrf_verify() themselves (see csrf.php).
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
  }
  header("Cache-control: private"); // IE6 hack to back forms + BACK button work
  header("Content-Type: text/html; charset=utf-8");
  if (isset($CSPenabled) && $CSPenabled === true)  header("Content-Security-Policy: default-src 'self';");
?>
