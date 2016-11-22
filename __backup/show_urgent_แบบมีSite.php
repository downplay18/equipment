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

unset($_SESSION['detail']);
unset($_SESSION['suffix']);
unset($_SESSION['owner']);
?>

<?php
//สร้าง Query Statement สำหรับแสดง ใบสั่งซื้อ(ปกติ)
$urgentQS = "";
$tableHeader = "";
$tableData = "";
$qryMsg = "";

if (isset($_POST['submitBtn'])) {
    $_SESSION['lastDiv'] = $_POST['divName'];
    $_SESSION['lastSite'] = $_POST['siteName'];
    do {
        //ไม่ได้เลือก
        if ($_POST['divName'] == "-- แยกตามกลุ่มงาน --" && $_POST['siteName'] == "-- แยกตามสถานที่ใช้งาน --") {
            //echo '1-- แยกตามกลุ่มงาน -- -- แยกตามสถานที่ใช้งาน --';
            $qryMsg = "(ไม่มีค่าที่เลือก)";
            break;
        }
        //เลือกทั้งกลุ่มงาน+สถานที่ใช้งาน
        if ($_POST['divName'] != "-- แยกตามกลุ่มงาน --" && $_POST['siteName'] != "-- แยกตามสถานที่ใช้งาน --") {
            //echo '99 เลือกทั้งกลุ่มงานและสถานที่ใช้งาน';
            $urgentQS = "SELECT `urg_detail`, SUM(`urg_qty`) as sum_urgQty, `urg_suffix`,`urg_site`, `urg_adder`"
                    . "FROM `item_urgent_record` "
                    . "WHERE `urg_adder` LIKE '" . $_POST['divName'] . "'"
                    . " AND `urg_site` LIKE '" . $_POST['siteName'] . "'"
                    . " GROUP BY `urg_detail`,`urg_suffix`,`urg_site`,`urg_adder`";
            $tableHeader = array("รายการ", "จำนวนรวม", "ใช้ที่", "เจ้าของ");
            $tableData = array("urg_detail", "sum_urgQty", "urg_suffix", "urg_site", "urg_adder");
            $qryMsg = $_POST['divName'] . ", " . $_POST['siteName'];
            break;
        }
        //เลือกเฉพาะกลุ่มงาน
        if ($_POST['divName'] != "-- แยกตามกลุ่มงาน --") {
            //echo '2-- แยกตามกลุ่มงาน --';
            $urgentQS = "SELECT `urg_detail`, SUM(`urg_qty`) as sum_urgQty, `urg_suffix`, `urg_adder`"
                    . " FROM `item_urgent_record`"
                    . " WHERE `urg_adder` LIKE '" . $_POST['divName'] . "'"
                    . " GROUP BY `urg_detail`,`urg_suffix`";
            $tableHeader = array("รายการ", "จำนวนรวม", "เจ้าของ");
            $tableData = array("urg_detail", "sum_urgQty", "urg_suffix", "urg_adder");
            $qryMsg = $_POST['divName'];
            break;
        }
        //เลือกเฉพาะสถานที่ใช้งาน
        if ($_POST['siteName'] != "-- แยกตามสถานที่ใช้งาน --") {
            //echo '3-- แยกตามสถานที่ใช้งาน --';
            $urgentQS = "SELECT `urg_detail`,SUM(`urg_qty`) as sum_urgQty,`urg_suffix`,`urg_site`"
                    . " FROM `item_urgent_record`"
                    . " WHERE `urg_site` LIKE '" . $_POST['siteName'] . "'"
                    . " GROUP BY `urg_detail`,`urg_suffix`";
            $tableHeader = array("รายการ", "จำนวน", "ใช้ที่");
            $tableData = array("urg_detail", "sum_urgQty", "urg_suffix", "urg_site");
            $qryMsg = $_POST['siteName'];
            break;
        }
    } while (0);
} else { //กดปุ่ม แสดงทั้งหมด
    //echo "enter else";
    $urgentQS = "SELECT `urg_detail`,SUM(`urg_qty`) as sum_urgQty,`urg_suffix`,`urg_adder`,`urg_purpose`"
            . " FROM `item_urgent_record`"
            . " GROUP BY `urg_detail`,`urg_adder`,`urg_suffix`";
    $tableHeader = array("รายการ", "จำนวนรวม", "เจ้าของ");
    $tableData = array("urg_detail", "sum_urgQty", "urg_suffix", "urg_adder");
    $qryMsg = "รายการสั่งซื้อ(เร่งด่วน) ทั้งหมด";
    $_SESSION['lastDiv'] = "-- แยกตามกลุ่มงาน --";
    $_SESSION['lastSite'] = "-- แยกตามสถานที่ใช้งาน --";
}
?>

<html>
    <head>

        <title>ADMIN</title>
        <!-- Bootstrap Core CSS -->
        <?php include 'main_head.php'; ?>
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs/jqc-1.12.3/dt-1.10.12/datatables.min.css"/>
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
            </div>

            <div class="col-md-10">

                <!-- Main container -->
                <div class="container-fluid">

                    <div class="page-header">
                        <h2>สืบค้น <small>สำหรับรายการที่สั่งซื้อแบบ เร่งด่วน</small></h2>
                    </div>


                    <div class="row">
                        <!-- แถว แสดง dropdown กลุ่มงาน+site -->
                        <div class="col-md-12" style="padding: 1.5em">

                            <form action="" method="post">
                                <div class="col-md-3">
                                    <select id="selDiv" class="form-control" name="divName">
                                        <option>-- แยกตามกลุ่มงาน --</option>
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
                                <div class="col-md-3">
                                    <select id="selBuilding" class="form-control" name="siteName">
                                        <option>-- แยกตามสถานที่ใช้งาน --</option>
                                        <?php
                                        //เรียก list ตุกทั้งหมด
                                        $siteQS = "SELECT `listBuilding` FROM `list_building` ORDER BY `buildingID` ASC";
                                        $siteQry = mysqli_query($connection, $siteQS);
                                        while ($rowSite = mysqli_fetch_assoc($siteQry)) {
                                            ?>
                                            <option 
                                            <?php
                                            //แยกตามสถานที่ใช้งานล่าสุด
                                            if ($rowSite['listBuilding'] == $_SESSION['lastSite']) {
                                                echo 'selected';
                                            }
                                            ?>>
                                                <?= $rowSite['listBuilding']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div> <!-- /.col-md-3 -->
                                <div class="col-md-1">
                                    <button class="btn btn-success" type="submit" name="submitBtn" value="submit" autofocus=""><span class="glyphicon glyphicon-search"></span> ค้นหา</button>
                                </div>
                                <div class="col-md-1">
                                    <button class="btn btn-default" type="submit" name="SubmitAll" value="submit"><span class="glyphicon glyphicon-list"></span> แสดงทั้งหมด</button>
                                </div>
                            </form>

                        </div> <!-- /.col-md-12 -->






                        <?php
                        $divSiteQry = mysqli_query($connection, $urgentQS);
                        $divSiteCount = mysqli_num_rows($divSiteQry);
                        if ($divSiteCount == 0) {
                            ?>
                            <div><b>คำค้น: </b><?= $qryMsg; ?> (0 รายการ)</div>
                        <?php } else { ?>
                            <div class="col-md-12">
                                <div><b>คำค้น: </b><?= $qryMsg ?> (<?= $divSiteCount ?> รายการ)</div>
                                <table id="example" class="table table-bordered table-hover table-condensed table-striped nowrap" width="100%" data-display-length='-1'>
                                    <thead>
                                        <tr align="center">
                                            <?php
                                            foreach ($tableHeader as $value) {
                                                echo "<th>" . $value . "</th>";
                                            }
                                            ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        //echo '<br/>' . $urgentQS;
                                        while ($rowDivSite = mysqli_fetch_assoc($divSiteQry)) {
                                            ?>
                                            <tr align="center">
                                                <td align="left">
                                                    <a href="show_item_urgent.php?detail=<?= $rowDivSite['urg_detail'] ?>&adder=<?= $rowDivSite['urg_adder'] ?>&suffix=<?= $rowDivSite['urg_suffix'] ?>" target="_blank">
                                                        <?= $rowDivSite[$tableData[0]] ?>
                                                    </a>
                                                </td>
                                                <td><?= $rowDivSite[$tableData[1]] . " " . $rowDivSite[$tableData[2]] ?></td>
                                                <td><?= $rowDivSite[$tableData[3]] ?></td>
                                                <?php if (isset($rowDivSite[$tableData[4]])) { ?>
                                                    <td><?= $rowDivSite[$tableData[4]] ?></td>
                                                <?php } ?>
                                                <?php if (isset($rowDivSite[$tableData[5]])) { ?>
                                                    <td><?= $rowDivSite[$tableData[5]] ?></td>
                                                <?php } ?>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div> <!-- /.col-md-12 -->
                        <?php } ?>

                    </div><!-- Main container -->
                </div> <!-- /.col-md-10 -->

            </div> <!-- /.row -->



            <?php include 'main_script.php'; ?>

            <script>
                $(document).ready(function () {
                    var table = $('#example').DataTable({
                        dom:
                                "<'row'<'col-sm-6'l><'col-sm-6'f>>" +
                                "<'row'<'col-sm-12'tr>>" +
                                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                        lengthChange: false,
                        buttons: ['copy', 'excel', 'print', 'colvis']
                    });


                    table.buttons().container()
                            .appendTo($('#example_wrapper .col-sm-6:eq(0)'));
                });
            </script>

    </body>
</html>
