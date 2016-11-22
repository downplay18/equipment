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
        /*
          echo '<br/>';
          echo 'SESSION = ';
          print_r($_SESSION);
          echo '<br/>POST = <br/>';
          print_r($_POST); */
        /*
          $test = array(x,'y',z);
          $test1 = array();
          echo '<br>test1='.isset($test1);
          echo '<br>test1='.isset($test1[0]);
          echo '<br>test='.$test[1]; */
        ?>


        <div class="row">

            <div class="col-md-2 sidebar">
                <?php
                include 'sidebar.php';
                echo "<div class = 'alert alert-info'>";
                echo "<b>สถานะ:</b><br/>";
                if (isset($_SESSION['addMsg'])) {
                    foreach ($_SESSION['addMsg'] as $v) {
                        echo $v . "<br/>";
                    }
                    unset($_SESSION['addMsg']);
                } else {
                    echo "ยังไม่มีรายการเพิ่มเติม";
                }
                echo "</div>";
                ?>
            </div>

            <div class="col-md-10">

                <!-- main container -->
                <div class="container-fluid">

                    <div class="page-header">
                        <h2>เพิ่มใบสั่งซื้อ <small>บันทึกใบสั่งซื้อ(แบบปกติ)</small></h2>
                    </div>

                    <!-- form and submit button -->
                    <form id="mainForm" action="add_process.php" method="post" enctype="multipart/form-data">

                        <!-- เลขที่ใบเสร็จ+วันที่+กลุ่มงานเข้าของรายการ -->
                        <div class="col-md-12">

                            <div class="row alert alert-danger">

                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-addon">ZPO ใบสั่งซื้อ</span>
                                        <input type="number" class="form-control" name="var_zpo" placeholder="" style="padding: 0.4em;" required>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-addon">วันที่ใบเสร็จ</span>
                                        <input type="date" class="form-control" name="var_slipDate" required>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-addon">เจ้าของ</span>
                                        <input type="text" class="form-control" name="var_adder" value="<?= $_SESSION["division"]; ?>" style="padding: 0.4em;" readonly>
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
                                <col width="10%"> <!-- zdir -->
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
                                    <th>ZDIR</th>
                                    <th>รายการ(ตามใบราคากลาง)</th>
                                    <th>หน่วย</th>
                                    <th>จำนวน</th>
                                    <th>ราคา/หน่วย</th>
                                    <th>จำนวนเงิน</th>
                                    <th bgcolor="#ffff66">หน่วยย่อย</th>
                                    <th bgcolor="#ffff66">แปลงหน่วย</th>
                                </tr>
                                <tr>

                                    <td><input type='checkbox' class='case'/></td>
                                    <td><span id='snum'>1.</span></td>
                                    <td><input class="form-control" type='text' id='zdir_1' name='var_zdir[]' required/></td>
                                    <td><input class="form-control" type='text' id='varDetail_1' name='varDetail[]' maxlength="100" required/></td>
                                    <td><input class="form-control" type='text' id='var_slipSuffix_1' name='var_slipSuffix[]' required/> </td>
                                    <td><input class="form-control" type='number' id='var_qty_1' name='var_qty[]' required/> </td>
                                    <td><input class="form-control" type='number' id='var_unitPrice_1' name='var_unitPrice[]' required/> </td>
                                    <td><input class="form-control" type='number' id='var_amount_1' name='var_amount[]' required/> </td>
                                    <td bgcolor="#ffffe6"><input class="form-control" type='text' id='var_lastSuffix_1' name='var_lastSuffix[]' required/></td>
                                    <td bgcolor="#ffffe6"><input class="form-control" type='number' id='var_lastQty_1' name='var_lastQty[]' required/></td>

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

                            <button id="submitBtn" class="btn btn-lg btn-success" type="submit">
                                <span class="glyphicon glyphicon-ok"></span>&nbsp;เสร็จสิ้น
                            </button>

                        </div> <!-- /ปุ่มตกลงและรีเซ็ท -->

                    </form> <!-- /form and submit button -->


                    <div class="container col-md-4">
                        <div class = "alert alert-info">
                            <b>ขั้นตอนการเพิ่ม:</b>
                            </br><code>1</code> กรอกเลขที่ใบเสร็จ
                            </br><code>2</code> เลือกไฟล์ที่สแกนจากใบเสร็จ เป็นนามสกุล *.pdf, *.jpg, *.jpeg, *.png, *.gif เท่านั้น
                            </br><code>3</code> หาก 1 ใบเสร็จมีหลายรายการ ให้กดปุ่ม <kbd>+ Add More</kbd> 
                            </br><code>4</code> กรอกรายละเอียดให้ครบทุกช่อง 
                            </br><code>5</code> ตรวจสอบข้อมูลให้ถูกต้อง แล้วกดปุ่ม <kbd>เสร็จสิ้น</kbd>
                        </div> 
                    </div>

                    <div class="container col-md-4">
                        <div class = "alert alert-info">
                            <span class = "label label-info">INFO</span> การซื้อแบบปกติ ให้ใส่ทุกช่อง<br/>
                            <span class = "label label-info">INFO</span> การซื้อแบบเร่งด่วน จะไม่มี ZPO และ ZDIR<br/>
                            <span class = "label label-info">INFO</span> การซื้อแบบเร่งด่วน ยอดรวม และ รวมสุทธิ(+VAT) จะเป็นเลขเดียวกัน<br/>
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
        <script src="js/autocWithAddRow.js" type="text/javascript"></script>

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