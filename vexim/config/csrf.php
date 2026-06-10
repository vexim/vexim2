<?php
  /*
    Cross-site request forgery protection.

    A random token is stored in the session and must accompany every
    state-changing request. httpheaders.php verifies it on every POST;
    the few delete actions that use GET call csrf_verify() directly.
    Forms include the token with csrf_input().

    This file has no dependencies beyond core PHP so it can be included
    early (from httpheaders.php) without worrying about include order.
  */

  // Return the session's CSRF token, creating it on first use.
  function csrf_token()
  {
    if (empty($_SESSION['csrf_token'])) {
      $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
  }

  // A hidden field carrying the token. Place inside every state-changing form.
  function csrf_input()
  {
    // The token is hex (bin2hex), so it needs no HTML escaping.
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
  }

  // Abort the request unless it carries the correct token.
  function csrf_verify()
  {
    $sent = isset($_POST['csrf_token']) ? $_POST['csrf_token']
          : (isset($_GET['csrf_token']) ? $_GET['csrf_token'] : '');
    if (empty($_SESSION['csrf_token']) || !is_string($sent)
        || !hash_equals($_SESSION['csrf_token'], $sent)) {
      http_response_code(403);
      die('Invalid or missing CSRF token. Please reload the form and try again.');
    }
  }
