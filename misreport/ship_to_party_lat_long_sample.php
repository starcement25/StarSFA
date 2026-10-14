<?php

require_once("../sfa_connection.php");

$localDB = new sfa_connection();
$link = $localDB->conn;

ini_set('memory_limit', '1G');
set_time_limit(0);

/* ================= FILE NAME ================= */

$file_name = "ship_to_party_lat_long_sample.csv";

header('Content-Type: text/csv');
header("Content-Disposition: attachment;filename=$file_name");

/* ================= GET SHIP TO PARTY CUSTOMERS ================= */

$sql = "SELECT dns_customer_code,
               customer_name,
               base_latt,
               base_longi
        FROM customer_master
        WHERE UPPER(TRIM(cust_type)) = 'SHIP TO PARTY'
        AND acedns = 'Y'
        AND dns_customer_code != ''
        ORDER BY dns_customer_code ASC";

$res = mysqli_query($link, $sql);

/* ================= OUTPUT ================= */

$output = fopen("php://output", "w");

/* Header */
fputcsv($output, array(
    "Customer SFA Code",
    "Customer Name",
    "Latitude",
    "Longitude"
));

/* Rows */
if($res){

    while($row = mysqli_fetch_assoc($res)){

        fputcsv($output, array(
            $row['dns_customer_code'],
            $row['customer_name'],
            $row['base_latt'],
            $row['base_longi']
        ));
    }
}

fclose($output);
exit;
?>
