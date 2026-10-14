<?php

require_once("../sfa_connection.php");
header('Content-Type: application/json');

$localDB = new sfa_connection();
$conn = $localDB->conn;

if (!isset($_GET['emp_code'])) {
    $res_data = array("process_status" => "No", "process_message" => "Validation Failed!", 'error' => 'emp_code is required');
    echo json_encode($res_data);
    exit;
}

$emp_code = addslashes(trim($_GET['emp_code']));


$emp_sql = "SELECT * FROM employee_master WHERE emp_code = '$emp_code' ";
// echo $emp_sql;
//  die;
$emp_result = mysqli_query($conn, $emp_sql);
if (!$emp_result) {
    echo json_encode([
        "process_status" => "No",
        "process_message" => "Query Failed",
        "error" => mysqli_error($conn)
    ]);
    exit;
}

// if (mysqli_num_rows($emp_result) > 0) {
    $row = mysqli_fetch_assoc($emp_result);
    $val = $row['level'];


 
        echo json_encode([
            "process_status" => "Yes",
            "process_message" => "Success!",
            "Level" => $val
        ]);
    
// } else {
//     echo json_encode([
//         "process_status" => "No",
//         "process_message" => "Failed!",
//         "error" => "Not Found!"
//     ]);
// }
