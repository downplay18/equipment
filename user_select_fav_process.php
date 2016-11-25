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

$_SESSION['favMsg'] = array();

$chkFavCount = count($_POST['check_favlist']);

////แนวคิด: ทำลิสต์ เทียบ new|old หาตัวซํ้าที่มีอยู่แล้ว จากนั้นเวลาลบ ลบตัวที่ไม่มีในlistนี้
//$newList = array();
//$oldFavQS = "SELECT * FROM user_favlist WHERE userID = '" . $_SESSION['user_id'] . "'";
//$oldFavQry = mysqli_query($connection, $oldFavQS);
//while ($rowOldFav = mysqli_fetch_assoc($oldFavQry)) {
//    foreach ($_POST['check_favlist'] as $newFavID) {
//        if ($rowOldFav['itemID'] == $newFavID) {
//            array_push($newList, $newFavID);
//        }
//    }
//}
//
////เริ่มลบทุกอันที่ไม่อยู่ในลิสต์
//$deleteQS = "DELETE FROM user_favlist WHERE userID = '" . $_SESSION['user_id'] . "'";
//foreach ($newList as $v) {
//    $deleteQS .= " AND itemID != '$v'";
//}
//$deleteQry = mysqli_query($connection, $deleteQS) or die(mysqli_error($connection));
//
////เปลี่ยนแปลงลิสต์
//$newQS="";
//foreach($_POST['check_favlist'] as $val) {
//    $newQS .= "INSERT INTO user_favlist (userID, itemID) VALUES ('". $_SESSION['user_id'] ."','$val') ON DUPLICATE KEY UPDATE itemID = itemID;";
//}

if ($chkFavCount != 0) {
    $deleteQry = mysqli_query($connection, "DELETE FROM user_favlist WHERE userID = '" . $_SESSION['user_id'] . "';") or die(mysqli_error($connection));
    foreach ($_POST['check_favlist'] as $val) {
        $favQry = mysqli_query($connection, "INSERT INTO `user_favlist` (`userID`,`itemID`) VALUES ('" . $_SESSION['user_id'] . "',$val);") or die(mysqli_error($connection));
        //เก็บ msg แสดงสถานะ
        if ($favQry) {
            array_push($_SESSION['favMsg'], "เพิ่ม $val ...OK!");
        } else {
            array_push($_SESSION['favMsg'], "เพิ่ม $val ...FAIL!");
        }
    }
} elseif ($chkFavCount == 0) {
    mysqli_query($connection, "DELETE FROM user_favlist WHERE userID = '" . $_SESSION['user_id'] . "';") or die(mysqli_error($connection));
    array_push($_SESSION['favMsg'], "รายการเฝ้าดูทั้งหมดถูกลบแล้ว");
}

header('Location: ' . $root_url . '/_login_user.php');
?>

<?php

echo "<pre>";
print_r($newList);
echo "<br/>";
print_r($deleteQS);
echo "<br/>";
print_r($newQS);
echo "<br/>";
echo "</pre>";
?>