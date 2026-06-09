<?php
ob_start();


ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);


ini_set('log_errors', 1);
ini_set('error_log', '/tmp/php_errors.log');

ini_set('memory_limit', '1024M');
set_time_limit(0);
date_default_timezone_set("Asia/Kolkata");

define("SERVER", "localhost");
define("USER", "root");
define("PASSWORD", "Passw0rd123#$");
define("DB", "acedns_STAR");


ob_clean();

try {
    $link = mysqli_connect(SERVER, USER, PASSWORD, DB);

    if (!$link) {
        throw new Exception('Database connection failed: ' . mysqli_connect_error());
    }

  
    mysqli_set_charset($link, "utf8mb4");

    $start_date = '2025-07-01';
    $end_date = '2025-12-31';

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $start_date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $end_date)) {
        throw new Exception('Invalid date format. Use YYYY-MM-DD');
    }

    $start_date = mysqli_real_escape_string($link, $start_date);
    $end_date = mysqli_real_escape_string($link, $end_date);

    $STATUS_ACTIVE = 1;

    $mainQuery = "
    WITH competitor_list AS (
        SELECT DISTINCT competitor_name
        FROM competitor_group_master 
        WHERE acedns='yes' 
        AND branch_code IS NOT NULL 
        AND branch_code != ''
    ),
    ne_pricing_data AS (
        SELECT 
            'ne' AS report_type,
            NE_P_C.pricing_group_number,
            NE_P_C.product_display_name AS grade,
            NE_P_C.company_display_name AS brand,
            NE_P_C.is_compared_with,
            NE_P_C.incoterm_type,
            GROUP_CONCAT(DISTINCT NE_P_C.zone_name SEPARATOR ' & ') AS zone_name,
            GROUP_CONCAT(DISTINCT RM.route_name SEPARATOR ' & ') AS route_name,
            GROUP_CONCAT(DISTINCT NE_P_C.display_branch_name SEPARATOR ' & ') AS branch_name,
            GROUP_CONCAT(DISTINCT NE_P_C.product_display_name SEPARATOR ' & ') AS product_display_name,
            GROUP_CONCAT(DISTINCT NE_P_C.route_code) AS route_codes,
            GROUP_CONCAT(DISTINCT COMP_M.competitor_name) AS competitor_names
        FROM ne_pricing_competitor AS NE_P_C
        INNER JOIN route_master AS RM ON NE_P_C.route_code = RM.route_code
        INNER JOIN branch_master AS BM ON RM.branch_code = BM.branch_code
        INNER JOIN competitor_group_master AS COMP_M ON NE_P_C.competitor_id = COMP_M.sl_no
        WHERE NE_P_C.status = {$STATUS_ACTIVE}
        GROUP BY 
            NE_P_C.pricing_group_number,
            NE_P_C.product_display_name,
            NE_P_C.company_display_name,
            NE_P_C.is_compared_with,
            NE_P_C.incoterm_type
    ),
    roe_pricing_data AS (
        SELECT 
            'roe' AS report_type,
            ROE_P_C.pricing_group_number,
            ROE_P_C.product_display_name AS grade,
            ROE_P_C.company_display_name AS brand,
            ROE_P_C.is_compared_with,
            ROE_P_C.incoterm_type,
            GROUP_CONCAT(DISTINCT ROE_P_C.zone_name SEPARATOR ' & ') AS zone_name,
            GROUP_CONCAT(DISTINCT BM.branch_name SEPARATOR ' & ') AS route_name,
            GROUP_CONCAT(DISTINCT BM.branch_name SEPARATOR ' & ') AS branch_name,
            GROUP_CONCAT(DISTINCT ROE_P_C.product_display_name SEPARATOR ' & ') AS product_display_name,
            GROUP_CONCAT(DISTINCT ROE_P_C.branch_code) AS route_codes,
            GROUP_CONCAT(DISTINCT COMP_M.competitor_name) AS competitor_names
        FROM roe_pricing_competitor AS ROE_P_C
        INNER JOIN branch_master AS BM ON ROE_P_C.branch_code = BM.branch_code
        INNER JOIN competitor_group_master AS COMP_M ON ROE_P_C.competitor_id = COMP_M.sl_no
        WHERE ROE_P_C.status = {$STATUS_ACTIVE}
        GROUP BY 
            ROE_P_C.pricing_group_number,
            ROE_P_C.product_display_name,
            ROE_P_C.company_display_name,
            ROE_P_C.is_compared_with,
            ROE_P_C.incoterm_type
    ),
    all_pricing_data AS (
        SELECT * FROM ne_pricing_data
        UNION ALL
        SELECT * FROM roe_pricing_data
    ),
    market_feedback_agg AS (
        SELECT 
            RM.route_code,
            BM.branch_code,
            RM.route_name,
            BM.branch_name,
            CM.customer_name,
            CM.cust_type,
            CM.dns_customer_code,
            MF.competitor_name,
            SUBSTRING(MF.market_feedback_id, 3, 5) AS emp_code,
            DATE_FORMAT(SUBSTRING(MF.market_feedback_id, -14, 8), '%Y-%m-%d') AS feedback_date,
            MF.customer_code,
            EM.emp_name,
            SUM(MF.PTD) AS PTD,
            SUM(MF.PTR) AS PTR,
            SUM(MF.PTC) AS PTC,
            SUM(MF.PV) AS PV,
            SUM(MF.billing_ex_for) AS billing_ex_for,
            SUM(MF.wsp_ex_for) AS wsp_ex_for
        FROM market_feedback MF
        JOIN customer_master CM ON MF.customer_code = CM.customer_code
        JOIN employee_master EM ON SUBSTRING(MF.market_feedback_id, 3, 5) = EM.emp_code
        JOIN route_master RM ON CM.route_code = RM.route_code
        JOIN branch_master BM ON CM.branch_code = BM.branch_code
        CROSS JOIN competitor_list CL
        WHERE 
            DATE_FORMAT(SUBSTRING(MF.market_feedback_id, -14, 8), '%Y-%m-%d') 
                BETWEEN '{$start_date}' AND '{$end_date}'
            AND MF.competitor_name = CL.competitor_name
        GROUP BY 
            RM.route_code,
            BM.branch_code,
            RM.route_name,
            BM.branch_name,
            CM.customer_name,
            CM.cust_type,
            CM.dns_customer_code,
            MF.competitor_name,
            emp_code,
            feedback_date,
            MF.customer_code,
            EM.emp_name
    ),
    ptc_freq AS (
        SELECT 
            PTC, 
            COUNT(*) AS cnt,
            RANK() OVER (ORDER BY COUNT(*) DESC, PTC DESC) AS rnk
        FROM market_feedback_agg
        WHERE PTC > 0
        GROUP BY PTC
    )
    SELECT 
        apd.report_type,
        apd.pricing_group_number AS pricing_group,
        apd.zone_name AS zone,
        apd.branch_name AS branch,
        apd.route_name AS route,
        apd.product_display_name AS product,
        CASE WHEN apd.incoterm_type = 0 THEN 'FOR' ELSE 'EX' END AS incoterm_type,
        apd.is_compared_with,
        apd.grade,
        apd.brand,
        mfa.route_name AS mfa_route_name,
        mfa.branch_name AS mfa_branch_name,
        mfa.customer_name,
        mfa.cust_type,
        mfa.dns_customer_code,
        mfa.competitor_name,
        mfa.emp_code,
        mfa.feedback_date,
        mfa.customer_code,
        mfa.emp_name,
        mfa.PTD,
        mfa.PTR,
        mfa.PTC,
        mfa.PV,
        mfa.billing_ex_for,
        mfa.wsp_ex_for,
        (SELECT PTC FROM ptc_freq WHERE rnk = 1 LIMIT 1) AS rpc
    FROM all_pricing_data apd
    JOIN market_feedback_agg mfa ON (
        (apd.report_type = 'ne' AND FIND_IN_SET(mfa.route_code, apd.route_codes) > 0)
        OR 
        (apd.report_type = 'roe' AND FIND_IN_SET(mfa.branch_code, apd.route_codes) > 0)
    )
    WHERE FIND_IN_SET(mfa.competitor_name, apd.competitor_names) > 0
    ORDER BY apd.report_type, apd.pricing_group_number
    ";
// echo  $mainQuery;die;
    $result = mysqli_query($link, $mainQuery);

    if (!$result) {
        throw new Exception('Query failed: ' . mysqli_error($link));
    }

    $results = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $results[] = $row;
    }

    mysqli_close($link);


    ob_clean();
    
    
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    header('Cache-Control: no-cache, must-revalidate');
    

    echo json_encode(['data' => $results], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    
    
    ob_end_flush();

} catch (Exception $e) {
    
    ob_clean();
    

    header('Content-Type: application/json; charset=utf-8');
    header('HTTP/1.1 500 Internal Server Error');
    
   
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
    
    ob_end_flush();
    exit;
}
?>

