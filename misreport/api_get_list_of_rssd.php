<?php

require_once("../sfa_connection.php");
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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
$row = mysqli_fetch_assoc($emp_result);
if (!$emp_result || mysqli_num_rows($emp_result) == 0) {
     $res_data = array("process_status" => "No", "process_message" => " Failed!", 'error' => 'Employee not found');
    echo json_encode($res_data);
    exit;
}

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
    SELECT DISTINCT CRER.route_code, RM.route_name,CRER.customer_code
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



// $route_sql="
//     SELECT route_name,route_code
//     FROM route_master 
//     WHERE branch_code LIKE '%{$row['branch_code']}%' 

// ";

// $route_results = mysqli_query($conn, $route_sql);

// if (!$route_results) {
//     echo json_encode([
//         "process_status" => "No",
//         "process_message" => "Query Failed",
//         "error" => mysqli_error($conn)
//     ]);
//     exit;
// }

// $routes = [];

// while ($rows = mysqli_fetch_assoc($route_results)) {
    
//     $routes[] = [
//         "route_code" => $rows['route_code'],
//         "route_name" => $rows['route_name']
//     ];
// }

// echo '<pre>';
// print_r($routes);
// die;
// $cust_sqls = "
//     SELECT customer_name,customer_code
//     FROM customer_master 
//     WHERE branch_code LIKE '%{$row['branch_code']}%' 

// ";

// $cust_results = mysqli_query($conn, $cust_sqls);

// if (!$cust_results) {
//     echo json_encode([
//         "process_status" => "No",
//         "process_message" => "Query Failed",
//         "error" => mysqli_error($conn)
//     ]);
//     exit;
// }

// $customers = [];
// while ($rows = mysqli_fetch_assoc($cust_results)) {
//     $customers[] = [
//         "customer_code" => $rows['customer_code'],
//         "customer_name" => $rows['customer_name']
//     ];
// }
$customers = [];

foreach ($routes as $route) {
    $customer_code = $route['customer_code'];

    // $cust_sql = "
    //     SELECT 
    //         CRER.customer_code,
    //         CM.customer_name,
            
    //         CRER.route_code
    //     FROM customer_route_emp_relation CRER
    //     JOIN customer_master CM ON CM.customer_code = CRER.customer_code
    //     WHERE CRER.route_code = '$route_code'
    //    AND CRER.acedns = 'Y'
    // ";
    $cust_sql = "
        SELECT 
            customer_code,
            customer_name FROM
            
            
        customer_master 
        WHERE customer_code = '$customer_code'
      
    ";
// echo $cust_sql;
// die;
    $cust_result = mysqli_query($conn, $cust_sql);

    if ($cust_result && mysqli_num_rows($cust_result) > 0) {
        while ($row = mysqli_fetch_assoc($cust_result)) {
            $customers[$row['customer_code']] = [
                 "customer_code"  => $row['customer_code'],
                "customer_name"  => $row['customer_name'],
               
                // "route_code" => $row['route_code']
            ];
        }
    }
}
$customers = array_values($customers);

//$customers = array_map("unserialize", array_unique(array_map("serialize", $customers)));
echo json_encode([
    "process_status" => "Yes",
    "process_message" => "Dealers found",
    "dealers" => $customers
]);
exit;


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


