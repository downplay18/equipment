<?php
//var_dump($_SESSION);
session_start();
//error_reporting(0);
require_once 'connection.php';
include 'root_url.php';

print_r($_POST);
print_r($_GET);

$escapeName = $_SESSION['name'];
$escapeMykey = $_GET['getmykey'];

//แก้ช่อง `mykey` ให้ว่าง
$deleteQS = "UPDATE `user_config` SET `mykey`='' WHERE `cname`='$escapeName'";
$deleteQry = mysqli_query($connection, $deleteQS) or die("DELETE FROM fail: " . mysqli_error($connection));

//แก้ status จาก KEY >เป็น> USER
$statusQS = "UPDATE `user` SET `status`='USER' WHERE `user_id`='$escapeMykey'";
$statusQry = mysqli_query($connection,$statusQS);

$wstatusQS = "UPDATE `worker` SET `status`='USER' WHERE `username`='$escapeMykey'";
$wstatusQry = mysqli_query($connection,$wstatusQS);

header("Location: $root_url/_login_check.php", true, 302);
?>