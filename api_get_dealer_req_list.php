<?php
require_once("sfa_connection.php");
header('Content-Type: application/json');

if (!isset($_GET['emp_code'])) {
    echo json_encode(['error' => 'emp_code is required']);
    exit;
}

$emp_Code = trim($_GET['emp_code']);

$apiUrl = SAATHI_URL."/SAP/api_get_all_exclusive_req_emp.php?emp_code=" . urlencode($emp_Code);

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
echo $response;
exit;
?>
