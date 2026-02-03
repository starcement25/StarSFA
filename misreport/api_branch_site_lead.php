<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once("../sfa_connection.php");
header('Content-Type: application/json');

$localDB = new sfa_connection();
$conn = $localDB->conn;









    $branch_sql = "SELECT branch_name, branch_code FROM branch_master WHERE acedns = 'Y'";
    $branch_result = mysqli_query($conn, $branch_sql);

    $count = mysqli_num_rows($branch_result);
$contentsrowcolumn = trim($count . '¥' . '2');

if ($count > 0) {
   
    $datetime = gmdate('Y-m-d€H:i:s', strtotime('+330 minute'));

    $linecontents = '';

    while ($row = mysqli_fetch_assoc($branch_result)) {
        
                $linecontents .= $row['branch_code'] ."^".$row['branch_name'] ."\n";
            
        }
   

    
    $linecontents = trim($linecontents);


    $datacontents = $contentsrowcolumn ."\n" . $linecontents;

    echo $datacontents;
} else {
    echo "No Data Found";
}
