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
        , adder
FROM item_add_record AS iar";

    $allDetailQS .= " LEFT OUTER JOIN 
(
    SELECT add_detail,  SUM( add_qty ) AS periodAddQty , adder AS allAdder
    FROM item_add_record
    GROUP BY add_detail ,allAdder 
) AS item_add 
ON item_add.add_detail = iar.add_detail 
AND item_add.allAdder=iar.adder

LEFT OUTER JOIN 			
(		
    SELECT take_detail AS add_detail, SUM(take_qty) AS sumTakeQty, taker AS allAdder
    FROM item_take_record
    GROUP BY add_detail ,allAdder 	
) AS item_take 
ON item_take.add_detail = iar.add_detail 
AND item_take.allAdder=iar.adder	

LEFT OUTER JOIN 
(
    SELECT add_detail,  SUM( add_qty ) AS sumHisAddQty , adder AS allAdder
    FROM item_add_record
    GROUP BY add_detail ,allAdder 
) AS his_item_add 
ON his_item_add.add_detail = iar.add_detail 
AND his_item_add.allAdder=iar.adder

LEFT OUTER JOIN 			
(		
    SELECT take_detail AS add_detail, SUM(take_qty) AS sumHisTakeQty, taker AS allAdder
    FROM item_take_record
    GROUP BY add_detail ,allAdder 	
) AS his_item_take 
ON his_item_take.add_detail = iar.add_detail 
AND his_item_take.allAdder=iar.adder";

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
AND b" . $v['buildingID'] . ".allAdder=iar.adder
AND b" . $v['buildingID'] . ".site = '" . $v['listBuilding'] . "'";
    }
    $allDetailQS .= " GROUP BY iar.add_detail,adder";
    
    
    
} elseif (isset($_POST['submitBtn'])) {
    //เคส... ใส่วันที่ + เลือกแยกตามกลุ่มงาน
    $tmpLastDiv = $_SESSION['lastDiv'];

    $allDetailQS = "SELECT iar.add_detail, add_suffix,IFNULL(sumHisAddQty,0) - IFNULL(sumHisTakeQty,0) AS historical"
            . ", IFNULL(periodAddQty, 0) AS periodAddQty"
            . ", (IFNULL(sumHisAddQty,0) - IFNULL(sumHisTakeQty,0)) + IFNULL(periodAddQty, 0) AS sumAddQty,";
    foreach ($buildingArray as $v) {
        $allDetailQS .= "IFNULL(sumB" . $v['buildingID'] . "TakeQty,0) AS b" . $v['buildingID'] . ",";
    }
    $allDetailQS .= " IFNULL(sumTakeQty,0) AS sumTakeQty, IFNULL( ( (IFNULL(sumHisAddQty,0) - IFNULL(sumHisTakeQty,0)) + IFNULL(periodAddQty, 0)) - IFNULL(sumTakeQty, 0),0) AS remaining, adder
FROM item_add_record AS iar";



    $allDetailQS .= " LEFT OUTER JOIN 
(
    SELECT add_detail,  SUM( add_qty ) AS periodAddQty , adder AS allAdder
    FROM item_add_record
    WHERE (slip_date BETWEEN '$startDate' AND '$endDate')
    GROUP BY add_detail ,allAdder 
) AS item_add 
ON item_add.add_detail = iar.add_detail 
AND item_add.allAdder=iar.adder

LEFT OUTER JOIN 			
(		
    SELECT take_detail AS add_detail, SUM(take_qty) AS sumTakeQty, taker AS allAdder
    FROM item_take_record
    WHERE (take_date BETWEEN '$startDate' AND '$endDate')
    GROUP BY add_detail ,allAdder 	
) AS item_take 
ON item_take.add_detail = iar.add_detail 
AND item_take.allAdder=iar.adder	

LEFT OUTER JOIN 
(
    SELECT add_detail,  SUM( add_qty ) AS sumHisAddQty , adder AS allAdder
    FROM item_add_record
    WHERE (slip_date BETWEEN '$minDate' AND '$hisDate')
    GROUP BY add_detail ,allAdder 
) AS his_item_add 
ON his_item_add.add_detail = iar.add_detail 
AND his_item_add.allAdder=iar.adder

LEFT OUTER JOIN 			
(		
    SELECT take_detail AS add_detail, SUM(take_qty) AS sumHisTakeQty, taker AS allAdder
    FROM item_take_record
    WHERE (take_date BETWEEN '$minDate' AND '$hisDate')
    GROUP BY add_detail ,allAdder 	
) AS his_item_take 
ON his_item_take.add_detail = iar.add_detail 
AND his_item_take.allAdder=iar.adder";

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
AND b" . $v['buildingID'] . ".allAdder=iar.adder
AND b" . $v['buildingID'] . ".site = '" . $v['listBuilding'] . "'";
    }

    $allDetailQS .= " WHERE adder LIKE '$tmpLastDiv' GROUP BY iar.add_detail,adder";
}



//print_r($allDetailQS);




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
          print_r($_POST); */
        ?>

        <div class="row">

            <div class="col-md-2 sidebar">
                <?php include 'sidebar.php'; ?>
                <!--
                <div class = "alert alert-info"> 
                    <span class = "label label-warning">INFO</span> หากช่วงเวลาที่เลือก ไม่มีการเพิ่ม/ถอน รายการนั้นๆเลย จะไม่มีรายชื่อแสดง<br/>
                </div> 
                -->
            </div>

            <div class="col-md-10">

                <!-- Main container -->
                <div class="container-fluid">

                    <div class="page-header">
                        <h2>รายงานตามช่วงเวลา <small>เลือกช่วงเวลาที่ต้องการ</small></h2>
                    </div>


                    <div class="col-md-12">
                        <form action="" method="post">

                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                    <input id="datetimepicker" class="pull-right" type="text" name="daterange" 
                                           style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%" 
                                           autocomplete="off">
                                </div>
                            </div>


                            <div class="col-md-3">
                                <select id="selDiv" class="form-control" name="divName">
                                    <option value="%">-- แยกตามกลุ่มงาน --</option>
                                    <?php
//เรียก list กลุ่มงานทั้งหมด
                                    $divQS = "SELECT `listDivision` FROM `list_division` ORDER BY `divisionID` ASC";
                                    $divQry = mysqli_query($connection, $divQS);
                                    while ($rowDiv = mysqli_fetch_assoc($divQry)) {
                                        ?>
                                        <option 
                                        <?php
                                        //แยกตามกลุ่มงานล่าสุด
                                        if ($rowDiv['listDivision'] == $_SESSION['lastDiv']) {
                                            echo 'selected';
                                        }
                                        ?>>
                                                <?php echo $rowDiv['listDivision']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div> <!-- /.col-md-3 -->

                            <!-- ปุ่มตกลงและรีเซ็ท -->
                            <div>

                                <button class="btn btn-success" type="submit" name="submitBtn" value="submit">
                                    <span class="glyphicon glyphicon-search"></span> ค้นหา
                                </button>
                                <button class="btn btn-sm btn-default" type="submit" name="resetBtn" value="reset">
                                    <span class="glyphicon glyphicon-repeat"></span>&nbsp;แสดงทั้งหมด
                                </button>

                            </div> <!-- /ปุ่มตกลงและรีเซ็ท -->

                        </form>
                    </div>



                    <table id="example" class="table table-bordered table-condensed table-striped table-hover" width="100%" data-display-length='-1'>
                        <thead>
                            <?php
                            $query3 = "";
                            ?>
                            <tr align="center">
                                <?php foreach ($allDetailHeader as $val) { ?>
                                    <th><?= $val ?></th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody align='center'>
                            <?php
                            $x = 1;
                            $allDetailQry = mysqli_query($connection, $allDetailQS);
                            while ($row = mysqli_fetch_assoc($allDetailQry)) {
                                //"allDetail", "allSuffix", "sumAddQty", "sumTakeQty", "remaining", "allAdder"
                                ?>
                                <tr>
                                    <td><?= $x++ ?></td>
                                    <td align="left"><?= $row['add_detail'] ?></td>
                                    <td><?= $row['add_suffix'] ?></td>
                                    <td  style="background-color: #fffcb2;"><?= $row['historical'] ?></td>
                                    <td><?= $row['periodAddQty'] ?></td>
                                    <td style="background-color: #d6ffdd;"><?= $row['sumAddQty'] ?></td>
                                    <td><?= $row['b1'] ?></td>
                                    <td><?= $row['b2'] ?></td>
                                    <td><?= $row['b3'] ?></td>
                                    <td><?= $row['b4'] ?></td>
                                    <td><?= $row['b5'] ?></td>
                                    <td><?= $row['b6'] ?></td>
                                    <td><?= $row['b7'] ?></td>
                                    <td><?= $row['b8'] ?></td>
                                    <td><?= $row['b9'] ?></td>
                                    <td><?= $row['b10'] ?></td>
                                    <td><?= $row['b11'] ?></td>
                                    <td><?= $row['b12'] ?></td>
                                    <td><?= $row['b13'] ?></td>
                                    <td><?= $row['b14'] ?></td>
                                    <td><?= $row['b15'] ?></td>
                                    <td><?= $row['b16'] ?></td>
                                    <td><?= $row['b17'] ?></td>
                                    <td><?= $row['b18'] ?></td>
                                    <td><?= $row['b19'] ?></td>
                                    <td><?= $row['b20'] ?></td>
                                    <td style="background-color: #ffe4af;"><?= $row['sumTakeQty'] ?></td>
                                    <td style="background-color: #84ff8a;"><?= $row['remaining'] ?></td>
                                    <td><?= $row['adder'] ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>


                </div><!-- Main container -->
            </div> <!-- /.col-md-10 -->

        </div> <!-- /.row -->




        <?php include 'main_script.php'; ?>
        <script src="bootstrap-daterangepicker/moment.min.js" type="text/javascript"></script>
        <script src="bootstrap-daterangepicker/daterangepicker.js" type="text/javascript"></script>

        <script type="text/javascript">
            $(function () {

                var start = '<?php echo $_SESSION['startDate']; ?>';//moment().startOf('month'); 
                var end = '<?php echo $_SESSION['endDate']; ?>'; //moment().endOf('month');

<?php
unset($_SESSION['startDate']);
unset($_SESSION['endDate']);
?>

                function cb(start, end) {
                    $('#datetimepicker span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
                }

                $('#datetimepicker').daterangepicker({
                    "autoApply": true,
                    "alwaysShowCalendars": true,
                    startDate: start,
                    endDate: end,
                    minDate: '01/01/2015',
                    opens: "center",
                    linkedCalendars: false,
                    "locale": {
                        "format": "DD/MM/YYYY",
                        "separator": " - ",
                        "applyLabel": "ตกลง",
                        "cancelLabel": "ยกเลิก",
                        "fromLabel": "จาก",
                        "toLabel": "ถึง",
                        "customRangeLabel": "เลือกเอง",
                        "weekLabel": "W",
                        "daysOfWeek": [
                            "อ.",
                            "จ.",
                            "อ.",
                            "พ.",
                            "พฤ.",
                            "ศ.",
                            "ส."
                        ],
                        "monthNames": [
                            "มกราคม",
                            "กุมภาพันธ์",
                            "มีนาคม",
                            "เมษายน",
                            "พฤษภาคม",
                            "มิถุนายน",
                            "กรกฎาคม",
                            "สิงหาคม",
                            "กันยายน",
                            "ตุลาคม",
                            "พฤศจิกายน",
                            "ธันวาคม"
                        ],
                        "firstDay": 1
                    },
                    ranges: {
                        'ไตรมาส 1': ['<?= date("01/01/Y"); ?>', '<?= date("31/03/Y"); ?>'],
                        'ไตรมาส 2': ['<?= date("01/04/Y"); ?>', '<?= date("30/06/Y"); ?>'],
                        'ไตรมาส 3': ['<?= date("01/07/Y"); ?>', '<?= date("30/09/Y"); ?>'],
                        'ไตรมาส 4': ['<?= date("01/10/Y"); ?>', '<?= date("31/12/Y"); ?>'],
                        'ทั้งหมด': ['<?= date("01/01/2015"); ?>', '<?= date("d/m/Y"); ?>']
                    }
                }, cb);

                //cb(start, end);

            });
        </script>

        <script>
            $(document).ready(function () {
                var table = $('#example').DataTable({
                    dom:
                            "<'row'<'col-sm-6'l><'col-sm-6'f>>" +
                            "<'row'<'col-sm-12'tr>>" +
                            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                    lengthChange: false,
                    buttons: [
                        {
                            extend: 'copy',
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                        {
                            extend: 'excel',
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                        {
                            extend: 'print',
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                        'colvis'],
                    "columnDefs": [{
                            "visible": false,
                            "targets": [6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 28]
                        }]
                });


                table.buttons().container()
                        .appendTo($('#example_wrapper .col-sm-6:eq(0)'));
            });
        </script>

    </body>
</html>
