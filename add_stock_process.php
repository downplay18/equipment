<?php
//var_dump($_SESSION);
session_start();
error_reporting(0);
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

$_SESSION['addStockMsg'] = array();
?>



<head>
    <?php include 'main_head.php'; ?>  
</head>

<?php
if (!isset($_POST['varLastSuffix'])) {
    array_push($_SESSION['addStockMsg'], "<p style='font-size:1.5em;color:red;'>ERROR! เลือกชื่อเครื่องมือเครื่องใช้จากในลิสต์เท่านั้น</p>");
    echo "<p style='font-size:1.5em;color:red;'>ERROR! โปรดเลือกชื่อเครื่องมือเครื่องใช้จากในลิสต์เท่านั้น</p>";
    echo "<p style='font-size:1.25em;color:red;'>กลับไปแก้ไข กด <a class='btn btn-danger' href='add_urgent.php'><span class='glyphicon glyphicon-arrow-left'></span> ที่นี่</a></p>";
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
    array_push($_SESSION['addStockMsg'], "ไม่พบไฟล์ใบเสร็จ...");
    $uploadOk = 0;
} else { //กรณีมีไฟล์ถูกอัปโหลด ให้มีไฟล์ก่อนถึงค่อยสร้าง full path ไม่งั้นถึงไม่มีไฟล์ก็สร้าง จำทำให้มีแต่ชื่อไม่มีไฟล์
    //สร้างfullpath โดย basenameคือแสดงชื่อไฟล์แบบมีนามสกุลด้วย
    $target_file = $target_dir . "s" . $_SESSION['user_id'] . "_" . date('Y-m-d') . "_" . date('His') . "." . pathinfo($_FILES['fileToUpload']['name'], PATHINFO_EXTENSION);

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
        array_push($_SESSION['addStockMsg'], "<p style='font-size:1.5em;color:red;'>พบชื่อไฟล์ใบเสร็จซํ้ากัน โปรดติดต่อผู้ดูแลระบบ!</p>");
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
        array_push($_SESSION['addStockMsg'], "<p style='font-size:1.5em;color:red;'>ไม่อนุญาตให้อัปโหลดไฟล์ใบเสร็จที่มีขนาดเกิน 10MB!</p>");
        echo "<p style='font-size:1.5em;color:red;'>ไม่อนุญาตให้อัปโหลดไฟล์ใบเสร็จที่มีขนาดเกิน 10MB!</p>";
        echo "<p style='font-size:1.25em;color:red;'>กลับไปแก้ไข กด <a class='btn btn-danger' href='add_urgent.php'><span class='glyphicon glyphicon-arrow-left'></span> ที่นี่</a></p>";
        $uploadOk = 0;
    } else {
        echo "ขนาดไฟล์ไม่เกิน" . $maxFileSize / 1048576 . " MB ...OK!<br/>";
    }

// เช็คนามสกุลไฟล์
    $allowedExts = array("pdf", "doc", "docx", "jpg", "jpeg", "png", "gif");
    if ($imageFileType != "pdf" && $imageFileType != "doc" && $imageFileType != "docx" && $imageFileType != "jpg" && $imageFileType != "jpeg" && $imageFileType != "png" && $imageFileType != "gif") {
        array_push($_SESSION['addStockMsg'], "<p style='font-size:1.5em;color:red;'>นามสกุลไฟล์ไม่ถูกต้อง รองรับเฉพาะ pdf, doc, docx, jpg, jpeg, png, gif เท่านั้น</p>");
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




//filter array เพื่อทำให้แถวที่เป็น empty string ถูกทำให้ null โดยไม่เสียตำแหน่งที่ถูกต้องของ index
$postDetail = array_filter($_POST['varDetail']);
$postSlipSuffix = array_filter($_POST['varSlipSuffix']);
$postQty = array_filter($_POST['varQty']);
$postLastSuffix = array_filter($_POST['varLastSuffix']);
$postLastQty = array_filter($_POST['varLastQty']);


//การสร้างประโยค query
$slipQS = ""; /* บันทึกลงใน table: item_slip */
$addRecordQS = ""; /* บันทึก record ของ table: item_add_record */
$itemQS = "";
// for ของ $row_count (aka. $rc) 
include 'item_headerInfo.php';
for ($rc = 0; $rc < $row_count; $rc++) { /* 1 $rc คือ 1 แถวของรายการใน 1 ใบเสร็จ */
    $slipQS .= "INSERT INTO `item_slip` (`zpo`,`slip_date`,`detail`,`slip_suffix`,`qty`,`adder`)";
    $addRecordQS .= "INSERT INTO `item_add_record` (`add_detail`,`add_suffix`,`add_qty`,`add_date`,`add_time`,`adder`,`owner`,`slip`,`slip_date`)";
    $itemQS .= "INSERT INTO `item` (`detail`,`suffix`,`quantity`,`owner`,`kid`)";

    //ใส่ใน TABLE: item_slip
    $slipQS .= " VALUES ('" . $_POST['varZDIR'][$rc] . "'"; /* zpo */
    $slipQS .= ",'" . $_POST['var_slipDate'] . "'"; /* slip_date */
    $slipQS .= ",'" . $postDetail[$rc] . "'"; /* detail[] */
    $slipQS .= ",'" . $postSlipSuffix[$rc] . "'"; /* slip_suffix[] */
    $slipQS .= ",'" . $postQty[$rc] . "'"; /* qty[] */
    $slipQS .= ",'" . $_SESSION['division'] . "'"; /* adder */
    $slipQS .= ");";

    //ใส่ใน TABLE: item_add_record
    $addRecordQS .= " VALUES ('" . $postDetail[$rc] . "'";
    $addRecordQS .= ",'" . $postLastSuffix[$rc] . "'";
    $qtysum = ($_POST['varQty'][$rc] * $postLastSuffix[$rc]); //ผลคูณของการแปลง slip suffix เป็น item suffix
    $addRecordQS .= ",'" . ($_POST['varQty'][$rc] * $postLastQty[$rc]) . "'";
    date_default_timezone_set("Asia/Bangkok");
    $addRecordQS .= ",'" . date('Y-m-d') . "'"; /* ต้องใช้วันที่ปัจจุบัน */
    $addRecordQS .= ",'" . date("H:i") . "'"; /* ต้องใช้เวลาปัจจุบัน */
    $addRecordQS .= ",'" . $_SESSION['division'] . "'";
    $addRecordQS .= ",'" . $_POST['var_owner'] . "'";
    $addRecordQS .= ",'" . $target_file . "'";
    $addRecordQS .= ",'" . $_POST['var_slipDate'] . "'";
    $addRecordQS .= ");";

    /* ปัญหาในกรณีที่ `detail` เดียวกัน แต่คนละกลุ่มงาน จะไป update ซํ้า 
     * SOLUTION: query เอา item มาเช็คก่อน แล้วค่อย insert into    
     *  */


    //ใส่ใน TABLE: item
    $itemQS .= " VALUES ('" . $postDetail[$rc] . "'";
    $itemQS .= ",'" . $postLastSuffix[$rc] . "'";
    $itemQS .= ",'" . ($_POST['varQty'][$rc] * $postLastQty[$rc]) . "'";
    $itemQS .= ",'" . $_POST['var_owner'] . "'";
    $itemQS .= ",'" . $_POST['varKID'][$rc] . "'";
    $itemQS .= ") ON DUPLICATE KEY UPDATE `quantity`=`quantity`+" . ($_POST['varQty'][$rc] * $postLastQty[$rc]) . ";";

    //เก็บเป็น Alert ให้userดูอีกที
    $userAlert = $rc + 1;
    array_push($_SESSION['addStockMsg'], "เพิ่มเบิกคลัง#$userAlert ...OK!");
}


/* คิวรี่รวดเดียว เพราะถ้า error จะได้หยุดทั้งหมด (จริงเหรอ)จากที่ทดสอบ พบว่าคิวรี่1-2-3 ถ้าerror2 จะทำให้3ไม่ทำงานจริง แต่1ก็ไม่rollbackให้  */
$fullStatement = "START TRANSACTION;";
$fullStatement .= $slipQS;    /* TABLE: item_slip */
$fullStatement .= $addRecordQS; /* TABLE: item_add_record */
$fullStatement .= $itemQS; /* TABLE: item */
$fullStatement .= "COMMIT;";

//$item_add จะพิเศษหน่อยตรงที่ มันมี detail เป็น key ทำให้เวลา INSERT INTO ที่เป็ฯ statement ใหญ่ๆ ถ้ามีบางตัวที่ซํ้า มันจะทำให้ตัวอื่นที่ไม่ซํ้า error ไปหมด

//$fullQry = mysqli_multi_query($connection, $fullStatement) or die("<br/>add_confirm.php/fullStatement FAIL" . mysqli_error($connection));


if ($fullQry) {
    array_push($_SESSION['addStockMsg'], "<p style='font-size:1.25em;color:blue;'>-- เพิ่ม $row_count รายการเสร็จสิ้น --</p>");
}
?>





<?php
// if everything is ok, try to upload file
if ($uploadOk == 1 && move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
    array_push($_SESSION['addStockMsg'], "อัปโหลดไฟล์ " . basename($_FILES["fileToUpload"]["name"]) . " เสร็จสมบูรณ์!");
    array_push($_SESSION['addStockMsg'], "<a href=\"$target_file\" target=\"_blank\" style='font-size:1.25em;color:blue;'>คลิกที่นี่เพื่อดูใบเสร็จ</a>");
} else {
    array_push($_SESSION['addStockMsg'], "ไม่มีการอัปโหลดใบเสร็จ(" . $uploadOk . ")");
}
?>


<div class="container-fluid">
    <?php
    foreach ($_SESSION['addStockMsg'] as $msg) {
        echo "<br/>" . $msg;
    }
    ?>
    <br/>
    <a href='add_stock.php' class='btn btn-danger btn-lg' style="margin:1em"><span class='glyphicon glyphicon-arrow-left'></span> กลับ</a>
</div>

<?php 
    //header("Location: $root_url/add_stock.php", true, 302);
?>

<?php 
echo "<pre>";
echo '<br/>';
echo 'SESSION = ';
print_r($_SESSION);
echo '<br/>POST = <br/>';
print_r($_POST);
echo "</pre>";

echo "<br/></br>itemQS= " . $itemQS;
echo "<br/></br>addRecordQS= " . $addRecordQS;
echo "<br/></br>slipQS= " . $slipQS;
echo "<br/></br>rowCount= " . $row_count;
echo "<br/><br/>"; 
?>