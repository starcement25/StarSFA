<?php
// Required headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="allocation_report.csv"');

// API base URL
$sathi_url = "https://starsaathi.com/"; // Replace with actual URL
$apiUrl = $sathi_url . "SAP/sfa_allocation_report_export_api.php?";
session_start();
// Get GET parameters
$sl_branch = $_GET['sl_branch'] ?? '';
$srch_linked_dealer = $_GET['srch_linked_dealer'] ?? '';
$srch_sub_dealer = $_GET['srch_sub_dealer'] ?? '';
$month = $_GET['month'] ?? '';
$emp_code = $_GET['emp_code'] ?? '';

// Build API query
$query = http_build_query([

    'start_from' => 0,
    'limit' => 10000, // Increase if needed
    'sl_branch' => $sl_branch,
    'srch_linked_dealer' => $srch_linked_dealer,
    'srch_sub_dealer' => $srch_sub_dealer,
    'month' => $month,
    'emp_code' => $_SESSION['admin_login']
]);
//echo"<pre>";print_r($_SESSION['admin_login']);die;
//echo"<pre>";print_r("$apiUrl&$query");die;

// Fetch data
$response = file_get_contents("$apiUrl&$query");
$data = json_decode($response, true);
$records = $data['data'] ?? [];
//echo"<pre>";print_r($data);die;
// Open output stream
$output = fopen('php://output', 'w');

// Write CSV header
fputcsv($output, [
    'Date and Time',
    'App Order No',
    'Invoice Date',
    'Linked Dealer Code',
    'Linked Dealer SAP Code',
    'Linked Dealer Name',
    'Sub Dealer RSSD Code',
    'Sub Dealer RSSD SAP Code',
    'Sub Dealer Name',
    'Branch',
    'Month',
    'Product Display Name',
    'Total Invoice Qty',
    'Total Allocation Qty',
    'Remaining Allocation Qty',
    'Invoice No',
    'Invoice Cancelled'
]);

// Write records
foreach ($records as $row) {
    fputcsv($output, [
        $row['date_and_time'] ?? '',
        $row['APPORDERNO'] ?? '',
        $row['inv_date'] ?? '',
        $row['linked_dealer_code'] ?? '',
        $row['linked_dealer_sap_code'] ?? '',
        $row['linked_dealer_name'] ?? '',
        $row['sub_dealer_rssd_code'] ?? '',
        $row['sub_dealer_rssd_sap_code'] ?? '',
        $row['sub_dealer_rssd_name'] ?? '',
        $row['branch'] ?? '',
        $row['month'] ?? '',
        $row['prod_display_name'] ?? '',
        $row['total_inv_qty'] ?? '',
        $row['total_allocation_qty'] ?? '',
        $row['remaining_allocation_qty'] ?? '',
        $row['inv_no'] ?? '',
        $row['inv_cancl'] ?? ''
    ]);
}

fclose($output);
exit;
