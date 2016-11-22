<?php
session_start();
if ($_SESSION['user_id'] == "") {
    echo "<br/>โปรดยืนยันตัวตนก่อน !";
    exit();
}
/* * ************************************************************************
 *  ทั้ง ADMIN BOSS USER เข้าหน้านี้ได้หมด การกระทำจะดูจาก item_edit_record เท่านั้น   *
 * ************************************************************************* */

/*
  if ($_SESSION['status'] != "USER") {
  echo "<br/>สำหรับ -พนักงาน- เท่านั้น!";
  exit();
  } */
$_SESSION['iid'] = $_GET['iid'];
?>

<html>

    <head>
        <?php include 'main_head.php'; ?>
    </head>

    <body>

        <?php
        /* navbar */
        /* ไม่ใช้ case unauthen เพราะไม่มีสิทธิ์เข้าหน้านี้อยู่แล้ว */
        if (!isset($_SESSION['user_id'])) {
            include("navbar_unauthen.php");
        } else {
            include("navbar_authen.php");
        }

        echo '<br/>';
        echo 'SESSION = ';
        print_r($_SESSION);
        echo '<br/>loginResult =<br/>';
        print_r($loginResult);
        echo '<br/>POST = <br/>';
        print_r($_POST);
        ?>


        <?php
        require("connection.php");
        $strSQL = "SELECT * FROM `item` WHERE `iid` LIKE '" . $_GET["iid"] . "'";
        $itemEditQuery = mysqli_query($connection, $strSQL) or die("<br/>item_edit คิวรี่ล้มเหลว!");
        $itemEditResult = mysqli_fetch_assoc($itemEditQuery);

        echo '<br/>$itemEditResult = <br/>';
        print_r($itemEditResult);
        ?>

        <!-- MAIN CONTAINER, EDIT BOX COLUMN -->
        <div class="container-fluid">

            <!-- breadcrumb -->
            <ol class="breadcrumb">
                <li><a href="index.php"><span class="glyphicon glyphicon-home"></span> หน้าแรก</a></li>
                <li><a href="_login_user.php">รายการหลัก</a></li>
                <li><a href="edit_itemList.php">เลือกรายการที่ต้องการแก้ไข</a></li>
                <li class="active">กำลังแก้ไข</li>
            </ol> <!-- /breadcrumb -->

            <div class="page-header">
                <h2>กำลังแก้ไข <small><?= $itemEditResult['detail'] ?></small></h2>
            </div>

            <!-- MAIN EDIT BOX COLUMN -->
            <form id="mainForm" action="edit_process.php" method="post" target="">
                <div class="col-lg-8">
                    <div class="col-md-12">
                        <div class="col-md-2" align="right" style="padding:0.4em"><b>Unique ID</b></div>
                        <div class="col-md-4"><p type="text" class="form-control-static"><?= $itemEditResult['iid']; ?></div>
                        <div class="col-md-4"></div>
                        <div class="col-md-2"></div>
                    </div>

                    <div class="col-md-12">
                        <div class="col-md-2" align="right" style="padding:0.4em"><b>รายละเอียด</b></div>
                        <div class="col-md-4"><p type="text" class="form-control-static"><?= $itemEditResult['detail']; ?></div>
                        <div class="col-md-6"><input type="text" class="form-control input-sm" name="iDetail" value="<?= $itemEditResult['detail']; ?>"></div>
                    </div>

                    <div class="col-md-12">
                        <div class="col-md-2" align="right" style="padding:0.4em"><b>จำนวน</b></div>
                        <div class="col-md-4"><p type="text" class="form-control-static"><?= $itemEditResult['quantity']; ?></div>
                        <div class="col-md-3"><input type="text" class="form-control input-sm" name="iQuantity" value="<?= $itemEditResult['quantity']; ?>"></div>
                    </div>

                    <div class="col-md-12">
                        <div class="col-md-2" align="right" style="padding:0.4em"><b>หน่วย</b></div>
                        <div class="col-md-4"><p type="text" class="form-control-static"><?= $itemEditResult['suffix']; ?></div>
                        <div class="col-md-3"><input type="text" class="form-control input-sm" name="iSuffix" value="<?= $itemEditResult['suffix']; ?>"></div>
                    </div>

                    <div class="col-md-12">
                        <div class="col-md-3" align="right" style="padding:0.4em"><b>**หมายเหตุ**</b></div>
                        <div class="col-md-8"><input type="text" class="form-control input-sm" name="iNote" placeholder="เช่น กรอกสลิปผิดพลาด, กรอกเบิกใช้ผิดพลาด 3 ชิ้น ฯลฯ" required=""></div>
                        <div class="col-md-1"></div>
                    </div>

                    <!-- input button -->
                    <div class="form-group col-md-12" align="center" style="padding: 15px">
                        <a class="btn btn-default" href="edit_itemList.php" target=""><span class="glyphicon glyphicon-backward"></span> กลับ</a>
                        <button id="submitBtn" class="btn btn-lg btn-warning" type="submit">
                            <span class="glyphicon glyphicon-pencil"></span>&nbsp;แก้ไข
                        </button>
                    </div>  <!-- /input button -->

                </div> 
            </form>
            <!-- /MAIN EDIT BOX COLUMN --->

            <div class="alert alert-danger col-md-4">
                <span class = "label label-warning" role="alert">Warning!</span> เนื่องจากการแก้ไขเป็นกระบวนการที่เกิดผลกระทบกับการนับจำนวนได้ง่าย ผู้แก้ไขควรกำกับคำอธิบายลงในหมายเหตุทุกครั้ง<br/>
            </div>

             <div class="alert alert-default col-md-4">
                รายการเปลี่ยนแปลงล่าสุด
            </div>
            
            <div class="alert alert-info col-md-4">
                <span class = "label label-info" role="alert">Info</span> กรอกเฉพาะช่องที่ต้องการแก้ไข และสามารถเว้นช่องว่างไว้ หากไม่ต้องการเปลี่นแปลง<br/>
            </div>



        </div> <!-- /MAIN CONTAINER -->

        <?php
        $_SESSION['detail'] = $itemEditResult['detail'];
        $_SESSION['suffix'] = $itemEditResult['suffix'];
        $_SESSION['quantity'] = $itemEditResult['quantity'];
        session_write_close();
        ?>

        <?php include 'main_script.php'; ?>
        <script> /*PREVENT DOUBLE SUBMIT: ทำให้ปุ่ม submit กดได้ครั้งเดียว ป้องกับปัญหาเนต lag แล้ว user กดเบิ้ล มันจะทำให้ส่งค่า 2 เท่า */
            $(document).ready(function () {
                $("#mainForm").submit(function () {
                    $("#submitBtn").attr("disabled", true);
                    return true;
                });
            });
        </script>

    </body>
</html>
