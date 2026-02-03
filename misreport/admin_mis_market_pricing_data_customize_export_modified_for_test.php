<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('memory_limit', '512M'); // increase only moderately
set_time_limit(0);

session_start();
require("adminUtils.php");
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");

if (isset($_SESSION['competitor_name_passing_array'])) {
    $storedArray = $_SESSION['competitor_name_passing_array'];
} else {
    die("Competitor array missing in session.");
}

$start_date = "2025-11-10";
$end_date   = "2025-11-19";

// build header
$header_not_star = [
    "SI. No", "Date of Visit", "Emp Code", "Emp Name", "Branch", "Cust Category",
    "Cust Code", "Cust Name", "Route Name", "Contact No"
];

foreach ($storedArray as $competitor) {
    array_push(
        $header_not_star,
        $competitor . " Billing EX",
        $competitor . " Billing For",
        $competitor . " WSP EX",
        $competitor . " WSP For",
        $competitor . " RSP"
    );
}

$count       = 1;
$queryFrom   = 0;
$queryTo     = 1000;
$filename    = 'large_data_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.csv';
$filePath    = __DIR__ . "/exporting_test/" . $filename;

// open file for writing
$fileHandle = fopen($filePath, 'w');
if (!$fileHandle) {
    die("Unable to open file for writing.");
}

// write header
fputcsv($fileHandle, $header_not_star);

// get competitor names as SQL IN string
$competitor_string = implode(",", array_map(fn($v) => "'" . mysqli_real_escape_string($link, $v) . "'", $storedArray));

while (true) {
    $sql = "
        SELECT 
            CM.customer_name, CM.phone_no, CM.cust_type, CM.dns_customer_code, 
            RM.route_name, EM.emp_name, EM.dns_emp_code,
            SUM(MF.PTD) AS PTD, SUM(MF.PTR) AS PTR, SUM(MF.PTC) AS PTC,
            SUM(MF.PV) AS PV, SUM(MF.billing_ex_for) AS billing_ex_for,
            SUM(MF.wsp_ex_for) AS wsp_ex_for,
            DATE_FORMAT(SUBSTRING(MF.market_feedback_id,-14,8),'%d-%m-%Y') AS visit_date,
            BM.branch_name, MF.competitor_name 
        FROM market_feedback MF
        JOIN customer_master CM ON MF.customer_code = CM.customer_code
        JOIN route_master RM ON CM.route_code = RM.route_code
        JOIN employee_master EM ON SUBSTRING(MF.market_feedback_id,3,5) = EM.emp_code
        JOIN branch_master BM ON CM.branch_code = BM.branch_code
        WHERE DATE_FORMAT(SUBSTRING(MF.market_feedback_id,-14,8),'%Y-%m-%d')
              BETWEEN '$start_date' AND '$end_date'
          AND MF.competitor_name IN ($competitor_string)
        GROUP BY EM.dns_emp_code, visit_date, CM.dns_customer_code, MF.competitor_name
        ORDER BY visit_date DESC,
                 FIELD(MF.competitor_name, 'STAR PSC','STAR PPC','STAR') DESC,
                 MF.competitor_name ASC
        LIMIT $queryFrom, $queryTo
    ";

    $res = mysqli_query($link, $sql);
    if (!$res) {
        die("SQL Error: " . mysqli_error($link));
    }

    $countRows = mysqli_num_rows($res);
    if ($countRows === 0) {
        break; // no more rows
    }

    // organize rows grouped by emp-customer-date
    $grouped = [];
    while ($r = mysqli_fetch_assoc($res)) {
        $key = $r['dns_emp_code'] . '#' . $r['dns_customer_code'] . '#' . $r['visit_date'];

        if (!isset($grouped[$key])) {
            $grouped[$key] = [
                'dns_emp_code'     => $r['dns_emp_code'],
                'emp_name'         => $r['emp_name'],
                'branch_name'      => $r['branch_name'],
                'cust_type'        => $r['cust_type'],
                'dns_customer_code'=> $r['dns_customer_code'],
                'customer_name'    => $r['customer_name'],
                'route_name'       => $r['route_name'],
                'phone_no'         => $r['phone_no'],
                'visit_date'       => $r['visit_date'],
                'competitors'      => []
            ];
        }

        $grouped[$key]['competitors'][$r['competitor_name']] = [
            'PTD'            => $r['PTD'],
            'PTR'            => $r['PTR'],
            'PTC'            => $r['PTC'],
            'billing_ex_for' => $r['billing_ex_for'],
            'wsp_ex_for'     => $r['wsp_ex_for']
        ];
    }

    // write rows directly to CSV
    foreach ($grouped as $entry) {
        $row = [
            $count++,
            $entry['visit_date'],
            $entry['dns_emp_code'],
            $entry['emp_name'],
            $entry['branch_name'],
            $entry['cust_type'],
            $entry['dns_customer_code'],
            $entry['customer_name'],
            $entry['route_name'],
            $entry['phone_no']
        ];

        foreach ($storedArray as $competitor) {
            $compData = $entry['competitors'][$competitor] ?? null;
            $row[] = $compData['PTD'] ?? '-';
            $row[] = $compData['billing_ex_for'] ?? '-';
            $row[] = $compData['PTR'] ?? '-';
            $row[] = $compData['wsp_ex_for'] ?? '-';
            $row[] = $compData['PTC'] ?? '-';
        }

        fputcsv($fileHandle, $row);
    }

    // release memory for this chunk
    mysqli_free_result($res);
    unset($grouped);
    gc_collect_cycles();

    $queryFrom += $queryTo;
}

// close file and connection
fclose($fileHandle);
mysqli_close($link);

// send file for download
if (file_exists($filePath)) {
    header('Content-Description: File Transfer');
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filePath));

    readfile($filePath);
    unlink($filePath);
    exit;
} else {
    echo "<span style='font-weight:bold; color:red;'>No Records Found!</span>";
}
?>
