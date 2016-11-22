<?php
//var_dump($_SESSION);
session_start();
//error_reporting(0);
require_once 'connection.php';

include 'root_url.php';
if ($_SESSION['user_id'] == "") {
    header("Location: $root_url/index.php", true, 302);
    exit();
}
if ($_SESSION['status'] != "BOSS") {
    header("Location: $root_url/_login_check.php", true, 302);
}
?>


<html>
    <head>

        <title>ADMIN</title>
        <?php include 'main_head.php'; ?>
    </head>

    <body>
        <?php
        include("navbar.php");

        /*
          echo '<br/>';
          echo 'SESSION = ';
          print_r($_SESSION);
          echo '<br/>loginResult =<br/>';
          print_r($loginResult);
          echo '<br/>POST = <br/>';
          print_r($_POST); */
        ?>


        <div class="row">
            <div class="col-md-2 sidebar">
                <?php include 'sidebar.php'; ?>
            </div>


            <div class="col-md-10">
                <div class="container-fluid">
                    <?php
                    $configQS = "SELECT `cname`,`mykey`,`favworker` FROM `user_config` WHERE `cname` LIKE '" . $_SESSION['name'] . "'";
                    $configQry = mysqli_query($connection, $configQS) or die("_login_boss query error: " . mysqli_error($connection));
                    $resultConfig = mysqli_fetch_assoc($configQry);

                    //ถ้ายังไม่มี KEY
                    if (count($resultConfig['cname']) == 0 || $resultConfig['mykey'] == "") {
                        echo "ยังไม่มีผู้ดูแลประจำกลุ่มงาน:";
                        ?>
                        <form action="_login_boss_selkey.php" method="post">
                            <select class="form-control" name="boss_selkey" style="width: 20em">
                                <?php
                                echo "<option>-- เลือกผู้ดูแลที่นี่ --</option>";
                                $ulistQS = "SELECT `name` FROM `user` WHERE `division` LIKE '" . $_SESSION['division'] . "'"
                                        . " AND `status` LIKE 'USER'";
                                $ulistQry = mysqli_query($connection, $ulistQS);
                                while ($rowUlist = mysqli_fetch_assoc($ulistQry)) {
                                    echo "<option>" . $rowUlist['name'] . "</option>";
                                }
                                ?>
                            </select>
                            <div style="padding: 5px">
                                <button class="btn btn-success" type="submit">
                                    <span class="glyphicon glyphicon-check"></span>&nbsp;ตกลง
                                </button>
                            </div>
                        </form>

                        <?php
                        //ถ้าเลือก KEY ไว้แล้ว 
                    } else {
                        ?>
                        <!--แสดง/ลบ ผู้ดูแลประจำกลุ่มงาน -->
                        <div  style="margin-bottom: 10px">
                            <form action="_login_boss_deleteSelkey.php" method="get" style="margin: 0; padding: 0">
                                <b>ผู้ดูแลประจำกลุ่มงานของคุณคือ </b>
                                <?php
                                $cuserQS = "SELECT `mykey` FROM `user_config` WHERE `cname` LIKE '" . $_SESSION['name'] . "'";
                                $cuserQry = mysqli_query($connection, $cuserQS) or die("cuserQS fail: " . mysqli_error($connection));
                                $cuserResult = mysqli_fetch_assoc($cuserQry);
                                echo $cuserResult['mykey'];
                                ?>
                                <input class="hidden" name="getmykey" value="<?= $cuserResult['mykey'] ?>"/>
                                <button class="btn btn-danger btn-sm" type="submit">
                                    <span class="glyphicon glyphicon-remove"></span>
                                </button>
                            </form>
                        </div>
                    <?php } ?>














                    <?php
                    //ส่วนของการแจ้งว่ามีรายการใหม่รออนุมัติ
                    date_default_timezone_set("Asia/Bangkok");
                    ?>
                    <div class="col-md-6">
                        <?php
                        $chkDateQry = mysqli_query($connection, "SELECT `add_detail`,`take_detail`,`add_date`"
                                . " FROM `item_add_record`,`item_take_record`"
                                . " WHERE `add_known` LIKE ''"
                                . " OR `take_known` LIKE ''"
                                . " AND `adder`='" . $_SESSION['division'] . "'"
                                . " AND `taker`='". $_SESSION['division'] ."'")
                                or die("chkDateQry failed: " . mysqli_error($connection));
                        if (mysqli_num_rows($chkDateQry) !== 0) {
                            ?>
                            <div class="alert alert-warning">
                                <h4><u>รายการเปลี่ยนแปลงล่าสุดโดยผู้ดูแลประจำกลุ่มงาน (แจ้งเพื่อทราบ)</u></h4>
                                <form action="_login_boss_process.php" method="post">

                                    <!-- วันที่ 1 -->
                                    <?php
                                    $date1 = date("Y-m-d");
                                    $a1qry = mysqli_query($connection, 
                                            "SELECT * FROM item_add_record"
                                            . " WHERE add_date <= '" . $date1 . "'"
                                            . " AND add_known LIKE ''"
                                            . " AND `adder` LIKE '". $_SESSION['division'] ."'")
                                            or die("a1qry" . mysqli_error($connection));
                                    $t1qry = mysqli_query($connection, 
                                            "SELECT * FROM item_take_record"
                                            . " WHERE take_date <= '" . $date1 . "'"
                                            . " AND take_known LIKE ''"
                                            . " AND `taker` LIKE '". $_SESSION['division'] ."'")
                                            or die("t1qry" . mysqli_error($connection));
                                    if (mysqli_num_rows($a1qry) != 0 || mysqli_num_rows($t1qry) != 0) {
                                        ?>
                                        <span class="button-checkbox">
                                            <button type="button" class="btn" data-color="success" style="margin-top: 1em">อนุมัติ</button>
                                            <input type="checkbox" class="hidden" name="date[]" value="<?= $date1 ?>"/>
                                        </span>
                                        <u>วันที่ <?= date("d/m/Y") ?></u><br/>
                                        <?php
                                        while ($rowa1 = mysqli_fetch_assoc($a1qry)) {
                                            echo "<div class='label label-success'>ซื้อเพิ่ม</div>" . " " . $rowa1['add_detail'] . " (" . $rowa1['add_qty'] . " " . $rowa1['add_suffix'] . ")" . "<br/>";
                                        }
                                        while ($rowt1 = mysqli_fetch_assoc($t1qry)) {
                                            echo "<div class='label label-warning'>เบิกใช้</div>" . " " . $rowt1['take_detail'] . " (" . $rowt1['take_qty'] . " " . $rowt1['take_suffix'] . ")" . "<br/>";
                                        }
                                    }
                                    ?>

                                    <!-- วันที่ 2 -->
                                    <?php
                                    $date2 = date("Y-m-d", strtotime(' -1 day'));
                                    $a2qry = mysqli_query($connection, 
                                            "SELECT * FROM item_add_record"
                                            . " WHERE add_date <= '" . $date2 . "'"
                                            . " AND add_known LIKE ''"
                                            . " AND `adder` LIKE '". $_SESSION['division'] ."'")
                                            or die("a2qry" . mysqli_error($connection));
                                    $t2qry = mysqli_query($connection, 
                                            "SELECT * FROM item_take_record"
                                            . " WHERE take_date <= '" . $date2 . "'"
                                            . " AND take_known LIKE ''"
                                            . " AND `taker` LIKE '". $_SESSION['division'] ."'")
                                            or die("t2qry" . mysqli_error($connection));
                                    if (mysqli_num_rows($a2qry) != 0 || mysqli_num_rows($t2qry) != 0) {
                                        ?>
                                        <span class="button-checkbox">
                                            <button type="button" class="btn" data-color="success" style="margin-top: 1em">อนุมัติ</button>
                                            <input type="checkbox" class="hidden" name="date[]" value="<?= $date2 ?>"/>
                                        </span>
                                        <u>วันที่ <?= date("d/m/Y", strtotime('-1 day')) ?></u><br/>
                                        <?php
                                        while ($rowa2 = mysqli_fetch_assoc($a2qry)) {
                                            echo "<div class='label label-success'>ซื้อเพิ่ม</div>" . " " . $rowa2['add_detail'] . " (" . $rowa2['add_qty'] . " " . $rowa2['add_suffix'] . ")" . "<br/>";
                                        }
                                        while ($rowt2 = mysqli_fetch_assoc($t2qry)) {
                                            echo "<div class='label label-warning'>เบิกใช้</div>" . " " . $rowt2['take_detail'] . " (" . $rowt2['take_qty'] . " " . $rowt2['take_suffix'] . ")" . "<br/>";
                                        }
                                    }
                                    ?>

                                    <!-- วันที่ 3 -->
                                    <?php
                                    $date3 = date("Y-m-d", strtotime(' -2 day'));
                                    $a3qry = mysqli_query($connection, 
                                            "SELECT * FROM item_add_record"
                                            . " WHERE add_date <= '" . $date3 . "'"
                                            . " AND add_known LIKE ''"
                                            . " AND `adder` LIKE '". $_SESSION['division'] ."'")
                                            or die("a3qry" . mysqli_error($connection));
                                    $t3qry = mysqli_query($connection, 
                                            "SELECT * FROM item_take_record"
                                            . " WHERE take_date <= '" . $date3 . "'"
                                            . " AND take_known LIKE ''"
                                            . " AND `taker` LIKE '". $_SESSION['division'] ."'")
                                            or die("t3qry" . mysqli_error($connection));
                                    if (mysqli_num_rows($a3qry) != 0 || mysqli_num_rows($t3qry) != 0) {
                                        ?>
                                        <span class="button-checkbox">
                                            <button type="button" class="btn" data-color="success" style="margin-top: 1em">อนุมัติ</button>
                                            <input type="checkbox" class="hidden" name="date[]" value="<?= $date7 ?>"/>
                                        </span>
                                        <u>วันที่ <?= date("d/m/Y", strtotime('-2 day')) ?></u><br/>
                                        <?php
                                        while ($rowa3 = mysqli_fetch_assoc($a3qry)) {
                                            echo "<div class='label label-success'>ซื้อเพิ่ม</div>" . " " . $rowa3['add_detail'] . " (" . $rowa3['add_qty'] . " " . $rowa3['add_suffix'] . ")" . "<br/>";
                                        }
                                        while ($rowt3 = mysqli_fetch_assoc($t3qry)) {
                                            echo "<div class='label label-warning'>เบิกใช้</div>" . " " . $rowt3['take_detail'] . " (" . $rowt3['take_qty'] . " " . $rowt3['take_suffix'] . ")" . "<br/>";
                                        }
                                    }
                                    ?>

                                    <!-- วันที่ 4 -->
                                    <?php
                                    $date4 = date("Y-m-d", strtotime(' -3 day'));
                                    $a4qry = mysqli_query($connection, 
                                            "SELECT * FROM item_add_record"
                                            . " WHERE add_date <= '" . $date4 . "'"
                                            . " AND add_known LIKE ''"
                                            . " AND `adder` LIKE '". $_SESSION['division'] ."'")
                                            or die("a4qry" . mysqli_error($connection));
                                    $t4qry = mysqli_query($connection, 
                                            "SELECT * FROM item_take_record"
                                            . " WHERE take_date <= '" . $date4 . "'"
                                            . " AND take_known LIKE ''"
                                            . " AND `taker` LIKE '". $_SESSION['division'] ."'")
                                            or die("t4qry" . mysqli_error($connection));
                                    if (mysqli_num_rows($a4qry) != 0 || mysqli_num_rows($t4qry) != 0) {
                                        ?>
                                        <span class="button-checkbox">
                                            <button type="button" class="btn" data-color="success" style="margin-top: 1em">อนุมัติ</button>
                                            <input type="checkbox" class="hidden" name="date[]" value="<?= $date4 ?>"/>
                                        </span>
                                        <u>วันที่ <?= date("d/m/Y", strtotime('-3 day')) ?></u><br/>
                                        <?php
                                        while ($rowa4 = mysqli_fetch_assoc($a4qry)) {
                                            echo "<div class='label label-success'>ซื้อเพิ่ม</div>" . " " . $rowa4['add_detail'] . " (" . $rowa4['add_qty'] . " " . $rowa4['add_suffix'] . ")" . "<br/>";
                                        }
                                        while ($rowt4 = mysqli_fetch_assoc($t4qry)) {
                                            echo "<div class='label label-warning'>เบิกใช้</div>" . " " . $rowt4['take_detail'] . " (" . $rowt4['take_qty'] . " " . $rowt4['take_suffix'] . ")" . "<br/>";
                                        }
                                    }
                                    ?>

                                    <!-- วันที่ 5 -->
                                    <?php
                                    $date5 = date("Y-m-d", strtotime(' -4 day'));
                                    $a5qry = mysqli_query($connection, 
                                            "SELECT * FROM item_add_record"
                                            . " WHERE add_date <= '" . $date5 . "'"
                                            . " AND add_known LIKE ''"
                                            . " AND `adder` LIKE '". $_SESSION['division'] ."'")
                                            or die("a5qry" . mysqli_error($connection));
                                    $t5qry = mysqli_query($connection, 
                                            "SELECT * FROM item_take_record"
                                            . " WHERE take_date <= '" . $date5 . "'"
                                            . " AND take_known LIKE ''"
                                            . " AND `taker` LIKE '". $_SESSION['division'] ."'")
                                            or die("t5qry" . mysqli_error($connection));
                                    if (mysqli_num_rows($a5qry) != 0 || mysqli_num_rows($t5qry) != 0) {
                                        ?>
                                        <span class="button-checkbox">
                                            <button type="button" class="btn" data-color="success" style="margin-top: 1em">อนุมัติ</button>
                                            <input type="checkbox" class="hidden" name="date[]" value="<?= $date5 ?>"/>
                                        </span>
                                        <u>วันที่ <?= date("d/m/Y", strtotime('-4 day')) ?></u><br/>
                                        <?php
                                        while ($rowa5 = mysqli_fetch_assoc($a5qry)) {
                                            echo "<div class='label label-success'>ซื้อเพิ่ม</div>" . " " . $rowa5['add_detail'] . " (" . $rowa5['add_qty'] . " " . $rowa5['add_suffix'] . ")" . "<br/>";
                                        }
                                        while ($rowt5 = mysqli_fetch_assoc($t5qry)) {
                                            echo "<div class='label label-warning'>เบิกใช้</div>" . " " . $rowt5['take_detail'] . " (" . $rowt5['take_qty'] . " " . $rowt5['take_suffix'] . ")" . "<br/>";
                                        }
                                    }
                                    ?>

                                    <!-- วันที่ 6 -->
                                    <?php
                                    $date6 = date("Y-m-d", strtotime(' -5 day'));
                                    $a6qry = mysqli_query($connection, 
                                            "SELECT * FROM item_add_record"
                                            . " WHERE add_date <= '" . $date6 . "'"
                                            . " AND add_known LIKE ''"
                                            . " AND `adder` LIKE '". $_SESSION['division'] ."'")
                                            or die("a6qry" . mysqli_error($connection));
                                    $t6qry = mysqli_query($connection, 
                                            "SELECT * FROM item_take_record"
                                            . " WHERE take_date <= '" . $date6 . "'"
                                            . " AND take_known LIKE ''"
                                            . " AND `taker` LIKE '". $_SESSION['division'] ."'")
                                            or die("t6qry" . mysqli_error($connection));
                                    if (mysqli_num_rows($a6qry) != 0 || mysqli_num_rows($t6qry) != 0) {
                                        ?>
                                        <span class="button-checkbox">
                                            <button type="button" class="btn" data-color="success" style="margin-top: 1em">อนุมัติ</button>
                                            <input type="checkbox" class="hidden" name="date[]" value="<?= $date6 ?>"/>
                                        </span>
                                        <u>วันที่ <?= date("d/m/Y", strtotime('-5 day')) ?></u><br/>
                                        <?php
                                        while ($rowa6 = mysqli_fetch_assoc($a6qry)) {
                                            echo "<div class='label label-success'>ซื้อเพิ่ม</div>" . " " . $rowa6['add_detail'] . " (" . $rowa6['add_qty'] . " " . $rowa6['add_suffix'] . ")" . "<br/>";
                                        }
                                        while ($rowt6 = mysqli_fetch_assoc($t6qry)) {
                                            echo "<div class='label label-warning'>เบิกใช้</div>" . " " . $rowt6['take_detail'] . " (" . $rowt6['take_qty'] . " " . $rowt6['take_suffix'] . ")" . "<br/>";
                                        }
                                    }
                                    ?>


                                    <!-- วันที่ 7 -->
                                    <?php
                                    $date7 = date("Y-m-d", strtotime(' -6 day'));
                                    $a7qry = mysqli_query($connection, 
                                            "SELECT * FROM item_add_record"
                                            . " WHERE add_date <= '" . $date7 . "'"
                                            . " AND add_known LIKE ''"
                                            . " AND `adder` LIKE '". $_SESSION['division'] ."'")
                                            or die("a7qry" . mysqli_error($connection));
                                    $t7qry = mysqli_query($connection, 
                                            "SELECT * FROM item_take_record"
                                            . " WHERE take_date <= '" . $date7 . "'"
                                            . " AND take_known LIKE ''"
                                            . " AND `taker` LIKE '". $_SESSION['division'] ."'")
                                            or die("t7qry" . mysqli_error($connection));
                                    if (mysqli_num_rows($a7qry) != 0 || mysqli_num_rows($t7qry) != 0) {
                                        ?>
                                        <span class="button-checkbox">
                                            <button type="button" class="btn" data-color="success" style="margin-top: 1em">อนุมัติ</button>
                                            <input type="checkbox" class="hidden" name="date[]" value="<?= $date7 ?>"/>
                                        </span>
                                        <u>วันที่ <?= date("d/m/Y", strtotime('-6 day')) ?></u><br/>
                                        <?php
                                        while ($rowa7 = mysqli_fetch_assoc($a7qry)) {
                                            echo "<div class='label label-success'>ซื้อเพิ่ม</div>" . " " . $rowa7['add_detail'] . " (" . $rowa7['add_qty'] . " " . $rowa7['add_suffix'] . ")" . "<br/>";
                                        }
                                        while ($rowt7 = mysqli_fetch_assoc($t7qry)) {
                                            echo "<div class='label label-warning'>เบิกใช้</div>" . " " . $rowt7['take_detail'] . " (" . $rowt7['take_qty'] . " " . $rowt7['take_suffix'] . ")" . "<br/>";
                                        }
                                    }
                                    ?>

                                    <!-- วันที่ 8 เป็นต้นไป -->
                                    <?php
                                    $date8 = date("Y-m-d", strtotime(' -7 day'));
                                    $a8qry = mysqli_query($connection, 
                                            "SELECT * FROM item_add_record"
                                            . " WHERE add_date <= '" . $date8 . "'"
                                            . " AND add_known LIKE ''"
                                            . " AND `adder` LIKE '". $_SESSION['division'] ."'")
                                            or die("a8qry" . mysqli_error($connection));
                                    $t8qry = mysqli_query($connection, 
                                            "SELECT * FROM item_take_record"
                                            . " WHERE take_date <= '" . $date8 . "'"
                                            . " AND take_known LIKE ''"
                                            . " AND `taker` LIKE '". $_SESSION['division'] ."'")
                                            or die("t8qry" . mysqli_error($connection));
                                    if (mysqli_num_rows($a8qry) != 0 || mysqli_num_rows($t8qry) != 0) {
                                        $_SESSION['otherDate'] = date("Y-m-d", strtotime(' -7 day'));
                                        ?>
                                        <span class="button-checkbox">
                                            <button type="button" class="btn" data-color="success" style="margin-top: 1em">อนุมัติ</button>
                                            <input type="checkbox" class="hidden" name="date[]" value="otherDate"/>
                                        </span>
                                        <u>รายการที่เก่ากว่า 7 วัน</u><br/>
                                        <?php
                                        while ($rowa8 = mysqli_fetch_assoc($a8qry)) {
                                            echo "<div class='label label-success'>ซื้อเพิ่ม</div>" . " " . $rowa8['add_detail'] . " (" . $rowa8['add_qty'] . " " . $rowa8['add_suffix'] . ")" . "<br/>";
                                        }
                                        while ($rowt8 = mysqli_fetch_assoc($t8qry)) {
                                            echo "<div class='label label-warning'>เบิกใช้</div>" . " " . $rowt8['take_detail'] . " (" . $rowt8['take_qty'] . " " . $rowt8['take_suffix'] . ")"."<br/>";
                                        }
                                    }
                                    ?>

                                    <!-- submit button -->
                                    <div class="form-group" align="center" style="margin: 1em">
                                        <button id='submitBtn' class="btn btn-lg btn-success" type="submit">
                                            <span class="glyphicon glyphicon-pencil"></span>&nbsp;ยืนยัน
                                        </button>
                                    </div>  <!-- /submit button -->
                                    
                                </form>
                            </div> <!-- /.alert -->
                            <?php
                        } else {
                            echo "<div class='alert alert-success'>";
                            echo "<h4><u>รายการเปลี่ยนแปลงล่าสุดโดยผู้ดูแลประจำกลุ่มงาน (แจ้งเพื่อทราบ)</u></h4>";
                            echo "ยังไม่มีรายการแจ้งเพิ่มเติม กดปุ่ม <kbd><span class='glyphicon glyphicon-refresh'></span></kbd> เพื่ออัปเดตล่าสุด";
                            echo "</div>";
                        }
                        ?>


                    </div> <!-- /.col-md-6 -->









                </div> <!-- /.container-fluid -->
            </div> <!-- /.col-md-10 -->

        </div> <!-- /.row -->









        <?php include 'main_script.php'; ?>
        <script src="js/jQueryChkbxBtn.js" type="text/javascript"></script>


    </body>
</html>
