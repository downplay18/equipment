<?php
//var_dump($_SESSION);
session_start();
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
if (isset($_POST['fileToUpload'])) {
    //echo "xxx";
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
                        <h2>เพิ่มใบเสร็จ</h2>
                    </div>

                    <div class="col-md-12 col-md-offset-1">
                        <?php
                        $editQS = "SELECT * FROM item_add_record WHERE add_id = '" . $_GET['add_id'] . "'";
                        $editQry = mysqli_query($connection, $editQS) or die(mysqli_error($connection));
                        $row = mysqli_fetch_assoc($editQry);

                        $format = "เพิ่มใบเสร็จของ: %s <br/>"
                                . "วันที่ใบเสร็จ: %s<br/>";
                        echo sprintf($format, $row['add_detail'], $row['slip_date']);
                        ?>
                    </div>

                    <div class="col-md-12" style="padding: 1em;">
                        <form id="mainForm" action="" method="post">
                            <div class="col-md-3 col-md-offset-1">


                                <!-- upload file -->
                                <div class="col-md-12 well" align="center">
                                    <input type="file" name="fileToUpload" id="fileToUpload">
                                </div>  <!-- /file upload -->


                            </div>
                            <div class="col-md-9"></div>

                            <div class="col-md-3 col-md-offset-1" align="center">
                                <button id="submitBtn" class="btn btn-success" type="submit" name="submitBtn" value="submit"><span class="glyphicon glyphicon-check"></span> ยืนยัน</button>
                            </div>
                        </form>
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
