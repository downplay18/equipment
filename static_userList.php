

<?php
include("connection.php");
include 'main_head.php';
$output = '';
$searchq_size = '6'; /* เดิมเป็น 9 แต่ -3 จากคอลัมน์(รหัสผ่าน,?,สถานะ) */


/* array('ชื่อหัวตาราง', 'ชื่อในฐานข้อมูล', 'ชื่อตัวแปร') */
/* array([0][0], [0][1], [0][2]) */
$header_info = array(
    array('รหัสพนักงาน', 'user_id', 'login_cid'),
    array('ชื่อพนักงาน', 'name', ''),
    //array('รหัสผ่าน', 'Password', 'login_pwd'),
    array('ตำแหน่ง', 'rank', ''),
    array('ที่อยู่', 'building', ''),
    array('ห้อง', 'room', ''),
    array('เบอร์โต๊ะ', 'office_tel', ''),
    //array('?', 'mis', ''),
    //array('สถานะ', 'Status', ''),
);

/* สร้าง query_statement */
$query_statement = "SELECT * FROM user";

/* ส่งคำสั่ง sql query */
$query = mysqli_query($connection, $query_statement) or die("could not search!");
$count = mysqli_num_rows($query);

echo "พบ " . $count . " รายการ!\n";

if ($count == 0) {
    $output = 'ไม่พบข้อมูลที่ค้นหา!';
} else {
    ?>
    <table width = "100%" border = "1" cellspacing = "0" cellpadding = "0">
        <tr align = "center">
            <td><b>ลำดับ</b></td>
            <?php
            /* พิมพ์หัวตาราง */
            for ($i = 0; $i < $searchq_size; $i++) {

                echo "<td><b>" . $header_info[$i][0] . "</b></td>";
            }
            ?>
        </tr> 

        <?php
        $jj = '1'; /* ตัวแปรนับลำดับที่เพื่อแสดงในตารางให้ user */
        /* แกะ sql query มาไว้ใน $row */
        while ($row = mysqli_fetch_array($query)) {
            ?>
            <tr align="center">
                <td><?= $jj++; ?></td>
                <?php
                /* แกะคำค้นหาเป็น $row ออกมาแสดง */
                for ($i = 0; $i < $searchq_size; $i++) {
                    echo "<td>" . $row[$header_info[$i][1]] . "</td>";
                }
                ?>
            </tr> 
        <?php } ?> 
    </table> <?php
}

mysqli_close($connection);
?>
