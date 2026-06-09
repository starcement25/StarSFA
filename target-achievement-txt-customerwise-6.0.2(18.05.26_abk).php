<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once("sfa_connection.php");

$localDB = new sfa_connection();
$conn = $localDB->conn;

header("Content-type: application/text");

/* ===== INPUT ===== */
if (!isset($_GET['emp_code']) || empty($_GET['emp_code'])) {
    echo "emp_code is required";
    exit;
}

//getting emp dns code
 $emp_sql = "SELECT dns_emp_code 
            FROM employee_master 
            WHERE emp_code = '".trim($_GET['emp_code'])."'";

  $res_emp = mysqli_query($conn, $emp_sql);
  while ($r = mysqli_fetch_assoc($res_emp)) {
        $emp_code = $r['dns_emp_code'];
    }
//echo $emp_code;die;
$emp_code = urlencode(trim($emp_code));

/* ===== CALL API ===== */
$url = "http://starsaathi.com/SAP/sfa_product-wise-target-achievement-txt_v2-6.0.2-1.php?emp_code=" . $emp_code;

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_FOLLOWLOCATION => true
]);

$response = curl_exec($ch);
//echo"<pre>";print_r($response);die;

if (curl_errno($ch)) {
    echo "Curl Error: " . curl_error($ch);
    curl_close($ch);
    exit;
}
curl_close($ch);

/* ===== DECODE JSON ===== */
$data = json_decode($response, true);

if (!$data || !isset($data['data'])) {
    echo "Invalid API response";
    exit;
}

/* =========================================================
   OPTIONAL: FETCH ALL CUSTOMER CODES IN ONE QUERY (FAST)
========================================================= */
$dns_list = [];

foreach ($data['data'] as $row) {
    if (!empty($row['dns_customer_code'])) {
        $dns_list[] = "'" . mysqli_real_escape_string($conn, $row['dns_customer_code']) . "'";
    }
}

$customer_map = [];

if (!empty($dns_list)) {
    $dns_in = implode(",", $dns_list);

    $sql = "SELECT SAP_customer_code, customer_code 
            FROM customer_master 
            WHERE SAP_customer_code IN ($dns_in)";

    $res = mysqli_query($conn, $sql);

    while ($r = mysqli_fetch_assoc($res)) {
        $customer_map[$r['SAP_customer_code']] = $r['customer_code'];
    }
}
$current_date = date('Y-m-d');
$current_time = date('H:i:s');

echo count($data['data'])."¥10\n";
echo $current_date . "€" . $current_time . "\n";
/* =========================================================
   OUTPUT TEXT FORMAT
========================================================= */
foreach ($data['data'] as $row) {

    $dns_customer_code = trim($row['dns_customer_code']);
    $customer_name     = trim($row['customer_name']);
    $month             = trim($row['month']);
    $cur_target        = trim($row['current_target']);
    $cur_ach           = trim($row['current_ach']);
    $pre_target        = trim($row['previous_target']);
    $pre_ach           = trim($row['previous_ach']);

    // Get mapped customer_code (if needed)
    $customer_code = isset($customer_map[$dns_customer_code]) 
                     ? $customer_map[$dns_customer_code] 
                     : '';

    /* ===== FINAL OUTPUT ===== */
    echo $customer_code . "^" .
         $customer_name . "^" .
         "^" .
         "^" .
         "^" .
         $month . "^" .
         $cur_target . "^" .
         $cur_ach . "^" .
         $pre_target . "^" .
         $pre_ach . "\n";
}
?>