<?php
  include_once dirname(__FILE__) . "/config/variables.php";
  include_once dirname(__FILE__) . "/config/authsite.php";
  include_once dirname(__FILE__) . "/config/functions.php";
  include_once dirname(__FILE__) . "/config/httpheaders.php";

  if (isset($_POST['avscan'])) {
    $_POST['avscan'] = 1;
  } else {
    $_POST['avscan'] = 0;
  }
  if (isset($_POST['spamassassin'])) {
    $_POST['spamassassin'] = 1;
  } else {
    $_POST['spamassassin'] = 0;
  }
  if (isset($_POST['enabled'])) {
    $_POST['enabled'] = 1;
  } else {
    $_POST['enabled'] = 0;
  }
  if (isset($_POST['pipe'])) {
    $_POST['pipe'] = 1;
  } else {
    $_POST['pipe'] = 0;
  }
  if ($_POST['type'] == "relay") {
    $_POST['clear'] = $_POST['vclear'] = "BLANK";
  }
  if ($_POST['type'] == "alias") {
    $_POST['clear'] = $_POST['vclear'] = "BLANK";
  }
  if ($_POST['max_accounts'] == '') {
    $_POST['max_accounts'] = '0';
  }
  
// User can specify either UID, or username, the former being preferred.
// Using posix_getpwuid/posix_getgrgid even when we have an UID is so we
// are sure the UID exists.
  if (isset ($_POST['uid'])) {
    $uid = $_POST['uid'];
  }
  if (isset ($_POST['gid'])) {
    $gid = $_POST['gid'];
  }
  
  if ($userinfo = @posix_getpwuid ($uid)) {
    $uid = $userinfo['uid'];
  } elseif ($userinfo = @posix_getpwnam ($uid)) {
    $uid = $userinfo['uid'];
  } else {
    header ("Location: site.php?failuidguid={$_POST['domain']}");
    die;
  }
  
  if ($groupinfo = @posix_getgrgid ($gid)) {
    $gid = $groupinfo['gid'];
  } elseif ($groupinfo = @posix_getgrnam ($gid)) {
    $gid = $groupinfo['gid'];
  } else {
    header ("Location: site.php?failuidguid={$_POST['domain']}");
    die;
  }
        
  $smtphomepath = realpath($_POST['maildir'] . "/") .
    "/" . $_POST['domain'] . "/" . $_POST['localpart'] . "/Maildir";
  $pophomepath = realpath($_POST['maildir'] . "/") .
    "/" . $_POST['domain'] . "/" . $_POST['localpart'];
//Gah. Transactions!! -- GCBirzan
  if ((validate_password($_POST['clear'], $_POST['vclear'])) &&
    ($_POST['type'] != "alias")) {
    $query = "INSERT INTO domains 
              (domain, spamassassin, sa_tag, sa_refuse, avscan,
              max_accounts, quotas, maildir, pipe, enabled, uid, gid,
              type, maxmsgsize)
              VALUES ('" . $_POST['domain'] . "'," .
            "'{$_POST['spamassassin']}','".
            ((isset($_POST['sa_tag'])) ? $_POST['sa_tag']  : 0) . "','" .
            ((isset($_POST['sa_refuse'])) ? $_POST['sa_refuse']  : 0) . "','" .
            "{$_POST['avscan']}','".
            "{$_POST['max_accounts']}','".
            ((isset($_POST['quotas'])) ? $_POST['quotas'] : 0) . "','" .
            realpath ($_POST['maildir'] . "/") . "/" . $_POST['domain'] ."','" .
            "{$_POST['pipe']}','" .
            "{$_POST['enabled']}'," .
            "'$uid'," .
            "'$gid'," .
            "'{$_POST['type']}','".
            ((isset($_POST['maxmsgsize'])) ? $_POST['maxmsgsize'] : 0) . "')";
    $domresult = $db->query($query);
    if (!DB::isError($domresult)) {
      if ($_POST['type'] == "local") {
  $query = "INSERT INTO users
            (domain_id, localpart, username, clear, crypt, uid, gid,
            smtp, pop, realname, type, admin)
            SELECT domain_id, '" . $_POST['localpart'] . "'," .
            "'{$_POST['localpart']}@{$_POST['domain']}'," .
            "'{$_POST['clear']}'," .
            "'". crypt_password($_POST['clear'],$salt) . "','" .
            $uid . "','" .
            $gid . "', " .
            "'{$smtphomepath}', '{$pophomepath}'," .
            "'Domain Admin', 'local', 1 FROM domains
            WHERE domains.domain = '{$_POST['domain']}'";
// Is using indexes worth setting the domain_id by hand? -- GCBirzan
        $usrresult = $db->query($query);
        if (DB::isError($usrresult)) {
          header ("Location: site.php?failaddedusrerr={$_POST['domain']}");
          die;
        } else {
          header ("Location: site.php?added={$_POST['domain']}" .
                  "&type={$_POST['type']}");
          mail("{$_POST['localpart']}@{$_POST['domain']}",
                vexim_encode_header(_("Welcome Domain Admin!")),
                "$welcome_newdomain",
                "From: {$_POST['localpart']}@{$_POST['domain']}\r\nMIME-Version: 1.0\r\nContent-Type: text/plain; charset=utf-8\r\nContent-Transfer-Encoding: 8bit\r\n");
          die;
        }
      } else {
        header ("Location: site.php?added={$_POST['domain']}" .
                "&type={$_POST['type']}");
        mail("{$_POST['localpart']}@{$_POST['domain']}",
              vexim_encode_header(_("Welcome Domain Admin!")),
              "$welcome_newdomain",
              "From: {$_POST['localpart']}@{$_POST['domain']}\r\nMIME-Version: 1.0\r\nContent-Type: text/plain; charset=utf-8\r\nContent-Transfer-Encoding: 8bit\r\n");
        die;
      }
    } else {
      header ("Location: site.php?failaddeddomerr={$_POST['domain']}");
      die;
    }
  } else if ($_POST['type'] == "alias") {
    $idquery = "SELECT domain_id FROM domains
                WHERE domain = '{$_POST['aliasdest']}'
                AND domain_id > 1";
    $idresult = $db->query($idquery);
    if (DB::isError($idresult)) {
      header ("Location: site.php?baddestdom={$_POST['domain']}");
      die;
    } else {
      $idrow = $idresult->fetchRow();
      if (!isset($idrow['domain_id'])) {
        header ("Location: site.php?baddestdom={$_POST['domain']}");
        die;
      }
    }
    $query = "INSERT INTO domainalias (domain_id, alias)
              VALUES ('{$idrow['domain_id']}', '{$_POST['domain']}')";
    $result = $db->query($query);
    if (DB::isError($result)) {
      header ("Location: site.php?failaddeddomerr={$_POST['domain']}");
      die;
    } else {
      header ("Location: site.php?added={$_POST['domain']}" .
              "&type={$_POST['type']}");
      die;
    }
  } else {
    header ("Location: site.php?failaddedpassmismatch={$_POST['domain']}");
  }

?>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
