<?php
  session_start();
  header("Cache-control: private"); // IE6 hack to back forms + BACK button work
  header("Content-Type: text/html; charset=utf-8");
  if (isset($CSPenabled) && $CSPenabled === true)  header("Content-Security-Policy: default-src 'self';");
?>
