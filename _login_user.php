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

unset($_SESSION['loginMsg']);

include("fusioncharts.php");
?>

<html>
    <head>

        <title>ADMIN</title>
        <!-- Bootstrap Core CSS -->
        <?php include 'main_head.php'; ?>
        <script src="fusioncharts/fusioncharts-suite-xt/js/fusioncharts.js" type="text/javascript"></script>
        <script src="fusioncharts/fusioncharts-suite-xt/js/fusioncharts.charts.js" type="text/javascript"></script>
        <script src="fusioncharts/fusioncharts-suite-xt/js/themes/fusioncharts.theme.zune.js" type="text/javascript"></script>

        <!--</head>-->

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

                    <h4 align="center">รายการที่สนใจ
                        <a class="btn btn-info btn-sm" href="user_select_fav.php" target="" role="button"><span class="glyphicon glyphicon-edit"></span></a>
                    </h4>



                    <?php
                    //ดึงรายการโปรดมาแสดง
                    $favShowQS = "SELECT iid, detail, suffix, quantity, IFNULL(qty_alert,0) AS qty_alert, owner"
                            . " FROM user_favlist, item "
                            . " WHERE userID = '" . $_SESSION['user_id'] . "'"
                            . " AND itemID = iid";
                    $favShowQry = mysqli_query($connection, $favShowQS);

                    $count = 1;
                    while ($rowFavShow = mysqli_fetch_assoc($favShowQry)) {

                        //โซนแสดงผลการ alert
                        date_default_timezone_set("Asia/Bangkok");
                        $array = array(//[0]=startdate,, [1]=enddate,, [2]=value
                            "label1" => array(date("2015-01-01"), date("Y-m-t", strtotime("today -5 month"))),
                            "label2" => array(date("Y-m-01", strtotime("today -4 month")), date("Y-m-t", strtotime("today -4 month"))),
                            "label3" => array(date("Y-m-01", strtotime("today -3 month")), date("Y-m-t", strtotime("today -3 month"))),
                            "label4" => array(date("Y-m-01", strtotime("today -2 month")), date("Y-m-t", strtotime("today -2 month"))),
                            "label5" => array(date("Y-m-01", strtotime("today -1 month")), date("Y-m-t", strtotime("today -1 month"))),
                            "label6" => array(date("Y-m-01"), date("Y-m-t")),
                        );


                        //สร้าง ตารางสำหรับเอาไปสร้างกราฟต่อ
                        $add1QS = "
                                    SELECT COALESCE(
                                    (   
                                        SELECT SUM(add_qty) AS value
                                        FROM item_add_record
                                        WHERE add_detail = '" . $rowFavShow['detail'] . "' 
                                        AND adder = '" . $_SESSION['division'] . "' 
                                        AND(add_date BETWEEN '" . $array['label1'][0] . "' AND '" . $array['label1'][1] . "')
                                        GROUP BY add_detail
                                    ),0) AS value";
                        $add1Qry = mysqli_query($connection, $add1QS);
                        $rowAdd1 = mysqli_fetch_assoc($add1Qry);
                        $array['label1'][2] = $rowAdd1['value'];
                        $take1QS = "
                                    SELECT COALESCE(
                                    (   
                                        SELECT SUM(take_qty) AS tvalue
                                        FROM item_take_record
                                        WHERE take_detail = '" . $rowFavShow['detail'] . "' 
                                        AND taker = '" . $_SESSION['division'] . "' 
                                        AND(take_date BETWEEN '" . $array['label1'][0] . "' AND '" . $array['label1'][1] . "')
                                        GROUP BY take_detail
                                    ),0) AS tvalue";
                        $take1Qry = mysqli_query($connection, $take1QS);
                        $rowTake1 = mysqli_fetch_assoc($take1Qry);
                        $array['label1'][2] -= $rowTake1['tvalue'];


                        $add2QS = "
                                    SELECT COALESCE(
                                    (   
                                        SELECT SUM(add_qty) AS value
                                        FROM item_add_record
                                        WHERE add_detail = '" . $rowFavShow['detail'] . "' 
                                        AND adder = '" . $_SESSION['division'] . "' 
                                        AND(add_date BETWEEN '" . $array['label2'][0] . "' AND '" . $array['label2'][1] . "')
                                        GROUP BY add_detail
                                    ),0) AS value";
                        $add2Qry = mysqli_query($connection, $add2QS);
                        $rowAdd2 = mysqli_fetch_assoc($add2Qry);
                        $array['label2'][2] = $array['label1'][2] + $rowAdd2['value'];
                        $take2QS = "
                                    SELECT COALESCE(
                                    (   
                                        SELECT SUM(take_qty) AS tvalue
                                        FROM item_take_record
                                        WHERE take_detail = '" . $rowFavShow['detail'] . "' 
                                        AND taker = '" . $_SESSION['division'] . "' 
                                        AND(take_date BETWEEN '" . $array['label2'][0] . "' AND '" . $array['label2'][1] . "')
                                        GROUP BY take_detail
                                    ),0) AS tvalue";
                        $take2Qry = mysqli_query($connection, $take2QS);
                        $rowTake2 = mysqli_fetch_assoc($take2Qry);
                        $array['label2'][2] -= $rowTake2['tvalue'];

                        $add3QS = "
                                    SELECT COALESCE(
                                    (   
                                        SELECT SUM(add_qty) AS value
                                        FROM item_add_record
                                        WHERE add_detail = '" . $rowFavShow['detail'] . "' 
                                        AND adder = '" . $_SESSION['division'] . "' 
                                        AND(add_date BETWEEN '" . $array['label3'][0] . "' AND '" . $array['label3'][1] . "')
                                        GROUP BY add_detail
                                    ),0) AS value";
                        $add3Qry = mysqli_query($connection, $add3QS);
                        $rowAdd3 = mysqli_fetch_assoc($add3Qry);
                        $array['label3'][2] = $array['label2'][2] + $rowAdd3['value'];
                        $take3QS = "
                                    SELECT COALESCE(
                                    (   
                                        SELECT SUM(take_qty) AS tvalue
                                        FROM item_take_record
                                        WHERE take_detail = '" . $rowFavShow['detail'] . "' 
                                        AND taker = '" . $_SESSION['division'] . "' 
                                        AND(take_date BETWEEN '" . $array['label3'][0] . "' AND '" . $array['label3'][1] . "')
                                        GROUP BY take_detail
                                    ),0) AS tvalue";
                        $take3Qry = mysqli_query($connection, $take3QS);
                        $rowTake3 = mysqli_fetch_assoc($take3Qry);
                        $array['label3'][2] -= $rowTake3['tvalue'];

                        $add4QS = "
                                    SELECT COALESCE(
                                    (   
                                        SELECT SUM(add_qty) AS value
                                        FROM item_add_record
                                        WHERE add_detail = '" . $rowFavShow['detail'] . "' 
                                        AND adder = '" . $_SESSION['division'] . "' 
                                        AND(add_date BETWEEN '" . $array['label4'][0] . "' AND '" . $array['label4'][1] . "')
                                        GROUP BY add_detail
                                    ),0) AS value";
                        $add4Qry = mysqli_query($connection, $add4QS);
                        $rowAdd4 = mysqli_fetch_assoc($add4Qry);
                        $array['label4'][2] = $array['label3'][2] + $rowAdd4['value'];
                        $take4QS = "
                                    SELECT COALESCE(
                                    (   
                                        SELECT SUM(take_qty) AS tvalue
                                        FROM item_take_record
                                        WHERE take_detail = '" . $rowFavShow['detail'] . "' 
                                        AND taker = '" . $_SESSION['division'] . "' 
                                        AND(take_date BETWEEN '" . $array['label4'][0] . "' AND '" . $array['label4'][1] . "')
                                        GROUP BY take_detail
                                    ),0) AS tvalue";
                        $take4Qry = mysqli_query($connection, $take4QS);
                        $rowTake4 = mysqli_fetch_assoc($take4Qry);
                        $array['label4'][2] -= $rowTake4['tvalue'];

                        $add5QS = "
                                    SELECT COALESCE(
                                    (   
                                        SELECT SUM(add_qty) AS value
                                        FROM item_add_record
                                        WHERE add_detail = '" . $rowFavShow['detail'] . "' 
                                        AND adder = '" . $_SESSION['division'] . "' 
                                        AND(add_date BETWEEN '" . $array['label5'][0] . "' AND '" . $array['label5'][1] . "')
                                        GROUP BY add_detail
                                    ),0) AS value";
                        $add5Qry = mysqli_query($connection, $add5QS);
                        $rowAdd5 = mysqli_fetch_assoc($add5Qry);
                        $array['label5'][2] = $array['label4'][2] + $rowAdd5['value'];
                        $take5QS = "
                                    SELECT COALESCE(
                                    (   
                                        SELECT SUM(take_qty) AS tvalue
                                        FROM item_take_record
                                        WHERE take_detail = '" . $rowFavShow['detail'] . "' 
                                        AND taker = '" . $_SESSION['division'] . "' 
                                        AND(take_date BETWEEN '" . $array['label5'][0] . "' AND '" . $array['label5'][1] . "')
                                        GROUP BY take_detail
                                    ),0) AS tvalue";
                        $take5Qry = mysqli_query($connection, $take5QS);
                        $rowTake5 = mysqli_fetch_assoc($take5Qry);
                        $array['label5'][2] -= $rowTake5['tvalue'];

                        $add6QS = "
                                    SELECT COALESCE(
                                    (   
                                        SELECT SUM(add_qty) AS value
                                        FROM item_add_record
                                        WHERE add_detail = '" . $rowFavShow['detail'] . "' 
                                        AND adder = '" . $_SESSION['division'] . "' 
                                        AND(add_date BETWEEN '" . $array['label6'][0] . "' AND '" . $array['label6'][1] . "')
                                        GROUP BY add_detail
                                    ),0) AS value";
                        $add6Qry = mysqli_query($connection, $add6QS);
                        $rowAdd6 = mysqli_fetch_assoc($add6Qry);
                        $array['label6'][2] = $array['label5'][2] + $rowAdd6['value'];
                        $take6QS = "
                                    SELECT COALESCE(
                                    (   
                                        SELECT SUM(take_qty) AS tvalue
                                        FROM item_take_record
                                        WHERE take_detail = '" . $rowFavShow['detail'] . "' 
                                        AND taker = '" . $_SESSION['division'] . "' 
                                        AND(take_date BETWEEN '" . $array['label6'][0] . "' AND '" . $array['label6'][1] . "')
                                        GROUP BY take_detail
                                    ),0) AS tvalue";
                        $take6Qry = mysqli_query($connection, $take6QS);
                        $rowTake6 = mysqli_fetch_assoc($take6Qry);
                        $array['label6'][2] -= $rowTake6['tvalue'];



                        echo '<div class="col-md-4">';
                        if ($array['label6'][2] <= $rowFavShow['qty_alert']) {
                            echo '<div class="alert alert-danger">';
                        } else {
                            echo '<div class="alert alert-info">';
                        }
                        
                        ?>

                        <?php
                        /*
                          //แจ้งทางเมล์ เมื่อเหลือของตํ่ากว่าที่ตั้งไว้
                          if ($rowFavShow['quantity'] <= $rowFavShow['qty_alert']) {
                          echo "<span class='label label-danger'>ALERT!</span> ";

                          //E-Mail
                          $to = $_SESSION['user_id'] . "@egat.co.th";
                          $subject = "แจ้งเตือนรายการเครื่องมือเครื่องใช้เฝ้าระวัง ถึงกำหนดซื้อเพิ่มเติม!";
                          $msg = "รายการ" . $rowFavShow['detail'] . "คงเหลือจำนวน " . $rowFavShow['quantity'] . " " . $rowFavShow['suffix'];
                          mail($to, $subject, $msg);
                          } */

                        $format = "<a href='fav_forecast.php?iid=%d' target=''>%s (ปัจจุบัน %d %s) <br/>[ตั้งค่า:เตือนเมื่อน้อยกว่า %d %s]</a><br/><br/>";
                        echo "<center>" . sprintf($format, $rowFavShow['iid'], $rowFavShow['detail'], $rowFavShow['quantity'], $rowFavShow['suffix']
                                , $rowFavShow['qty_alert'], $rowFavShow['suffix']) . "</center>";
                        ?>

                        <?php
                        //ทำเดือนไทยใส่ใน $array['label?'][3]
                        ?>

                        <script>
                            FusionCharts.ready(function () {
                                var revenueChart = new FusionCharts({
                                    type: 'column2d',
                                    renderAt: 'chart-container-<?php echo $count ?>',
                                    width: '100%',
                                    height: '30%',
                                    dataFormat: 'json',
                                    dataSource: {
                                        "chart": {
                                            "caption": "",
                                            "subCaption": "",
                                            "xAxisName": "(เดือน/ปี)",
                                            "yAxisName": "จำนวนคงเหลือ (<?php echo $rowFavShow['suffix'] ?>)",
                                            "numberSuffix": "",
                                            "paletteColors": "#0075c2",
                                            "bgColor": "#ffffff",
                                            "borderAlpha": "20",
                                            "canvasBorderAlpha": "0",
                                            "usePlotGradientColor": "0",
                                            "plotBorderAlpha": "10",
                                            "placevaluesInside": "1",
                                            "rotatevalues": "1",
                                            "valueFontColor": "#ffffff",
                                            "showXAxisLine": "1",
                                            "xAxisLineColor": "#999999",
                                            "divlineColor": "#999999",
                                            "divLineIsDashed": "1",
                                            "showAlternateHGridColor": "0",
                                            "subcaptionFontBold": "0",
                                            "subcaptionFontSize": "14"
                                        },
                                        "data": [
                                            {
                                                "label": "<?php echo date("m/Y", strtotime("today -5 month")); ?>",
                                                "value": "<?php echo $array['label1'][2] ?>"
                                            },
                                            {
                                                "label": "<?php echo date("m/Y", strtotime("today -4 month")); ?>",
                                                "value": "<?php echo $array['label2'][2] ?>"
                                            },
                                            {
                                                "label": "<?php echo date("m/Y", strtotime("today -3 month")); ?>",
                                                "value": "<?php echo $array['label3'][2] ?>"
                                            },
                                            {
                                                "label": "<?php echo date("m/Y", strtotime("today -2 month")); ?>",
                                                "value": "<?php echo $array['label4'][2] ?>"
                                            },
                                            {
                                                "label": "<?php echo date("m/Y", strtotime("today -1 month")); ?>",
                                                "value": "<?php echo $array['label5'][2] ?>"
                                            },
                                            {
                                                "label": "<?php echo date("m/Y"); ?>",
                                                "value": "<?php echo $array['label6'][2] ?>"
                                            }
                                        ],
                                        "trendlines": [
                                            {
                                                "line": [
                                                    {
                                                        "startvalue": "<?php echo $rowFavShow['qty_alert'] ?>",
                                                        "color": "#ff0000",
                                                        "valueOnRight": "1",
                                                        "displayvalue": "<?php echo $rowFavShow['qty_alert'] ?>"
                                                    }
                                                ]
                                            }
                                        ]
                                    }
                                }).render();
                            });
                        </script>


                        <center>
                            <div  id="chart-container-<?php echo $count ?>">FusionCharts will render here</div>
                        </center>



                    </div> <!-- /.alert -->
                </div> <!-- /.col-md-6 -->

                <?php
                $count++;
            }
            ?>








        </div> <!-- /.container-fluid -->
    </div> <!-- /.col-md-10 -->

</div> <!-- /.row -->





<!--Script -->
<?php include 'main_script.php'; ?>

</body>
</html>
