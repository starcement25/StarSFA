<?php
header("Content-Type: application/json");
date_default_timezone_set('Asia/Kolkata');


error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);


require_once("../sfa_connection.php");

require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("../include/functions.php"); // assuming function is here

$localDB = new sfa_connection();
$conn = $localDB->conn;

/* ================= VALIDATE INPUT ================= */
if(
    !isset($_POST['customer_code']) || 
    !isset($_POST['competitor_name']) || 
    !isset($_POST['emp_code'])
){
    echo json_encode([
        "status" => false,
        "message" => "customer_code, competitor_name and emp_code required"
    ]);
    exit;
}

$customer_code   = mysqli_real_escape_string($conn, $_POST['customer_code']);
$competitor_name = mysqli_real_escape_string($conn, $_POST['competitor_name']);
$emp_code        = mysqli_real_escape_string($conn, $_POST['emp_code']);

/* ================= EMP HIERARCHY ================= */
$employee_hierarchy = return_employee_hierarchy($emp_code); 
$emp_condition = "c1.emp_code IN ($employee_hierarchy)";

/* ================= QUERY ================= */
$sql = "
SELECT 
    c.customer_name,
    cq.customer_code,
    cq.competitor_name,
    cq.qty
FROM competitor_quantity cq

LEFT JOIN customer_master c 
    ON c.customer_code = cq.customer_code

INNER JOIN customer_route_emp_relation c1
    ON c1.customer_code = cq.customer_code

WHERE cq.customer_code = '$customer_code'
AND cq.competitor_name = '$competitor_name'
AND cq.acedns = 'yes'
AND $emp_condition

ORDER BY cq.created_at DESC
LIMIT 1
";

$result = mysqli_query($conn, $sql);

/* ================= RESPONSE ================= */
if($result && mysqli_num_rows($result) > 0){

    $row = mysqli_fetch_assoc($result);

    // FIX qty format
    $qty_array = explode('¥', $row['qty']);

    echo json_encode([
        "status" => true,
        "data" => [
            "customer_name"   => $row['customer_name'],
            "customer_code"   => $row['customer_code'],
            "competitor_name" => $row['competitor_name'],
            "qty_array"       => $qty_array,              // ["1","5"]
            "qty_string"      => implode("\n", $qty_array) // "1\n5"
        ]
    ]);

}else{
    echo json_encode([
        "status" => false,
        "message" => "No record found"
    ]);
}

exit;
?>