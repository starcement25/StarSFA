<?php

require_once("../sfa_connection.php");
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$localDB = new sfa_connection();
$conn = $localDB->conn;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        "process_status" => "No",
        "process_message" => "Failed!",
        'error' => 'Only POST method is allowed'
    ]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);


$required = [
    'site_id',
    'approval_status'
];

foreach ($required as $field) {
    if (!isset($data[$field]) || trim($data[$field]) === '') {
        http_response_code(400);
        echo json_encode([
            "process_status" => "No",
            "process_message" => "Failed!",
            "error" => "$field is required"
        ]);
        exit;
    }
}


function escapeOrNull($conn, $value)
{
    if (is_null($value) || trim($value) === '' || strtolower(trim($value)) === 'null') {
        return "NULL";
    }
    return "'" . mysqli_real_escape_string($conn, $value) . "'";
}


foreach ($data as $key => $val) {
    ${"escaped_" . $key} = escapeOrNull($conn, $val);
}


$sqlupdateapprovedet = "
UPDATE new_site_lead_visit_master SET 
    approval_status = $escaped_approval_status,
    approval_date_time = CURRENT_TIMESTAMP(),
    actual_date_of_delivery = $escaped_actual_date_of_delivery,
    delivery_remarks = $escaped_delivery_remarks,
    reason_for_not_delivery = $escaped_reason_for_not_delivery
WHERE new_site_lead_unique_id = $escaped_site_id
";

// echo $sqlupdateapprovedet;die;
if (!mysqli_query($conn, $sqlupdateapprovedet)) {
    mysqli_rollback($conn);
    echo json_encode([
        "process_status" => "No",
        "process_message" => "Update new_site_lead_visit_master failed",
        "error" => mysqli_error($conn)
    ]);
    exit;
}

mysqli_commit($conn);

$site_data = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT * FROM new_site_lead_master WHERE unique_id = $escaped_site_id"
));
//  echo  $escaped_site_id;die;
$visit_data = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT * FROM new_site_lead_visit_master 
     WHERE new_site_lead_unique_id = $escaped_site_id 
     ORDER BY id DESC LIMIT 1"
));


echo json_encode([
    "process_status" => "Yes",
    "process_message" => "Success",
    "site_id" => $data['site_id'],
    "site_master" => $site_data,
    "site_visit_master" => $visit_data
]);
exit;

?>
