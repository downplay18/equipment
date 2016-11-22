<?php
//var_dump($_SESSION);
session_start();
error_reporting(0);
require_once 'connection.php';
include 'root_url.php';

if (isset($_SESSION['user_id'])) {
    header("Location: $root_url/_login_check.php", true, 302);
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
        ?>

        <div class="row">

            <div class="col-md-2 sidebar">
                <div class="list-group">
                    <a href="#" class="list-group-item active" align="center"><span class="glyphicon glyphicon-user"></span>&nbsp;&nbsp;ล็อกอิน</a>
                    <a href="<?= $root_url ?>/#" class="list-group-item"><span class="glyphicon glyphicon-book"></span> คู่มือการใช้งาน</a>
                </div>   
            </div>

            <div class="col-md-10" style="padding: 80px">
                <form action="_login_check.php" method="post" role="form" target="">
                    <div class="container-fluid">
                        <form action="_login_check.php" method="post" role="form" target="">
                            <div class="col-md-4 col-md-offset-4">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title" align="center">ระบบยืนยันตัวตน</h3>
                                    </div>
                                    <div class="panel-body">
                                        <form accept-charset="UTF-8" role="form">
                                            <fieldset>
                                                <div class="form-group">
                                                    <input class="form-control" name="login_cid" placeholder="เลขพนักงาน" type="text" autocomplete="on">
                                                </div>
                                                <div class="form-group">
                                                    <input class="form-control" placeholder="รหัสผ่าน" name="login_pwd" type="password" value="">
                                                </div>
                                                <!--
                                                <div class="checkbox">
                                                    <label>
                                                        <input name="remember" type="checkbox" value="Remember Me"> Remember Me
                                                    </label>
                                                </div> -->
                                                <input class="btn btn-lg btn-success btn-block" type="submit" value="เข้าสู่ระบบ">
                                            </fieldset>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </form> <!-- /Sign In form -->
                        
                        <div class="col-md-12" align="center" style="margin-top: 2em;">
                        <?php 
                        foreach($_SESSION['loginMsg'] as $msg) {
                            echo $msg;
                        }
                        foreach($_SESSION['addMsg'] as $msg) {
                            echo $msg;
                        }
                        foreach($_SESSION['addStockMsg'] as $msg) {
                            echo $msg;
                        }
                        foreach($_SESSION['addUrgentMsg'] as $msg) {
                            echo $msg;
                        }
                        unset($_SESSION['loginMsg']);
                        unset($_SESSION['addMsg']);
                        unset($_SESSION['addStockMsg']);
                        unset($_SESSION['addUrgentMsg']);
                        ?>
                        </div>
                        
                    </div> <!-- /.container-fluid -->
                </form> <!-- /Sign In form -->
            </div> <!-- /.col-md-10 -->

        </div> <!-- /.row -->




        <!--Script -->
        <?php include 'main_script.php'; ?>




    </body>
</html>
