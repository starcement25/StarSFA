
<?php

require_once("sfa_connection.php");

header('Content-Type: application/json');

$localDB = new sfa_connection();
$conn = $localDB->conn;


/*
|--------------------------------------------------------------------------
| Allow only POST method
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    http_response_code(405);

    echo json_encode([
        "process_status" => "No",
        "process_message" => "Failed!",
        "error" => "Only POST method is allowed"
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| Get JSON Input
|--------------------------------------------------------------------------
*/
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {

    echo json_encode([
        "process_status" => "No",
        "process_message" => "Failed!",
        "error" => "Invalid JSON input"
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| Validate Required Fields
|--------------------------------------------------------------------------
*/
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


/*
|--------------------------------------------------------------------------
| Input Values
|--------------------------------------------------------------------------
*/
$dealer_id = htmlspecialchars(trim($data['dealer_id']));
$emp_code  = htmlspecialchars(trim($data['emp_code']));
$table_id  = htmlspecialchars(trim($data['table_id']));
$status_input = strtolower(trim($data['status']));
$reason = isset($data['reason']) 
            ? strtolower(trim($data['reason'])) 
            : '';


/*
|--------------------------------------------------------------------------
| Status Mapping
|--------------------------------------------------------------------------
*/
$status_map = [
    'approve'  => 1,
    'approved' => 1,
    'reject'   => 2,
    'rejected' => 2,
    'pending'  => 0
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


/*
|--------------------------------------------------------------------------
| Get dns_emp_code from employee_master
|--------------------------------------------------------------------------
*/
$dns_emp_code = '';

$sql = "SELECT dns_emp_code
        FROM employee_master
        WHERE emp_code='$emp_code'
        LIMIT 1";

$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {

    $row = mysqli_fetch_assoc($result);

    $dns_emp_code = $row['dns_emp_code'];
}


/*
|--------------------------------------------------------------------------
| Send Data To API
|--------------------------------------------------------------------------
*/
$postData = [

    'dealer_id'    => $dealer_id,
    'emp_code'     => $emp_code,
    'dns_emp_code' => $dns_emp_code,
    'status'       => $status_string,
    'table_id'     => $table_id,
    'reason'       => $reason
];


$apiUrl = SAATHI_URL . "/SAP/api_asm_approve_reject_request.php";


/*
|--------------------------------------------------------------------------
| CURL Request
|--------------------------------------------------------------------------
*/
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


/*
|--------------------------------------------------------------------------
| CURL Error
|--------------------------------------------------------------------------
*/
if (curl_errno($ch)) {

    echo json_encode([
        'error' => 'cURL Error: ' . curl_error($ch)
    ]);

    curl_close($ch);
    exit;
}

curl_close($ch);


/*
|--------------------------------------------------------------------------
| Output API Response
|--------------------------------------------------------------------------
*/
echo $response;

exit;

?>

