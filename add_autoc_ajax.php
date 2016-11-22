<?php
/*สำหรับ autocomplete ใช้ใน 'add' และ 'take'*/
session_start();
require_once 'connection.php';
if ($_POST['type'] == 'item_table') {
    $row_num = $_POST['row_num'];
    $name = $_POST['name_startsWith'];
    $query = "SELECT `key_detail`,`last_suffix` FROM `key_item` WHERE `key_detail` LIKE '%" . $name . "%'";
    //%" . $name . "%
    $result = mysqli_query($connection, $query);
    $data = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $name = $row['key_detail'] . '|' . $row['last_suffix'];
        array_push($data, $name);
    }
    echo json_encode($data);
}

?>

