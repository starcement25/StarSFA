<?php
require_once("../sfa_connection.php");
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$localDB = new sfa_connection();
$conn = $localDB->conn;

if (!isset($_GET['emp_code'])) {
    $res_data = array("process_status" => "No", "process_message" => "Validation Failed!", 'error' => 'emp_code is required');
    echo json_encode($res_data);
    exit;
}

$emp_code = addslashes(trim($_GET['emp_code']));


$emp_sql = "SELECT * FROM employee_master WHERE emp_code = '$emp_code'";

$emp_result = mysqli_query($conn, $emp_sql);

if (!$emp_result || mysqli_num_rows($emp_result) == 0) {
     $res_data = array("process_status" => "No", "process_message" => " Failed!", 'error' => 'Employee not found');
    echo json_encode($res_data);
    exit;
}
$today= date('Y-m-d');
$sql = "
    SELECT *
    FROM site_visit_master 
    WHERE visited_by = '$emp_code'
    AND DATE(created_at) = '$today'
";

 $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
     $res_data = array("process_status" => "Yes", "process_message" => "Success", 'count_visit' => mysqli_num_rows($result));
    echo json_encode($res_data);
    exit;
    }

     $res_data = array("process_status" => "No", "process_message" => " Failed!", 'error' => 'Record not found');
    echo json_encode($res_data);
    exit;