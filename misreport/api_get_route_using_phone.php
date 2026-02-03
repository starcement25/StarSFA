<?php

require_once("../sfa_connection.php");
header('Content-Type: application/json');

$localDB = new sfa_connection();
$conn = $localDB->conn;


if (!isset($_GET['cust_phone'])) {
    $res_data = array("process_status" => "No", "process_message" => "Validation Failed!", 'error' => 'Phone Number is required');
    echo json_encode($res_data);
    exit;
}

$cust_phone = addslashes(trim($_GET['cust_phone']));


$site_sql = "SELECT * FROM site_master WHERE cust_phone = '$cust_phone' GROUP BY route_code";

$site_result = mysqli_query($conn, $site_sql);

if (!$site_result || mysqli_num_rows($site_result) == 0) {
     $res_data = array("process_status" => "No", "process_message" => " Failed!", 'error' => 'Routes not found');
    echo json_encode($res_data);
    exit;
}
 $sites = [];
 
while ($row = mysqli_fetch_assoc($site_result)) {
    
    $route = $row['route_code'];
    
$route_sql = "SELECT route_name FROM route_master WHERE route_code = '$route' ";
$route_result = mysqli_query($conn, $route_sql);
if (!$route_result) {
    echo json_encode([
        "process_status" => "No",
        "process_message" => "Query Failed",
        "error" => mysqli_error($conn)
    ]);
    exit;
}

$row_route = mysqli_fetch_assoc($route_result);
$routes = $row_route['route_name'];
    $sites[] = [
        "site_id"     => $row['site_code'],
        "route_code"  => $row['route_code'],
        "cust_phone"  => $row['cust_phone'],
        "route" => $routes
    ];
}


echo json_encode([
    "process_status" => "Yes",
    "process_message" => "Routes fetched successfully",
    "data" => $sites
]);




// $route_sql = "SELECT route_code,route_name FROM route_master";
// $route_result = mysqli_query($conn, $route_sql);
// if (!$route_result) {
//     echo json_encode([
//         "process_status" => "No",
//         "process_message" => "Query Failed",
//         "error" => mysqli_error($conn)
//     ]);
//     exit;
// }

// $row_route = mysqli_fetch_assoc($route_result);
// $routes = $row_route['route_name'];



// echo json_encode([
//     "process_status" => "Yes",
//     "process_message" => "Routes fetched successfully",
//     "data" => $routes
// ]);