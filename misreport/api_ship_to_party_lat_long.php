<?php

require_once("../sfa_connection.php");
header('Content-Type: application/json');

$localDB = new sfa_connection();
$conn = $localDB->conn;

ini_set('memory_limit', '1G');
set_time_limit(0);

date_default_timezone_set('Asia/Kolkata');

/* ================= OPTIONAL FILTER ================= */

$dns_customer_code = isset($_REQUEST['dns_customer_code'])
    ? trim($_REQUEST['dns_customer_code'])
    : '';

$filter_condition = "";

if($dns_customer_code != ''){
    $filter_condition = " AND dns_customer_code = '"
        . mysqli_real_escape_string($conn, $dns_customer_code) . "' ";
}

/* ================= GET SHIP TO PARTY CUSTOMERS ================= */

$sql = "SELECT dns_customer_code,
               customer_name,
               base_latt,
               base_longi
        FROM customer_master
        WHERE UPPER(TRIM(cust_type)) = 'SHIP TO PARTY'
        AND acedns = 'Y'
        AND dns_customer_code != ''
        $filter_condition
        ORDER BY dns_customer_code ASC";

$res = mysqli_query($conn, $sql);

if(!$res){

    echo json_encode(array(
        "process_status"  => "No",
        "process_message" => "Failed to retrieve Ship To Party data.",
        "error"           => mysqli_error($conn)
    ));

    exit;
}

/* ================= BUILD DATA ================= */

$data = array();

while($row = mysqli_fetch_assoc($res)){

    $data[] = array(
        "dns_customer_code" => $row['dns_customer_code'],
        "customer_name"     => $row['customer_name'],
        "base_latt"         => $row['base_latt'],
        "base_longi"        => $row['base_longi']
    );
}

/* ================= RESPONSE ================= */

if(!empty($data)){

    $res_data = array(
        "process_status"  => "Yes",
        "process_message" => "Ship To Party data successfully received.",
        "total_records"   => count($data),
        "data"            => $data
    );
}
else{

    $res_data = array(
        "process_status"  => "No",
        "process_message" => "No Ship To Party data found.",
        "total_records"   => 0,
        "data"            => array()
    );
}

echo json_encode($res_data);

exit;
