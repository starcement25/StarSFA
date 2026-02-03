<?php

require_once("../sfa_connection.php");
require("../include/config.php");
require("../include/config-setup.php");
require("../include/dbcon.php");
require("../include/functions.php");
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

// echo 647654756;
// die;
	$employee_hierarchy=getReportingChain($conn,$emp_code);
    $unique_hierarchy = [];

foreach ($employee_hierarchy as $item) {
    $code = $item['reports_to_code'];

 
    if ($code === $emp_code || isset($unique_hierarchy[$code])) {
        continue;
    }

    $unique_hierarchy[$code] = $item;
}




$employee_hierarchy = array_values($unique_hierarchy);
    // print_r($employee_hierarchy);
    // exit;
  $employees = [];
 foreach($employee_hierarchy as $emp){
    $emp_sqls = "
    SELECT emp_code, emp_name 
    FROM employee_master 
    WHERE emp_code  = '{$emp['reports_to_code']}' AND emp_code != '$emp_code'

 ";

	//$emp_hierarchy_condition=' AND CM.emp_code IN('.$employee_hierarchy.') AND CM.acedns="Y"' ;

// echo json_encode([
//     "employee_hierarchy" => $employee_hierarchy
// ]);
// exit;
// $emp_sqls = "
//     SELECT emp_code, emp_name 
//     FROM employee_master 
//     WHERE emp_code IN ($employee_hierarchy) 
//     AND branch_code = '{$row['branch_code']}' 

// ";
// $emp_sqls = "
//     SELECT emp_code, emp_name 
//     FROM employee_master 
//     WHERE branch_code LIKE '%{$row['branch_code']}%' AND emp_code != '$emp_code'

// ";
$emp_results = mysqli_query($conn, $emp_sqls);

if (!$emp_results) {
    echo json_encode([
        "process_status" => "No",
        "process_message" => "Query Failed",
        "error" => mysqli_error($conn)
    ]);
    exit;
}

$rows = mysqli_fetch_assoc($emp_results);
$employees[] = [
        "emp_code" => $rows['emp_code'],
        "emp_name" => $rows['emp_name']
    ];
// while ($rows = mysqli_fetch_assoc($emp_results)) {
//     $employees[] = [
//         "emp_code" => $rows['emp_code'],
//         "emp_name" => $rows['emp_name']
//     ];
// }
 }
echo json_encode([
    "process_status" => "Yes",
    "process_message" => "Employees found",
    "employees" => $employees
]);
exit;


function getReportingChain($conn, $empCode, &$visited = [])
{
    $chain = [];

    $empCodeEscaped = mysqli_real_escape_string($conn, $empCode);

   
    if (in_array($empCodeEscaped, $visited)) {
        return $chain;
    }
    $visited[] = $empCodeEscaped;

   
    $sql = "SELECT emp_code, emp_name, reporting_to FROM employee_master WHERE emp_code = '$empCodeEscaped' LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if ($row = mysqli_fetch_assoc($result)) {
     
        // $chain[] = [
        //     'emp_code'         => $row['emp_code'],
        //     'emp_name'         => $row['emp_name'],
        //     'reports_to_code'  => null,
        //     'reports_to_name'  => null
        // ];

        $reportingTos = array_map('trim', explode(',', $row['reporting_to']));

        foreach ($reportingTos as $managerCode) {
            if (empty($managerCode)) continue;

         
            $mgrSql = "SELECT emp_code, emp_name, reporting_to FROM employee_master WHERE emp_code = '$managerCode' LIMIT 1";
            $mgrResult = mysqli_query($conn, $mgrSql);

            if ($mgrRow = mysqli_fetch_assoc($mgrResult)) {
                $chain[] = [
                    //'emp_code'         => $mgrRow['emp_code'],
                   // 'emp_name'         => $mgrRow['emp_name'],
                    'reports_to_code'  => $row['emp_code'],  
                    'reports_to_name'  => $row['emp_name']
                ];

                
                $chain = array_merge($chain, getReportingChain($conn, $managerCode, $visited));
            }
        }
    }

    return $chain;
}





