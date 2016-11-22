<?php
//var_dump($_SESSION);
session_start();
//error_reporting(0);
require_once 'connection.php';

include 'root_url.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: $root_url/index.php", true, 302);
    exit();
}
if ($_SESSION['status'] == "BOSS") { //เพราะ ขี้เกียจเขียน 2เคส ทั้ง USER และ ADMIN
    header("Location: $root_url/_login_check.php", true, 302);
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
        include("navbar.php");

        /*
          echo 'SESSION = <br/>';
          print_r($_SESSION);
          echo '<br/>POST = <br/>';
          print_r($_POST); */
        ?>

        <div class="row">
            <div class="col-md-2 sidebar">
                <?php include 'sidebar.php'; ?>
            </div>


            <div class="col-md-10">
                <div class="container-fluid">

                    <div class="col-md-6">
                        <div class="alert alert-info">

                            <h4 align="center">รายการที่สนใจ
                                <a class="btn btn-info" href="user_select_fav.php" target="" role="button"><span class="glyphicon glyphicon-edit"></span></a>
                            </h4>

                            <?php
                            //<!-- part1 favlist+favCount -->
                            //แสดงรายการที่สนใจที่ user เลือกเอาไว้
                            $favQS = "SELECT `favlist` FROM `user_favlist` WHERE `uid` LIKE " . $_SESSION['user_id']; /* `uid` & `favlist` */
                            $favQry = mysqli_query($connection, $favQS) or die("<br/>_login_user favQS คิวรี่ล้มเหลว: " . mysqli_error($connection));
                            $favRow = mysqli_fetch_assoc($favQry);
                            $favEx = explode("|", $favRow['favlist']); /* $favEx เป็นarray เก็บ iid */

                            $itemQS = "SELECT `iid`,`detail`,`suffix`,`quantity` FROM `item` WHERE `owner` LIKE '" . $_SESSION['division'] . "'";
                            $itemQry = mysqli_query($connection, $itemQS) or die("<br/>_login_user itemQS คิวรี่ล้มเหลว: " . mysqli_error($connection));

                            //แสดงรายการที่สนใจพร้อมจำนวนปัจจุบันที่เหลืออยู่ ท่ายากตรงที่เก็บ favlist เป็น iid ไม่ใช่ detail
                            $favlistDetail = ""; //เป็น detail ของ favlist
                            while ($itemRow = mysqli_fetch_assoc($itemQry)) {
                                foreach ($favEx as $value) { //แกะ $favEx ออกมาเป็น $favlistDetail
                                    if ($value == $itemRow['iid']) {
                                        //echo "<br/><kbd>" . $itemRow['iid'] . "</kbd> " . $itemRow['detail'] . " <b>(" . $itemRow['quantity'] . " " . $itemRow['suffix'] . ")</b>";
                                        $favlistDetail[] = $itemRow['detail'];
                                    }
                                }
                            }
                            /*
                              echo "<pre>favlistDetail_";
                              print_r($favlistDetail);
                              echo "</pre>"; */

                            //หารายการที่ add ล่าสุดจาก item_add_record
                            //ที่ไม่ใช้ count($favEx) เพราะมันนับรวม empty string เป็น 1 ด้วย
                            $favCount = 0;
                            foreach ($favEx as $value) {
                                if ($value != "") {
                                    $favCount++;
                                }
                            }
                            //echo '<br>favCount=' . $favCount;
                            ?> <!-- /part1 favlist+favCount -->

                            <?php
                            if ($favCount == 0) { //อันดับแรกต้องเช็คก่อนว่ามี favlist หรือไม่
                                echo "<br/><div align='center'>ไม่มีรายการเฝ้าดู</div>";
                            } else {
                                //สร้างตารางดังนี้
                                /* ---0---+-----1----+-----2-----+---3---+----4---+----5-----+---6----+
                                 * favlist|maxAddDate|maxTakeDate|takeSum|diffdate|unitPerDay|forecast|
                                 * -------|----------|-----------|-------|--------|----------|--------|
                                 *        |          |           |       |        |          |        | */
                                //สร้าง array ขึ้นมารองรับตารางข้างบนก่อน
                                for ($d = 0; $d < $favCount; $d++) {
                                    $forecast[$d]['favlist'] = "";
                                    $forecast[$d]['maxAddDate'] = "";
                                    $forecast[$d]['maxTakeDate'] = "";
                                    $forecast[$d]['diffdate'] = 0;
                                    $forecast[$d]['takeSum'] = 0;
                                    $forecast[$d]['unitPerDay'] = 0;
                                    $forecast[$d]['forecast'] = "";
                                }

                                //ใส่ favlist--------------------------------------------------------------------------------
                                for ($z = 0; $z < $favCount; $z++) {
                                    $forecast[$z]['favlist'] = $favlistDetail[$z];
                                }

                                //หา maxAddDate--------------------------------------------------------------------------------
                                if ($favCount == 1) { //เมื่อ favlist ตัวเดียว
                                    $lastAddQS = "SELECT `add_detail`,MAX(`add_date`) AS maxAddDate FROM `item_add_record`"
                                            . " WHERE `add_detail` IN (SELECT `add_detail` FROM `item_add_record`"
                                            . " WHERE `add_detail` LIKE '" . $favlistDetail[0] . "'";
                                } else { //เมื่อมี favlist > 1
                                    $lastAddQS = "SELECT `add_detail`,MAX(`add_date`) AS maxAddDate FROM `item_add_record`"
                                            . " WHERE `add_detail` IN (SELECT `add_detail` FROM `item_add_record`"
                                            . " WHERE `add_detail` LIKE '" . $favlistDetail[0] . "'";
                                    $c = 1;
                                    foreach ($favlistDetail as $value) {
                                        if ($c == 1) { //skip สมาชิกตัวแรกเพราะทำข้างบนไปแล้ว
                                            $c++;
                                            continue;
                                        } else {
                                            $lastAddQS .= " OR `add_detail` LIKE '" . $value . "'";
                                        }
                                    }
                                } $lastAddQS .= ") GROUP BY `add_detail`;";
                                //echo "<br><br>lastAddQS= " . $lastAddQS;
                                $lastAddQry = mysqli_query($connection, $lastAddQS);
                                while ($rowAQ = mysqli_fetch_assoc($lastAddQry)) {
                                    for ($i = 0; $i < $favCount; $i++) {
                                        if ($forecast[$i]['favlist'] == $rowAQ['add_detail']) {
                                            $forecast[$i]['maxAddDate'] = $rowAQ['maxAddDate'];
                                        }
                                    }
                                }

                                //หา maxTakeDate--------------------------------------------------------------------------------
                                if ($favCount == 1) { //เมื่อ favlist ตัวเดียว
                                    $lastTakeQS = "SELECT `take_detail`,MAX(`take_date`) AS maxTakeDate FROM `item_take_record`"
                                            . " WHERE `take_detail` IN (SELECT `take_detail` FROM `item_take_record`"
                                            . " WHERE `take_detail` LIKE '" . $favlistDetail[0] . "'";
                                } else { //เมื่อมี favlist > 1
                                    $lastTakeQS = "SELECT `take_detail`,MAX(`take_date`) AS maxTakeDate FROM `item_take_record`"
                                            . " WHERE `take_detail` IN (SELECT `take_detail` FROM `item_take_record`"
                                            . " WHERE `take_detail` LIKE '" . $favlistDetail[0] . "'";
                                    $c = 1;
                                    foreach ($favlistDetail as $value) {
                                        if ($c == 1) { //skip สมาชิกตัวแรกเพราะทำข้างบนไปแล้ว
                                            $c++;
                                            continue;
                                        } else {
                                            $lastTakeQS .= " OR `take_detail` LIKE '" . $value . "'";
                                        }
                                    }
                                } $lastTakeQS .= ") GROUP BY `take_detail`;";
                                //echo "<br><br>lastTakeQS= " . $lastTakeQS;
                                $lastTakeQry = mysqli_query($connection, $lastTakeQS);
                                while ($rowTQ = mysqli_fetch_assoc($lastTakeQry)) {
                                    for ($i = 0; $i < $favCount; $i++) {
                                        if ($forecast[$i]['favlist'] == $rowTQ['take_detail']) {
                                            $forecast[$i]['maxTakeDate'] = $rowTQ['maxTakeDate'];
                                        }
                                    }
                                }

                                //หา takeSum--------------------------------------------------------------------------------
                                $takeSumQS = "SELECT `take_detail`,`take_qty` FROM `item_take_record`";
                                $takeSumQry = mysqli_query($connection, $takeSumQS);
                                while ($rowTS = mysqli_fetch_assoc($takeSumQry)) {
                                    for ($i = 0; $i < $favCount; $i++) {
                                        if ($forecast[$i]['favlist'] == $rowTS['take_detail']) {
                                            $forecast[$i]['takeSum'] += $rowTS['take_qty'];
                                        }
                                    }
                                }

                                //หา diffdate--------------------------------------------------------------------------------
                                for ($i = 0; $i < $favCount; $i++) {
                                    $day1 = strtotime($forecast[$i]['maxAddDate']);
                                    $day2 = strtotime($forecast[$i]['maxTakeDate']);
                                    $forecast[$i]['diffdate'] = (abs($day1 - $day2)) / (60 * 60 * 24);
                                }

                                //หา unitPerDay--------------------------------------------------------------------------------
                                for ($i = 0; $i < $favCount; $i++) {
                                    //เหตุที่ใช้ ceiling แทนที่จะเป็น floor เพราะอัตราการใช้จริงมีแนวโน้มน้อยกว่าที่คำนวณได้ 
                                    //forecast มีหน่วยเป็น ชิ้นต่อวัน
                                    if ($forecast[$i]['diffdate'] == 0 || $forecast[$i]['takeSum'] == 0) {
                                        $forecast[$i]['unitPerDay'] = "diffdate หรือ takeSum เป็นศูนย์ ไม่สามารถคำนวณได้";
                                    } else {
                                        $forecast[$i]['unitPerDay'] = ceil($forecast[$i]['diffdate'] / $forecast[$i]['takeSum']);
                                    }
                                }

                                //หา forecast + ส่วนการแสดงค่า ----------------------------------------------------------------
                                $remainQS = "SELECT `iid`,`detail`,`quantity`,`suffix` FROM `item`";
                                $remainQry = mysqli_query($connection, $remainQS);
                                while ($rowRemain = mysqli_fetch_assoc($remainQry)) {
                                    for ($i = 0; $i < $favCount; $i++) {
                                        if ($rowRemain['detail'] == $forecast[$i]['favlist']) { //เช็คให้แสดงเอาเฉพาะตัวที่มีใน favlist
                                            $forecast[$i]['forecast'] = $rowRemain['quantity'] * $forecast[$i]['unitPerDay'];
                                            echo "<br/><kbd>" . $rowRemain['iid'] . "</kbd> <b>[</b>" . $rowRemain['detail'] . "<b>]</b>"
                                            . " จำนวน " . $rowRemain['quantity'] . " " . $rowRemain['suffix']
                                            . " <br/>เบิกใช้งาน " . $forecast[$i]['takeSum'] . " " . $rowRemain['suffix'] . " ในเวลา " . $forecast[$i]['diffdate'] . " วัน"
                                            . " <br/><font color='red'>(คาดว่าจะหมดในอีก " . $forecast[$i]['forecast'] . " วัน)</font><br/>";
                                        }
                                    }
                                }

                                /*
                                  echo "<pre>";
                                  print_r($forecast);
                                  echo "</pre>"; */
                            }
                            ?>

                        </div> <!-- /.clert -->
                    </div> <!-- /.col-md-12 -->


                    <div class="col-md-6">
                        <?php
                        /*PART หาdetailที่userเลือกไว้*/
                        $iQry = mysqli_query($connection, "SELECT `detail`,`quantity`,`suffix`,`owner` FROM `item`"
                                . " WHERE `owner` LIKE '" . $_SESSION['division'] . "'")
                                or die("iQry fail!: ".  mysqli_error($connection));
                        while ($rowi = mysqli_fetch_assoc($iQry)) {
                            echo $rowi['detail'] . "<br>";
                        }

                        /*PART คำนวณ*/
                        //ดึง SUM(add) ย้อนหลัง 1 ปี
                        $aQry = mysqli_query($connection, "SELECT SUM(`add_qty`) as sum_addQty"
                                . " FROM `item_add_record`"
                                . " WHERE `adder` LIKE '" . $_SESSION['division'] . "'"
                                . " AND `add_date` BETWEEN curdate()-365 AND curdate()")
                                or die("aQry fail!: ".  mysqli_error($connection));;
                        //ดึง SUM(take) ย้อนหลัง 1 ปี
                        $tQry = mysqli_query($connection, "SELECT SUM(`take_qty`) as sum_takeQty"
                                . " FROM `item_take_record`"
                                . " WHERE `taker` LIKE '" . $_SESSION['division'] . "'")
                                or die("tQry fail!: ".  mysqli_error($connection));;
                        
                        $rowa = mysqli_fetch_assoc($aQry);
                        $rowt = mysqli_fetch_assoc($tQry);
                        
                        //หาปริมาณใช้งานต่อวัน โดย SUM(take)หาร365 แบบปัดเศษขึ้น
                        $itemPerDay = ceil($rowt['sum_takeQty']/365);
                        
                        //คาดว่าจะหมด
                        $forecastDay = $rowa['sum_addQty']/$itemPerDay;
                        
                        echo $rowa['sum_addQty'] . "หาร".$rowt['sum_takeQty']."9jv;yo".$itemPerDay."sdfsd".$forecastDay;
                        ?>
                    </div>

                </div> <!-- /.container-fluid -->
            </div> <!-- /.col-md-10 -->

        </div> <!-- /.row -->





        <!--Script -->
<?php include 'main_script.php'; ?>


    </body>
</html>
