<?php

/* สำหรับ autocomplete ใช้ใน 'add' และ 'take' */
session_start();
require_once 'connection.php';
if ($_POST['type'] == 'item_table') {
    $row_num = $_POST['row_num'];
    $name = $_POST['name_startsWith'];
    $query = "
        SELECT `key_id`,`key_code`,`key_detail`,`slip_suffix`,`last_suffix`,`last_xqty` 
        FROM `key_item` 
        WHERE `key_code` 
        LIKE '%" . $name . "%'";
    //%" . $name . "%
    $result = mysqli_query($connection, $query);
    $data = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $name = $row['key_code'] . '|' . $row['key_detail'] . '|' . $row['slip_suffix'] . '|' . $row['last_suffix'] . '|' . $row['last_xqty'] . '|' . $row['key_id'];
        array_push($data, $name);
    }
    echo json_encode($data);
}
?>

