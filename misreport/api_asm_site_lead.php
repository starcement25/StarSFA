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

// echo 6363746;die;
if (!isset($_GET['emp_code'])) {
    $res_data = array("process_status" => "No", "process_message" => "Validation Failed!", 'error' => 'emp_code is required');
    echo json_encode($res_data);
    exit;
}

$emp_code = addslashes(trim($_GET['emp_code']));

// FIX 1: Check if employee exists AND has acedns='Y'
// $emp_sql = "SELECT * FROM employee_master WHERE emp_code = '$emp_code' AND acedns='Y'";

// $emp_result = mysqli_query($conn, $emp_sql);
// $row = mysqli_fetch_assoc($emp_result);
// if (!$emp_result || mysqli_num_rows($emp_result) == 0) {
//     $res_data = array("process_status" => "No", "process_message" => "Failed!", 'error' => 'Employee not found or inactive');
//     echo json_encode($res_data);
//     exit;
// }

// $employee_hierarchy = getReportingChain($conn, $emp_code);

// $unique_hierarchy = [];

// foreach ($employee_hierarchy as $item) {
//     $code = $item['reports_to_code'];

//     if ($code === $emp_code || isset($unique_hierarchy[$code])) {
//         continue;
//     }

//     $unique_hierarchy[$code] = $item;
// }

// $employee_hierarchy = array_values($unique_hierarchy);

$employees = [];
// foreach ($employee_hierarchy as $emp) {
    // FIX 2: Add extra check to ensure acedns='Y'
    // $emp_sqls = "
    // SELECT emp_code, emp_name, level
    // FROM employee_master 
    // WHERE emp_code = '{$emp['reports_to_code']}' 
    // AND emp_code != '$emp_code' 
    // AND acedns = 'Y'
    // ";
$emp_sqls = "
  SELECT DISTINCT emp_code,emp_name,level FROM employee_master WHERE sale_access='Primary' AND emp_code!='E0555' AND acedns='Y' ORDER BY emp_code ASC
    ";
    $emp_results = mysqli_query($conn, $emp_sqls);

    if (!$emp_results) {
        echo json_encode([
            "process_status" => "No",
            "process_message" => "Query Failed",
            "error" => mysqli_error($conn)
        ]);
        exit;
    }

    if ($emp_results && mysqli_num_rows($emp_results) > 0) {
        while ($rows = mysqli_fetch_assoc($emp_results)) {
            if (!empty($rows['emp_code'])) {
                $employees[] = [
                    "emp_code" => $rows['emp_code'],
                    "emp_name" => $rows['emp_name'],
                    "level"    => $rows['level'],
                   
                ];
            }
        }
    }
// }


$count = count($employees);
$output = trim($count . '¥' . '3') . "\n";

foreach ($employees as $emp) {
    $output .= $emp['emp_code'] . "^" . $emp['emp_name'] . "^" . $emp['level']. "\n";
}

header('Content-Type: text/plain');
echo trim($output);
exit;

function getReportingChain($conn, $empCode, &$visited = [])
{
    $chain = [];

    $empCodeEscaped = mysqli_real_escape_string($conn, $empCode);

    if (in_array($empCodeEscaped, $visited)) {
        return $chain;
    }
    $visited[] = $empCodeEscaped;

    $sql = "SELECT emp_code, emp_name, reporting_to, level 
            FROM employee_master 
            WHERE emp_code = '$empCodeEscaped' AND acedns='Y' LIMIT 1";

    $result = mysqli_query($conn, $sql);

    if ($row = mysqli_fetch_assoc($result)) {

        $reportingTos = array_map('trim', explode(',', $row['reporting_to']));

        foreach ($reportingTos as $managerCode) {
            if (empty($managerCode)) continue;

            // FIX 3: Manager must also have acedns = 'Y'
            $mgrSql = "SELECT emp_code, emp_name, reporting_to, level 
                       FROM employee_master 
                       WHERE emp_code = '" . mysqli_real_escape_string($conn, $managerCode) . "' 
                       AND acedns='Y' LIMIT 1";

            $mgrResult = mysqli_query($conn, $mgrSql);

            if ($mgrRow = mysqli_fetch_assoc($mgrResult)) {

                // ADD ONLY MANAGER
                $chain[] = [
                    'reports_to_code' => $mgrRow['emp_code'],
                    'level'           => $mgrRow['level']
                ];

                // RECURSION for valid manager only
                $chain = array_merge($chain, getReportingChain($conn, $managerCode, $visited));
            }
        }
    }

    return $chain;
}