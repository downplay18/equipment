<?php
include("root_url.php");
?>

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
                <li class="active"><a href="index.php"><span class="glyphicon glyphicon-knight"></span> &nbsp;ระบบเครื่องมือเครื่องใช้และวัสดุสิ้นเปลือง</a></li>

                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">ลิงค์ที่เกี่ยวข้อง<span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="<?= $root_url . '/static_userList.php' ?>" target="_blank">รายชื่อพนักงาน</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="mmtc.egat.co.th" target="_blank">MMTC Home Page</a></li>
                        <li><a href="http://10.249.50.18/stock/" target="_blank">เว็บครุภัณฑ์</a></li>
                        
                    </ul>
                </li>

                <li><a href="#">คู่มือ</a></li>
            </ul> 

            <!-- Sign In form
            <form class="navbar-form navbar-right" action="_login_check.php" method="post" role="form" target="">

                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                    <input type="text" class="form-control" name="login_cid" placeholder="เลขพนักงาน" autocomplete="on" 
                           maxlength="6" size="6" pattern="[0-9]+" title="ตัวเลข 6 ตัว เท่านั้น!">                                        
                </div>

                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                    <input type="password" class="form-control" name="login_pwd" placeholder="รหัสผ่าน"
                           maxlength="50" size="8">                                        
                </div>

                <div class="form-group">
                    <button name="submit" type="submit" value="login" class="btn btn-default">
                        <span class="glyphicon glyphicon-log-in"></span>&nbsp;เข้าสู่ระบบ
                    </button>
                </div> 
                
                
                
            </form>  /Sign In form -->

        </div>
    </div>
</nav>

