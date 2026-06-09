<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ini_set('log_errors', 1);
ini_set('error_log', '/tmp/php_errors.log');

ini_set('memory_limit', '1024M');
set_time_limit(0);
date_default_timezone_set("Asia/Kolkata");


define("SERVER", "localhost");
define("USER", "root");
define("PASSWORD", "Passw0rd123#$");
define("DB", "acedns_STAR");

try {

    $link = mysqli_connect(SERVER, USER, PASSWORD, DB);
    if (!$link) {
        throw new Exception('Database connection failed: ' . mysqli_connect_error());
    }
    mysqli_set_charset($link, "utf8mb4");

   
    $yesterday = date('Y-m-d', strtotime('-1 day'));

$start_date = $yesterday;
$end_date   = $yesterday;

    $start_date_numeric = str_replace('-', '', $start_date);
    $end_date_numeric   = str_replace('-', '', $end_date);

   
    $rpc_query = "
        SELECT PTC, COUNT(*) AS cnt
        FROM market_feedback
        WHERE SUBSTRING(market_feedback_id, -14, 8) BETWEEN '{$start_date_numeric}' AND '{$end_date_numeric}'
          AND PTC > 0
          AND competitor_name IN (
              'STAR CEMENT ANTIRUST',
              'STAR CEMENT OPC 43',
              'STAR CEMENT PPC',
              'STAR CEMENT PPC(ADSTAR)',
              'STAR DHALAI MASTER TRADE',
              'STAR WEATHER SHIELD (TRADE)'
          )
        GROUP BY PTC
        ORDER BY cnt DESC, PTC DESC
        LIMIT 1
    ";

    $rpc_result = mysqli_query($link, $rpc_query);
    if (!$rpc_result) {
        throw new Exception(mysqli_error($link));
    }

    $rpc_row   = mysqli_fetch_assoc($rpc_result);
    $rpc_value = $rpc_row ? $rpc_row['PTC'] : null;
    mysqli_free_result($rpc_result);

    // Optimize MySQL session settings for large result sets
    mysqli_query($link, "SET SESSION sql_big_selects=1");
    mysqli_query($link, "SET SESSION tmp_table_size=1073741824");
    mysqli_query($link, "SET SESSION max_heap_table_size=1073741824");
    
    $mainQuery = "
        SELECT
            CM.customer_name,
            CM.phone_no,
            CM.cust_type,
            CM.dns_customer_code,
            RM.route_name AS mfa_route_name,
            BM.branch_name AS mfa_branch_name,
            EM.emp_name,
            MF.competitor_name,
            MF.customer_code,
            MF.PTD,
            MF.PTR,
            MF.PTC,
            MF.PV,
            MF.billing_ex_for,
            MF.wsp_ex_for,
            SUBSTRING(MF.market_feedback_id, 3, 5) AS emp_code,
            SUBSTRING(MF.market_feedback_id, -14, 8) AS feedback_date_raw
        FROM market_feedback MF
        INNER JOIN customer_master CM ON MF.customer_code = CM.customer_code
        INNER JOIN employee_master EM ON SUBSTRING(MF.market_feedback_id, 3, 5) = EM.emp_code
        INNER JOIN route_master RM ON CM.route_code = RM.route_code
        INNER JOIN branch_master BM ON CM.branch_code = BM.branch_code
        WHERE (
            MF.competitor_name LIKE 'STAR%' 
            OR MF.competitor_name LIKE 'ULTRATECH%'
            OR MF.competitor_name LIKE 'DALMIA%'
            OR MF.competitor_name LIKE 'AMBUJA%'
        )
          AND SUBSTRING(MF.market_feedback_id, -14, 8) BETWEEN '{$start_date_numeric}' AND '{$end_date_numeric}'
        ORDER BY SUBSTRING(MF.market_feedback_id, -14, 8) DESC
    ";

    // Use unbuffered query to reduce memory usage
    if (!mysqli_real_query($link, $mainQuery)) {
        throw new Exception('Query failed: ' . mysqli_error($link));
    }
    
    $result = mysqli_use_result($link);
    if (!$result) {
        throw new Exception('Result fetch failed: ' . mysqli_error($link));
    }

    // Set headers before starting output
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    header('Cache-Control: no-cache, must-revalidate');

    // Stream JSON output instead of building array in memory
    echo '{"data":[';
    
    $first = true;
    $row_count = 0;

    while ($row = mysqli_fetch_assoc($result)) {
        $row_count++;
        
        if (!$first) {
            echo ',';
        }
        $first = false;

        $date_raw = $row['feedback_date_raw'];
        $formatted_date = substr($date_raw, 0, 4) . '-' .
                          substr($date_raw, 4, 2) . '-' .
                          substr($date_raw, 6, 2);

        $record = [
            'route_name'        => $row['mfa_route_name'],
            'branch_name'       => $row['mfa_branch_name'],
            'customer_name'     => $row['customer_name'],
            'phone_no'          => $row['phone_no'],
            'cust_type'         => $row['cust_type'],
            'dns_customer_code' => $row['dns_customer_code'],
            'product'           => $row['competitor_name'],
            'competitor_name'   => $row['competitor_name'],
            'emp_code'          => $row['emp_code'],
            'feedback_date'     => $formatted_date,
            'customer_code'     => $row['customer_code'],
            'emp_name'          => $row['emp_name'],
            'PTD'               => $row['PTD'],
            'PTR'               => $row['PTR'],
            'PTC'               => $row['PTC'],
            'PV'                => $row['PV'],
            'billing_ex_for'    => $row['billing_ex_for'],
            'wsp_ex_for'        => $row['wsp_ex_for'],
            'rpc'               => $rpc_value
        ];

        echo json_encode($record, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        
        // Flush output buffer every 100 rows to prevent memory buildup
        if ($row_count % 100 == 0) {
            flush();
        }
    }

    echo ']}';

    mysqli_free_result($result);
    mysqli_close($link);

} catch (Exception $e) {

    header('Content-Type: application/json; charset=utf-8');
    header('HTTP/1.1 500 Internal Server Error');

    echo json_encode([
        'status'  => 'error',
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>