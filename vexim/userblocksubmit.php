<?php
include_once dirname(__FILE__) . "/config/variables.php";
include_once dirname(__FILE__) . "/config/authuser.php";
include_once dirname(__FILE__) . "/config/functions.php";
include_once dirname(__FILE__) . "/config/httpheaders.php";

if ($_GET['action'] == "delete") {
    $query = "DELETE FROM blocklists WHERE block_id=:block_id";
    $sth = $dbh->prepare($query);
    $success = $sth->execute(array(':block_id' => $_GET['block_id']));
    if ($success) {
        header("Location: userchange.php?updated");
    } else {
        header("Location: userchange.php?failed");
    }
}

# Finally 'the rest' which is handled by the profile form
if (preg_match("/^\s*$/", $_POST['blockval'])) {
    header("Location: userchange.php");
    die;
}
$query = "INSERT INTO blocklists (domain_id, user_id, blockhdr, blockval, color) VALUES (
    :domain_id, :user_id, :blockhdr, :blockval, :color)";
$sth = $dbh->prepare($query);
$success = $sth->execute(array(':domain_id' => $_SESSION['domain_id'], ':user_id' => $_SESSION['user_id'],
    ':blockhdr' => $_POST['blockhdr'], ':blockval' => $_POST['blockval'], ':color' => $_POST['color']));
if ($success) {
    header("Location: userchange.php?updated");
} else {
    header("Location: userchange.php?failed");
}
?>
<!-- Layout and CSS tricks obtained from http://www.bluerobot.com/web/layouts/ -->
