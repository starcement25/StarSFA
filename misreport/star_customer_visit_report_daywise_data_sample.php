<?php
ob_start();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
/**
 * One-time CSV export: Customer Visit Details Report
 * Run via browser or CLI: php export_visit_report_csv.php
 * Output: visit_report_YYYY-MM-DD_HH-MM-SS.csv (written to same directory)
 */

// ── Dependencies ─────────────────────────────────────────────────────────────
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");

// ── Hard-coded parameters ─────────────────────────────────────────────────────
$employee   = "'E2391','E1963','E2184','E1250','E1995','E1717','E1986','E1940','E2371','E1153','E0877','E1732','E2115','E2457','E2242','E2147','E0884','E2138','E1990','E2114','E1119','E0794','E1210','E0881','E0754','E2158','E2459','E2306','E2458','E1859','E0896','E2384','E2062','E1151','E1985','E1333','E2185','E1703','E1698','E0256','E2370','E2366','E2453','E2289','E2420','E2167','E0839','E1733','E2315','E2213','E1347','E0780','E0402','E1958','E2426','E2259','E2470','E2186','E2140','E1720','E2395','E0938','E1988','E2318','E0078','E2082','E2387','E2443','E1349','E0107','E1044','E0464','E0670','E0527','E2393','E0750','E1262','E2088','E2160','E2270','E2405','E2255','E2396','E2409','E2261','E1740','E2291','E1677','E1968','E1989','E2423','E2246','E2392','E1326','E1734','E2352','E2296','E2151','E0202','E1459','E2218','E1964','E2084','E2308','E2422','E2403','E2428','E1752','E2243','E1997','E0163','E2362','E2244','E1736','E2368','E0222','E2116','E0580','E2467','E0024','E2118','E2157','E0879','E2163','E0725','E2331','E2117','E2398','E0206','E2113','E0897','E2142','E2364','E0571','E2349','E0832','E0533','E0554','E0553','E2256','E0555','E0556','E1706','E1837','E0666','E1299','E1303','E2136','E1123','E0168','E2334','E2214','E2083','E2400','E1984','E2385','E0249','E2241','E2202','E2187','E2222','E1731','E2407','E2298','E2394','E1323','E2290','E2316','E1233','E1451','E2424','E2380','E2144','E1071','E1317','E0875','E1751','E2340','E0772','E2220','E1705','E2446','E1145','E2208','E2447','E1258','E1214','E1973','E1976','E2245','E0703','E2307','E2397','E1253','E2234','E1932','E2435','E2381','E1357','E2077','E2353','E1330','E2262','E1735','E2379','E2406','E1450','E1188','E0406','E2080','E1189','E1980','E1449','E1725','E2425','E0843','E2399','E2267','E0186','E0890','E2411','E2344','E1147','E0520','E1310','E0672','E2066','E2207','E1212','E0538','E2421','E0741','E1987','E1220','E1884','E1849','E1722','E2369','E1211','E2321','E0807','E2269','E1138','E2079','E2209','E2469','E2414','E0777','E2388','E1712','E0770','E1969','E2383','E1730','E2286','E1074','E2271','E2363','E2410','E1983','E2112','E0178','E2328','E2180','E2159','E0681','E2433','E1109','E2068','E2254','E0825','E2203','E2093','E2382','E1313','E2442','E2129','E1865','E1033','E2389','E2372','E2390','E2401','E0714','E2137','E0174','E2065','E0251','E1996','E0243','E2258','E2386','E1977','E1729','E1114','E2130','E2206','E2456'";

$start_date = '2025-04-01';
$end_date   = '2026-03-31';

// Dates in YYYYMMDD form (for trans_id SUBSTRING comparisons)
$start_date_raw = str_replace('-', '', $start_date); // 20250401
$end_date_raw   = str_replace('-', '', $end_date);   // 20260331

// ── Output file ───────────────────────────────────────────────────────────────
$output_file = __DIR__ . '/visit_report_' . date('Y-m-d_H-i-s') . '.csv';
$fh = fopen($output_file, 'w');
if (!$fh) {
    die("ERROR: Cannot open output file for writing: $output_file\n");
}

// Write UTF-8 BOM so Excel opens it correctly
fwrite($fh, "\xEF\xBB\xBF");

// ── Helper: write one CSV row ─────────────────────────────────────────────────
function write_row($fh, array $cols) {
    fputcsv($fh, $cols);
}

// ── STEP 1: Pre-fetch check_in_out_details (per customer per day per emp) ────
$check_in_map  = [];   // key: customer_code|visit_date|emp_code → time string
$check_out_map = [];

$sql_cio = "SELECT customer_code,
                   check_in_time,
                   check_out_time,
                   DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%Y-%m-%d') AS visit_date,
                   SUBSTRING(trans_id,3,5) AS emp_code
            FROM check_in_out_details
            WHERE SUBSTRING(trans_id,3,5) IN($employee)
              AND SUBSTRING(trans_id,-14,8) BETWEEN '$start_date_raw' AND '$end_date_raw'";

$res_cio = mysqli_query($link, $sql_cio);
if (!$res_cio) {
    die("Query failed (check_in_out_details): " . mysqli_error($link) . "\n");
}
while ($row = mysqli_fetch_assoc($res_cio)) {
    $k = $row['customer_code'] . '|' . $row['visit_date'] . '|' . $row['emp_code'];
    $check_in_map[$k]  = date('H:i:s', strtotime($row['check_in_time']));
    $check_out_map[$k] = date('H:i:s', strtotime($row['check_out_time']));
}
mysqli_free_result($res_cio);

// ── STEP 2: Pre-fetch fallback from location table (AE* = daily in, CHE* = daily out) ──
$fb_check_in_map  = [];   // key: emp_code|visit_date → time string
$fb_check_out_map = [];

$sql_loc = "SELECT emp_code,
                   DATE(date) AS visit_date,
                   trans_id,
                   date AS loc_time
            FROM location
            WHERE emp_code IN($employee)
              AND DATE(date) BETWEEN '$start_date' AND '$end_date'
              AND (trans_id LIKE 'AE%' OR trans_id LIKE 'CHE%')
            ORDER BY date ASC";

$res_loc = mysqli_query($link, $sql_loc);
if (!$res_loc) {
    die("Query failed (location fallback): " . mysqli_error($link) . "\n");
}
while ($row = mysqli_fetch_assoc($res_loc)) {
    $k    = $row['emp_code'] . '|' . $row['visit_date'];
    $time = date('H:i:s', strtotime($row['loc_time']));
    if (substr($row['trans_id'], 0, 3) === 'CHE') {
        if (!isset($fb_check_out_map[$k])) $fb_check_out_map[$k] = $time;
    } elseif (substr($row['trans_id'], 0, 2) === 'AE') {
        if (!isset($fb_check_in_map[$k]))  $fb_check_in_map[$k]  = $time;
    }
}
mysqli_free_result($res_loc);

// ── STEP 3: Office plant exclusion list ──────────────────────────────────────
$sql_op = "SELECT customer_code FROM office_plant_master";
$res_op = mysqli_query($link, $sql_op);
$office_plant_list = [];
while ($row = mysqli_fetch_assoc($res_op)) {
    $office_plant_list[] = "'" . $row['customer_code'] . "'";
}
mysqli_free_result($res_op);
$office_plant_string = !empty($office_plant_list) ? implode(',', $office_plant_list) : "''";

// ── STEP 4: Main report query ─────────────────────────────────────────────────
$sql_main = "SELECT * FROM (
                SELECT DISTINCT
                    CVD.emp_code,
                    DATE_FORMAT(SUBSTRING(CVD.trans_id,-14,8),'%Y-%m-%d') AS visit_date,
                    SUBSTRING(CVD.trans_id,-14,14)                        AS visit_date_parts,
                    CVD.customer_code,
                    CVD.customer_name,
                    CVD.route_name,
                    CVD.route_code,
                    CVD.cust_type,
                    EM.emp_name,
                    EM.dns_emp_code,
                    CM.dns_customer_code,
                    BM.branch_name,
                    CVD.trans_id,
                    CVD.hint_remarks,
                    LO.purpose_of_visit
                FROM customer_visit_details CVD
                JOIN employee_master  EM ON CVD.emp_code      = EM.emp_code
                JOIN customer_master  CM ON CVD.customer_code = CM.customer_code
                JOIN branch_master    BM ON CM.branch_code    = BM.branch_code
                JOIN location         LO ON LO.trans_id       = CVD.trans_id
                WHERE CVD.emp_code IN($employee)
                  AND SUBSTRING(CVD.trans_id,-14,8) BETWEEN '$start_date_raw' AND '$end_date_raw'
                  AND CM.dns_customer_code NOT IN($office_plant_string)
                ORDER BY SUBSTRING(CVD.trans_id,-14,14) DESC
             ) AS SAT
             ORDER BY SAT.emp_name ASC";

$res_main = mysqli_query($link, $sql_main);
if (!$res_main) {
    fclose($fh);
    die("Main query failed: " . mysqli_error($link) . "\n");
}

// ── STEP 5: Write CSV header ──────────────────────────────────────────────────
write_row($fh, [
    'SI',
    'Date of Visit',
    'Customer Code',
    'Customer Name',
    'Route',
    'Type',
    'Branch',
    'Employee Code',
    'Employee Name',
    'Check In Time',
    'Check Out Time',
    'Duration',
    'Visit Status (Productive / Non Productive)',
    'Remarks',
    'Purpose Of Visit',
]);

// ── STEP 6: Write data rows ───────────────────────────────────────────────────
$count              = 1;
$seen               = [];  // dedup: visit_date + dns_customer_code + emp_code

while ($row = mysqli_fetch_assoc($res_main)) {
    $emp_code          = $row['emp_code'];
    $dns_emp_code      = $row['dns_emp_code'];
    $emp_name          = $row['emp_name'];
    $customer_code     = $row['customer_code'];
    $customer_name     = $row['customer_name'];
    $dns_customer_code = $row['dns_customer_code'];
    $route_name        = $row['route_name'];
    $cust_type         = $row['cust_type'];
    $branch_name       = $row['branch_name'];
    $trans_id          = $row['trans_id'];
    $visit_date_fmt    = $row['visit_date'];                                    // YYYY-MM-DD
    $visit_date_disp   = date('d-m-Y', strtotime(substr($trans_id, -14, 8)));  // DD-MM-YYYY
    $remark            = $row['hint_remarks'];
    $purpose_of_visit  = $row['purpose_of_visit'];

    // ── Visit status ──────────────────────────────────────────────────────────
    $pfx2 = substr($trans_id, 0, 2);
    $pfx1 = substr($trans_id, 0, 1);
    $visit_status = '';
    if (in_array($pfx2, ['OE', 'SE', 'PE']))        $visit_status = 'Productive';
    elseif (in_array($pfx2, ['NS', 'NO', 'NC']))     $visit_status = 'Non Productive';
    if ($pfx1 === 'M')                               $visit_status = 'Productive';

    // ── Check-in / Check-out resolution ──────────────────────────────────────
    // Priority 1: check_in_out_details (customer-specific)
    $cio_key        = $customer_code . '|' . $visit_date_fmt . '|' . $emp_code;
    $check_in_time  = $check_in_map[$cio_key]  ?? '';
    $check_out_time = $check_out_map[$cio_key] ?? '';

    // Priority 2: daily fallback from location table
    $fb_key = $emp_code . '|' . $visit_date_fmt;
    if ($check_in_time  === '') $check_in_time  = $fb_check_in_map[$fb_key]  ?? '';
    if ($check_out_time === '') $check_out_time = $fb_check_out_map[$fb_key] ?? '';

    // ── Duration calculation ──────────────────────────────────────────────────
    $time_duration = '';
    if ($check_in_time !== '' && $check_out_time !== '') {
        $diff = strtotime($check_out_time) - strtotime($check_in_time);
        if ($diff >= 3600) {
            $h = floor($diff / 3600);
            $m = floor(($diff % 3600) / 60);
            $s = $diff % 60;
            $time_duration = "{$h} Hour(s) {$m} Minute(s) {$s} Second(s)";
        } elseif ($diff >= 60) {
            $m = floor($diff / 60);
            $s = $diff % 60;
            $time_duration = "{$m} Minute(s) {$s} Second(s)";
        } else {
            $time_duration = "{$diff} Second(s)";
        }
    }

    // ── Dedup check ───────────────────────────────────────────────────────────
    $dedup_key = $visit_date_disp . '|' . $dns_customer_code . '|' . $emp_code;
    if (isset($seen[$dedup_key])) continue;
    $seen[$dedup_key] = true;

    // ── Write row ─────────────────────────────────────────────────────────────
    write_row($fh, [
        $count,
        $visit_date_disp,
        $dns_customer_code,
        $customer_name,
        $route_name,
        $cust_type,
        $branch_name,
        $dns_emp_code,
        $emp_name,
        $check_in_time,
        $check_out_time,
        $time_duration,
        $visit_status,
        $remark,
        $purpose_of_visit,
    ]);
    $count++;
}

mysqli_free_result($res_main);
fclose($fh);

$total = $count - 1;
echo "Done. $total rows written to: $output_file\n";