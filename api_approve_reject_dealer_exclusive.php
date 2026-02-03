<?php
require_once("sfa_connection.php");
header('Content-Type: application/json');


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        "process_status" => "No",
        "process_message" => "Failed!",
        "error" => "Only POST method is allowed"
    ]);
    exit;
}


$data = json_decode(file_get_contents("php://input"), true);


if (!$data) {
    echo json_encode([
        "process_status" => "No",
        "process_message" => "Failed!",
        "error" => "Invalid JSON input"
    ]);
    exit;
}


$required = ['dealer_id', 'emp_code', 'status', 'table_id'];
foreach ($required as $field) {
    if (empty($data[$field])) {
        http_response_code(400);
        echo json_encode([
            "process_status" => "No",
            "process_message" => "Failed!",
            "error" => "$field is required"
        ]);
        exit;
    }
}


$dealer_id = htmlspecialchars(trim($data['dealer_id']));
$emp_code  = htmlspecialchars(trim($data['emp_code']));
//$status    = (int) $data['status'];
$table_id  = htmlspecialchars(trim($data['table_id']));
$status_input = strtolower(trim($data['status']));
$reason = strtolower(trim($data['reason'])); // normalize user input
$status_map = [
    'approve' => 1,
    'approved' => 1,
    'reject' => 2,
    'rejected' => 2,
    'pending' => 0
];

if (!isset($status_map[$status_input])) {
    http_response_code(400);
    echo json_encode([
        "process_status" => "No",
        "process_message" => "Failed!",
        "error" => "Invalid status. Allowed values: Approve, Reject, Pending"
    ]);
    exit;
}

$status_string = ucfirst($status_input);

$postData = [
    'dealer_id' => $dealer_id,
    'emp_code'  => $emp_code,
    'status'    => $status_string,
    'table_id'  => $table_id,
    'reason' => $reason,
];

$apiUrl = SAATHI_URL."/SAP/api_asm_approve_reject_request.php";


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen(json_encode($postData))
]);





$response = curl_exec($ch);


if (curl_errno($ch)) {
    echo json_encode([
        'error' => 'cURL Error: ' . curl_error($ch)
    ]);
    curl_close($ch);
    exit;
}

curl_close($ch);


echo $response;
exit;
?>
