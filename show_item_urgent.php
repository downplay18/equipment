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

if (isset($_GET['detail'])) {
    $_SESSION['urg_detail'] = $_GET['detail'];
    $_SESSION['urg_adder'] = $_GET['adder'];
    $_SESSION['urg_suffix'] = $_GET['suffix'];
}
?>

<html>
    <head>

        <title>ADMIN</title>
        <!-- Bootstrap Core CSS -->
        <?php include 'main_head.php'; ?>
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
            <div class="col-md-12">
                <!-- Main container -->
                <div class="container-fluid">
                    <div class="page-header">
                        <h2><?= $_SESSION['urg_detail'] ?> <small></small></h2>
                    </div>
                </div>
            </div>


            <div class="container-fluid">
                <div class="col-md-12">
                    <?php
                    $urgentQS = "SELECT * FROM `item_urgent_record` WHERE `urg_detail` LIKE '" . $_SESSION['urg_detail'] . "'"; //มันแค่เช็ค add ใช้แค่ owner ไม่ใช่ division
                    $urgentSize = count($urgentHeader);
                    $addTakeQry = mysqli_query($connection, $urgentQS) or die("addTakeQry failed: " . mysqli_error($connection));
                    ?>
                    <b>กำลังแสดง:</b> รายการ = <?= $_SESSION['urg_detail'] ?>
                    <table id="example" class="table table-bordered table-hover table-condensed table-striped nowrap" width="100%" data-display-length='-1'>
                        <thead>
                            <tr align="center">
                                <th>รายการ</th>
                                <th>จน.</th>
                                <th>วันที่ตามใบเสร็จ</th>
                                <th>ผู้ลงบันทึก</th>
                                <th>ใช้เพื่อ</th>
                                <th>ใช้ที่</th>
                                <th>ใบเสร็จ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($rowAddTake = mysqli_fetch_assoc($addTakeQry)) {
                                echo '<tr align="center">';
                                echo '<td align="left" nowrap>' . $rowAddTake['urg_detail'] . '</td>';
                                echo '<td>' . $rowAddTake['urg_qty'] . ' ' . $rowAddTake['urg_suffix'] . '</td>';
                                echo '<td>' . preg_replace("/(\d+)\D+(\d+)\D+(\d+)/", "$3-$2-$1", $rowAddTake['urg_slipDate']) . '</td>';
                                echo '<td>' . $rowAddTake['urg_adder'] . '</td>';
                                echo '<td>' . $rowAddTake['urg_purpose'] . '</td>';
                                echo '<td>' . $rowAddTake['urg_site'] . '</td>';
                                if ($rowAddTake['urg_slip'] != "") {
                                    echo '<td width="1%"><a href="' . $rowAddTake['urg_slip'] . '" target=\'_blank\' "><span class="label label-success"><span class="glyphicon glyphicon-file"></span></span></td>';
                                } else {
                                    echo '<td width="1%"></td>';
                                }
                                echo '</tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div> <!-- /.col-md-12 -->
            </div> <!-- /.container-fluid -->
        </div> <!-- /.col-md-10 -->
    </div> <!-- /.row -->

    <?php include 'main_script.php'; ?>
    <script> /*PREVENT DOUBLE SUBMIT: ทำให้ปุ่ม submit กดได้ครั้งเดียว ป้องกับปัญหาเนต lag แล้ว user กดเบิ้ล มันจะทำให้ส่งค่า 2 เท่า */
        $(document).ready(function () {
            $("#singleSubmitForm").submit(function () {
                $("#singleSubmitBtn").attr("disabled", true);
                return true;
            });
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
                buttons: ['copy', 'excel', 'print', 'colvis']
            });


            table.buttons().container()
                    .appendTo($('#example_wrapper .col-sm-6:eq(0)'));
        });
    </script>



</body>
</html>
