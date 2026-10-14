<?php
date_default_timezone_set('Asia/Kolkata');

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
header("Content-Type: text/plain");

require_once("../sfa_connection.php");

$localDB = new sfa_connection();
$conn = $localDB->conn;

/*
    Table Structure:
    
    gram_panchayat_data
    -------------------
    sr_no
    state_ut
    district
    block_name
    gram_panchayat
*/

$sql = "SELECT 
            sr_no,
            state_ut,
            district,
            block_name,
            gram_panchayat
        FROM gram_panchayat_data";

$result = mysqli_query($conn, $sql);
$total = ($result) ? mysqli_num_rows($result) : 0;
if (!$result) {
    die("Query Failed : " . mysqli_error($conn));
}
echo $total."¥5 \n"; 
$data = array();

while ($row = mysqli_fetch_assoc($result)) {

    $line = $row['sr_no'] . "^" .
            $row['state_ut'] . "^" .
            $row['district'] . "^" .
            $row['block_name'] . "^" .
            $row['gram_panchayat'];

    $data[] = $line;
}

/*
    Output Example:

    1^West Bengal^Hooghly^Chanditala-I^Haripur
    2^West Bengal^Howrah^Domjur^Begri
*/

echo implode("\n", $data);

mysqli_close($conn);

?>