<?php

// Create connection
$servername = "localhost";
$username = "root"; 
$password = "mysql"; //simulator
$dbname = "equipment";

//$mysqli->set_charset("utf8");

$connection = mysqli_connect($servername, $username, $password, $dbname) 
        or die("ไม่สามารถเชื่อมต่อฐานข้อมูลหลักได้!");

mysqli_set_charset($connection, "utf8");    
?>