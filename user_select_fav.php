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
          echo '<br/>';
          echo 'SESSION = ';
          print_r($_SESSION);
          echo '<br/>POST = <br/>';
          print_r($_POST); */
        ?>

        <!-- Main container -->
        <div class="container-fluid">

            <?php include 'root_url.php'; ?>

            <!--
            <h3 class="page-header">ระบบเครื่องมือเครื่องใช้และวัสดุสิ้นเปลือง <small>กองพัฒนาด้านเทคโนโลยีโรงไฟฟ้าถ่านหินและเหมือง (กพทถ-ห.)</small></h3>
            -->

            <?php
            /* เรียก "รายการโปรด" ที่ user เคยเลือกไว้แล้วมาก่อน */
            $qss = "SELECT `userID`,`itemID` FROM `user_favlist` WHERE `userID` LIKE " . $_SESSION['user_id'];
            $qrs = mysqli_query($connection, $qss) or die("<br/>_login_user - user_fav คิวรี่ล้มเหลว!<br/>" . mysqli_error($connection));
            $rwarr = array();
            while ($row = mysqli_fetch_assoc($qrs)) {
                array_push($rwarr, $row['itemID']);
            }
            $rwarrSize = count($rwarr);
            /*
              echo 'rw=';
              print_r($rw);
              echo 'rwarr=';
              print_r($rwarr); */
            ?>


            <div class="row">
                <div class="col-md-2 sidebar">
                    <?php
                    include 'sidebar.php';
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
                    echo "</div>";
                    ?>
                </div>

                <div class="col-md-10">

                    <div class="page-header">
                        <h2>รายการที่สนใจ</h2>
                    </div>

                    <div class="col-md-4 col-md-offset-2">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>
                            <input type="text" id="search_live" class="form-control" placeholder="ค้นหาทันที..." autocomplete="on">
                        </div>
                    </div>

                    <div class="col-md-12 col-md-offset-2">
                        <?php
                        /* เรียก `iid`กับ `detail` มาแสดงให้ user เอาไว้เลือก */
                        //$qryArr = array('detail');
                        //$qryArr_size = count($qryArr);
                        $qstatement = "SELECT `iid`,`detail`,`suffix`,`quantity`,`owner` FROM `item` WHERE `owner` LIKE '" . $_SESSION['division'] . "'";
                        $itemQuery = mysqli_query($connection, $qstatement) or die("<br/>user_select_fav item table คิวรี่ล้มเหลว!<br/>" . mysql_error());
                        $count = mysqli_num_rows($itemQuery);
                        echo "<br/><h4>มีทั้งหมด " . $count . " รายการ ที่เป็นของ" . $_SESSION['division'] . "</h4><br/>";
                        ?> 
                    </div>

                    <div class="col-md-10 col-md-offset-2">
                        <form id='mainForm' action="user_select_fav_process.php" method="post">
                            <table id="search_table" width = "100%" border = "0">
                                <?php
                                while ($itemRow = mysqli_fetch_assoc($itemQuery)) {
                                    ?>
                                    <tr>
                                        <td style="padding: 0.1em">
                                            <span class="button-checkbox">
                                                <button type="button" class="btn" data-color="info" ><?php echo $itemRow['detail'] . '<b> (' . $itemRow['quantity'] . ' ' . $itemRow['suffix'] . ')</b>' ?></button>
                                                <input type="checkbox" class="hidden" name="check_favlist[]" value="<?php echo $itemRow['iid'] ?>" 
                                                <?php
                                                /* เช็คว่าก่อนหน้านี้userจำค่าอะไรไว้ */
                                                /* ถ้ามีค่าที่จำไว้ จะถูกติ๊กไว้โดย echo checked */
                                                foreach ($rwarr as $value) {
                                                    if ($value == $itemRow['iid']) {
                                                        echo 'checked';
                                                    }
                                                }
                                                ?>/>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </table>

                            <div class="col-md-12"><br/>------------------------<br/><br/></div>

                            <!-- submit button -->
                            <div class="form-group col-md-12">
                                <a class="btn btn-default" href="_login_user.php" target=""><span class="glyphicon glyphicon-backward"></span> กลับ</a>
                                <button id='submitBtn' class="btn btn-lg btn-success" type="submit">
                                    <span class="glyphicon glyphicon-pencil"></span>&nbsp;แก้ไข
                                </button>
                            </div>  <!-- /submit button -->
                        </form> <!-- /.form -->
                    </div>
                </div> <!-- /.col-md-8 -->
                <div class="col-md-2">
                </div>

                <br/>



            </div> <!-- /.row -->

        </div><!-- Main container -->









        <?php include 'main_script.php'; ?>

        <script src="js/jQueryChkbxBtn.js" type="text/javascript"></script>

        <!--Live Search Script -->
        <script>
            var $search_rows = $('#search_table tr');
            $('#search_live').keyup(function () {
                var val = $.trim($(this).val()).replace(/ +/g, ' ').toLowerCase();
                $search_rows.show().filter(function () {
                    var text = $(this).text().replace(/\s+/g, ' ').toLowerCase();
                    return !~text.indexOf(val);
                }).hide();
            });
        </script><!-- /Live Search Script -->

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
