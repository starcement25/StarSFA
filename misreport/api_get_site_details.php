<?php

require_once("../sfa_connection.php");
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$localDB = new sfa_connection();
$conn = $localDB->conn;

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
// if (!isset($_GET['cust_phone']) || !isset($_GET['route_code'])) {
//     $res_data = array("process_status" => "No", "process_message" => "Validation Failed!", 'error' => 'Both Customer Number and Route is required');
//     echo json_encode($res_data);
//     exit;
// }
// $route_code = addslashes(trim($_GET['route_code']));
// $cust_phone = addslashes(trim($_GET['cust_phone']));
$route_code = mysqli_real_escape_string($conn,$data['route_code']);
$cust_phone = mysqli_real_escape_string($conn,$data['cust_phone']);
// echo $route_code.$cust_phone;
// die;
 $sites = [];

// $site_sql = "
//    SELECT SM.*, VM.*
// FROM site_master SM
// LEFT JOIN (
//     SELECT *
//     FROM site_visit_master
//     WHERE (site_id, created_at) IN (
//         SELECT site_id, MAX(created_at)
//         FROM site_visit_master
//         GROUP BY site_id
//     )
// ) VM ON VM.site_id = SM.id
// WHERE SM.route_code = '$route_code' AND SM.cust_phone = '$cust_phone'
// ";

$site_sql = "
SELECT 
    SM.*, 
    VM.*, 
    CM.customer_name AS rssd_name,
    BM.branch_name AS branch_name, 
    EM1.emp_name AS visited_by_name,
    EM2.emp_name AS approved_by_name
FROM site_master SM
LEFT JOIN (
    SELECT *
    FROM site_visit_master
    WHERE (site_id, created_at) IN (
        SELECT site_id, MAX(created_at)
        FROM site_visit_master
        GROUP BY site_id
    )
) VM ON VM.site_id = SM.id
LEFT JOIN customer_master CM ON CM.customer_code = VM.rssd
LEFT JOIN employee_master EM1 ON EM1.emp_code = VM.visited_by
LEFT JOIN employee_master EM2 ON EM2.emp_code = VM.approved_by
LEFT JOIN branch_master BM ON BM.branch_code = SM.branch_code
WHERE SM.route_code = '$route_code' AND SM.cust_phone = '$cust_phone'
";
// echo $site_sql;
// die;
$site_result = mysqli_query($conn, $site_sql);

$site_result = mysqli_query($conn, $site_sql);

if (!$site_result || mysqli_num_rows($site_result) == 0) {
    echo json_encode([
        "process_status" => "No",
        "process_message" => "Site not found",
        "error" => "No data"
    ]);
    exit;
}

while ($row = mysqli_fetch_assoc($site_result)) {
    
    $visit_fields = $row;
    unset($visit_fields['route_code'], $visit_fields['cust_phone'], $visit_fields['site_id']);

    $sites[] = [
        "site_id"     => $row['site_id'],
        "route_code"  => $row['route_code'],
        "cust_phone"  => $row['cust_phone'],
        "latest_visit" => $visit_fields 
    ];
}

// echo json_encode([
//     "process_status" => "Yes",
//     "process_message" => "Latest visit found",
//     "sites" => $sites
// ]);
$response = [
    "process_status" => "Yes",
    "process_message" => "Latest visit found",
    "sites" => $sites
];

$response = replace_null_with_empty_string($response);
echo json_encode($response);

function replace_null_with_empty_string($data) {
    foreach ($data as $key => $value) {
        if (is_array($value)) {
            $data[$key] = replace_null_with_empty_string($value);
        } elseif (is_null($value)) {
            $data[$key] = "";
        }
    }
    return $data;
}
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

// echo json_encode([
//     "process_status" => "Yes",
//     "process_message" => "Dealers found",
//     "dealers" => $customers
// ]);
//exit;




