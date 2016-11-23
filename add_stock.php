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
if ($_SESSION['status'] != "KEY") {
    header("Location: $root_url/index.php", true, 302);
}
?>

<html>
    <head>
        <?php include 'main_head.php'; ?>  
    </head>

    <body>

        <?php
        include("navbar.php");

//        echo '<br/>';
//        echo 'SESSION = ';
//        print_r($_SESSION);
//        echo '<br/>POST = <br/>';
//        print_r($_POST); 
        ?>


        <div class="row">

            <div class="col-md-2 sidebar">
                <?php include 'sidebar.php'; ?>
                <div class = 'alert alert-info'>
                    <b>สถานะ:</b><br/>
                    <?php
                    if (isset($_SESSION['addStockMsg'])) {
                        foreach ($_SESSION['addStockMsg'] as $v) {
                            echo $v . "<br/>";
                        }
                        unset($_SESSION['addStockMsg']);
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
                        <h2>เบิกคลัง <small>บันทึกใบเบิกคลัง</small></h2>
                    </div>

                    <!-- form and submit button -->
                    <form id="mainForm" action="add_stock_process.php" method="post" enctype="multipart/form-data">

                        <!-- เลขที่ใบเสร็จ+วันที่+กลุ่มงานเข้าของรายการ -->
                        <div class="col-md-12">

                            <div class="row alert alert-info">

                                <div class="col-md-4"></div>

                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-addon">วันที่สร้างเอกสาร</span>
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
                        </div> 

                        <!-- input field -->
                        <div>

                            <table class="table table-bordered">
                                <col width="0.5%"> <!-- checkbox -->
                                <col width="0.5%"> <!-- row number i -->
                                <col width="1%"> <!-- KID -->
                                <col width="10%"> <!-- ZDIR -->
                                <col width="20%"> <!-- detail -->
                                <col width="5%"> <!-- slipSuffix -->
                                <col width="5%"> <!-- qty -->
                                <col width="5%"> <!-- unitPrice -->
                                <col width="5%"> <!-- amount -->
                                <col width="5%"> <!-- lastSuffix -->
                                <col width="5%"> <!-- lastQty -->
                                <tr>
                                    <th><input class='check_all' type='checkbox' onclick="select_all()"/></th>
                                    <th>#</th>
                                    <th>id</th>
                                    <th>รหัสพัสดุ*</th>
                                    <th>รายการ</th>
                                    <th>หน่วย</th>
                                    <th>จำนวน</th>
                                    <!-- <th>ราคา/หน่วย</th> -->
                                    <!-- <th>จำนวนเงิน</th> -->
                                    <th bgcolor="#ffff66">หน่วยย่อย</th>
                                    <th bgcolor="#ffff66">แปลงหน่วย</th>
                                </tr>

                                <tr>

                                    <td><input type='checkbox' class='case'/></td>
                                    <td><span id='snum'>1.</span></td>
                                    
                                    <td><input class="form-control" type='number' id='varKID_1' name='varKID[]' required readonly/></td>
                                    <td><input class="form-control" type='text' id='varZDIR_1' name='varZDIR[]' required/></td>
                                    <td><input class="form-control col-md-9" type='text' id='varDetail_1' name='varDetail[]' style="width:100%;" maxlength="100" required readonly/></td>
                                    <td><input class="form-control" type='text' id='varSlipSuffix_1' name='varSlipSuffix[]' required readonly/> </td>
                                    <td><input class="form-control" type='number' id='varQty_1' name='varQty[]' required/> </td>
                                    <!--
                                    <td><input class="form-control" type='number' id='var_unitPrice_1' name='var_unitPrice[]' required/> </td>
                                    <td><input class="form-control" type='number' id='var_amount_1' name='var_amount[]' required/> </td>
                                    -->
                                    <td bgcolor="#ffffe6"><input class="form-control" type='text' id='varLastSuffix_1' name='varLastSuffix[]' required readonly/></td>
                                    <td bgcolor="#ffffe6"><input class="form-control" type='number' id='varLastQty_1' name='varLastQty[]' required/></td>
                                </tr>
                            </table>
                            <button type="button" class='btn btn-danger delete'><span class="glyphicon glyphicon-minus-sign"></span> Delete</button>
                            <button type="button" class='btn btn-success addmore'><span class="glyphicon glyphicon-plus-sign"></span> Add More</button>
                            <span style="font-size: 1.25em;color: red;">  ***หากไม่มีรายชื่อเครื่องมือฯ ที่ต้องการในลิสต์  
                                <a href="key_item.php" class="btn btn-warning btn-sm" target="_blank"><span class="glyphicon glyphicon-plus"></span> เพิ่มรายการใหม่</a></span>

                        </div> <!-- /input field -->

                        <!-- วางจุดที่จะให้เพิ่ม elementจากการกดปุ่ม +เพิ่มรายการ -->
                        <div class="input_fields_wrap"></div>


                        <!-- ยอดรวม+รวมสุทธิ --
                        <div align="center">

                            <div class="col-md-4"></div>

                            <div class="col-md-2"> 
                                <label>ยอดรวม</label>
                                <input type="number" class="form-control" name="var_subTotal" min="0" step="0.01" title="ตัวเลขเท่านั้น!" required>
                            </div>

                            <div class="col-md-2">
                                <label>รวมสุทธิ(+VAT)</label>
                                <input type="number" class="form-control" name="var_grandTotal" min="0" step="0.01" title="ทศนิยม 2 ตำแหน่ง" required>
                            </div>

                        </div> <!-- /ยอดรวม+รวมสุทธิ -->

                        <!-- ปุ่มตกลงและรีเซ็ท -->
                        <div class="form-group col-md-12" align="center" style="padding: 20px;">

                            <button class="btn btn-sm btn-default" type="reset">
                                <span class="glyphicon glyphicon-repeat"></span>&nbsp;รีเซ็ท
                            </button>

                            <button id="submitBtn" class="btn btn-lg btn-info" type="submit">
                                <span class="glyphicon glyphicon-ok"></span>&nbsp;เสร็จสิ้น
                            </button>

                        </div> <!-- /ปุ่มตกลงและรีเซ็ท -->

                    </form> <!-- /form and submit button -->


                    <div class="container col-md-4">
                        <div class = "alert alert-info">
                            <b>ขั้นตอนการเพิ่ม:</b>
                            </br>1. กรอกเลขที่ใบเสร็จ
                            </br>2. เลือกไฟล์ที่สแกนจากใบเสร็จ เป็นนามสกุล *.pdf, *.jpg, *.jpeg, *.png, *.gif เท่านั้น
                            </br>3. หาก 1 ใบเสร็จมีหลายรายการ ให้กดปุ่ม <kbd>+ Add More</kbd> 
                            </br>4. กรอกรายละเอียดให้ครบทุกช่อง 
                            </br>5. ตรวจสอบข้อมูลให้ถูกต้อง แล้วกดปุ่ม <kbd>เสร็จสิ้น</kbd>
                        </div> 
                    </div>

                    <div class="container col-md-4">
                        <div class = "alert alert-info"> 
                            <span class = "label label-warning">INFO</span> แปลงหน่วย คือ ใน 1 หน่วย มีกี่ชิ้น<br/>
                            <span class = "label label-warning">INFO</span> ใส่ค่าให้ครบทุกช่อง<br/>
                            <span class = "label label-warning">INFO</span> <font color="red">จน./หน่วย</font> คือ จำนวน "ของ" ใน 1 หน่วยใบเสร็จ<br/>
                            <span class = "label label-warning">INFO</span> ถ้า<font color="blue">หน่วยย่อย</font>ไม่เปลี่ยน ให้ใช้ <font color="red">จำนวนย่อย/หน่วยย่อย</font> เป็น 1<br/>
                            <span class = "label label-warning">INFO</span> <code>เลขที่ใบเสร็จที่กรอกลงไป</code> และ <code>เลขที่ใบเสร็จบนไฟล์</code> ต้องตรงกัน!<br/>
                            <span class = "label label-info">INFO</span> ราคาต่อรายการ** หมายถึง ผลรวมราคาของทุกชิ้นในรายการนั้น<br/>
                        </div> 
                    </div>

                </div> <!-- /main container -->
            </div> <!-- /.col-md-10 -->

        </div> <!-- /.row -->




        <?php include 'main_script.php'; ?>
        <script src="js/autocWithAddRow_stock.js" type="text/javascript"></script>

        <script> /*PREVENT DOUBLE SUBMIT: ทำให้ปุ่ม submit กดได้ครั้งเดียว ป้องกับปัญหาเนต lag แล้ว user กดเบิ้ล มันจะทำให้ส่งค่า 2 เท่า */
                                        $(document).ready(function () {
                                            $("#mainForm").submit(function () {
                                                $("#submitBtn").attr("disabled", true);
                                                return true;
                                            });
                                        });
        </script>

        <script>
            var config = {
                '.chosen-select': {},
                '.chosen-select-deselect': {allow_single_deselect: true},
                '.chosen-select-no-single': {disable_search_threshold: 10},
                '.chosen-select-no-results': {no_results_text: 'Oops, nothing found!'},
                '.chosen-select-width': {width: "95%"}
            }
            for (var selector in config) {
                $(selector).chosen(config[selector]);
            }
        </script>

    </body>

</html>