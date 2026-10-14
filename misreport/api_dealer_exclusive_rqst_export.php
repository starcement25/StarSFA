<?php
ini_set('memory_limit', '1024M');
ob_start();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set("Asia/Kolkata");

require("adminUtils.php");
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require_once("../sfa_connection.php");

/* ================= INPUT ================= */
$emp_code        = $_SESSION['admin_login'] ?? '';
$dns_emp_code    = isset($_REQUEST['dns_emp_code']) ? $_REQUEST['dns_emp_code'] : '';
$month           = isset($_REQUEST['month']) ? $_REQUEST['month'] : '';
$year            = isset($_REQUEST['year']) ? $_REQUEST['year'] : '';
$approval_status = isset($_REQUEST['status']) ? $_REQUEST['status'] : '';
$search          = isset($_REQUEST['search']) ? $_REQUEST['search'] : '';

/* If dns_emp_code was not passed, resolve it from the logged in employee */
if ($dns_emp_code === '' && $emp_code !== '') {

    $getEmpCodeQry = "
        SELECT dns_emp_code
        FROM employee_master
        WHERE emp_code = '" . mysqli_real_escape_string($link, $emp_code) . "'
    ";

    $getEmpCodeRs = mysqli_query($link, $getEmpCodeQry);

    if ($getEmpCodeRs && mysqli_num_rows($getEmpCodeRs) > 0) {
        $getEmpCodeRow = mysqli_fetch_assoc($getEmpCodeRs);
        $dns_emp_code  = $getEmpCodeRow['dns_emp_code'];
    }
}

/* ================= FETCH JSON FROM SAP API ================= */
$sathi_url = SAATHI_URL;
$apiUrl    = $sathi_url . "/SAP/api_export_dealer_exclusive.php";

$queryParams = [
    'emp_code'     => $emp_code,
    'dns_emp_code' => $dns_emp_code,
    'month'        => $month,
    'year'         => $year,
    'status'       => $approval_status,
    'search'       => $search,
];

$queryParams = array_filter($queryParams, function ($v) {
    return !is_null($v) && $v !== '';
});

$fullUrl = $apiUrl . '?' . http_build_query($queryParams);
// echo $fullUrl;die;

$response = file_get_contents($fullUrl);

if ($response === false) {
    echo "Error fetching data from API.";
    exit;
}

$apiData = json_decode($response, true);

if (!is_array($apiData)) {
    echo "Invalid response received from API.";
    exit;
}

$records = isset($apiData['data']) ? $apiData['data'] : [];
//echo"<pre>";print_r($_POST);die;

/* =========================================================
 | RESOLVE ASM / RSM emp_code AND emp_name FROM THE LOCAL
 | employee_master, MATCHING THE ASM / RSM CODE RETURNED BY
 | THE SAP API AGAINST employee_master.dns_emp_code
 |
 | SELECT emp_code FROM employee_master WHERE dns_emp_code = '<ASM Code>'
 | SELECT emp_code FROM employee_master WHERE dns_emp_code = '<RSM Code>'
 ========================================================= */
$dnsCodes = [];

foreach ($records as $row) {

    if (!empty($row['ASM Code']) && $row['ASM Code'] != 'N/A') {
        $dnsCodes[] = $row['ASM Code'];
    }

    if (!empty($row['RSM Code']) && $row['RSM Code'] != 'N/A') {
        $dnsCodes[] = $row['RSM Code'];
    }
}

$dnsCodes = array_unique(array_filter($dnsCodes));

$empByDnsCode = [];

if (!empty($dnsCodes)) {

    $escapedCodes = [];

    foreach ($dnsCodes as $code) {
        $escapedCodes[] = mysqli_real_escape_string($link, $code);
    }

    $dnsCodesStr = "'" . implode("','", $escapedCodes) . "'";

    $empSql = "
        SELECT dns_emp_code, emp_code, emp_name
        FROM employee_master
        WHERE dns_emp_code IN ($dnsCodesStr)
    ";

    $empRs = mysqli_query($link, $empSql);

    while ($empRs && $empRow = mysqli_fetch_assoc($empRs)) {
        $empByDnsCode[$empRow['dns_emp_code']] = [
            'emp_code' => $empRow['emp_code'],
            'emp_name' => $empRow['emp_name'],
        ];
    }
}

/* ================= CSV DOWNLOAD ================= */
while (ob_get_level() > 0) {
    ob_end_clean();
}

$filename = "dealer_exclusive_requests_" . date('Ymd_His') . ".csv";

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header("Pragma: no-cache");
header("Expires: 0");

$output = fopen('php://output', 'w');

fputcsv($output, [
    'Sr. No', 'ID', 'Dealer Code', 'Dealer ID', 'Dealer Name', 'Month',
    'Branch', 'Lifting Qty', 'ASM DNS Code', 'ASM Approval Status', 'Approved By ASM',
    'ASM Approved Date', 'RSM DNS Code', 'RSM Approval Status', 'Approved By RSM',
    'RSM Approved Date', 'Status', 'Created At'
]);

$i = 0;

foreach ($records as $row) {

    $asm_dns_code = isset($row['ASM DNS Emp Code']) ? $row['ASM DNS Emp Code'] : '';
    $rsm_dns_code = isset($row['RSM DNS Emp Code']) ? $row['RSM DNS Emp Code'] : '';

    /* Name from local employee_master, fall back to the name sent by the API */
    $asm_name = isset($empNameByDnsCode[$asm_dns_code])
        ? $empNameByDnsCode[$asm_dns_code]
        : (isset($row['Approved By ASM']) ? $row['Approved By ASM'] : 'N/A');

    $rsm_name = isset($empNameByDnsCode[$rsm_dns_code])
        ? $empNameByDnsCode[$rsm_dns_code]
        : (isset($row['Approved By RSM']) ? $row['Approved By RSM'] : 'N/A');

    fputcsv($output, [
        ++$i,
        $row['ID'],
        $row['Dealer Code'],
        $row['Dealer ID'],
        $row['Dealer Name'],
        $row['Month'],
        $row['Branch'],
        $row['Lifting Qty'],
        $asm_dns_code,
        $row['ASM Approval Status'],
        $asm_name,
        $row['ASM Approved Date'],
        $rsm_dns_code,
        $row['RSM Approval Status'],
        $rsm_name,
        $row['RSM Approved Date'],
        $row['Status'],
        $row['Created At'],
    ]);
}

fclose($output);

mysqli_close($link);
exit;
?>
