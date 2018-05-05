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
  if (!isset($_POST['max_accounts']) || $_POST['max_accounts'] == '') {
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
  if(isset($_POST['maildir']) && isset($_POST['localpart'])) {
    if (substr($_POST['maildir'], 0, 1) !== '/') {
      header ("Location: site.php?failmaildirnonabsolute={$_POST['maildir']}");
	  die();
    }
    if ($testmailroot && is_dir(realpath($_POST['maildir'])) === false) {
      header ("Location: site.php?failmaildirmissing={$_POST['maildir']}");
      die();
	}
    $domainpath = $_POST['maildir'];
    if (substr($domainpath, -1) !== '/') {
      $domainpath .= '/';
    }
    $domainpath .= $_POST['domain'];
    $smtphomepath = $domainpath . "/" . $_POST['localpart'] . "/Maildir";
    $pophomepath = $domainpath . "/" . $_POST['localpart'];
  }

  if ($_POST['type'] === "alias") {
    $query = "INSERT INTO domainalias (domain_id, alias)
              SELECT domains.domain_id, :alias FROM domains WHERE domains.domain_id=:domain_id";
    $sth = $dbh->prepare($query);
    $sth->execute(array(':domain_id'=>$_POST['aliasdest'], ':alias'=>$_POST['domain']));
    if ($sth->rowCount()!==1) {
      header ("Location: site.php?failaddeddomerr={$_POST['domain']}");
      die;
    } else {
      header ("Location: site.php?added={$_POST['domain']}" .
              "&type={$_POST['type']}");
      die;
    }
  } else { // local or relay
      if ($_POST['type'] === "local") {
        if (!validate_password($_POST['clear'], $_POST['vclear'])) {  
          header ("Location: site.php?failaddedpassmismatch={$_POST['domain']}");
          die;
        }
        if (!password_strengthcheck($_POST['clear'])) {  
          header ("Location: site.php?weakpass={$_POST['domain']}");
          die;
	}
    }
    $query = "INSERT INTO domains 
              (domain, spamassassin, sa_tag, sa_refuse, avscan,
              max_accounts, quotas, maildir, pipe, enabled, uid, gid,
              type, maxmsgsize)
              VALUES (:domain, :spamassassin, :sa_tag, :sa_refuse,
              :avscan, :max_accounts, :quotas, :maildir, :pipe, :enabled,
              :uid, :gid, :type, :maxmsgsize)";
    $sth = $dbh->prepare($query);
    $success = $sth->execute(array(':domain'=>$_POST['domain'],
        ':spamassassin'=>$_POST['spamassassin'],
        ':sa_tag'=>((isset($_POST['sa_tag'])) ? $_POST['sa_tag']  : $sa_tag),
        ':sa_refuse'=>((isset($_POST['sa_refuse'])) ? $_POST['sa_refuse']  : $sa_refuse),
        ':avscan'=>$_POST['avscan'], ':max_accounts'=>$_POST['max_accounts'],
        ':quotas'=>((isset($_POST['quotas'])) ? $_POST['quotas'] : 0),
        ':maildir'=>((isset($_POST['maildir'])) ? $domainpath : ''),
        ':pipe'=>$_POST['pipe'], ':enabled'=>$_POST['enabled'],
        ':uid'=>$uid, ':gid'=>$gid, ':type'=>$_POST['type'],
        ':maxmsgsize'=>((isset($_POST['maxmsgsize'])) ? $_POST['maxmsgsize'] : 0)
        ));
    if ($success) {
      if ($_POST['type'] == "local") {
        $query = "INSERT INTO users
          (domain_id, localpart, username, crypt, uid, gid, smtp, pop, realname, type, admin)
           SELECT domain_id, :localpart, :username, :crypt, :uid, :gid, :smtp, :pop, 'Domain Admin', 'local', 1
            FROM domains
            WHERE domains.domain=:domain";
        $sth = $dbh->prepare($query);
        $success = $sth->execute(array(':localpart'=>$_POST['localpart'],
                ':username'=>$_POST['localpart'].'@'.$_POST['domain'],
                ':crypt'=>crypt_password($_POST['clear']),
                ':uid'=>$uid, ':gid'=>$gid, ':smtp'=>$smtphomepath,
                ':pop'=>$pophomepath,
                ':domain'=>$_POST['domain'],
                ));
        if (!$success) {
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
        die;
      }
    } 
    header ("Location: site.php?failaddeddomerr={$_POST['domain']}");
  }
?>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
