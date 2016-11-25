<?php
//var_dump($_SESSION);
session_start();
error_reporting(0);
require_once 'connection.php';
include 'root_url.php';

if ($_SESSION['user_id'] == "") {
    header("Location: $root_url/index.php", true, 302);
    exit();
}

$_SESSION['lastDiv'] = $_POST['divName'];
$_SESSION['pMsg'] = array();
?>

<?php
//หาวันแรก กับวันสุดท้ายก่อน
$daterange = explode(" - ", $_POST['daterange']);

//เพื่อ plugin
if (isset($_POST['submitBtn'])) {
    $_SESSION['startDate'] = $daterange[0];
    $_SESSION['endDate'] = $daterange[1];
} else {
    $_SESSION['startDate'] = date("d/m/Y");
    $_SESSION['endDate'] = date("d/m/Y");
    if (isset($_POST['resetBtn'])) {
        $_SESSION['lastDiv'] = "-- แยกตามกลุ่มงาน --";
    }
}

$daterange[0] = str_replace("/", "-", $daterange[0]);
$daterange[1] = str_replace("/", "-", $daterange[1]);
$startDate = date('Y-m-d', strtotime($daterange[0]));
$endDate = date('Y-m-d', strtotime($daterange[1]));

//ยอดยกมา***
$minDate = '2015-01-01'; //เป็นวันแรกที่เริ่มเอา record เข้าระบบ
$hisDate = date('Y-m-d', strtotime("$startDate -1 day"));
/*
  echo "<pre>";
  echo "startDate=";
  print_r($startDate);
  echo "<br>";
  echo "endDate=";
  print_r($endDate);
  echo "<br>";
  echo "minDate=";
  print_r($minDate);
  echo "<br>";
  echo "hisDate=";
  print_r($hisDate);
  echo "<br>";
  echo "testf*kingDate=";
  print_r(date("Y-01-01"));
  echo "<br>";
  echo "</pre>"; */

//array_push($_SESSION['pMsg'], "SUBMIT SET");
//คิวรี่แยกตามตึก
$buildingArray = array();
$buildingQuery = mysqli_query($connection, "SELECT buildingID, listBuilding FROM list_building ORDER BY buildingID ASC");
while ($row = mysqli_fetch_assoc($buildingQuery)) {
    array_push($buildingArray, array('buildingID' => $row["buildingID"], 'listBuilding' => $row["listBuilding"]));
}
/*
  $test = "";
  foreach ($buildingArray as $v) {
  $test .= "IFNULL(sumB" . $v['buildingID'] . "TakeQty,0) AS b" . $v['buildingID'] . ",";
  }
  echo "<pre>";
  print_r($test);
  echo "</pre>"; */

if (empty($_POST['submitBtn']) || isset($_POST['resetBtn'])) {
    //เคสกดเข้ามา รายงาน ครั้งแรก ==> แสดงทั้งหมด
    $allDetailQS = "SELECT iar.add_detail, add_suffix"
            . ", IFNULL(NULL,0) AS historical"
            . ", IFNULL(periodAddQty, 0) AS periodAddQty"
            . ", IFNULL(periodAddQty, 0) AS sumAddQty,";
    foreach ($buildingArray as $v) {
        $allDetailQS .= "IFNULL(sumB" . $v['buildingID'] . "TakeQty,0) AS b" . $v['buildingID'] . ",";
    }
    $allDetailQS .= " IFNULL(sumTakeQty,0) AS sumTakeQty
        , IFNULL( (IFNULL(periodAddQty, 0) - IFNULL(sumTakeQty, 0)),0) AS remaining
        , owner
FROM item_add_record AS iar";

    $allDetailQS .= " LEFT OUTER JOIN 
(
    SELECT add_detail,  SUM( add_qty ) AS periodAddQty , owner AS allAdder
    FROM item_add_record
    GROUP BY add_detail ,allAdder 
) AS item_add 
ON item_add.add_detail = iar.add_detail 
AND item_add.allAdder=iar.owner

LEFT OUTER JOIN 			
(		
    SELECT take_detail AS add_detail, SUM(take_qty) AS sumTakeQty, taker AS allAdder
    FROM item_take_record
    GROUP BY add_detail ,allAdder 	
) AS item_take 
ON item_take.add_detail = iar.add_detail 
AND item_take.allAdder=iar.owner	

LEFT OUTER JOIN 
(
    SELECT add_detail,  SUM( add_qty ) AS sumHisAddQty , owner AS allAdder
    FROM item_add_record
    GROUP BY add_detail ,allAdder 
) AS his_item_add 
ON his_item_add.add_detail = iar.add_detail 
AND his_item_add.allAdder=iar.owner

LEFT OUTER JOIN 			
(		
    SELECT take_detail AS add_detail, SUM(take_qty) AS sumHisTakeQty, taker AS allAdder
    FROM item_take_record
    GROUP BY add_detail ,allAdder 	
) AS his_item_take 
ON his_item_take.add_detail = iar.add_detail 
AND his_item_take.allAdder=iar.owner";

//สร้างส่วนรายชื่อตึก
    foreach ($buildingArray as $v) {
        $allDetailQS .= " LEFT OUTER JOIN
(
    SELECT take_detail AS add_detail, SUM(take_qty) AS sumB" . $v['buildingID'] . "TakeQty, taker AS allAdder, site
    FROM item_take_record
    WHERE (take_date BETWEEN '$startDate' AND '$endDate')
    GROUP BY add_detail ,allAdder, site 	
) AS b" . $v['buildingID'] . "
ON b" . $v['buildingID'] . ".add_detail = iar.add_detail 
AND b" . $v['buildingID'] . ".allAdder=iar.owner
AND b" . $v['buildingID'] . ".site = '" . $v['listBuilding'] . "'";
    }
    $allDetailQS .= " GROUP BY iar.add_detail,owner";
} elseif (isset($_POST['submitBtn'])) {
    //เคส... ใส่วันที่ + เลือกแยกตามกลุ่มงาน
    $tmpLastDiv = $_SESSION['lastDiv'];

    $allDetailQS = "SELECT iar.add_detail, add_suffix,IFNULL(sumHisAddQty,0) - IFNULL(sumHisTakeQty,0) AS historical"
            . ", IFNULL(periodAddQty, 0) AS periodAddQty"
            . ", (IFNULL(sumHisAddQty,0) - IFNULL(sumHisTakeQty,0)) + IFNULL(periodAddQty, 0) AS sumAddQty,";
    foreach ($buildingArray as $v) {
        $allDetailQS .= "IFNULL(sumB" . $v['buildingID'] . "TakeQty,0) AS b" . $v['buildingID'] . ",";
    }
    $allDetailQS .= " IFNULL(sumTakeQty,0) AS sumTakeQty, IFNULL( ( (IFNULL(sumHisAddQty,0) - IFNULL(sumHisTakeQty,0)) + IFNULL(periodAddQty, 0)) - IFNULL(sumTakeQty, 0),0) AS remaining, owner
FROM item_add_record AS iar";

    $allDetailQS .= " LEFT OUTER JOIN 
(
    SELECT add_detail,  SUM( add_qty ) AS periodAddQty , owner AS allAdder
    FROM item_add_record
    WHERE (slip_date BETWEEN '$startDate' AND '$endDate')
    GROUP BY add_detail ,allAdder 
) AS item_add 
ON item_add.add_detail = iar.add_detail 
AND item_add.allAdder=iar.owner

LEFT OUTER JOIN 			
(		
    SELECT take_detail AS add_detail, SUM(take_qty) AS sumTakeQty, taker AS allAdder
    FROM item_take_record
    WHERE (take_date BETWEEN '$startDate' AND '$endDate')
    GROUP BY add_detail ,allAdder 	
) AS item_take 
ON item_take.add_detail = iar.add_detail 
AND item_take.allAdder=iar.owner	

LEFT OUTER JOIN 
(
    SELECT add_detail,  SUM( add_qty ) AS sumHisAddQty , owner AS allAdder
    FROM item_add_record
    WHERE (slip_date BETWEEN '$minDate' AND '$hisDate')
    GROUP BY add_detail ,allAdder 
) AS his_item_add 
ON his_item_add.add_detail = iar.add_detail 
AND his_item_add.allAdder=iar.owner

LEFT OUTER JOIN 			
(		
    SELECT take_detail AS add_detail, SUM(take_qty) AS sumHisTakeQty, taker AS allAdder
    FROM item_take_record
    WHERE (take_date BETWEEN '$minDate' AND '$hisDate')
    GROUP BY add_detail ,allAdder 	
) AS his_item_take 
ON his_item_take.add_detail = iar.add_detail 
AND his_item_take.allAdder=iar.owner";

//สร้างส่วนรายชื่อตึก
    foreach ($buildingArray as $v) {
        $allDetailQS .= " LEFT OUTER JOIN
(
    SELECT take_detail AS add_detail, SUM(take_qty) AS sumB" . $v['buildingID'] . "TakeQty, taker AS allAdder, site
    FROM item_take_record
    WHERE (take_date BETWEEN '$startDate' AND '$endDate')
    GROUP BY add_detail ,allAdder, site	
) AS b" . $v['buildingID'] . "
ON b" . $v['buildingID'] . ".add_detail = iar.add_detail 
AND b" . $v['buildingID'] . ".allAdder=iar.owner
AND b" . $v['buildingID'] . ".site = '" . $v['listBuilding'] . "'";
    }

    $allDetailQS .= " WHERE owner LIKE '$tmpLastDiv' GROUP BY iar.add_detail,owner";
}








//สร้างหัวตาราง
$allDetailHeader = array();
array_push($allDetailHeader, "ลำดับ", "รายการ", "หน่วยนับ", "ยอดยกมา", "รับจำนวน", "รวมยอด");
foreach ($buildingArray as $v) {
    array_push($allDetailHeader, $v['listBuilding']);
}
array_push($allDetailHeader, "รวมจ่าย", "คงเหลือ", "เจ้าของ");
?>

<html>
    <head>

        <title>ADMIN</title>
        <?php include 'main_head.php'; ?>    
        <link href="bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet" type="text/css"/>
    </head>

    <body>
        <?php
        /* navbar */
        /* ไม่ใช้ case unauthen เพราะไม่มีสิทธิ์เข้าหน้านี้อยู่แล้ว */
        include 'navbar.php';

        /*
          echo 'SESSION = ';
          print_r($_SESSION);
          echo '<br/>POST = <br/>';
          print_r($_POST);
          echo '<br/>allDetailQS = <br/>';
          print_r($allDetailQS); */
        ?>

        <div class="row">

            <div class="col-md-2 sidebar">
                <?php include 'sidebar.php'; ?>
            </div>

            <div class="col-md-10">

                <!-- Main container -->
                <div class="container-fluid">

                    <div class="page-header">
                        <h2>รายงานตามช่วงเวลา <small>เลือกกลุ่มงานที่ต้องการ</small></h2>
                    </div>


                    <form action="period_report.php" method="get">



                        <ul>
                            <li>
                                <input type="checkbox" name="mainAll" id="mainAll" value="mainAll">
                                <label for="mainAll" style="font-size: 1.2em;">ทั้งหมด</label>
                                <ul>
                                    <li>
                                        <input type="checkbox" name="main1" id="main1" value="1">
                                        <label for="main1" style="font-size: 1.05em;">กองพัฒนาด้านเทคโนโลยีโรงไฟฟ้าถ่านหินและเหมือง (กพทถ-ห.)</label>
                                        <ul>
                                            <li>
                                                <input type="checkbox" name="sub6" id="sub6" value="6">
                                                <label for="sub6" style="font-size: 1.05em;">งานพัฒนาและบำรุงรักษาระบบ ICT</label>
                                            </li>
                                            <li>
                                                <input type="checkbox" name="sub7" id="sub7" value="7">
                                                <label for="sub7">งานพัฒนาและบำรุงรักษาสิมูเลเตอร์</label>
                                            </li>
                                            <li>
                                                <input type="checkbox" name="sub8" id="sub8" value="8">
                                                <label for="sub8">งานจัดการสำนักงาน</label>
                                            </li>
                                            <li>
                                                <input type="checkbox" name="sub9" id="sub9" value="9">
                                                <label for="sub9">งานพัฒนาเทคโนโลยีสื่อการสอน</label>
                                            </li>
                                            <li>
                                                <input type="checkbox" name="sub10" id="sub10" value="10">
                                                <label for="sub10">งานฝึกอบรมการจัดการและเทคโนโลยีสารสนเทศ</label>
                                            </li>
                                            <li>
                                                <input type="checkbox" name="sub11" id="sub11" value="11">
                                                <label for="sub11">งานจัดการศึกษาและความร่วมมือทางวิชาการ</label>
                                            </li>
                                            <li>
                                                <input type="checkbox" name="sub12" id="sub12" value="12">
                                                <label for="sub12">งานแผนและพัฒนาบุคลากร</label>
                                            </li>
                                            <li>
                                                <input type="checkbox" name="sub13" id="sub13" value="13">
                                                <label for="sub13">งานประเมินผลฝึกอบรม</label>
                                            </li>
                                        </ul>
                                    </li>

                                    <li>
                                        <input type="checkbox" name="main2" value="2">
                                        <label for="main2">แผนกพัฒนาด้านเทคโนโลยีโรงไฟฟ้าถ่านหิน (หพทถ-ห.)</label>
                                    </li>
                                    <li>
                                        <input type="checkbox" name="main3" value="3">
                                        <label for="main3">แผนกพัฒนาด้านเทคโนโลยีการทำเหมือง (หพทม-ห.)</label>
                                    </li>
                                    <li>
                                        <input type="checkbox" name="main4" value="4">
                                        <label for="main4">แผนกพัฒนาด้านเทคโนโลยีเครื่องจักรกล (หพทค-ห.)</label>
                                    </li>
                                    <li>
                                        <input type="checkbox" name="main5" value="5">
                                        <label for="main5">แผนกบริการฝึกอบรม (หบฝม-ห.)</label>

                                        <ul>
                                            <li>
                                                <input type="checkbox" name="sub14" id="sub14" value="14">
                                                <label for="sub14">งานบำรุงรักษาอาคารสถานที่และระบบไฟฟ้า</label>
                                            </li>
                                            <li>
                                                <input type="checkbox" name="sub15" id="sub15" value="15">
                                                <label for="sub15">งานบ้านพักและการรับรอง</label>
                                            </li>
                                            <li>
                                                <input type="checkbox" name="sub16" id="sub16" value="16">
                                                <label for="sub16">งานบริการยานพาหนะ</label>
                                            </li>
                                            <li>
                                                <input type="checkbox" name="sub17" id="sub17" value="17">
                                                <label for="sub17">งานโสตทัศนูปกรณ์</label>
                                            </li>
                                            <li>
                                                <input type="checkbox" name="sub18" id="sub18" value="18">
                                                <label for="sub18">งานบำรุงรักษาบริเวณและเพาะชำ</label>
                                            </li>
                                            <li>
                                                <input type="checkbox" name="sub19" id="sub19" value="19">
                                                <label for="sub19">งานเอกสารและตำราและศิลปกรรม</label>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                        </ul>




                        <span style="margin-left: 10em;">
                            <button class="btn btn-default btn-sm" type="reset" name="resetBtn" value="reset">
                                <span class="glyphicon glyphicon-refresh"></span> รีเซ็ท 
                            </button>

                            <button class="btn btn-success btn-lg" type="submit" name="submitBtn" value="submit">
                                ต่อไป <span class="glyphicon glyphicon-forward"></span>
                            </button>
                        </span>

                    </form>


                </div><!-- Main container -->
            </div> <!-- /.col-md-10 -->

        </div> <!-- /.row -->




        <?php include 'main_script.php'; ?>

        <script>
            $('input[type="checkbox"]').change(function (e) {

                var checked = $(this).prop("checked"),
                        container = $(this).parent(),
                        siblings = container.siblings();

                container.find('input[type="checkbox"]').prop({
                    indeterminate: false,
                    checked: checked
                });

                function checkSiblings(el) {

                    var parent = el.parent().parent(),
                            all = true;

                    el.siblings().each(function () {
                        return all = ($(this).children('input[type="checkbox"]').prop("checked") === checked);
                    });

                    if (all && checked) {

                        parent.children('input[type="checkbox"]').prop({
                            indeterminate: false,
                            checked: checked
                        });

                        checkSiblings(parent);

                    } else if (all && !checked) {

                        parent.children('input[type="checkbox"]').prop("checked", checked);
                        parent.children('input[type="checkbox"]').prop("indeterminate", (parent.find('input[type="checkbox"]:checked').length > 0));
                        checkSiblings(parent);

                    } else {

                        el.parents("li").children('input[type="checkbox"]').prop({
                            indeterminate: true,
                            checked: false
                        });

                    }

                }

                checkSiblings(container);
            });
        </script>

    </body>
</html>
