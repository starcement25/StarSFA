<?php
/*
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);*/
require_once("sfa_connection.php");

header('Content-Type: application/json');


$localDB = new sfa_connection();
$conn = $localDB->conn;


/*
|--------------------------------------------------------------------------
| Check required inputs — emp_code, history_id, action (POST only,
| matching the target Saathi endpoint's own POST-only requirement)
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] != 'POST') {

    echo json_encode([
        'process_status'  => 'NO',
        'process_message' => 'This endpoint only accepts POST requests.'
    ]);

    exit;
}

if (!isset($_POST['emp_code']) || !isset($_POST['history_id']) || !isset($_POST['action'])) {

    echo json_encode([
        'process_status'  => 'NO',
        'process_message' => 'emp_code, history_id and action are all required.'
    ]);

    exit;
}


$emp_Code   = trim($_POST['emp_code']);
$history_id = trim($_POST['history_id']);
$action     = trim($_POST['action']);


/*
|--------------------------------------------------------------------------
| Get dns_emp_code from employee_master
|--------------------------------------------------------------------------
*/
$dns_emp_code = '';

$sql = "SELECT dns_emp_code
        FROM employee_master
        WHERE emp_code = '$emp_Code'
        LIMIT 1";

$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {

    $row = mysqli_fetch_assoc($result);

    $dns_emp_code = $row['dns_emp_code'];
}


if ($dns_emp_code == '') {

    echo json_encode([
        'process_status'  => 'NO',
        'process_message' => 'Could not resolve dns_emp_code for this emp_code.'
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| API URL — approve/reject allocation update request
|--------------------------------------------------------------------------
*/
$apiUrl = "https://starsaathi.com/SAP/admin/approve_allocation_update_request.php";

$postFields = [
    'dns_emp_code' => $dns_emp_code,
    'history_id'   => $history_id,
    'action'       => $action
];


/*
|--------------------------------------------------------------------------
| CURL Request (POST, form-urlencoded — same as the target endpoint expects)
|--------------------------------------------------------------------------
*/
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);


/*
|--------------------------------------------------------------------------
| CURL Error Check
|--------------------------------------------------------------------------
*/
if (curl_errno($ch)) {

    echo json_encode([
        'error' => curl_error($ch)
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