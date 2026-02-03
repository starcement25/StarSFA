<?php

require_once("../sfa_connection.php");
header('Content-Type: application/json');

$localDB = new sfa_connection();
$conn = $localDB->conn;


if (!isset($_GET['emp_code'])) {
    $res_data = array("process_status" => "No", "process_message" => "Validation Failed!", 'error' => 'emp_code is required');
    echo json_encode($res_data);
    exit;
}

$emp_code = addslashes(trim($_GET['emp_code']));


$emp_sql = "SELECT * FROM employee_master WHERE emp_code = '$emp_code'";

$emp_result = mysqli_query($conn, $emp_sql);

if (!$emp_result || mysqli_num_rows($emp_result) == 0) {
     $res_data = array("process_status" => "No", "process_message" => " Failed!", 'error' => 'Employee not found');
    echo json_encode($res_data);
    exit;
}

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


// $routes = [];
// while ($row = mysqli_fetch_assoc($route_result)) {
//     $routes[] = $row;
// }


// echo json_encode([
//     "process_status" => "Yes",
//     "process_message" => "Routes fetched successfully",
//     "data" => $routes
// ]);

$subordinates = get_all_subordinates($emp_code, $conn);
$subordinates[] = $emp_code; 
$subordinates = array_unique($subordinates);
// print_r($subordinates);
// die;
if (empty($subordinates)) {
    echo json_encode([
        "process_status" => "Yes",
        "process_message" => "No subordinates found",
        "data" => []
    ]);
    exit;
}


$sub_emp_list = "'" . implode("','", $subordinates) . "'";


$route_sql = "
    SELECT DISTINCT CRER.route_code, RM.route_name
    FROM customer_route_emp_relation CRER
    JOIN route_master RM ON RM.route_code = CRER.route_code
    WHERE CRER.emp_code IN ($sub_emp_list)
    AND CRER.acedns = 'Y'
";
// echo $route_sql;
// die;
$route_result = mysqli_query($conn, $route_sql);
// echo mysqli_num_rows($route_result);
// die;
$routes = [];
if ($route_result && mysqli_num_rows($route_result) > 0) {
    while ($row = mysqli_fetch_assoc($route_result)) {
        $routes[] = $row;
    }
}

usort($routes, function($a, $b) {
    return strcmp($a['route_name'], $b['route_name']);
});
echo json_encode([
    "process_status" => "Yes",
    "process_message" => "Routes fetched successfully",
    "subordinates_count" => count($subordinates),
    "data" => $routes
]);

function get_all_subordinates($emp_code, $conn, &$collected = [], $depth = 0) {
    if ($depth > 20) return $collected; 

    $sql = "SELECT emp_code FROM employee_master WHERE FIND_IN_SET('$emp_code', reporting_to)";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $sub_code = $row['emp_code'];
            if (!in_array($sub_code, $collected)) {
                $collected[] = $sub_code;
                get_all_subordinates($sub_code, $conn, $collected, $depth + 1);
            }
        }
    }

    return $collected;
}
