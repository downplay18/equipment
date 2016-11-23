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

$keyMsg = array();
?>

<?php
if (isset($_POST['newSubmitBtn'])) {
    $newKeyCheckQS = "SELECT `key_detail` FROM `key_item` WHERE `key_detail`='" . $_POST['newKeyDetail'] . "'";
    $newKeyCheckQry = mysqli_query($connection, $newKeyCheckQS) or die(mysqli_error($connection));
    $duplicateRow = mysqli_num_rows($newKeyCheckQry);
    if ($duplicateRow) { //เจอซํ้า แจ้งข้อความเตือน
        array_push($keyMsg, "<p style='font-size:1.5em;color:red;'>ชื่อซํ้า!&nbsp;&nbsp;<u>" . $_POST['newKeyItem'] . "</u>&nbsp;&nbsp;มีอยู่แล้วในฐานข้อมูล!<br/>ไม่มีการเปลี่ยนแปลงเกิดขึ้น...</p>");
    } else { //ไม่เจอซํ้า เพิ่มเข้าไปใน :key_item
        $newKeyQS = "INSERT INTO key_item (key_code, key_detail, slip_suffix, last_suffix, last_xqty)
            VALUES ('" . $_POST['newKeyCode'] . "', '" . $_POST['newKeyDetail'] . "', '" . $_POST['newKeySuffix'] . "', '" . $_POST['newLastSuffix'] . "', '" . $_POST['newLastxQty'] . "');";
        $newKeyQry = mysqli_query($connection, $newKeyQS);
        if ($newKeyQry) {
            array_push($keyMsg, "<p style='font-size:1.25em;color:blue;'>เพิ่มรายชื่อ <u>" . $_POST['newKeyItem'] . "</u> ในรายชื่อกลางสำเร็จ!<br/>สามารถเรียกใช้งานในหน้าเพิ่มใบเสร็จได้ทันที!</p>");
        } else {
            array_push($keyMsg, "<p style='font-size:1.5em;color:red;'>ผิดพลาด! ไม่สามารถเพิ่ม<u>" . $_POST['newKeyDetail'] . "</u><br/>โปรดติดต่อผู้ดูแลระบบ!</p>");
        }
    }
}




if ($_POST['newKeyItem'] != "" && $_POST['newKeySuffix'] != "") {
    //คิวรี่เช็คว่ามันซํ้าในdbหรือเปล่า
//    $nkCheckQry = mysqli_query($connection, "SELECT `key_detail` FROM `key_item` WHERE `key_detail`='" . $_POST['newKeyItem'] . "'"
//            ) or die(mysqli_error($connection));
    $num_rows = mysqli_num_rows($nkCheckQry);

    if ($num_rows) { //เช็คแล้วเจอซํ้า
        array_push($keyMsg, "<p style='font-size:1.5em;color:red;'>ชื่อซํ้า!&nbsp;&nbsp;<u>" . $_POST['newKeyItem'] . "</u>&nbsp;&nbsp;มีอยู่แล้วในฐานข้อมูล!<br/>ไม่มีการเปลี่ยนแปลงเกิดขึ้น...</p>");
    } else { //ไม่เจอซํ้า เพิ่มเข้าไปใน :key_item
//        $keyItemAddQry = mysqli_query($connection, 
//                "INSERT INTO `key_item` (`key_detail`,`key_suffix`,`divisionID`)"
//                . " VALUES ('" . $_POST['newKeyItem'] . "','" . $_POST['newKeySuffix'] . "','" . $_SESSION['div_id'] . "')");
        array_push($keyMsg, "<p style='font-size:1.25em;color:blue;'>เพิ่มรายชื่อ <u>" . $_POST['newKeyItem'] . "</u> ในรายชื่อกลางสำเร็จ!<br/>สามารถเรียกใช้งานในหน้าเพิ่มใบเสร็จได้ทันที!</p>");
    }
}
?>


<html>
    <head>

        <title>ADMIN</title>
        <!-- Bootstrap Core CSS -->
        <?php require 'main_head.php'; ?>
    </head>

    <body>
        <?php
        include 'navbar.php';
        /*
        echo 'SESSION = ';
        print_r($_SESSION);
        echo '<br/>POST = <br/>';
        print_r($_POST); */
        ?>

        <div class="row">

            <div class="col-md-2 sidebar">
                <?php
                //status bar
                include 'sidebar.php';
                ?>
            </div>



            <div class="col-md-10">

                <!-- main container -->
                <div class="container-fluid">

                    <div class="page-header">
                        <h2>รายชื่อเครื่องมือเครื่องใช้ทั้งหมด <small></small></h2>
                    </div>


                    <div class="row">

                        <!-- เพิ่มชื่อเครื่องมือเครื่องใช้ -->
                        <div class="col-md-12">
                            <div class="alert alert-warning">

                                <form id="mainForm" action="" method="post">
                                    <h4 align="center">เพิ่มชื่อเครื่องมือเครื่องใช้</h4><br/>
                                    <h5 align="center" style="font-size:1.2em">***หากชื่อที่ต้องการเพิ่มมีอยู่แล้วในลิสต์ สามารถใช้ชื่อนั้นได้เลย***</h5>
                                    <?php
                                    echo "<center>";
                                    foreach ($keyMsg as $msg) {
                                        echo "<p align='center' style='font-size: 135%'>" . $msg . "</p><br/>";
                                    }
                                    unset($_POST['newKeyItem']);
                                    echo "</center>";
                                    ?>


                                    <div class="col-md-2">
                                        <input class="form-control" type="number"  name="newKeyCode" placeholder="รหัสพัสดุ" maxlength="10" autocomplete="off"/>
                                    </div>

                                    <div class="col-md-5">
                                        <input id="keyItem" class="form-control" type="text"  name="newKeyDetail" placeholder="ชื่อเครื่องมือเครื่องใช้ใหม่ที่ต้องการเพิ่ม" maxlength="100" autocomplete="off" required/>
                                    </div>
                                    
                                    <div class="col-md-1">
                                        <input class="form-control" type="text"  name="newKeySuffix" placeholder="หน่วยEA" maxlength="2" autocomplete="off" required/>
                                    </div>

                                    <div class="col-md-2">
                                        <input class="form-control" type="text"  name="newLastSuffix" placeholder="หน่วยย่อย" autocomplete="off" required/>
                                    </div>

                                    <div class="col-md-2">
                                        <input class="form-control" type="number"  name="newLastxQty" placeholder="แปลงหน่วย" autocomplete="off"/>
                                    </div>

                                    <div class="form-group" align="center">
                                        <button class="btn btn-lg btn-danger" type="submit" name="newSubmitBtn" value="submit" style="margin: 1em;">
                                            <span class="glyphicon glyphicon-ok-sign"></span>&nbsp;เพิ่ม
                                        </button>
                                        <button class="btn btn-sm btn-default" type="reset" name="resetBtn" value="reset">
                                            <span class="glyphicon glyphicon-repeat"></span>&nbsp;รีเซ็ท
                                        </button>
                                    </div>

                                </form>

                            </div> <!-- /.alert-warning -->

                            <div class = "alert alert-danger"> 
                                <span class = "label label-warning">คำเตือน!</span> หากรายชื่อที่ต้องการเพิ่ม มีอยู่แล้ว ควรใช้ชื่อเดิม เพื่อง่ายต่อการตรวจนับในภายหลัง<br/>
                                <span class = "label label-warning">คำเตือน!</span> รหัสพัสดุ สำหรับ เบิกคลัง<br/>
                                <span class = "label label-warning">คำเตือน!</span> ปล่อยว่างไว้ สำหรับ เบิกซื้อ<br/>
                            </div> 


                        </div> <!-- /.col-md-? -->

                        <div class="col-md-12">
                            <div class="alert alert-default">
                                <!-- <div align="center"><h4>รายชื่อเครื่องมือเครื่องใช้ทั้งหมด</h4></div> -->
                                <?php
                                $keyiQry = mysqli_query($connection, "SELECT key_id, key_detail, key_suffix, d.listDivision
                                        FROM key_item k
                                        LEFT JOIN list_division d
                                        ON k.divisionID = d.divisionID"
                                );
                                /*
                                  while ($rowKeyi = mysqli_fetch_assoc($keyiQry)) {
                                  echo $rowKeyi['key_id'] . " " . $rowKeyi['key_detail'] . "<br/>";
                                  } */
                                ?>
                                <table id="example" class="table table-bordered table-condensed table-striped table-hover" width="100%" data-display-length='5'>
                                    <thead>
                                        <tr align="center">
                                            <th>ID</th>
                                            <th>รายการ</th>
                                            <th>หน่วย</th>
                                            <th>ผู้เพิ่มแรก</th>
                                            <!-- <th>ทำการแก้ไข</th> -->
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php
                                        while ($rowKeyi = mysqli_fetch_assoc($keyiQry)) {
                                            ?>
                                            <tr align="center">
                                                <td><?= $rowKeyi['key_id'] ?></td>
                                                <td><?= $rowKeyi['key_detail'] ?></td>
                                                <td><?= $rowKeyi['key_suffix'] ?></td>
                                                <td><?= $rowKeyi['listDivision'] ?></td>
                                                <!--
                                                <td><a href="key_edit.php?kid=<?= $rowKeyi['key_id'] ?>" target="_blank"><span class="label label-warning"><span class="glyphicon glyphicon-edit"></span></span></a></td>
                                                -->
                                            </tr>
                                        <?php } ?>

                                    </tbody>
                                </table>

                            </div> <!-- /.alert -->
                        </div> <!-- /.col-md-? -->




                    </div> <!--/.row -->
                </div> <!-- /main container -->
            </div> <!-- /.col-md-10 -->





        </div> <!-- /.row -->



        <?php require("main_script.php"); ?>
        <script src="js/autoc_keyItem.js" type="text/javascript"></script>

        <script> /*PREVENT DOUBLE SUBMIT: ทำให้ปุ่ม submit กดได้ครั้งเดียว ป้องกับปัญหาเนต lag แล้ว user กดเบิ้ล มันจะทำให้ส่งค่า 2 เท่า */
            $(document).ready(function () {
                $("#mainForm").submit(function () {
                    $("#submitBtn").attr("disabled", true);
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
