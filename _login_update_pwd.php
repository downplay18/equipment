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
    $pwdQS = "SELECT password FROM user WHERE user_id = '" . $_SESSION['user_id'] . "'";
    $pwdQry = mysqli_query($connection, $pwdQS);
    $rowPwd = mysqli_fetch_assoc($pwdQry);

    print_r($rowPwd);
    
    $ok = 1;
    if ($rowPwd['password'] != $_POST['old_pwd']) {
        echo $rowPwd['password'] ."==". $_POST['old_pwd'];
        $ok = 0;
        array_push($_SESSION['editKeyMsg'], "<span style='color:red;font-size:1.25em;'>รหัสผ่านเก่าไม่ถูกต้อง...</span>");
    }
    if ($_POST['new_pwd'] != $_POST['retype_pwd']) {
        $ok = 0;
        array_push($_SESSION['editKeyMsg'], "<span style='color:red;font-size:1.25em;'>รหัสผ่านใหม่ไม่ตรงกัน...</span>");
    }
    if ($_POST['old_pwd'] == $_POST['new_pwd'] || $_POST['old_pwd'] == $_POST['retype_pwd']) {
        $ok = 0;
        array_push($_SESSION['editKeyMsg'], "<span style='color:red;font-size:1.25em;'>รหัสผ่านเก่ากับรหัสผ่านใหม่ ต้องไม่เหมือนกัน...</span>");
    }

    if ($ok == 1) {
        echo "query".$ok;
        $pwdChangeQS = "UPDATE user SET password='" . $_POST['new_pwd'] . "' WHERE user_id='" . $_SESSION['user_id'] . "'";
        $pwdChangeQry = mysqli_query($connection, $pwdChangeQS) or die(mysqli_error($connection));
        if (!$pwdChangeQry) {
            array_push($_SESSION['editKeyMsg'], "<span style='color:red;font-size:1.25em;'>ขั้นตอนการอัปเดตฐานข้อมูลล้มเหลว! ติดต่อผู้ดูแลระบบ...</span>");
        } else {
            array_push($_SESSION['editKeyMsg'], "<span style='color:blue;font-size:1.25em;'>เปลี่ยนรหัสผ่าน ...สำเร็จ!</span>");
        }
    }

    unset($_POST['old_pwd']);
    unset($_POST['new_pwd']);
    unset($_POST['retype_pwd']);
    unset($_POST['submitBtn']);
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
                  /*
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
                  echo "</div>"; */
                ?>
            </div>



            <div class="col-md-10">

                <!-- main container -->
                <div class="container-fluid">

                    <div class="page-header">
                        <h2>เปลี่ยนรหัสผ่าน <small></small></h2>
                    </div>


                    <div class="row">
                        <div class="col-md-8">

                            <form id="mainForm" action="" method="post">


                                <div class="col-md-12">
                                    <div class="col-md-4" align="right" style="padding:0.4em"><b>ป้อนรหัสผ่านเก่า</b></div> 
                                    <div class="col-md-4"><input type="text" class="form-control input-sm" name="old_pwd" autocomplete="off" required></div>
                                </div>

                                <div class="col-md-12">
                                    <div class="col-md-4" align="right" style="padding:0.4em"><b>รหัสผ่านใหม่</b></div> 
                                    <div class="col-md-4"><input type="password" class="form-control input-sm" name="new_pwd" autocomplete="off" required></div>
                                </div>

                                <div class="col-md-12">
                                    <div class="col-md-4" align="right" style="padding:0.4em"><b>รหัสผ่านใหม่อีกครั้ง</b></div> 
                                    <div class="col-md-4"><input type="password" class="form-control input-sm" name="retype_pwd" autocomplete="off" required></div>
                                </div>

                                <!-- input button -->
                                <div id="submitBtn" class="form-group col-md-12" align="center" style="padding: 1em;">
                                    <button class="btn btn-lg btn-warning" type="submit" name="submitBtn" value="submit">
                                        <span class="glyphicon glyphicon-pencil"></span>&nbsp;เปลี่ยนรหัสผ่าน
                                    </button>
                                </div>  <!-- /input button -->
                            </form>

                            <div class="col-md-6 col-md-offset-3">
                                <?php
                                echo "<center>";
                                echo "<div class = 'alert alert-default'>";
                                if ($_SESSION['editKeyMsg'][0] != "") {
                                    foreach ($_SESSION['editKeyMsg'] as $v) {
                                        echo $v . "<br/>";
                                        unset($_SESSION['editKeyMsg']);
                                    }
                                } else {
                                    echo "";
                                }
                                echo "</div>";
                                echo "</center>";
                                ?>
                            </div>

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
