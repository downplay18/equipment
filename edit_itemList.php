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

unset($_SESSION['iid']);
unset($_SESSION['detail']);
session_write_close();

require("connection.php");
?>

<html>
    <head>

        <title>ADMIN</title>
        <!-- Bootstrap Core CSS -->
        <?php include 'main_head.php'; ?>

    </head>

    <body>
        <?php
        /* navbar */
        /* ไม่ใช้ case unauthen เพราะไม่มีสิทธิ์เข้าหน้านี้อยู่แล้ว */
        require("navbar_authen.php");


        echo '<br/>';
        echo 'SESSION = ';
        print_r($_SESSION);
        echo '<br/>loginResult =<br/>';
        print_r($loginResult);
        echo '<br/>POST = <br/>';
        print_r($_POST);
        ?>

        <!-- Main container -->
        <div class="container-fluid">

            <!-- breadcrumb -->
            <ol class="breadcrumb">
                <li><a href="index.php"><span class="glyphicon glyphicon-home"></span> หน้าแรก</a></li>
                <li><a href="_login_user.php">รายการหลัก</a></li>
                <li class="active">เลือกรายการที่ต้องการแก้ไข</li>
            </ol> <!-- /breadcrumb -->   

            <div class="page-header">
                <h2>เลือกรายการที่ต้องการแก้ไข</h2>
            </div>







            <?php include 'root_url.php'; ?>

            <!--
            <h3 class="page-header">ระบบเครื่องมือเครื่องใช้และวัสดุสิ้นเปลือง <small>กองพัฒนาด้านเทคโนโลยีโรงไฟฟ้าถ่านหินและเหมือง (กพทถ-ห.)</small></h3>
            -->

            <!-- function container -->
            <div class="row">

                <div class="col-md-4" align="right"></div>

                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>
                        <input type="text" id="search_live" class="form-control" placeholder="ค้นหาทันที..." autocomplete="on">
                    </div>
                </div>

                <div class="col-md-4" align="left"></div>

            </div><!--/function container -->


            <?php
            /* ส่วนการสร้างตาราง */
            require 'connection.php';
            include 'item_headerInfo.php';
            $qstatement = "SELECT `iid`,`detail`,`suffix`,`quantity` FROM `item`";
            $query = mysqli_query($connection, $qstatement) or die("_login_user.php คิวรี่ล้มเหลว<br/>" . mysql_error());
            $count = mysqli_num_rows($query);
            ?>
            <div class='col-md-2'></div>
            <div class='col-md-8'>
                <?php
                echo "พบ " . $count . " รายการ!<br/>";
                if ($count == 0) {
                    echo 'ไม่พบข้อมูลที่ค้นหา!';
                } else { /* เมื่อพบรายการที่ค้นหา */
                    /* สร้างตาราง */
                    ?>
                    <table id="search_table" width = "100%" border = "1" cellspacing = "0" cellpadding = "0">
                        <!-- กำหนดสัดส่วนแต่ละช่อง -->
                        <col width="5%"> 
                        <col width="5%">
                        <col width="70%">
                        <col width="10%">
                        <col width="10%">
                        <!-- พิมพ์หัวตาราง -->
                        <tr align='center'>
                            <th>ลำดับ</th>
                            <th>Unique ID</th>
                            <th>รายการ</th>
                            <th>จำนวน</th>
                            <th>หน่วย</th>
                        </tr>

                        <?php
                        $jj = '1'; /* ตัวแปรนับลำดับที่เพื่อแสดงในตารางให้ user ไล่ลำดับได้ง่ายขึ้น */
                        /* แกะ sql query มาไว้ใน $row */

                        while ($row = mysqli_fetch_assoc($query)) {
                            ?>
                            <tr align='center'>
                                <td><?php echo $jj++ ?></td>
                                <td><?php echo $row['iid'] ?></td>
                                <td align='left'><a href="edit.php?iid=<?php echo $row["iid"] ?>" target=""><?php echo $row['detail'] ?></a></td>
                                <td><?php echo $row['quantity'] ?></td>
                                <td><?php echo $row['suffix'] ?></td>
                            </tr>

                        <?php } ?>
                    </table> <?php
                }
                /* /ส่วนการสร้างตาราง */
                ?> 
            </div>
            <div class='col-md-2'></div>
        </div><!-- Main container -->









        <?php
        include 'main_script.php';
        ?>

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

    </body>
</html>
