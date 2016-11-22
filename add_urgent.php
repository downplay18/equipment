<?php
//var_dump($_SESSION);
session_start();
error_reporting(0);
require_once 'connection.php';
include 'root_url.php';

if ($_SESSION['user_id'] == "") {
    //header("Location: $root_url/index.php", true, 302);
    exit();
}

unset($_SESSION['detail']);
unset($_SESSION['suffix']);
unset($_SESSION['owner']);
?>




<html>
    <head>

        <title>ADMIN</title>
        <!-- Bootstrap Core CSS -->
        <?php require 'main_head.php'; ?>
    </head>

    <body>
        <?php
        /* navbar */
        /* ไม่ใช้ case unauthen เพราะไม่มีสิทธิ์เข้าหน้านี้อยู่แล้ว */
        include 'navbar.php';
        /*
        echo 'SESSION = ';
        print_r($_SESSION);
        echo '<br/>POST = <br/>';
        print_r($_POST); */
        ?>

        <div class="row">

            <div class="col-md-2 sidebar">
                <?php include 'sidebar.php'; ?>
                <div class = 'alert alert-warning'>
                    <b>สถานะ:</b><br/>
                    <?php
                    if (isset($_SESSION['addUrgentMsg'])) {
                        foreach ($_SESSION['addUrgentMsg'] as $v) {
                            echo $v . "<br/>";
                        }
                        unset($_SESSION['addUrgentMsg']);
                    } else {
                        echo "-";
                    }
                    ?>
                </div>
            </div>



            <div class="col-md-10">

                <!-- main container -->
                <div class="container-fluid">

                    <div class="page-header">
                        <h2>ซื้อด่วน <small>บันทึกใบสั่งซื้อ(เร่งด่วน)</small></h2>
                    </div>



                    <!-- ทดสอบอัปโหลดไฟล์
                    <form action="upload.php" method="post" enctype="multipart/form-data">
    Select image to upload:
    <input type="file" name="fileToUpload" id="fileToUpload">
    <input type="submit" value="Upload Image" name="submit">
</form>
                    -->




                    <!-- form and submit button -->
                    <form id="mainForm" action="add_urgent_process.php" method="post" enctype="multipart/form-data">

                        <!-- เลขที่ใบเสร็จ+วันที่+กลุ่มงานเข้าของรายการ -->
                        <div class="col-md-12">

                            <div class="row alert alert-warning">

                                <div class="col-md-4"></div>

                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-addon">วันที่ใบเสร็จ</span>
                                        <input type="date" class="form-control" name="var_slipDate" required>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <!--
                                    <div class="input-group">
                                        <span class="input-group-addon">เจ้าของ</span>
                                        <input type="text" class="form-control" name="var_adder" value="<?= $_SESSION["division"]; ?>" style="padding: 0.4em;" readonly>
                                    </div> 
                                    -->

                                    <div class="input-group">
                                        <span class="input-group-addon">เลือกเจ้าของ</span>
                                        <select class="form-control" name="var_owner" required>
                                            <option></option>
                                            <?php
                                            //เรียก list กลุ่มงานทั้งหมด
                                            $divQS = "SELECT `listDivision` FROM `list_division` ORDER BY `divisionID` ASC";
                                            $divQry = mysqli_query($connection, $divQS);
                                            while ($rowDiv = mysqli_fetch_assoc($divQry)) {
                                                ?>
                                                <option><?php echo $rowDiv['listDivision']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div> 

                                </div>

                            </div>
                        </div> <!-- /เลขที่ใบเสร็จ+วันที่+กลุ่มงานเข้าของรายการ -->







                        <!-- file upload -->
                        <div class="col-md-12 well" align="center">
                            <input type="file" name="fileToUpload" id="fileToUpload">
                        </div>  <!-- /file upload -->








                        <!-- input field -->
                        <div>

                            <table class="table table-bordered">
                                <col width="0.5%"> <!-- checkbox -->
                                <col width="0.5%"> <!-- row number i -->
                                <col width="30%"> <!-- detail -->
                                <col width="5%"> <!-- slipSuffix -->
                                <col width="5%"> <!-- qty -->
                                <col width="5%"> <!-- unitPrice -->
                                <col width="5%"> <!-- amount -->
                                <col width="5%"> <!-- lastSuffix -->
                                <col width="5%"> <!-- lastQty -->
                                <tr>
                                    <th><input class='check_all' type='checkbox' onclick="select_all()"/></th>
                                    <th>#</th>
                                    <th>รายการ(ชื่อตามรายงานการซื้อ/จ้างเร่งด่วน)</th>
                                    <th>หน่วย</th>
                                    <th>จำนวน</th>
                                    <th>ราคาหน่วยละ</th>
                                    <th>จำนวนเงิน</th>
                                </tr>
                                <tr>
                                    <td><input type='checkbox' class='case'/></td>
                                    <td><span id='snum'>1.</span></td>

                                    <td><input class="form-control" type='text' id='varDetail_1' name='varDetail[]' maxlength="100" autocomplete="off" required/></td>
                                    <td><input class="form-control" type='text' id='var_lastSuffix_1' name='var_lastSuffix[]' readonly required/></td>
                                    <td><input class="form-control" type='number' id='var_qty_1' name='var_qty[]' autocomplete="off" required/></td>
                                    <td><input class="form-control" type='number' id='var_unitPrice_1' name='var_unitPrice[]' autocomplete="off" required/></td>
                                    <td><input class="form-control" type='number' id='var_amount_1' name='var_amount[]' autocomplete="off" required/></td>
                                </tr>
                            </table>
                            <button type="button" class='btn btn-danger delete'><span class="glyphicon glyphicon-minus-sign"></span> Delete</button>
                            <button type="button" class='btn btn-success addmore'><span class="glyphicon glyphicon-plus-sign"></span> Add More</button>
                            <span style="font-size: 1.25em;color: red;">  ***หากไม่มีรายชื่อเครื่องมือฯ ที่ต้องการในลิสต์ ให้เพิ่ม <a href="key_item.php" target="_blank">ที่นี่</a> ก่อน</span>
                        </div> <!-- /input field -->

                        <!-- วางจุดที่จะให้เพิ่ม elementจากการกดปุ่ม +เพิ่มรายการ -->
                        <div class="input_fields_wrap"></div>


                        <!-- ยอดรวม+รวมสุทธิ -->
                        <div align="center">
                            <div class="col-md-1"></div>
                            <div class="col-md-5">
                                <label for="purpose">เพื่อใช้งาน</label>
                                <td><input id="purpose" class="form-control" type='text' name='var_purpose' required/> </td>
                            </div>
                            <div class="col-md-2">
                                <label>สถานที่ใช้งาน</label>
                                <div class="form-group">
                                    <select class="form-control" name="var_site" required>
                                        <option></option>
                                        <?php
                                        $buildingQS = "SELECT `buildingID`,`listBuilding` FROM `list_building` ORDER BY `buildingID` ASC";
                                        $buildingQry = mysqli_query($connection, $buildingQS);
                                        while ($rowBuilding = mysqli_fetch_assoc($buildingQry)) {
                                            ?>
                                            <option <?php
                                            if ($rowBuilding['listBuilding'] == $_SESSION['lastTakeSite']) {
                                                echo 'selected';
                                            }
                                            ?>><?php echo $rowBuilding['listBuilding'] ?></option>
                                            <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3"> 
                                <label>รวมเงิน</label>
                                <input type="number" class="form-control" name="var_subTotal" min="0" step="0.01" title="ตัวเลขเท่านั้น!" required>
                            </div>
                            <div class="col-md-1"></div>
                        </div> <!-- /ยอดรวม+รวมสุทธิ -->

                        <!-- ปุ่มตกลงและรีเซ็ท -->
                        <div class="form-group col-md-12" align="center" style="padding: 20px;">

                            <button class="btn btn-sm btn-default" type="reset">
                                <span class="glyphicon glyphicon-repeat"></span>&nbsp;รีเซ็ท
                            </button>

                            <button id="submitBtn" class="btn btn-lg btn-warning" type="submit">
                                <span class="glyphicon glyphicon-ok"></span>&nbsp;เสร็จสิ้น
                            </button>

                        </div> <!-- /ปุ่มตกลงและรีเซ็ท -->

                    </form> <!-- /form and submit button -->
















                    <div class="container col-md-4">
                        <div class = "alert alert-warning">
                            <code>!</code> ไฟล์ที่อนุญาต pdf, doc, docx, jpg, jpeg, png, gif
                        </div> 
                    </div>

                    <div class="container col-md-4">
                        <div class = "alert alert-warning">
                            <span class = "label label-info">INFO</span> NOTHING<br/>
                        </div> 

                    </div>

                    <div class="container col-md-4">
                        <div class = "alert alert-warning"> 
                            <span class = "label label-warning">INFO</span> NOTHING<br/>
                        </div> 
                    </div>

                </div> <!-- /main container -->
            </div> <!-- /.col-md-10 -->





        </div> <!-- /.row -->



        <?php require("main_script.php"); ?>
        <script src="js/autocWithAddRow_urgent.js" type="text/javascript"></script>

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
