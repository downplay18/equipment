<?php

session_start();
if ($_SESSION['user_id'] == "") {
    echo "โปรดยืนยันตัวตนก่อน !";
    exit();
}
/*
  var_dump($_SESSION);
  echo "<br>"
 */
?>

<?php

/* การเรียกใช้ array([0][0], [0][1], [0][2]) */
$customer_header = array(
    //array('เลขพนักงาน', 'user_id', ''), /*แก้ไขเองไม่ได้*/
    array('ชื่อพนักงาน', 'name', 'lupdate_name'),
    array('รหัสผ่าน', 'password', 'lupdate_pwd'),
    array('สังกัด', 'division', 'lupdate_div'),
    array('ตำแหน่ง', 'rank', 'lupdate_rank'),
    array('ที่อยู่', 'building', 'lupdate_building'),
    array('ห้อง', 'room', 'lupdate_room'),
    array('เบอร์โทรศัพท์โต๊ะทำงาน', 'office_tel', 'lupdate_tel'),
        //array('สถานะ', 'Status', ''), /*แก้ไขเองไม่ได้*/
);
$ch_count = count($customer_header);

/* algorithm การสร้าง query statement */
$update_statement = "UPDATE `user` SET "; /* ประโยค query statement ตั้งต้น */
$check_empstr = 0; /* ตัวแปรนับจำนวน $_POST เพื่อเติ่ม , คั่นระหว่างข้อความให้ถูกต้อง */
for ($i = 0; $i < $ch_count; $i++) {
    /* ดูเฉพาะช่อง $_POST ที่ผู้ใช้แก้ค่า */
    if ($_POST[$customer_header[$i][2]] != "") {
        //echo "case " . $customer_header[$i][1] . "<br>";
        if ($check_empstr == 0) {
            //echo $check_empstr . 'case if <br>';
            $check_empstr++;
            $update_statement .= $customer_header[$i][1] . "='" . mysql_real_escape_string($_POST[$customer_header[$i][2]]) . "'";
        } else {
            //echo $check_empstr . 'case else<br>';
            $update_statement .= "," . $customer_header[$i][1] . "='" . mysql_real_escape_string($_POST[$customer_header[$i][2]]) . "'";
        }
    }
}

require 'root_url.php';
$update_url = "$root_url/index.php";
/* เคสที่ผู้ใช้ไม่ได้แก้ค่าแม้แต่ช่องเดียว แต่กดปุ่ม submit */
if ($check_empstr == 0) {
    header("location: $root_url/_login_update.php"); /* redirect มาหน้าเดิม ใน tab เดิม */
    exit(0);
}

$update_statement .= " WHERE `user_id`='" . $_SESSION['user_id'] . "'";
echo $update_statement . '<br/>';

include 'connection.php';
$updateQuery = mysqli_query($connection, $update_statement) or die("_login_update_process.php query ล้มเหลว!");

/* เมื่อทำการ update query เรียบร้อย redirect กลับหน้าเดิม */
header("location: $root_url/_login_update.php");
?>
