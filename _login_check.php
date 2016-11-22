<?php

$lifetime = '30600'; /* หน่วยเป็นวินาที ดังนั้น 3600 = 1 ชั่วโมง ดังนั้น 30600 = 8 ชม. 30 นาที */
session_set_cookie_params($lifetime, "/"); /* ตั้งเวลาให้ session cookie */
session_start();

$_SESSION['loginMsg'] = array();

echo '<br/>';
echo 'SESSION = ';
print_r($_SESSION); /*
  echo '<br/>loginResult =<br/>';
  print_r($loginResult); */
echo '<br/>POST = <br/>';
print_r($_POST);

if (isset($_SESSION['user_id'])) {
    if ($_SESSION["status"] == "BOSS") {
        header("location:_login_boss.php");
    } else {
        header("location:_login_user.php");
    }
} else {
    header("location:index.php");
}



/* =====_login_connection===== */
/* รับ $_POST มาจาก navbar_unauthen.php */
/* $_POST ของ Username = login_cid */
/* $_POST ของ Password = login_pwd */
require("connection.php");

$strSQL = "
SELECT * FROM 
(
SELECT  `user_id` ,  `name` ,  `divisionID` , `password` , `division` ,  `status` 
FROM user
INNER JOIN list_division ON listDivision = division

UNION 

SELECT username, wname, divisionID, password ,wdivision, status
STATUS 
FROM worker
INNER JOIN list_division ON listDivision = wdivision
) as allUser

where user_id = '" . mysql_real_escape_string($_POST["login_cid"]) . "'
and password = '" . mysql_real_escape_string($_POST["login_pwd"]) . "'
    ";

/* ไม่ SELECT `Password` เพราะไม่ได้แสดงค่ามัน */
$loginQuery = mysqli_query($connection, $strSQL) or die("_login_check: " . mysqli_error($connection));
$loginResult = mysqli_fetch_assoc($loginQuery); /* ได้ loginResult เป็นผลลัพธ์ของการ query */
if(empty($loginResult)) {
    array_push($_SESSION['loginMsg'], "<p style='color: red; font-size: 1.5em;'>เข้าสู่ระบบล้มเหลว!<br/> ชื่อผู้ใช้ หรือ รหัสผ่าน ผิดพลาด!</font>");
}
/* =====/_login_connection===== */

/* เช็คว่า Username กับ Password ที่ป้อนเข้ามา มีใน db หรือไม่ */
if (!$loginResult) {
    echo "รหัสพนักงาน หรือ รหัสผ่าน ไม่ถูกต้อง!";
} else {
    $_SESSION["user_id"] = $loginResult["user_id"]; /* user_id เป็น PRIMARY KEY ของ `user` */
    $_SESSION["status"] = $loginResult["status"];
    $_SESSION["name"] = $loginResult["name"];
    $_SESSION["division"] = $loginResult['division'];
    $_SESSION['div_id'] = $loginResult['divisionID'];

    session_write_close();

    if ($loginResult["status"] == "BOSS") {
        header("location:_login_boss.php");
    } elseif ($loginResult["status"] == "USER") {
        header("location:_login_user.php");
    } else {
        header("location:index.php");
    }

    /*
      switch ($loginResult["Status"]) {
      case 'ADMIN':
      header("location:_login_admin.php");
      break;
      case 'BOSS':
      header("location:_login_boss.php");
      break;
      case 'USER':
      header("location:_login_user.php");
      break;
      }
     */
}
?>
