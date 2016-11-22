<?php

session_start();
if ($_SESSION['user_id'] == "") {
    echo "โปรดยืนยันตัวตนก่อน !";
    exit();
}

echo '<br/>';
echo 'SESSION = ';
print_r($_SESSION);
echo '<br/>loginResult =<br/>';
print_r($loginResult);
echo '<br/>POST = <br/>';
print_r($_POST);
?>

<?php

$item_header = array(
    array('รายละเอียด', 'detail', 'iDetail'),
    array('จำนวน', 'quantity', 'iQuantity'),
    array('หน่วย', 'suffix', 'iSuffix'),
);
$item_count = count($item_header);

/* สร้าง query statement ของการแก้ TABLE:item */
$update_statement = "UPDATE `item` SET "; /* ประโยค query statement ตั้งต้น */

//นับก่อนว่ามีการเปลี่ยนแปลงไหม 
$editAr1 = array('detail', 'quantity', 'suffix');
$editAr2 = array('iDetail', 'iQuantity', 'iSuffix');
$changeCount = 0;
for ($i = 0; $i < 3; $i++) {
    if ($_SESSION[$editAr1[$i]] != $_POST[$editAr2[$i]]) {
        echo "<br>" . $_SESSION[$editAr1[$i]] . "!=" . $_POST[$editAr2[$i]] . "<br/>";
        $changeCount++;
    }
}

include 'root_url.php';
$update_url = $root_url."/_login_user.php";

if ($changeCount == 0) { // เคสไม่ได้แก้ค่าแม้แต่ช่องเดียว แต่กดปุ่ม submit ... ให้redirect กลับหน้าเดิมทันที 
    header("location: $update_url");
} else { //มีการแก้ค่าช่องใดช่องหนึ่ง
    $check_empstr = 0; /* ตัวแปรนับจำนวน $_POST เพื่อเติม "comma(,)" คั่นระหว่างข้อความให้ถูกต้อง */
    for ($i = 0; $i < $item_count; $i++) {
        /* ดูเฉพาะช่อง $_POST ที่ผู้ใช้แก้ค่า */
        if ($_POST[$item_header[$i][2]] != "") {
            if ($check_empstr == 0) { //กรณีที่เป็นช่องแรกที่ค้นเจอ ต้องเข้าเคสนี้เพื่อการเติมcomma(,) ให้ถูกตำแหน่ง
                //echo $check_empstr . 'case if <br>';
                $check_empstr++;
                $update_statement .= $item_header[$i][1] . "='" . mysql_real_escape_string($_POST[$item_header[$i][2]]) . "'";
            } else { //กรณีมีการเปลี่ยนแปลงมากกว่า 1 ช่อง
                //echo $check_empstr . 'case else<br>';
                $update_statement .= "," . $item_header[$i][1] . "='" . mysql_real_escape_string($_POST[$item_header[$i][2]]) . "'";
            }
        }
    }
    $update_statement .= " WHERE `iid`='" . $_SESSION['iid'] . "'";

//ทำงานกับ $editAr1 และ $editAr2
    $editRS1 = "INSERT INTO `item_edit_record` (";
    $editRS2 = " VALUES (";
//เช็ค ถ้ามีการเปลี่ยนแปลง ($changeCount > 0) ทำต่อ
    $editRS1 .= "`detail`";
    $editRS1 .= ",`edit_detail`";
    if ($_SESSION['detail'] != $_POST['iDetail']) { //detail เปลี่ยน
        $editRS2 .= "'" . $_SESSION['detail'] . "'";
        $editRS2 .= ",'" . $_POST['iDetail'] . "'";
    } else { //detail ไม่เปลี่ยน
        $editRS2 .= "'" . $_SESSION['detail'] . "'";
        $editRS2 .= ",''";
    }
    $editRS1 .= ",`qty`";
    $editRS1 .= ",`edit_qty`";
    if ($_SESSION['quantity'] != $_POST['iQuantity']) { //quantity เปลี่ยน
        $editRS2 .= ",'" . $_SESSION['quantity'] . "'";
        $editRS2 .= ",'" . $_POST['iQuantity'] . "'";
    } else { //quantity ไม่เปลี่ยน
        $editRS2 .= ",''";
        $editRS2 .= ",''";
    }
    $editRS1 .= ",`suffix`";
    $editRS1 .= ",`edit_suffix`";
    if ($_SESSION['suffix'] != $_POST['iSuffix']) { //suffix เปลี่ยน
        $editRS2 .= ",'" . $_SESSION['suffix'] . "'";
        $editRS2 .= ",'" . $_POST['iSuffix'] . "'";
    } else { //quantity ไม่เปลี่ยน
        $editRS2 .= ",''";
        $editRS2 .= ",''";
    }
    $editRS1 .= ",`edit_date`,`edit_time`,`editor`,`note`";
    $editRS1 .= ")";

    date_default_timezone_set("Asia/Bangkok");
    $editRS2 .= ",'" . date('Y-m-d') . "'";
    $editRS2 .= ",'" . date("H:i") . "'";
    $editRS2 .= ",'" . $_SESSION['name'] . "'";
    $editRS2 .= ",'" . $_POST['iNote'] . "'";
    $editRS2 .= ");";
}
$editRS = $editRS1 . $editRS2;

echo '<br/>update_statement= ' . $update_statement . '<br/>';
echo '<br/>editRecordStatement(editRS)= ' . $editRS . '<br/>';

include 'connection.php';
$updateQuery = mysqli_query($connection, $update_statement) or die("<br/>update_statement query ล้มเหลว!");
mysqli_query($connection, $editRS) or die("<br/>editRS query ล้มเหลว!");
//echo 'update_statement= '. $update_statement . '<br/>';

/* เมื่อทำการ update query เรียบร้อย redirect กลับหน้าเดิม */
header("location: $update_url");
?>
