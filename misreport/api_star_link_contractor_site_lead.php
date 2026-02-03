<?php
require_once("../sfa_connection.php");
require("../include/config.php");
require("../include/config-setup.php");
require("../include/dbcon.php");
require("../include/functions.php");
header('Content-Type: text/plain'); 

if (!isset($_GET['emp_code'])) {
    echo "error^emp_code is required";
    exit;
}

$emp_Code = trim($_GET['emp_code']);
$apiUrl = "https://starlinkinfluencers.in/api/v1/get-mason-list?emp_code=" . urlencode($emp_Code) . "&per_page=1000";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo "error^" . curl_error($ch);
    curl_close($ch);
    exit;
}

curl_close($ch);


$data = json_decode($response, true);

//  print_r($data);
if (!isset($data['status']) || !$data['status'] || !isset($data['data'])) {
    echo "error^Invalid response";
    exit;
}


$users = $data['data'];


$output = "";
$total = count($users);


$output .= $total."¥3\n";

foreach ($users as $user) {
   
    $id = $user['id'] ?? '';
    $name = $user['name'] ?? '';
    $phone = $user['phone'] ?? '';
    $output .= "{$id}^{$name}^{$phone}\n";
}


echo $output;
exit;


?>
