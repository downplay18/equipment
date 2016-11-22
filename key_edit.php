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

$_SESSION['editKeyMsg'] = array();
?>

<?php
if (isset($_POST['submitBtn'])) {
    //เช็คไม่ซํ้าเดิมก่อน
    //-->แนวคิด: เนื่องจากทั้ง add,take,item เอาdetailมาจาก :key_item หมด เวลาเช็คซํ้า เช็คแค่ :key_item ไม่ซํ้าอันเดียว ที่เหลือก็ update ไปเลย
    $checkQS = "SELECT key_id, key_detail, key_suffix, k.divisionID, listDivision 
                FROM key_item k
                INNER JOIN list_division l
                ON key_id = '" . $_GET['kid'] . "'
                AND k.divisionID = l.divisionID";
    $checkQry = mysqli_query($connection, $checkQS);
    $rowCheck = mysqli_fetch_assoc($checkQry);

    //print_r($rowCheck);
    //Array ( [key_id] => 54 [key_detail] => ปปป [key_suffix] => [divisionID] => 7 [lastEditID] => )
    //edited_detail edited_sfx updated_div
    //เช็ค detail, suffix, divisionID
    if ($rowCheck['key_detail'] != $_POST['edited_detail'] || $rowCheck['key_suffix'] != $_POST['edited_sfx']) {
        //ต้อง query แก้ทั้งหมด
        if ($rowCheck['key_detail'] != $_POST['edited_detail']) {
            array_push($_SESSION['editKeyMsg'], "<span style='color:blue;'>แก้ไข: <b>ชื่อ</b> ...OK!</span>");
        }
        if ($rowCheck['key_suffix'] != $_POST['edited_sfx']) {
            array_push($_SESSION['editKeyMsg'], "<span style='color:blue;'>แก้ไข: <b>หน่วย</b> ...OK!</span>");
        }
    } else {
        array_push($_SESSION['editKeyMsg'], "<span style='color:red;'>ไม่มีการเปลี่ยนแปลงใดๆเกิดขึ้น</span>");
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

        echo 'SESSION = ';
        print_r($_SESSION);
        echo '<br/>POST = <br/>';
        print_r($_POST);
        ?>

        <div class="row">

            <div class="col-md-2 sidebar">
                <?php
                //status bar
                include 'sidebar.php';

                //print_r($_SESSION['editKeyMsg']);
                echo "<div class = 'alert alert-info'>";
                echo "<b>สถานะ:</b><br/>";
                if ($_SESSION['editKeyMsg'][0] != "") {
                    foreach ($_SESSION['editKeyMsg'] as $v) {
                        echo $v . "<br/>";
                    }
                    unset($_SESSION['editKeyMsg']);
                } else {
                    echo "-";
                }
                echo "</div>";
                ?>
            </div>

            <div class="col-md-10">

                <!-- main container -->
                <div class="container-fluid">

                    <div class="page-header">
                        <h2>แก้ไขรายชื่อกลางเครื่องมือเครื่องใช้ <small></small></h2>
                    </div>


                    <div class="row">
                        <div class="col-md-8">
                            <div class="col-md-12">
                                <?php
                                $thisQS = "SELECT k.*, d.listDivision"
                                        . " FROM key_item k"
                                        . " INNER JOIN list_division d"
                                        . " ON k.divisionID = d.divisionID"
                                        . " AND key_id = '" . $_GET['kid'] . "'";
                                $thisQry = mysqli_query($connection, $thisQS);

                                $row = mysqli_fetch_assoc($thisQry);
                                ?>
                            </div>

                            <form id="mainForm" action="" method="post">


                                <div class="col-md-12">
                                    <div class="col-md-3" align="right" style="padding:0.4em"><b>ชื่อกลาง</b></div> 
                                    <div class="col-md-4"><p type="text" class="form-control-static"><?php echo $row['key_detail']; ?></div>
                                    <div class="col-md-4"><input type="text" class="form-control input-sm" name="edited_detail" value="<?= $row['key_detail']; ?>"></div>
                                    <div class="col-md-1"></div>
                                </div>

                                <div class="col-md-12">
                                    <div class="col-md-3" align="right" style="padding:0.4em"><b>หน่วย</b></div> 
                                    <div class="col-md-4"><p type="text" class="form-control-static"><?php echo $row['key_suffix']; ?></div>
                                    <div class="col-md-4"><input type="text" class="form-control input-sm" name="edited_sfx" value="<?php echo $row['key_suffix']; ?>"></div>
                                    <div class="col-md-1"></div>
                                </div>

                                <div class="col-md-12">
                                    <div class="col-md-3" align="right" style="padding:0.4em"><b>ผู้เพิ่มคนแรก</b></div>
                                    <div class="col-md-4"><p type="text" class="form-control-static"><?php echo $row['listDivision'] ?></div>
                                    <!--
                                    <div class="col-md-4">
                                        <select class="form-control" name="updated_div">
                                            <option></option>
                                    <?php /*
                                      $divQS = "SELECT `divisionID`,`listDivision` FROM `list_division` ORDER BY `divisionID` ASC";
                                      $divQry = mysqli_query($connection, $divQS);
                                      while ($rowDiv = mysqli_fetch_assoc($divQry)) {
                                      ?>
                                      <option <?php
                                      if ($row['listDivision'] == $rowDiv['listDivision']) {
                                      echo 'selected';
                                      }
                                      ?> >
                                      <?php echo $rowDiv['listDivision'] ?>
                                      </option>
                                      <?php } */ ?>
                                        </select>
                                    </div>
                                    -->
                                    <div class="col-md-1"></div>
                                </div>

                                <!-- input button -->
                                <div id="submitBtn" class="form-group col-md-12" align="center" style="padding: 1em;">
                                    <button class="btn btn-lg btn-warning" type="submit" name="submitBtn" value="submit">
                                        <span class="glyphicon glyphicon-pencil"></span>&nbsp;แก้ไข
                                    </button>
                                </div>  <!-- /input button -->
                            </form>

                            <!-- แสดงรายการแก้ไข -->
                            แสดงตารางรายการแก้ไขทั้งหมดที่นี่

                        </div> <!--/.row -->
                    </div>
                </div> <!-- /main container -->
            </div> <!-- /.col-md-10 -->





        </div> <!-- /.row -->



        <?php require("main_script.php"); ?>

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
