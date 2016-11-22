<?php

session_start();

echo '<br/>';
echo 'SESSION = ';
print_r($_SESSION);
echo '<br/>POST = <br/>';
print_r($_POST);
?>


<?php

require 'connection.php';
include 'root_url.php';

//ตอนแรก จะเอา favOK ไว้แช็ค input แต่ตอนนี้ไม่ต้องเช็คแล้ว 
$favOK = true;
$_SESSION['favMsg'] = array();
        
$chkFavCount = count($_POST['check_favlist']);

if ($favOK == true && $chkFavCount != 0) {
    $deleteQry = mysqli_query($connection, "DELETE FROM user_favlist WHERE userID = '" . $_SESSION['user_id'] . "';") or die(mysqli_error($connection));
    foreach ($_POST['check_favlist'] as $val) {
        $favQry = mysqli_query($connection, "INSERT INTO `user_favlist` (`userID`,`itemID`) VALUES ('" . $_SESSION['user_id'] . "',$val);") or die(mysqli_error($connection));
        if ($favQry) {
            array_push($_SESSION['favMsg'], "เพิ่ม $val ...OK!");
        } else {
            array_push($_SESSION['favMsg'], "เพิ่ม $val ...FAIL!");
        }
    }
} elseif($chkFavCount == 0) {
    mysqli_query($connection, "DELETE FROM user_favlist WHERE userID = '" . $_SESSION['user_id'] . "';") or die(mysqli_error($connection));
    array_push($_SESSION['favMsg'], "รายการเฝ้าดูทั้งหมดถูกลบแล้ว");
}

header('Location: ' . $root_url . '/_login_user.php');
?>

