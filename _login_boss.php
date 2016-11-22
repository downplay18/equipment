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

unset($_SESSION['loginMsg']);
?>

<html>
    <head>

        <title>ADMIN</title>
        <?php include 'main_head.php'; ?>
        <script src="fusioncharts/fusioncharts-suite-xt/js/fusioncharts.js" type="text/javascript"></script>
        <script src="fusioncharts/fusioncharts-suite-xt/js/fusioncharts.charts.js" type="text/javascript"></script>
        <script src="fusioncharts/fusioncharts-suite-xt/js/themes/fusioncharts.theme.zune.js" type="text/javascript"></script>
    </head>

    <body>
        <?php
        include("navbar.php");

        
          echo '<br/>';
          echo 'SESSION = ';
          print_r($_SESSION);
          echo '<br/>loginResult =<br/>';
          print_r($loginResult);
          echo '<br/>POST = <br/>';
          print_r($_POST); 
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
                                $ulistQS = "
SELECT user_id, name
FROM `user`
WHERE division LIKE '". $_SESSION['division'] ."'
AND status = 'USER'
UNION 
SELECT username, wname
FROM `worker`
WHERE wdivision = '". $_SESSION['division'] ."'";
                                $ulistQry = mysqli_query($connection, $ulistQS);
                                
                                while ($rowUlist = mysqli_fetch_assoc($ulistQry)) {
                                    echo "<option value='".$rowUlist['user_id']."'>" . $rowUlist['name'] . "</option>";
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
                            <form action="_login_boss_deleteSelkey.php" method="get" style="margin: 0; padding: 0;">
                                <b>ผู้ดูแลประจำกลุ่มงานของคุณคือ </b>
                                <?php
                                $cuserQS = "
SELECT wname , mykey
FROM worker
INNER JOIN user_config
ON cname='". $_SESSION['name'] ."'
AND username=mykey
                                    ";
                                $cuserQry = mysqli_query($connection, $cuserQS) or die("cuserQS fail: " . mysqli_error($connection));
                                $cuserResult = mysqli_fetch_assoc($cuserQry);
                                echo $cuserResult['wname'];
                                ?>
                                
                                
                                <a  href="_login_boss_deleteSelkey.php?getmykey=<?= $cuserResult['mykey'] ?>" class="btn btn-danger btn-sm" type="submit">
                                    <span class="glyphicon glyphicon-remove"></span>
                                </a>
                                
                            </form>
                        </div>
                    <?php } ?>





<?php
//อนุมัติ key_item ที่ถูกเพิ่มโดย KEY
$keyKnownQS = "SELECT key_id, key_detail, key_suffix, divisionID, lastEditID, key_known"
        . " FROM key_item"
        . " WHERE "
        . " AND key_known = ''";

echo "<pre>";
print_r($keyKnownQS);
echo "</pre>";
?>




                        
                        
                        







        </div> <!-- /.container-fluid -->
    </div> <!-- /.col-md-10 -->

</div> <!-- /.row -->









<?php include 'main_script.php'; ?>
<script src="js/jQueryChkbxBtn.js" type="text/javascript"></script>


</body>
</html>
