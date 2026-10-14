<?php
session_start();
if(strtoupper($_SESSION['admin_login']) == 'ACCOUNTS' && strtoupper($_SESSION['nick_name']) == 'STAR')
{
	require("adminUtils_accounts.php");
}
else
{
	require("adminUtils.php");
}
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");

date_default_timezone_set("Asia/Kolkata");

$link = mysqli_connect(SERVER, USER, PASSWORD, DB) or die("Database Connection Error.");

// ---------------------------
// Same filters as report page
// ---------------------------
$emp_code    = isset($_GET['emp_code']) ? trim($_GET['emp_code']) : '';
$branch_code = isset($_GET['branch_code']) ? trim($_GET['branch_code']) : '';
$esc_dept    = isset($_GET['esc_dept']) ? trim($_GET['esc_dept']) : '';
$status      = isset($_GET['status']) ? trim($_GET['status']) : '';
$from_date   = isset($_GET['from_date']) ? trim($_GET['from_date']) : '';
$to_date     = isset($_GET['to_date']) ? trim($_GET['to_date']) : '';
$search      = isset($_GET['search']) ? trim($_GET['search']) : '';

// ---------------------------
// Build WHERE clause (identical logic to report page)
// ---------------------------
$where = [];

if ($emp_code !== '') {
    $emp_code_esc = mysqli_real_escape_string($link, $emp_code);
    $where[] = "emp_code = '$emp_code_esc'";
}

if ($branch_code !== '') {
    $branch_code_esc = mysqli_real_escape_string($link, $branch_code);
    $where[] = "branch_code = '$branch_code_esc'";
}

if ($esc_dept !== '') {
    $esc_dept_esc = mysqli_real_escape_string($link, $esc_dept);
    $where[] = "esc_dept = '$esc_dept_esc'";
}

if ($status !== '') {
    $status_esc = mysqli_real_escape_string($link, $status);
    $where[] = "status = '$status_esc'";
}

if ($from_date !== '' && $to_date !== '') {
    $from_esc = mysqli_real_escape_string($link, $from_date);
    $to_esc   = mysqli_real_escape_string($link, $to_date);
    $where[] = "DATE(created_at) BETWEEN '$from_esc' AND '$to_esc'";
} elseif ($from_date !== '') {
    $from_esc = mysqli_real_escape_string($link, $from_date);
    $where[] = "DATE(created_at) >= '$from_esc'";
} elseif ($to_date !== '') {
    $to_esc = mysqli_real_escape_string($link, $to_date);
    $where[] = "DATE(created_at) <= '$to_esc'";
}

if ($search !== '') {
    $search_esc = mysqli_real_escape_string($link, $search);
    $where[] = "(emp_name LIKE '%$search_esc%' 
                 OR customer_name LIKE '%$search_esc%' 
                 OR customer_code LIKE '%$search_esc%' 
                 OR branch_name LIKE '%$search_esc%')";
}

$whereSql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

// ---------------------------
// Fetch ALL matching records (no pagination for export)
// ---------------------------
$dataSql = "SELECT * FROM raise_issue $whereSql ORDER BY created_at DESC";
$dataRs  = mysqli_query($link, $dataSql);

if (!$dataRs) {
    die("Query failed: " . mysqli_error($link));
}

// ---------------------------
// Stream as downloadable CSV
// ---------------------------
$filename = "raise_issue_report_" . date('Y-m-d_His') . ".csv";

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

$output = fopen('php://output', 'w');

// UTF-8 BOM so Excel renders special characters correctly
fputs($output, "\xEF\xBB\xBF");

// Header row
fputcsv($output, [
    "Sr. No",
    "Emp Code",
    "Emp Name",
    "Branch Code",
    "Branch Name",
    "Customer Code",
    "Customer Name",
    "Category",
    "State",
    "District",
    "Escalation Dept",
    "Issue",
    "Status",
    "Status Change Date",
    "Created At"
]);

// Data rows
$sr = 1;
while ($row = mysqli_fetch_assoc($dataRs)) {
    fputcsv($output, [
        $sr++,
        $row['emp_code'],
        $row['emp_name'],
        $row['branch_code'],
        $row['branch_name'],
        $row['customer_code'],
        $row['customer_name'],
        $row['category'],
        $row['state'],
        $row['district'],
        $row['esc_dept'],
        $row['issue'],
        ucfirst($row['status']),
        date('d-m-Y', strtotime($row['status_cnange_date'])),
        date('d-m-Y', strtotime($row['created_at']))
    ]);
}

fclose($output);
exit;