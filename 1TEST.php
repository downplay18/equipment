<?php
//var_dump($_SESSION);
session_start();
error_reporting(0);
require_once 'connection.php';
include 'root_url.php';

if ($_SESSION['user_id'] == "") {
    header("Location: $root_url/index.php", true, 302);
    exit();
}

$_SESSION['status'] = 0 ;

unset($_SESSION['detail']);
unset($_SESSION['suffix']);
unset($_SESSION['owner']);
unset($_SESSION['msg']);
?>


<!-- Modal -->
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Modal Header</h4>
            </div>
            <div class="modal-body">
                <p>Some text in the modal.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div> <!-- /.modal-content -->
    </div> <!-- /.modal-dialog -->
</div> <!-- /.modal fade -->


<html>
    <head>
        <title>MMTC Equipment</title>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

        <link href="css/bootstrap-3.3.7/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>

        <?php
        /* navbar */
        /* ไม่ใช้ case unauthen เพราะไม่มีสิทธิ์เข้าหน้านี้อยู่แล้ว */
        include 'navbar.php';

        echo 'SESSION = ';
        print_r($_SESSION);
        echo '<br/>POST = <br/>';
        print_r($_POST);
        $_SESSION['msg'] = "";
        echo "<br?>";
        print_r($_SESSION['msg']);
        $msg="";
        ?>

        <?php
        if(isset($_POST['submitBtn'])) {
            $_SESSION['status'] = 1;
        }
        ?>
        
        <div>TODO write content</div>
        <form action="" method="post">
            <button class="btn btn-success" type="submit" name="submitBtn" value="submit">Submit</button>
        </form>
        
        
        
        <script src="js/jquery-1.12.2.min.js" type="text/javascript"></script>
        <script src="js/bootstrap-3.3.7/bootstrap.min.js" type="text/javascript"></script>

        <script type="text/javascript"> //MODAL 
            $(window).load(function () {
                if (isset($msg)) {
                    $('#myModal').modal('show');
                }
            });
        </script>
        
        
    </body>
</html>
