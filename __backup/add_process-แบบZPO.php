<?php

//var_dump($_SESSION);
session_start();
require_once 'connection.php';

include 'root_url.php';
if ($_SESSION['user_id'] == "") {
    header("Location: $root_url/index.php", true, 302);
    exit();
}
if ($_SESSION['status'] != "KEY") {
    header("Location: $root_url/index.php", true, 302);
}

$_SESSION['addMsg'] = array();
?>


<?php

echo "<pre>";
echo '<br/>';
echo 'SESSION = ';
print_r($_SESSION);
echo '<br/>POST = <br/>';
print_r($_POST);
echo "</pre>";
?>


<?php

//เช็ค varDetail ว่ามีอยู่ใน list key_item หรือไม่
$detailOK = 0;
$suffixOK = 0;
$keyQry = mysqli_query($connection, "SELECT `key_detail`,`key_suffix` FROM `key_item`");
while ($rowKey = mysqli_fetch_assoc($keyQry)) {
    foreach ($_POST['varDetail'] as $vd) {
        if ($rowKey['key_detail'] == $vd) {
            $detailOK = 1;
            break;
        }
    }
    foreach ($_POST['var_lastSuffix'] as $vls) {
        if ($rowKey['key_suffix'] == $vls) {
            $suffixOK = 1;
            break;
        }
    }
}
/*
echo "ok=".$detailOK;
echo $suffixOK;
*/
if($detailOK != 1 || $suffixOK != 1) {
    array_push($_SESSION['addMsg'], "<p style='color:red;'>กรุณาเลือกชื่อเครื่องมือเครื่องใช้จากในลิสต์เท่านั้น</p>");
    header("Location: $root_url/add.php", true, 302);
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
    echo "<br/>ไม่ได้เลือกไฟล์เพื่ออัปโหลด";
    array_push($_SESSION['addMsg'], "ไม่พบไฟล์สลิป...");
    $uploadOk = 0;
} else { //กรณีมีไฟล์ถูกอัปโหลด ให้มีไฟล์ก่อนถึงค่อยสร้าง full path ไม่งั้นถึงไม่มีไฟล์ก็สร้าง จำทำให้มีแต่ชื่อไม่มีไฟล์
    //สร้างfullpath โดย basenameคือแสดงชื่อไฟล์แบบมีนามสกุลด้วย
    date_default_timezone_set("Asia/Bangkok"); //set default timezone
    $target_file = $target_dir . "n" . $_SESSION['user_id'] . "_" . date('Y-m-d') . "_" . date('His') . "." . pathinfo($_FILES['fileToUpload']['name'], PATHINFO_EXTENSION);
}

//เก็บนามสกุลไฟล์(extension)แบบไม่มีจุดนำหน้า
$imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);

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
    echo "มีไฟล์ชื่อซํ้ากันอยู่แล้ว<br/>";
    $uploadOk = 0;
} else {
    echo "ชื่อไฟล์ไม่ซํ้า ...OK!<br/>";
}

// Check file size
$maxFileSize = 10485760; //10485760=10MiB
if ($_FILES["fileToUpload"]["size"] > $maxFileSize) {
    echo "ไม่สามารถอัปโหลดไฟล์ที่มีขนาดเกิน&nbsp;" . $maxFileSize / 1048576 . " MB ได้<br/>";
    $uploadOk = 0;
} else {
    echo "ขนาดไฟล์ไม่เกิน" . $maxFileSize / 1048576 . " MB ...OK!<br/>";
}

// เช็คนามสกุลไฟล์
$allowedExts = array("pdf", "doc", "docx", "jpg", "jpeg", "png", "gif");
if ($imageFileType != "pdf" && $imageFileType != "doc" && $imageFileType != "docx" && $imageFileType != "jpg" && $imageFileType != "jpeg" && $imageFileType != "png" && $imageFileType != "gif") {
    echo "นามสกุลไฟล์ไม่ถูกต้อง กรุณาอัปโหลด pdf, doc, docx, jpg, jpeg, png, gif เท่านั้น<br/>";
    $uploadOk = 0;
} else {
    echo "นามสกุลไฟล์ถูกต้อง ...OK!<br/>";
}

// โค้ดย้ายไฟล์เข้าserver อยู่ข้างล่างการ query
?>






<?php

/* นับจำนวนแถวว่าต้องทำกี่แถว เพราะไม่รู้ว่า user จะเพิ่มเข้ามากี่แถว */
/* ในกรณีที่ป้อนแบบเว้นบรรทัด ใช้ array_filter ช่วยในการตัดบรรทัดที่ไม่ได้ป้อน */
/* ใช้ array_values ช่วยให้มันเรียง index ใหม่ จาก 0 ถึงตัวหลังสุด */
/* ###### ใช้ได้ในกรณีที่userกรอกทุกบรรทัดติดกัน เท่านั้น ถ้าป้อนแบบเว้นบรรทัดจะขึ้น warning ทันที */
$row_count = count($_POST['varDetail']);




/* บังคับให้ "ชื่อผู้เพิ่มรายการ" เป็นชื่อคนที่ล็อกอินในขณะนั้น */
//$_POST['var_adder'] = $_SESSION['name'];
//filter array เพื่อทำให้แถวที่เป็น empty string ถูกทำให้ null โดยไม่เสียตำแหน่งที่ถูกต้องของ index
$postZDIR = array_filter($_POST['var_zdir']);
$postDetail = array_filter($_POST['varDetail']);
$postSlipSuffix = array_filter($_POST['var_slipSuffix']);
$postQty = array_filter($_POST['var_qty']);
$postUnitPrice = array_filter($_POST['var_unitPrice']);
$postAmount = array_filter($_POST['var_amount']);
$postLastSuffix = array_filter($_POST['var_lastSuffix']);
$postLastQty = array_filter($_POST['var_lastQty']);


//เช็ค `item` ว่ามีชื่อเดียวกันแต่ต่างกลุ่มงานอยู่หรือไม่
$itemCheckQS = "SELECT `detail`,`owner` FROM `item`"; //ได้เคส itemซํ้า แต่ กลุ่มงานต่าง
$itemCheckQry = mysqli_query($connection, $itemCheckQS);
$itemDuplicate = "";
$addDuplicate = true;
for ($i = 0; $i < $row_count; $i++) {
    while ($rowItemCheck = mysqli_fetch_assoc($itemCheckQry)) {
        echo '<br>rowItemCheck=';
        print_r($rowItemCheck);
        if ($rowItemCheck['detail'] == $_POST['varDetail'][$i]) {
            echo '<br>ENTER big if';
            if ($rowItemCheck['owner'] != $_SESSION['division']) {
                //$itemDuplicate .= $rowItemCheck['detail'];
                $addDuplicate = false;
            } else {
                $addDuplicate = true;
                break; // <-เจออันที่addปกติแล้วไม่ต้องเช็คต่อ รีบออกเลย
            }
        }
    }
}



//การสร้างประโยค query
$addItemStatement = ""; /* บันทึกลงใน table: item_slip */
$item_add_record_statement = ""; /* บันทึก record ของ table: item_add_record */
$item_statement = "";
// for ของ $row_count (aka. $rc) 
include 'item_headerInfo.php';
for ($rc = 0; $rc < $row_count; $rc++) { /* 1 $rc คือ 1 แถวของรายการใน 1 ใบเสร็จ */
    if ($_POST['varDetail'][$rc] != "") { /* เช็คที่ detail เพราะมันเป็น primary key */
        $addItemStatement .= "INSERT INTO `item_slip` (`zpo`,`zdir`,`slip_date`,`detail`,`slip_suffix`,`qty`,`unit_price`,`amount`,`sub_total`,`grand_total`,`adder`)";
        $item_add_record_statement .= "INSERT INTO `item_add_record` (`add_detail`,`add_suffix`,`add_qty`,`add_date`,`add_time`,`adder`,`slip`)";
        $item_statement .= "INSERT INTO `item` (`detail`,`suffix`,`quantity`,`owner`)";

        //ใส่ใน TABLE: item_slip
        $addItemStatement .= " VALUES ('" . $_POST['var_zpo'] . "'"; /* zpo */
        $addItemStatement .= ",'" . $postZDIR[$rc] . "'"; /* zdir[] */
        $addItemStatement .= ",'" . $_POST['var_slipDate'] . "'"; /* slip_date */
        $addItemStatement .= ",'" . $postDetail[$rc] . "'"; /* detail[] */
        $addItemStatement .= ",'" . $postSlipSuffix[$rc] . "'"; /* slip_suffix[] */
        $addItemStatement .= ",'" . $postQty[$rc] . "'"; /* qty[] */
        $addItemStatement .= ",'" . $postUnitPrice[$rc] . "'"; /* unit_price[] */
        $addItemStatement .= ",'" . $postAmount[$rc] . "'"; /* amount[] */
        $addItemStatement .= ",'" . $_POST['var_subTotal'] . "'"; /* sub_total */
        $addItemStatement .= ",'" . $_POST['var_grandTotal'] . "'"; /* grand_total */
        $addItemStatement .= ",'" . $_POST['var_adder'] . "'"; /* adder */
        $addItemStatement .= ");";

        //ใส่ใน TABLE: item_add_record
        $item_add_record_statement .= " VALUES ('" . $postDetail[$rc] . "'";
        $item_add_record_statement .= ",'" . $postLastSuffix[$rc] . "'";
        $qtysum = ($_POST['var_qty'][$rc] * $postLastSuffix[$rc]); //ผลคูณของการแปลง slip suffix เป็น item suffix
        $item_add_record_statement .= ",'" . ($_POST['var_qty'][$rc] * $postLastQty[$rc]) . "'";
        date_default_timezone_set("Asia/Bangkok");
        $item_add_record_statement .= ",'" . date('Y-m-d') . "'"; /* ต้องใช้วันที่ปัจจุบัน */
        $item_add_record_statement .= ",'" . date("H:i") . "'"; /* ต้องใช้เวลาปัจจุบัน */
        $item_add_record_statement .= ",'" . $_POST['var_adder'] . "'";
        $item_add_record_statement .= ",'" . $target_file . "'"; //slip
        $item_add_record_statement .= ");";

        /* ปัญหาในกรณีที่ `detail` เดียวกัน แต่คนละกลุ่มงาน จะไป update ซํ้า 
         * SOLUTION: query เอา item มาเช็คก่อน แล้วค่อย insert into    
         *  */


        //ใส่ใน TABLE: item
        $item_statement .= " VALUES ('" . $postDetail[$rc] . "'";
        $item_statement .= ",'" . $postLastSuffix[$rc] . "'";
        $item_statement .= ",'" . ($_POST['var_qty'][$rc] * $postLastQty[$rc]) . "'";
        $item_statement .= ",'" . $_POST['var_adder'] . "'";
        if ($addDuplicate == true) {
            $item_statement .= ") ON DUPLICATE KEY UPDATE `quantity`=`quantity`+" . ($_POST['var_qty'][$rc] * $postLastQty[$rc]) . ";";
        } else {
            $item_statement .= ");";
        }
    }
}

echo '<br>itemduplicate=';
print_r($itemDuplicate);
echo '<br>addDuplicate=';
print_r($addDuplicate);

/* debug section */
//echo "<br/></br>addItemStatement= " . $addItemStatement;
//echo "<br/></br>additem= " . $addItemStatement;
//echo "<br/></br>additem__RECORD= " . $item_add_record_statement;
//echo "<br/><br/>";


/* คิวรี่รวดเดียว เพราะถ้า error จะได้หยุดทั้งหมด (จริงเหรอ)จากที่ทดสอบ พบว่าคิวรี่1-2-3 ถ้าerror2 จะทำให้3ไม่ทำงานจริง แต่1ก็ไม่rollbackให้  */
$fullStatement = "START TRANSACTION;";
$fullStatement .= $addItemStatement;    /* TABLE: item_slip */
$fullStatement .= $item_add_record_statement; /* TABLE: item_add_record */
$fullStatement .= $item_statement; /* TABLE: item */
$fullStatement .= "COMMIT;";

//$item_add จะพิเศษหน่อยตรงที่ มันมี detail เป็น key ทำให้เวลา INSERT INTO ที่เป็ฯ statement ใหญ่ๆ ถ้ามีบางตัวที่ซํ้า มันจะทำให้ตัวอื่นที่ไม่ซํ้า error ไปหมด


echo "<br/>row_count==" . $row_count;
echo "<br/><br/>add item_slip=" . $addItemStatement;
echo "<br/><br/>add item_add_record=" . $item_add_record_statement;
echo "<br/><br/>add item=" . $item_statement;
echo "<br/><br/>fullState=" . $fullStatement;


$fullQry = mysqli_multi_query($connection, $fullStatement) or die("<br/>add_confirm.php/fullStatement FAIL" . mysqli_error($connection));

if ($fullQry) {
    array_push($_SESSION['addMsg'], "เพิ่ม $row_count รายการ ...OK!");
}
?>





<?php

// เช็ค $uploadOK ไม่มี error
if ($uploadOk == 0) {
    echo "ไฟล์ไม่ถูกอัปโหลด";

// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        echo "อัปโหลดไฟล์ " . basename($_FILES["fileToUpload"]["name"]) . " เสร็จสมบูรณ์!";
        array_push($_SESSION['addMsg'], "อัปโหลดไฟล์ ...OK!");
        array_push($_SESSION['addMsg'], "<a href=\"$target_file\" target=\"_blank\">คลิกที่นี่เพื่อตรวจสอบไฟล์</a>");
    } else {
        echo "ไม่พบไฟล์แนบ (Status(" . $uploadOk . "))";
        array_push($_SESSION['addMsg'], "ไม่พบไฟล์แนบ (Status(" . $uploadOk . "))");
    }
}

header("Location: $root_url/add.php", true, 302);
?>


















