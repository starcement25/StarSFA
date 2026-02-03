<?php

require_once("../sfa_connection.php");
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$localDB = new sfa_connection();
$conn = $localDB->conn;


// echo json_encode([
//     "method" => $_SERVER['REQUEST_METHOD'],
//     "uri" => $_SERVER['REQUEST_URI'],
//     "post_data" => $_POST,
//     "raw_input" => file_get_contents("php://input"),
// ]);
// exit;
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    $res_data = array("process_status" => "No", "process_message" => "Failed!", 'error' => 'Only POST method is allowed');
    echo json_encode($res_data);
    //echo json_encode(["error" => "Only POST method is allowed"]);
    exit;
}
$data = json_decode(file_get_contents("php://input"), true);
$required = ['cust_phone', 'route_code'];
foreach ($required as $field) {
    if (empty($data[$field])) {
        http_response_code(400);
        $res_data = array("process_status" => "No", "process_message" => "Failed!", 'error' => "$field is required");
        echo json_encode($res_data);

        exit;
    }
}
$route_code = mysqli_real_escape_string($conn, $data['route_code']);
$cust_phone = mysqli_real_escape_string($conn, $data['cust_phone']);
// echo $route_code.$cust_phone;
// die;
$sites = [];

$site_sql = "SELECT * FROM site_master WHERE cust_phone = '$cust_phone'";
$site_result = mysqli_query($conn, $site_sql);
$row = mysqli_fetch_assoc($site_result);
if (!$site_result || mysqli_num_rows($site_result) == 0) {
    $res_data = array(
        "process_status" => "No",
        "process_message" => " Failed!",
        'error' => 'Site not found',
        "is_already_register_in_other_route" => 0,
        "is_register_in_this_route" => 0
    );
    echo json_encode($res_data);
    exit;
} else {

$sites_sql = "SELECT * FROM site_master WHERE route_code = '$route_code' AND cust_phone = '$cust_phone'";
$sites_result = mysqli_query($conn, $sites_sql);
$row = mysqli_fetch_assoc($sites_result);


$other_route_sql = "SELECT COUNT(*) AS count FROM site_master WHERE cust_phone = '$cust_phone' AND route_code != '$route_code'";
$other_route_result = mysqli_query($conn, $other_route_sql);
$other_route_data = mysqli_fetch_assoc($other_route_result);
$is_registered_in_other_route = ($other_route_data['count'] > 0) ? 1 : 0;

if (!$sites_result || mysqli_num_rows($sites_result) == 0) {
    $res_data = array(
        "process_status" => "No",
        "process_message" => "Failed!",
        'error' => 'The Phone Number is registered with another site',
        "is_already_register_in_other_route" => $is_registered_in_other_route,
        "is_register_in_this_route" => 0
    );
    echo json_encode($res_data);
    exit;
} else {
    echo json_encode([
        "process_status" => "Yes",
        "process_message" => "Site found with this Number and route",
        "is_already_register_in_other_route" => $is_registered_in_other_route,
        "is_register_in_this_route" => 1
    ]);
    exit;
}
}


