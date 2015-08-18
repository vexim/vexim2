<?php

include_once dirname(__FILE__) . '/config/variables.php';
include_once dirname(__FILE__) . '/config/authpostmaster.php';
include_once dirname(__FILE__) . '/config/functions.php';

# Fix the boolean values
$_POST['is_public'] = isset($_POST['is_public']) ? 'Y' : 'N';
$_POST['enabled'] = isset($_POST['enabled']) ? 1 : 0;

# validate localpart
if (preg_match("/['@%!\/\|\" ']/", $_POST['localpart'])) {
    header("Location: admingroupchange.php?group_id={$_POST['group_id']}&badname={$_POST['localpart']}");
    die;
}

$query = "UPDATE groups SET name=:localpart,
    enabled=:enabled, is_public=:is_public
    WHERE id=:group_id AND domain_id=:domain_id";
$sth = $dbh->prepare($query);
$success = $sth->execute(array(':localpart' => $_POST['localpart'], ':enabled' => $_POST['enabled'],
    ':is_public' => $_POST['is_public'], ':group_id' => $_POST['group_id'], ':domain_id' => $_SESSION['domain_id']));
if ($success) {
    header("Location: admingroupchange.php?group_id={$_POST['group_id']}&group_updated={$_POST['localpart']}");
} else {
    header("Location: admingroupchange.php?group_id={$_POST['group_id']}&group_failupdated={$_POST['localpart']}");
}
