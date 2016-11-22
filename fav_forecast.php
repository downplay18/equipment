<?php
//var_dump($_SESSION);
session_start();
//error_reporting(0);
require_once 'connection.php';
include 'root_url.php';

if ($_SESSION['user_id'] == "") {
    echo "<br/>โปรดยืนยันตัวตนก่อน !";
    exit();
}

/*
  if ($_SESSION['status'] != "USER") {
  echo "<br/>สำหรับ -พนักงาน- เท่านั้น!";
  exit();
  } */

require("connection.php");
?>

<?php
if (isset($_POST['itemAlert'])) {
    $qtyAlertQry = mysqli_query($connection, "INSERT INTO user_favlist (userID, itemID, qty_alert)"
            . " VALUES ('" . $_SESSION['user_id'] . "','" . $_GET['iid'] . "','" . $_POST['itemAlert'] . "')"
            . " ON DUPLICATE KEY UPDATE qty_alert = '" . $_POST['itemAlert'] . "'"
            ) or die(mysqli_error($connection));
    header("Location: $root_url/_login_check.php", true, 302);
}
?>

<html>
    <head>

        <?php include 'main_head.php'; ?>

    </head>

    <body>
        <?php
        /* navbar */
        /* ไม่ใช้ case unauthen เพราะไม่มีสิทธิ์เข้าหน้านี้อยู่แล้ว */
        require("navbar.php");

        /*
          echo 'SESSION = ';
          print_r($_SESSION);
          echo '<br/>POST = <br/>';
          print_r($_POST); */
        ?>

        <!-- Main container -->
        <div class="container-fluid">

            <?php include 'root_url.php'; ?>


            <div class="row">
                <div class="col-md-2 sidebar">
                    <?php
                    include 'sidebar.php';
                    /*
                      echo "<div class = 'alert alert-info'>";
                      echo "<b>สถานะ:</b><br/>";
                      if (isset($_SESSION['favMsg'])) {
                      foreach ($_SESSION['favMsg'] as $v) {
                      echo $v . "<br/>";
                      }
                      echo " ----- <br/>";
                      echo "มีรายการเฝ้าดูทั้งหมด " . $rwarrSize . " รายการ";
                      unset($_SESSION['favMsg']);
                      } else {
                      echo "มีรายการเฝ้าดูทั้งหมด " . $rwarrSize . " รายการ";
                      }
                      echo "</div>"; */
                    ?>
                </div>

                <div class="col-md-10">

                    <div class="page-header">
                        <h2>แก้ไขการแจ้งเตือน</h2>
                    </div>

                    <?php
                    $thisQS = "
SELECT detail, quantity, suffix, qty_alert, owner
    FROM 
    (
	SELECT itemID, qty_alert
        FROM user_favlist
        WHERE userID = '" . $_SESSION['user_id'] . "'
        AND itemID = '" . $_GET['iid'] . "'
    ) AS ok
LEFT JOIN item
ON iid=itemID;";
                    //echo $thisQS;
                    ?>
                    <div class="col-md-12 col-md-offset-1">
                        <?php
                        $thisQry = mysqli_query($connection, $thisQS) or die(mysqli_error($connection));
                        $thisRow = mysqli_fetch_assoc($thisQry);
                        $format = "ระบบจะแจ้งเตือนเมื่อ (<b>%s</b> ของ %s) <span style='color:red;'>เหลือน้อยกว่า %d %s</span>";
                        echo sprintf($format, $thisRow['detail'], $thisRow['owner'], $thisRow['qty_alert'], $thisRow['suffix']);
                        ?>
                    </div>

                    <div class="col-md-12" style="padding: 1em;">
                        <div class="col-md-3 col-md-offset-1">
                            <form id="mainForm" action="" method="post">
                                <input type="number" class="form-control" name="itemAlert" value="<?php echo $thisRow['qty_alert'] ?>"autocomplete="off"/>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-4 col-md-offset-1">
                            <a class="btn btn-default btn-sm" href="_login_user.php" target=""><span class="glyphicon glyphicon-backward"></span> กลับ</a>
                        
                            <button id="submitBtn" class="btn btn-success btn-lg" type="submit" name="submitBtn" value="submit"><span class="glyphicon glyphicon-check"></span> ยืนยัน</button>
                        
                    </div>


                </div> <!-- /.col-md-10 -->

            </div> <!-- /.row -->

        </div><!-- Main container -->









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
