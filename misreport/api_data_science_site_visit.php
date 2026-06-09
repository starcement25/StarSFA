<?php
/**
 * Technical Meet Report API
 * Output: One employee = one row
 * Visit dates become: visit1, visit2, visit3...
 */
ini_set('memory_limit', '512M');
require_once("../sfa_connection.php");
require("../include/config.php");
require("../include/config-setup.php");
require("../include/dbcon.php");
require("../include/functions.php");

ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');
// echo 11313;die;
// ----------------------------------------------
// DB CONNECTION
$localDB = new sfa_connection();
$conn = $localDB->conn; // mysqli object

if (!$conn) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'DB connection failed'
    ]);
    exit;
}

// ----------------------------------------------
// GET PARAMETERS
//  $month = isset($_REQUEST['month']) ? $_REQUEST['month'] : null;
//  $year  = isset($_REQUEST['year']) ? $_REQUEST['year'] : null;

 $month = date('m');
 $year  = date('Y');

// if (empty($month) || empty($year)) {
//     http_response_code(400);
//     echo json_encode([
//         'status' => 'error',
//         'message' => 'Month and year are required parameters',
//         'example' => '/api/technical_meet_report.php?month=11&year=2025'
//     ]);
//     exit;
// }

$month = str_pad($month, 2, '0', STR_PAD_LEFT);
$year  = intval($year);

// Escape values
$month_escaped = $conn->real_escape_string($month);
$year_escaped  = $conn->real_escape_string($year);

$start_date = '2026-02-26';
$end_date   = '2026-03-22';

// Default: last 7 days if not provided
if (empty($start_date)) $start_date = date('Y-m-d', strtotime('-7 days'));
if (empty($end_date))   $end_date   = date('Y-m-d');

// Escape values
$start_escaped = $conn->real_escape_string($start_date);
$end_escaped   = $conn->real_escape_string($end_date);
// $sql = "
// SELECT 
//     SUBSTRING(s.survey_id, 3, 5) AS emp_code,
//     e.emp_name,
//     e.level,
//     e.designation,
//     s.survey_id,

//     DATE_FORMAT(
//         STR_TO_DATE(SUBSTRING(s.survey_id, 8, 8), '%Y%m%d'),
//         '%Y-%m-%d'
//     ) AS survey_date,

//     DATE_FORMAT(
//         STR_TO_DATE(SUBSTRING(s.survey_id, 8, 8), '%Y%m%d'),
//         '%d-%m-%Y'
//     ) AS survey_date_formatted,

//     MAX(CASE WHEN s.row_id = 'RA125' THEN s.value END) AS visit_type,
//     MAX(CASE WHEN s.row_id = 'RA126' THEN s.value END) AS meet_category,
//     MAX(CASE WHEN s.row_id = 'RA124' THEN s.value END) AS customer_code,
//     c.customer_name,
//     c.cust_type,
//     c.zone,
//     MAX(CASE WHEN s.row_id = 'RA123' THEN s.value END) AS customer_type_detail,
//     MAX(CASE WHEN s.row_id = 'RA143' THEN s.value END) AS branch_code,
//     b.branch_name

// FROM survey_output s

// LEFT JOIN employee_master e 
//     ON SUBSTRING(s.survey_id, 3, 5) = e.emp_code

// LEFT JOIN customer_master c 
//     ON c.customer_code = (
//         SELECT value 
//         FROM survey_output s2
//         WHERE s2.survey_id = s.survey_id 
//           AND s2.row_id = 'RA124'
//         LIMIT 1
//     )

// LEFT JOIN branch_master b
//     ON b.branch_code = (
//         SELECT value 
//         FROM survey_output s2
//         WHERE s2.survey_id = s.survey_id 
//           AND s2.row_id = 'RA143'
//         LIMIT 1
//     )

// WHERE s.type = 'Technical Meets'
//   AND SUBSTRING(s.survey_id, 8, 4) = '$year_escaped'
//   AND SUBSTRING(s.survey_id, 12, 2) = '$month_escaped'
//   AND s.row_id IN ('RA123','RA124','RA125','RA126','RA143')

// GROUP BY 
//     s.survey_id,
//     emp_code,
//     e.emp_name,
//     e.level,
//     e.designation,
//     c.customer_name,
//     c.cust_type,
//     b.branch_code,
//     c.zone

// HAVING COUNT(DISTINCT s.row_id) = 5 

// ORDER BY emp_code, survey_date ASC
// ";


// $sql = "
// SELECT 
//     SUBSTRING(s.survey_id, 3, 5) AS emp_code,
//     e.emp_name,
//     e.level,
//     e.designation,
//     s.survey_id,

//     DATE_FORMAT(
//         STR_TO_DATE(SUBSTRING(s.survey_id, 8, 8), '%Y%m%d'),
//         '%Y-%m-%d'
//     ) AS survey_date,

//     DATE_FORMAT(
//         STR_TO_DATE(SUBSTRING(s.survey_id, 8, 8), '%Y%m%d'),
//         '%d-%m-%Y'
//     ) AS survey_date_formatted,

//     MAX(CASE WHEN s.row_id = 'RA125' THEN s.value END) AS visit_type,
//     MAX(CASE WHEN s.row_id = 'RA126' THEN s.value END) AS meet_category,
//     MAX(CASE WHEN s.row_id = 'RA124' THEN s.value END) AS customer_code,
//     c.customer_name,
//     c.cust_type,
//     c.zone,
//     MAX(CASE WHEN s.row_id = 'RA123' THEN s.value END) AS customer_type_detail,
//     MAX(CASE WHEN s.row_id = 'RA143' THEN s.value END) AS branch_code,
//     b.branch_name

// FROM survey_output s

// LEFT JOIN employee_master e 
//     ON SUBSTRING(s.survey_id, 3, 5) = e.emp_code

// LEFT JOIN customer_master c 
//     ON c.customer_code = (
//         SELECT value 
//         FROM survey_output s2
//         WHERE s2.survey_id = s.survey_id 
//           AND s2.row_id = 'RA124'
//         LIMIT 1
//     )

// LEFT JOIN branch_master b
//     ON b.branch_code = (
//         SELECT value 
//         FROM survey_output s2
//         WHERE s2.survey_id = s.survey_id 
//           AND s2.row_id = 'RA143'
//         LIMIT 1
//     )

// WHERE s.type = 'Technical Meets'
//   AND STR_TO_DATE(SUBSTRING(s.survey_id, 8, 8), '%Y%m%d')
//        = DATE_SUB(CURDATE(), INTERVAL 1 DAY)
//   AND s.row_id IN ('RA123','RA124','RA125','RA126','RA143')

// GROUP BY 
//     s.survey_id,
//     emp_code,
//     e.emp_name,
//     e.level,
//     e.designation,
//     c.customer_name,
//     c.cust_type,
//     b.branch_code,
//     c.zone

// HAVING COUNT(DISTINCT s.row_id) = 5 

// ORDER BY emp_code, survey_date ASC
// ";
$sql = "
SELECT 
    SUBSTRING(s.survey_id, 3, 5) AS emp_code,
    e.emp_name,
    e.level,
    e.designation,
    s.survey_id,

    DATE_FORMAT(
        STR_TO_DATE(SUBSTRING(s.survey_id, 8, 8), '%Y%m%d'),
        '%Y-%m-%d'
    ) AS survey_date,

    DATE_FORMAT(
        STR_TO_DATE(SUBSTRING(s.survey_id, 8, 8), '%Y%m%d'),
        '%d-%m-%Y'
    ) AS survey_date_formatted,

    MAX(CASE WHEN s.row_id = 'RA125' THEN s.value END) AS visit_type,
    MAX(CASE WHEN s.row_id = 'RA126' THEN s.value END) AS meet_category,
    MAX(CASE WHEN s.row_id = 'RA124' THEN s.value END) AS customer_code,
    c.customer_name,
    c.cust_type,
    c.zone,
    MAX(CASE WHEN s.row_id = 'RA123' THEN s.value END) AS customer_type_detail,
    MAX(CASE WHEN s.row_id = 'RA143' THEN s.value END) AS branch_code,
    b.branch_name

FROM survey_output s

LEFT JOIN employee_master e 
    ON SUBSTRING(s.survey_id, 3, 5) = e.emp_code

LEFT JOIN customer_master c 
    ON c.customer_code = (
        SELECT value 
        FROM survey_output s2
        WHERE s2.survey_id = s.survey_id 
          AND s2.row_id = 'RA124'
        LIMIT 1
    )

LEFT JOIN branch_master b
    ON b.branch_code = (
        SELECT value 
        FROM survey_output s2
        WHERE s2.survey_id = s.survey_id 
          AND s2.row_id = 'RA143'
        LIMIT 1
    )

WHERE s.type = 'Technical Meets'
  AND STR_TO_DATE(SUBSTRING(s.survey_id, 8, 8), '%Y%m%d')
     BETWEEN '$start_escaped' AND '$end_escaped'
  AND s.row_id IN ('RA123','RA124','RA125','RA126','RA143')

GROUP BY 
    s.survey_id,
    emp_code,
    e.emp_name,
    e.level,
    e.designation,
    c.customer_name,
    c.cust_type,
    b.branch_code,
    c.zone

HAVING COUNT(DISTINCT s.row_id) = 5 

ORDER BY emp_code, survey_date ASC
";
//   echo $sql; die;

$result = $conn->query($sql);
if (!$result) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Query error: ' . $conn->error
    ]);
    exit;
}

// ----------------------------------------------
// PROCESS RESULTS → ONE EMPLOYEE = ONE ROW
$employeeData = [];

while ($row = $result->fetch_assoc()) {
    $employeeData[] = $row; 
}

// while ($row = $result->fetch_assoc()) {

//     $empCode = $row['emp_code'];

//     if (!isset($employeeData[$empCode])) {
//         $employeeData[$empCode] = [
//             // 'emp_code'     => $empCode,
//             // 'employee_name'=> $row['emp_name'],
//             // 'level'        => $row['level'],
//             // 'designation'  => $row['designation'],
//             //'visit_dates'  => [],     // unique date array
//             //'visits'       => []      // all visit details
//         ];
//     }

//     // Add unique visit date
//     // if (!in_array($row['survey_date'], $employeeData[$empCode]['visit_dates'])) {
//     //     $employeeData[$empCode]['visit_dates'][] = $row['survey_date'];
//     // }

//     // Add full visit detail
//     //$employeeData[$empCode]['visits'][] = $row;
//     $employeeData[$empCode]= $row;
// }

// ----------------------------------------------
// PIVOT visit_dates → visit1, visit2, visit3...
// foreach ($employeeData as $code => &$emp) {

//     sort($emp['visit_dates']);  // sort by date

//     $i = 1;
//     foreach ($emp['visit_dates'] as $dt) {
//         $emp["visit$i"] = $dt;
//         $i++;
//     }

//     // remove raw date array (optional)
//     unset($emp['visit_dates']);
// }

// Convert associative array → indexed array
$employeeData = array_values($employeeData);

// ----------------------------------------------
// FINAL RESPONSE
$response = [
    // 'status' => 'success',
    // 'message' => 'Technical meet report fetched',
    // 'filter' => [
    //     'month' => $month,
    //     'year'  => $year
    // ],
    // 'total_employees' => count($employeeData),
    'data' => $employeeData
];

$conn->close();

// Output JSON
echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

?>
