<?php
//database configuration
include 'connection.php';

//get search term
$searchTerm = $_GET['term'];

//get matched data from `item` table
$qstate = "SELECT `detail` FROM item WHERE detail LIKE '%" . $searchTerm . "%'";
$query = mysqli_query($connection, $qstate);
while ($row = $query->fetch_assoc()) {
    $data[] = $row['detail'];
}

//return json data
echo json_encode($data);
?>

