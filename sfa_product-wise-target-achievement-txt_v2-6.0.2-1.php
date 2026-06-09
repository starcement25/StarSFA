<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");

/* ===== HEADER ===== */
header("Content-Type: application/json; charset=utf-8");

/* ===== FORCE UTF-8 DB CONNECTION ===== */
mysql_query("SET NAMES utf8");
mysql_query("SET CHARACTER SET utf8");
mysql_query("SET collation_connection = utf8_general_ci");

/* ===== INPUT ===== */
$emp_code = isset($_REQUEST['emp_code']) ? strtolower(trim($_REQUEST['emp_code'])) : "";

if ($emp_code == '') {
    echo json_encode([
        "status" => false,
        "message" => "Invalid Employee Code"
    ]);
    exit;
}

/* ===== DATE TIME ===== */
$current_date = date('Y-m-d');
$current_time = date('H:i:s');

/* ===== FINANCIAL YEAR ===== */
$current_year  = date('Y');
$current_month = date('n');
$fy = ($current_month >= 4) ? $current_year : $current_year - 1;

/* =========================================================
   UTF-8 CLEAN FUNCTION
========================================================= */
function clean_utf8($value) {
    if (is_array($value)) {
        return array_map('clean_utf8', $value);
    }
    if (is_string($value)) {
        return mb_convert_encoding($value, 'UTF-8', 'UTF-8');
    }
    return $value;
}

/* =========================================================
   STEP 1: EMPLOYEE HIERARCHY
========================================================= */
$employee_hierarchy = return_employee_hierarchy($emp_code);
$emp_hierarchy_condition = " emp_code IN(".$employee_hierarchy.") ";

/* =========================================================
   STEP 2: CUSTOMERS
========================================================= */
$sql_customer = "
SELECT customer_code, customer_name,customer_id as dns_customer_code 
FROM customer_master 
WHERE acedns='Y' 
AND customer_id IN (
    SELECT DISTINCT customer_code 
    FROM customer_route_emp_relation  
    WHERE $emp_hierarchy_condition AND acedns='Y'
) AND cust_type='Dealer'
ORDER BY customer_name ASC
";
//echo"<pre>";print_r($sql_customer);die;

$res_customer = mysql_query($sql_customer);

if (!$res_customer || mysql_num_rows($res_customer) == 0) {
    echo json_encode([
        "status" => false,
        "message" => "No Customers Found"
    ]);
    exit;
}

/* ===== RESPONSE ARRAY ===== */
$response = [
    "status" => true,
    "date"   => $current_date,
    "time"   => $current_time,
    "data"   => []
];

/* =========================================================
   STEP 3: LOOP CUSTOMERS
========================================================= */
while ($cust = mysql_fetch_assoc($res_customer)) {

    $cust_code = strtolower(trim($cust['customer_code']));
    $dns_customer_code = strtolower(trim($cust['dns_customer_code']));

    // ✅ FIXED CUSTOMER NAME (UTF-8 SAFE)
    $customer_name = mb_convert_encoding(trim($cust['customer_name']), 'UTF-8', 'UTF-8');

    /* ===== FETCH DATA ===== */
    $data = [];

    $sql_data = "
    SELECT month, year, target_qty, achievement_qty
    FROM month_wise_achievement_new
    WHERE customer_code = '$cust_code'
    ";

    $res_data = mysql_query($sql_data);

    if ($res_data) {
        while ($row = mysql_fetch_assoc($res_data)) {

            $m = (int)$row['month'];
            $y = (int)$row['year'];

            $data[$m][$y] = [
                'target' => (float)$row['target_qty'],
                'ach'    => (float)$row['achievement_qty']
            ];
        }
    }

    /* ===== MONTH LOOP ===== */
    for ($m = 1; $m <= 12; $m++) {

        $month = str_pad($m, 2, '0', STR_PAD_LEFT);

        // FY Logic
        if ($m >= 1 && $m <= 3) {
            $cur_year = $fy + 1;
            $pre_year = $fy;
        } else {
            $cur_year = $fy;
            $pre_year = $fy - 1;
        }

        $cur_target = isset($data[$m][$cur_year]) ? $data[$m][$cur_year]['target'] : 0;
        $cur_ach    = isset($data[$m][$cur_year]) ? $data[$m][$cur_year]['ach'] : 0;

        $pre_target = isset($data[$m][$pre_year]) ? $data[$m][$pre_year]['target'] : 0;
        $pre_ach    = isset($data[$m][$pre_year]) ? $data[$m][$pre_year]['ach'] : 0;

        $response['data'][] = [
            "dns_customer_code" => $dns_customer_code,
            "customer_name"     => $customer_name,
            "month"             => $month,
            "current_target"    => number_format($cur_target, 2, '.', ''),
            "current_ach"       => number_format($cur_ach, 2, '.', ''),
            "previous_target"   => number_format($pre_target, 2, '.', ''),
            "previous_ach"      => number_format($pre_ach, 2, '.', '')
        ];
    }
}

/* ===== FINAL CLEAN + OUTPUT ===== */
$response = clean_utf8($response);

echo json_encode(
    $response,
    JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
);
?>