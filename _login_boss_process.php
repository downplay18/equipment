<?php

//var_dump($_SESSION);
session_start();
//error_reporting(0);
require_once 'connection.php';
include 'root_url.php';

if ($_SESSION['user_id'] == "") {
    header("Location: $root_url/index.php", true, 302);
    exit();
}

if ($_SESSION['status'] != "BOSS") {
    header("Location: $root_url/_login_check.php", true, 302);
}
?>

<?php

echo '<br/>';
echo 'SESSION = ';
print_r($_SESSION);
echo '<br/>loginResult =<br/>';
print_r($loginResult);
echo '<br/>POST = <br/>';
print_r($_POST);
?>

<?php

foreach ($_POST['date'] as $date) {
    $addKnownQry = mysqli_query($connection, "UPDATE `item_add_record`"
            . " SET `add_known` = '" . $_SESSION['name'] . "'"
            . " WHERE `add_date` LIKE '" . $date . "' ");
    $takeKnownQry = mysqli_query($connection, "UPDATE `item_take_record`"
            . " SET `take_known` = '" . $_SESSION['name'] . "'"
            . " WHERE `take_date` LIKE '" . $date . "' ");
    if ($date == 'otherDate') {
        $otherAddKnownQry = mysqli_query($connection, "UPDATE `item_add_record`"
                . " SET `add_known` = '" . $_SESSION['name'] . "'"
                . " WHERE `add_date` < '" . $_SESSION['otherDate'] . "'");
        $otherTakeKnownQry = mysqli_query($connection, "UPDATE `item_take_record`"
                . " SET `take_known` = '" . $_SESSION['name'] . "'"
                . " WHERE `take_date` <'" . $_SESSION['otherDate'] . "'");
    }
}


header("Location: $root_url/_login_boss.php", true, 302);
?>