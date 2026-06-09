<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
require_once("../sfa_connection.php");
header('Content-Type: application/json');

$localDB = new sfa_connection();
$conn = $localDB->conn; 
//log code start 13-04-26
require_once("api_logger.php");

$api_name = basename(__FILE__);
 $emp_code = $_GET['emp_code'] ?? null;

$log_data = api_log_start($conn, $api_name, $_GET['emp_code']);


//log code end 13-04-26
if (!$conn) {
    echo json_encode([
        "process_status" => "No",
        "process_message" => "Database connection failed"
    ]);
    exit;
}


if (!isset($_GET['emp_code'])) {
    echo json_encode([
        "process_status" => "No",
        "process_message" => "Validation Failed!",
        "error" => "Employee Code is required"
    ]);
    exit;
}
$pending_count=0;
$emp_code = trim($_GET['emp_code']);

$stmt = $conn->prepare("SELECT * FROM employee_master WHERE emp_code = ?");
$stmt->bind_param("s", $emp_code);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode([
        "process_status" => "No",
        "process_message" => "Failed!",
        "error" => "Employee not found"
    ]);
    exit;
}

$employee = $result->fetch_assoc();

$status = 'pending';

    $stmt = $conn->prepare("
        SELECT COUNT(*) AS pending_count 
        FROM new_site_lead_visit_master 
        WHERE asm_id = ? 
        AND approval_status = ?
    ");
    $stmt->bind_param("ss", $emp_code, $status);
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $pending_count = (int)$row['pending_count'];
 


$res_data = [
    "process_status"   => "YES",
    "process_message"  => "success",
    "employee_name"    => $employee['emp_name'],
    "designation"      => $employee['designation'],
    "zone"             => $employee['zone'],
    "pending_requests" => $pending_count
];

echo json_encode($res_data);
api_log_success($conn, $log_data);
exit;
?>
