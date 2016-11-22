<!-- Navigation -->
<nav class="navbar navbar-default" role="navigation">
    <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <a class="navbar-brand" href="#">กพทถ-ห.</a>
            <!--สร้างปุ่มเมนู สำหรับมุมมองแบบ responsive-->
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <!--ปุ่ม HOME -->
                <li class="active"><a href="_login_check.php"><span class="glyphicon glyphicon-knight"></span> &nbsp;ระบบเครื่องมือเครื่องใช้และวัสดุสิ้นเปลือง</a></li>

                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">ลิงค์ที่เกี่ยวข้อง<span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="static_userList.php" target="_blank">รายชื่อพนักงาน</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="http://mmtc.egat.co.th" target="_blank">MMTC Home Page</a></li>
                        <li><a href="http://10.249.50.18/stock" target="_blank">เว็บครุภัณฑ์</a></li>

                    </ul>
                </li>

                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">คู่มือการใช้งาน<span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="#" target="_blank">ผู้ใช้ทั่วไป(USER)</a></li>
                        <li><a href="#" target="_blank">ผู้ดูแลประจำกลุ่มงาน(KEY)</a></li>
                        <li><a href="#" target="_blank">หัวหน้างาน(BOSS)</a></li>
                        <?php if (isset($_SESSION['status']) && $_SESSION['status'] == 'ADMIN') { ?>
                            <li><a href="#" target="_blank">ผู้ดูแลระบบ(ADMIN)</a></li>
                        <?php } ?>
                    </ul>
                </li>
            </ul> 



            <?php
            if (isset($_SESSION['user_id'])) {
                ?>
                <div class="nav navbar-nav navbar-right">
                    <p class = "navbar-text">
                        <a href="_login_update.php" target="" class="navbar-link"><?= "[" . $_SESSION["user_id"] . "] " . $_SESSION["name"] . "</a> (สถานะ: " . $_SESSION["status"] . ") &nbsp;" ?>
                    </p>

                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#"><span class="glyphicon glyphicon-cog"></span><span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="<?= $root_url ?>/_login_update.php" target="_blank" class="navbar-link"><span class="glyphicon glyphicon-pencil"></span> แก้ไขข้อมูลส่วนตัว</a></li>
                            <li><a href="<?= $root_url ?>/_login_update_pwd.php" target="_blank"><span class="glyphicon glyphicon-lock"></span> เปลี่ยนรหัสผ่าน</a></li>
                            <li><a href="<?= $root_url ?>/_logout.php"><span class="glyphicon glyphicon-log-out"></span> ออกจากระบบ</a></li>
                        </ul>
                    </li>
                </div> <!-- /.navbar-right -->
            <?php } ?>


            <div class="navbar-form navbar-left">

            </div>

        </div> <!-- /.collapse navbar-collapse -->
    </div> <!-- /.container-fluid -->
</nav>

