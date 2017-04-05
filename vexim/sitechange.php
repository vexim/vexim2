<?php
include_once dirname(__FILE__) . "/config/variables.php";
include_once dirname(__FILE__) . "/config/authsite.php";
include_once dirname(__FILE__) . "/config/functions.php";
include_once dirname(__FILE__) . "/config/httpheaders.php";
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo _("Virtual Exim") . ": " . _("Manage Domains"); ?></title>
    <link rel="stylesheet" href="style.css" type="text/css">
</head>
<body>
<?php include dirname(__FILE__) . "/config/header.php"; ?>
<div id="Menu">
    <a href="site.php"><?php echo _("Manage Domains"); ?></a><br>
    <a href="sitepassword.php"><?php echo _("Site Password"); ?></a><br>
    <br>
    <a href="logout.php"><?php echo _("Logout"); ?></a><br>
</div>
<div id="Forms">
    <form name="passwordchange" method="post" action="sitechangesubmit.php">
        <table align="center">
            <tr>
                <td colspan="2"><h4><?php echo _("Modify Domain Admin"); ?>:</h4></td>
            </tr>
            <tr>
                <td><?php echo _("Admin"); ?>:</td>
                <td><select name="localpart" class="textfield" autofocus>
                        <?php
                        $query = "SELECT localpart,domain FROM users,domains
                            WHERE domains.domain_id=:domain_id
                            AND admin=1 AND users.domain_id=domains.domain_id";
                        $sth = $dbh->prepare($query);
                        $sth->execute(array(':domain_id' => $_GET['domain_id']));
                        while ($row = $sth->fetch()) {
                            print '<option value="' . $row['localpart'] . '">' . $row['localpart'] . '</option>' . "\n\t";
                        }
                        ?>
                    </select>@<?php
                    $query = "SELECT * FROM domains WHERE domain_id=:domain_id";
                    $sth = $dbh->prepare($query);
                    $sth->execute(array(':domain_id' => $_GET['domain_id']));
                    if ($sth->rowCount()) {
                        $row = $sth->fetch();
                    }
                    print $row['domain'];
                    ?>
                </td>
                <td>
                    <input name="domain_id" type="hidden" value="<?php print $_GET['domain_id']; ?>">
                    <input name="domain" type="hidden" value="<?php print $_GET['domain']; ?>">
                </td>
            </tr>
            <tr>
                <td><?php echo _("Password"); ?>:</td>
                <td><input name="clear" size="25" type="password" class="textfield"></td>
            </tr>
            <tr>
                <td><?php echo _("Verify Password"); ?>:</td>
                <td><input name="vclear" size="25" type="password" class="textfield"></td>
            </tr>
            <tr>
                <td colspan="2" class="button">
                    <input name="submit" size="25" type="submit" value="<?php echo _("Submit Password"); ?>">
                </td>
            </tr>
        </table>
    </form><br>
    <form name="domainchange" method="post" action="sitechangesubmit.php">
        <table align="center">
            <tr>
                <td colspan="2"><h4><?php echo _("Modify Domain Properties"); ?>:</h4></td>
            </tr>
            <tr>
                <td><?php echo _("System UID"); ?>:</td>
                <td><input type="text" size="5" name="uid" value="<?php print $row['uid']; ?>" class="textfield"></td>
            </tr>
            <tr>
                <td><?php echo _("System GID"); ?>:</td>
                <td><input type="text" size="5" name="gid" value="<?php print $row['gid']; ?>" class="textfield"></td>
            </tr>
            <tr>
                <td><?php echo _("Maximum accounts") . "<br>(" . _("0 for unlimited") . ")"; ?>:</td>
                <td><input type="text" size="5" name="max_accounts" value="<?php print $row['max_accounts']; ?>" class="textfield"></td>
            </tr>
            <tr>
                <td><?php echo _("Max mailbox quota in MB") . "<br>(" . _("0 for disabled") . ")"; ?>:</td>
                <td><input type="text" size="5" name="quotas" value="<?php print $row['quotas']; ?>" class="textfield"> </td>
            </tr>
            <tr>
                <td><?php echo _("Maximum message size"); ?>:</td>
                <td><input name="maxmsgsize" size="5" type="text" class="textfield" value="<?php print $row['maxmsgsize']; ?>"> KB</td>
            </tr>
            <tr>
                <td><?php echo _("Spamassassin tag score"); ?>:</td>
                <td><input type="text" size="5" name="sa_tag" value="<?php print $row['sa_tag']; ?>" class="textfield"></td>
            </tr>
            <tr>
                <td><?php echo _("Spamassassin refuse score"); ?>:</td>
                <td><input type="text" size="5" name="sa_refuse" value="<?php print $row['sa_refuse']; ?>" class="textfield"></td>
            </tr>
            <tr>
                <td><?php echo _("Spamassassin"); ?>:</td>
                <td><input type="checkbox" name="spamassassin" <?php if ($row['spamassassin'] == 1) {print "checked";} ?>></td>
            </tr>
            <tr>
                <td><?php echo _("Anti-virus"); ?>:</td>
                <td><input type="checkbox" name="avscan" <?php if ($row['avscan'] == 1) {print "checked";} ?>></td>
            </tr>
            <tr>
                <td><?php echo _("Piping to command"); ?>:</td>
                <td><input type="checkbox" name="pipe" <?php if ($row['pipe'] == 1) {print "checked";} ?>></td>
            </tr>
            <tr>
                <td><?php echo _("Enabled"); ?>:</td>
                <td><input type="checkbox" name="enabled" <?php if ($row['enabled'] == 1) {print "checked";} ?>></td>
                <td>
                    <input name="domain_id" type="hidden" value="<?php print $_GET['domain_id']; ?>">
                    <input name="domain" type="hidden" value="<?php print $_GET['domain']; ?>">
                </td>
            </tr>
            <tr>
                <td colspan="2" class="button">
                    <input name="submit" size="25" type="submit" value="<?php echo _("Submit Changes"); ?>">
                </td>
            </tr>
        </table>
    </form><br>
    <form name="allusers" method="post" action="sitechangesubmit.php">
            <input name="allusers" type="hidden" value="<?php print $_GET['domain_id']; ?>">
            <input name="domain_id" type="hidden" value="<?php print $_GET['domain_id']; ?>">
            <input name="domain" type="hidden" value="<?php print $_GET['domain']; ?>">
        <table align="center">
            <tr>
                <td colspan="2"><h4><?php echo _("Modify SpamAssassin/Antivirus for all users").":"; ?></h4></td>
            </tr>
            <tr>
                <td valign="top"><?php echo _("SpamAssassin for all domain users") . "<br><b>(" . _("Warning: cannot be reversed!") . ")</b>:"; ?></td>
                <td>
                    <input type="radio" name="on_spamassassin" value="2" checked><?php echo _("Keep unchanged"); ?><br>
                    <input type="radio" name="on_spamassassin" value="1"><?php echo _("Enable"); ?><br>
                    <input type="radio" name="on_spamassassin" value="0"><?php echo _("Disable"); ?>
                </td>
            </tr>
            <tr>
                <td valign="top"><?php echo _("Anti-Virus for all domain users") . "<br><b>(" . _("Warning: cannot be reversed!") . ")</b>:"; ?></td>
                <td>
                    <input type="radio" name="on_avscan" value="2" checked><?php echo _("Keep unchanged"); ?><br>
                    <input type="radio" name="on_avscan" value="1"><?php echo _("Enable"); ?><br>
                    <input type="radio" name="on_avscan" value="0"><?php echo _("Disable"); ?>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="button">
                    <input name="submit" type="submit" value="<?php echo _("Submit"); ?>">
                </td>
            </tr>
        </table>
    </form>
</div>
</body>
</html>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
