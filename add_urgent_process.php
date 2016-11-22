<?php
//var_dump($_SESSION);
session_start();
//error_reporting(0);
require_once 'connection.php';
include 'root_url.php';
date_default_timezone_set("Asia/Bangkok");

if (empty($_SESSION['user_id'])) {
    echo "<p style='font-size:1.5em;color:red;'>กรุณายืนยันตัวตนก่อน!</p>";
    echo "<a href='index.php' style='font-size:1.1em;'> >> คลิกที่นี่เพื่อยืนยันตัวตน << </a>";
    exit();
}
if ($_SESSION['status'] != "KEY") {
    echo "<p style='font-size:1.5em;color:red;'>หน้านี้เฉพาะผู้ดูแลประจำกลุ่มงานเท่านั้น!</p>";
    echo "<a href='index.php' style='font-size:1.1em;'> >> คลิกที่นี่เพื่อยืนยันตัวตน << </a>";
    exit();
}

$_SESSION['addUrgentMsg'] = array();
?>

<head>
    <?php include 'main_head.php'; ?>  
</head>

<?php
if (!isset($_POST['var_lastSuffix'])) {
    array_push($_SESSION['addUrgentMsg'], "<p style='font-size:1.5em;color:red;'>ERROR! เลือกชื่อเครื่องมือเครื่องใช้จากในลิสต์เท่านั้น</p>");
    echo "<p style='font-size:1.5em;color:red;'>ERROR! โปรดเลือกชื่อเครื่องมือเครื่องใช้จากในลิสต์เท่านั้น</p>";
    echo "<p style='font-size:1.25em;color:red;'>กลับไปแก้ไข กด <a href='add_urgent.php' class='btn btn-danger'><span class='glyphicon glyphicon-arrow-left'></span> ที่นี่</a></p>";
    exit();
}
?>


<?php
// เช็คไฟล์ก่อน
//ตั้งfolder สำหรับเก็บไฟล์ที่อัปมา
$target_dir = "slip/";

//ตั้งเป็นdefaultว่าokไว้ก่อน ถ้าเช็คตามเคสแล้วfalse จะโดนเปลี่ยนเป็น 0
$uploadOk = 1;

//เช็คว่าได้เลือกไฟล์ไหม
if (empty($_FILES['fileToUpload']['name'])) {
    echo "ไม่พบไฟล์ใบเสร็จ";
    array_push($_SESSION['addUrgentMsg'], "ไม่พบไฟล์ใบเสร็จ...");
    $uploadOk = 0;
} else { //กรณีมีไฟล์ถูกอัปโหลด ให้มีไฟล์ก่อนถึงค่อยสร้าง full path ไม่งั้นถึงไม่มีไฟล์ก็สร้าง จำทำให้มีแต่ชื่อไม่มีไฟล์
    //สร้างfullpath โดย basenameคือแสดงชื่อไฟล์แบบมีนามสกุลด้วย
    $target_file = $target_dir . "u" . $_SESSION['user_id'] . "_" . date('Y-m-d') . "_" . date('His') . "." . pathinfo($_FILES['fileToUpload']['name'], PATHINFO_EXTENSION);

    //เก็บนามสกุลไฟล์(extension)แบบไม่มีจุดนำหน้า
    $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
}

//ถ้า User เลือกไฟล์ ค่อยทำอันนี้
if ($uploadOk == 1) {

// โค้ดเก่าสำหรับเช็คว่าเป็นรูปภาพหรือเปล่าเท่านั้น
// Check if image file is a actual image or fake image
    /*
      if (isset($_POST["submit"])) {
      $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
      if ($check !== false) {
      echo "File is an image - " . $check["mime"] . ".";
      $uploadOk = 1;
      } else {
      echo "File is not an image.";
      $uploadOk = 0;
      }
      } */

// เช็คไฟล์ซํ้า
    if (file_exists($target_file)) {
        array_push($_SESSION['addUrgentMsg'], "<p style='font-size:1.5em;color:red;'>พบชื่อไฟล์ใบเสร็จซํ้ากัน โปรดติดต่อผู้ดูแลระบบ!</p>");
        echo "<p style='font-size:1.5em;color:red;'>พบชื่อไฟล์ใบเสร็จซํ้ากัน โปรดติดต่อผู้ดูแลระบบ!</p>";
        echo "<p style='font-size:1.25em;color:red;'>กลับไปแก้ไข กด <a class='btn btn-danger' href='add_urgent.php'><span class='glyphicon glyphicon-arrow-left'></span> ที่นี่</a></p>";
        $uploadOk = 0;
    } else {
        echo "ชื่อไฟล์ไม่ซํ้า ...OK!<br/>";
    }

// Check file size
    $maxFileSize = 10485760; //10485760=10MiB
    if ($_FILES["fileToUpload"]["size"] > $maxFileSize) {
        echo "ไม่สามารถอัปโหลดไฟล์ที่มีขนาดเกิน&nbsp;" . $maxFileSize / 1048576 . " MB ได้<br/>";
        array_push($_SESSION['addUrgentMsg'], "<p style='font-size:1.5em;color:red;'>ไม่อนุญาตให้อัปโหลดไฟล์ใบเสร็จที่มีขนาดเกิน 10MB!</p>");
        echo "<p style='font-size:1.5em;color:red;'>ไม่อนุญาตให้อัปโหลดไฟล์ใบเสร็จที่มีขนาดเกิน 10MB!</p>";
        echo "<p style='font-size:1.25em;color:red;'>กลับไปแก้ไข กด <a class='btn btn-danger' href='add_urgent.php'><span class='glyphicon glyphicon-arrow-left'></span> ที่นี่</a></p>";
        $uploadOk = 0;
    } else {
        echo "ขนาดไฟล์ไม่เกิน" . $maxFileSize / 1048576 . " MB ...OK!<br/>";
    }

// เช็คนามสกุลไฟล์
    $allowedExts = array("pdf", "doc", "docx", "jpg", "jpeg", "png", "gif");
    if ($imageFileType != "pdf" && $imageFileType != "doc" && $imageFileType != "docx" && $imageFileType != "jpg" && $imageFileType != "jpeg" && $imageFileType != "png" && $imageFileType != "gif") {
        array_push($_SESSION['addUrgentMsg'], "<p style='font-size:1.5em;color:red;'>นามสกุลไฟล์ไม่ถูกต้อง รองรับเฉพาะ pdf, doc, docx, jpg, jpeg, png, gif เท่านั้น</p>");
        $uploadOk = 0;
    } else {
        echo "นามสกุลไฟล์ถูกต้อง ...OK!<br/>";
    }
}
// โค้ดย้ายไฟล์เข้าserver อยู่ข้างล่างการ query
?>




<?php
/* นับจำนวนแถวว่าต้องทำกี่แถว เพราะไม่รู้ว่า user จะเพิ่มเข้ามากี่แถว */
/* ในกรณีที่ป้อนแบบเว้นบรรทัด ใช้ array_filter ช่วยในการตัดบรรทัดที่ไม่ได้ป้อน */
/* ใช้ array_values ช่วยให้มันเรียง index ใหม่ จาก 0 ถึงตัวหลังสุด */
/* ###### ใช้ได้ในกรณีที่userกรอกทุกบรรทัดติดกัน เท่านั้น ถ้าป้อนแบบเว้นบรรทัดจะขึ้น warning ทันที */
$row_count = count($_POST['varDetail']);

//สร้าง Query Statement ของแต่ละitem
for ($rc = 0; $rc < $row_count; $rc++) {
    $addUrgRecQS = "INSERT INTO `item_urgent_record` (`urg_detail`,`urg_suffix`,`urg_qty`,`urg_unitPrice`,`urg_amount`,`urg_subTotal`,`urg_slipDate`,`urg_addDateTime`,`urg_adder`,`urg_owner`,`urg_purpose`,`urg_site`,`urg_slip`)";
    $addUrgRecQS .= " VALUES (";
    $addUrgRecQS .= "'" . $_POST['varDetail'][$rc] . "'"; //detail
    $addUrgRecQS .= ",'" . $_POST['var_lastSuffix'][$rc] . "'"; //suffix
    $addUrgRecQS .= ",'" . $_POST['var_qty'][$rc] . "'"; //qty
    $addUrgRecQS .= ",'" . $_POST['var_unitPrice'][$rc] . "'"; //unit_price
    $addUrgRecQS .= ",'" . $_POST['var_amount'][$rc] . "'"; //amount
    $addUrgRecQS .= ",'" . $_POST['var_subTotal'][$rc] . "'"; //sub_total
    $addUrgRecQS .= ",'" . $_POST['var_slipDate'] . "'"; //slipDate
    //$timezone = date_default_timezone_get();
    //echo "timezone=".$timezone;
    date_default_timezone_set("Asia/Bangkok"); //set default timezone
    $addUrgRecQS .= ",'" . date('Y-m-d H:i') . "'"; //addDateTime
    $addUrgRecQS .= ",'" . $_SESSION['division'] . "'"; //กลุ่มงานที่คีย์
    $addUrgRecQS .= ",'" . $_POST['var_owner'] . "'"; //กลุ่มงานที่เป็นเจ้าของจริง
    $addUrgRecQS .= ",'" . $_POST['var_purpose'] . "'"; //purpose
    $addUrgRecQS .= ",'" . $_POST['var_site'] . "'"; //adder
    $addUrgRecQS .= ",'" . $target_file . "'"; //slip
    $addUrgRecQS .= ");";

    //
    //Query
    $addUrgRecQry = mysqli_query($connection, $addUrgRecQS) or die("คิวรี่ครั้งที่ $rc ล้มเหลว!: " . $mysqli_error($connection));

    //เก็บเป็น Alert ให้userดูอีกที
    $userAlert = $rc + 1;
    if (mysqli_affected_rows($connection) != 0) {
        array_push($_SESSION['addUrgentMsg'], "เพิ่มรายการด่วน#$userAlert ...OK!");
    }
}

if ($addUrgRecQry) {
    array_push($_SESSION['addUrgentMsg'], "<p style='font-size:1.25em;color:blue;'>-- เพิ่ม $row_count รายการเสร็จสิ้น --</p>");
}
?>

<?php
// if everything is ok, try to upload file
if ($uploadOk == 1 && move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
    array_push($_SESSION['addUrgentMsg'], "อัปโหลดไฟล์ " . basename($_FILES["fileToUpload"]["name"]) . " เสร็จสมบูรณ์!");
    array_push($_SESSION['addUrgentMsg'], "<a href=\"$target_file\" target=\"_blank\" style='font-size:1.25em;color:blue;'>คลิกที่นี่เพื่อดูใบเสร็จ</a>");
} else {
    array_push($_SESSION['addUrgentMsg'], "ไม่มีการอัปโหลดใบเสร็จ(" . $uploadOk . ")");
}
?>


<div class="container-fluid">
    <?php
    foreach ($_SESSION['addUrgentMsg'] as $msg) {
        echo "<br/>" . $msg;
    }
    ?>
    <br/>
    <a href='add_urgent.php' class='btn btn-danger btn-lg' style="margin:1em"><span class='glyphicon glyphicon-arrow-left'></span> กลับ</a>
</div>


<?php /*
echo "<pre>";
echo '<br/>';
echo 'SESSION = ';
print_r($_SESSION);
echo '<br/>POST = <br/>';
print_r($_POST);
echo "</pre>";

echo '<br>itemduplicate=';
print_r($itemDuplicate);
echo '<br>addDuplicate=';
print_r($addDuplicate);

echo "<br/></br>UrgentQS= " . $addUrgRecQS;
echo "<br/><br/>"; */
?>