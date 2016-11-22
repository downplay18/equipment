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
$divSiteQS = "";
$tableHeader = "";
$tableData = "";
$qryMsg = "";


if ($_POST['divName'] == "-- แยกตามกลุ่มงาน --" || empty($_POST['divName']) || isset($_POST['submitAll'])) {
    //กดปุ่ม แสดงทั้งหมด
    //echo "SHOW ALL";
    unset($_SESSION['lastDiv']);
    unset($_POST['divName']);
    unset($queryMsg);
    $divSiteQS = "
SELECT add_detail, quantity, add_suffix, add_qty, add_date, adder, slip
FROM item
RIGHT JOIN
(
    SELECT add_detail, add_suffix, add_qty, add_date, adder, slip
    FROM item_add_record aa
    WHERE add_date IN 
	(
	SELECT MAX(add_date)
        FROM item_add_record ab
        GROUP BY ab.add_detail, ab.adder
	) 
    GROUP BY add_detail, adder
) AS main
ON detail = main.add_detail
AND owner = main.adder        
";
    $tableHeader = array("รายการ", "คงเหลือ", "เพิ่มล่าสุด", "เจ้าของ");
    $tableData = array("add_detail", "quantity", "add_suffix", "add_date", "add_qty", "adder", "slip");
    $qryMsg = "แสดงทั้งหมด";
} else { //แสดงเฉพาะที่เลือก
    //echo 'SHOW SELECTED';
    $tmpDivision = $_POST['divName'];
    $divSiteQS = " SELECT add_detail, quantity, add_suffix, add_qty, add_date, main.adder, slip
FROM item
RIGHT JOIN
(
    SELECT add_detail, add_suffix, add_qty, add_date, adder, slip
    FROM item_add_record aa
    WHERE add_date IN 
	(
            SELECT MAX(add_date)
            FROM item_add_record ab
            WHERE adder='$tmpDivision'
            GROUP BY ab.add_detail, ab.adder
	) 
    GROUP BY add_detail, adder
) AS main
ON detail = main.add_detail
AND owner = main.adder  
";
    $tableHeader = array("รายการ", "คงเหลือ", "เพิ่มล่าสุด", "เจ้าของ");
    $tableData = array("add_detail", "quantity", "add_suffix", "add_date", "add_qty", "adder", "slip");
    $qryMsg = $_POST['divName'];
    $_SESSION['lastDiv'] = $_POST['divName'];
}
?>

<html>
    <head>

        <title>ADMIN</title>
        <!-- Bootstrap Core CSS -->
        <?php include 'main_head.php'; ?>
        <link href="select2/dist/css/select2.min.css" rel="stylesheet" type="text/css"/>
        <script src="select2/dist/js/select2.min.js" type="text/javascript"></script>
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
          echo '<br/>divSiteQS = <br/>';
          print_r($divSiteQS);
          echo '<br/>tmpDivision = <br/>';
          print_r($tmpDivision); */
        ?>

        <div class="row">

            <div class="col-md-2 sidebar">
                <?php include 'sidebar.php'; ?>
            </div>

            <div class="col-md-10">

                <!-- Main container -->
                <div class="container-fluid">

                    <div class="page-header">
                        <h2>สืบค้น <small>ระบบสืบค้นและพิมพ์รายงาน</small></h2>
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

                                    
                                    
                                    
                                    <script type="text/javascript">
                                        $(".js-example-basic-multiple").select2();
                                    </script>
                                    <select class="js-example-basic-multiple" multiple="multiple">
                                        <option value="AL">Alabama</option>
                                        <option value="WY">Wyoming</option>
                                    </select>

                                    
                                    
                                    
                                </div> <!-- /.col-md-3 -->
                                <div class="col-md-1">
                                    <button class="btn btn-success" type="submit" name="submitBtn" value="submit"><span class="glyphicon glyphicon-search"></span> ค้นหา</button>
                                </div>
                                <div class="col-md-1">
                                    <button class="btn btn-default" type="submit" name="submitAll" value="-- แยกตามกลุ่มงาน --"><span class="glyphicon glyphicon-list" autofocus></span> แสดงทั้งหมด</button>
                                </div>
                            </form>

                        </div> <!-- /.col-md-12 -->











                        <?php
                        $divSiteQry = mysqli_query($connection, $divSiteQS);
                        $divSiteCount = mysqli_num_rows($divSiteQry);
                        if ($divSiteCount == 0) { //ค้นแล้วเจอ 0 รายการ
                            ?>
                            <div><b>คำค้น: </b><?= $qryMsg; ?> (0 รายการ)</div>
                        <?php } else { ?>
                            <div class="col-md-12">
                                <div><b>คำค้น: </b><?= $qryMsg ?> (<?= $divSiteCount ?> รายการ)</div>
                                <table id="example" class="table table-bordered table-condensed table-striped table-hover" width="100%" data-display-length='-1'>
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
                                        while ($rowDivSite = mysqli_fetch_assoc($divSiteQry)) {
                                            //"add_detail", "quantity", "add_suffix", "add_date", "add_qty", "adder", "slip"
                                            ?>
                                            <tr align="center">
                                                <td align="left">
                                                    <a href="show_item.php?detail=<?= $rowDivSite['add_detail'] ?>&owner=<?= $rowDivSite['adder'] ?>&suffix=<?= $rowDivSite['add_suffix'] ?>" target="_blank">
                                                        <?= $rowDivSite[$tableData[0]] ?>
                                                    </a>
                                                </td>
                                                <td nowrap><?= $rowDivSite[$tableData[1]] . ' ' . $rowDivSite[$tableData[2]] ?></td>

                                                
                                                <!-- ถ้าไม่ได้อัปโหลด slip (index6=="") ไม่ต้องแสดงปุ่ม  -->
                                                <?php if ($rowDivSite[$tableData[6]] != "") { ?>
                                                    <td align="left"><?=
                                                        date("d/m/Y", strtotime($rowDivSite[$tableData[3]]))
                                                        . " <p class='label label-success'>+ " . $rowDivSite[$tableData[4]] . " " . $rowDivSite[$tableData[2]] . "</p>"
                                                        //. " <a href='" . $root_url . "/" . $rowDivSite[$tableData[6]] . "' class='label label-success' target='_blank'><span class='glyphicon glyphicon-file'></span> ใบเสร็จ</a>"
                                                        ?>
                                                    </td>
                                                <?php } else { ?>
                                                    <td align="left"><?=
                                                        date("d/m/Y", strtotime($rowDivSite[$tableData[3]]))
                                                        . " <p class='label label-success'>+ " . $rowDivSite[$tableData[4]] . " " . $rowDivSite[$tableData[2]] . "</p>"
                                                        //. " <a class='label label-danger' target='_blank'><span class='glyphicon glyphicon-remove-sign'></span> ไม่พบใบเสร็จ</a>"
                                                        ?>
                                                    </td>
                                                <?php } ?>
                                                    

                                                <td><?= $rowDivSite[$tableData[5]] ?></td>
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
                            'colvis']
                    });


                    table.buttons().container()
                            .appendTo($('#example_wrapper .col-sm-6:eq(0)'));
                });
            </script>

    </body>
</html>
