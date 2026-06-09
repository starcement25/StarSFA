<?php
require_once("../sfa_connection.php");
require("../include/config.php");
require("../include/config-setup.php");
require("../include/dbcon.php");
require("../include/functions.php");
header('Content-Type: application/json');

if (!isset($_GET['emp_code'])) {
    echo json_encode(['error' => 'emp_code is required']);
    exit;
}

$emp_Code = trim($_GET['emp_code']);
$localDB = new sfa_connection();
$conn = $localDB->conn; 
//log code start 13-04-26
require_once("api_logger.php");

$api_name = basename(__FILE__);
 $emp_code = $_GET['emp_code'] ?? null;

$log_data = api_log_start($conn, $api_name, $_GET['emp_code']);

// Crash-safe shutdown
api_log_success($conn, $log_data);

//log code end 13-04-26
$apiUrl = "https://starstellar.com/get_engineer_by_empcode.php?emp_code=" . urlencode($emp_Code);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo json_encode(['error' => curl_error($ch)]);
    curl_close($ch);
    exit;
}

curl_close($ch);


$data = json_decode($response, true);

// print_r($response);die;
if (!$data || !isset($data['data'])) {
    echo "0¥3\n";
    exit;
}


$count = count($data['data']);


$output = $count . '¥3' . "\n";

foreach ($data['data'] as $row) {
    $eid = $row['eid'];
    $name = $row['e_name'];
    $mobile = $row['e_mobile'];
    $output .= "{$eid}^{$name}^{$mobile}\n";
}


echo $output;
api_log_success($conn, $log_data);
exit;
// echo $response;

?>
