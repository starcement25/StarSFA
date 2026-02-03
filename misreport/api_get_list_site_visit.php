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
// $sql = "
//     SELECT *
//     FROM site_visit_master 
//     WHERE visited_by = '$emp_code'
//     AND DATE(created_at) = '$today'
// ";
// $sql = "
// SELECT 
//     SM.*, 
//     VM.*, 
//     CM.customer_name AS rssd_name,
//     BM.branch_name AS branch_name, 
//      RM.route_name AS route_name, 
//     EM1.emp_name AS visited_by_name,
//     EM2.emp_name AS approved_by_name
// FROM site_visit_master VM
// LEFT JOIN site_master SM ON  SM.id = VM.site_id
// LEFT JOIN customer_master CM ON CM.customer_code = VM.rssd
// LEFT JOIN employee_master EM1 ON EM1.emp_code = VM.visited_by
// LEFT JOIN employee_master EM2 ON EM2.emp_code = VM.approved_by
// LEFT JOIN branch_master BM ON BM.branch_code = SM.branch_code
// LEFT JOIN route_master RM ON RM.route_code = SM.route_code
// WHERE VM.visited_by = '$emp_code' AND DATE(VM.created_at) = '$today';
// ";
$sql = "
SELECT 
    SM.*, 
    VM.*, 
    CM.customer_name AS rssd_name,
    BM.branch_name AS branch_name, 
    RM.route_name AS route_name, 
    EM1.emp_name AS visited_by_name,
    EM2.emp_name AS approved_by_name
FROM site_visit_master VM
LEFT JOIN site_master SM ON SM.id = VM.site_id
LEFT JOIN customer_master CM ON CM.customer_code = VM.rssd
LEFT JOIN employee_master EM1 ON EM1.emp_code = VM.visited_by
LEFT JOIN employee_master EM2 ON EM2.emp_code = VM.approved_by
LEFT JOIN branch_master BM ON BM.branch_code = SM.branch_code
LEFT JOIN route_master RM ON RM.route_code = SM.route_code
WHERE VM.visited_by = '$emp_code' AND DATE(VM.created_at) = '$today'
ORDER BY VM.created_at DESC
";
 $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
    //  $res_data = array("process_status" => "Yes", "process_message" => "Success", 'count_visit' => mysqli_num_rows($result));
    // echo json_encode($res_data);
    // exit;

    while ($row = mysqli_fetch_assoc($result)) {
    
    $visit_fields = $row;
    unset( $visit_fields['cust_phone'], $visit_fields['site_id']);

    $visits[] = [
        "visit_list" => $visit_fields 
    ];
}

echo json_encode([
    "process_status" => "Yes",
    "process_message" => "Latest visit found",
    "sites" => $visits
]);
exit;
    }

     $res_data = array("process_status" => "No", "process_message" => " Failed!", 'error' => 'Record not found');
    echo json_encode($res_data);
    exit;