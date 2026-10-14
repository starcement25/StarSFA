<?php

require_once("../sfa_connection.php");

$localDB = new sfa_connection();
$link = $localDB->conn;

ini_set('memory_limit', '1G');
set_time_limit(0);

/* ================= FILE NAME ================= */

$file_name = "customer_gram_panchayat_sample.csv";

header('Content-Type: text/csv');
header("Content-Disposition: attachment;filename=$file_name");

/* ================= GET CUSTOMERS WITH EXISTING GRAM PANCHAYAT DATA ================= */

$sql = "SELECT c.dns_customer_code,
               c.customer_name,
               c.cust_type,
               csat.customer_state,
               csat.customer_district,
               csat.customer_block_name,
               csat.customer_gram_panchayat
        FROM customer_master c
        LEFT JOIN customer_sfa_ac_type csat
            ON csat.customer_code = c.customer_code
        WHERE c.acedns = 'Y'
        AND c.dns_customer_code != ''
        ORDER BY c.dns_customer_code ASC";

$res = mysqli_query($link, $sql, MYSQLI_USE_RESULT);

/* ================= OUTPUT ================= */

$output = fopen("php://output", "w");

/* Header (customer_code = dns_customer_code) */
fputcsv($output, array(
    "customer_code",
    "customer_name",
    "Customer Type",
    "State",
    "District",
    "Block",
    "Gram Panchayat"
));

/* Rows */
if($res){

    while($row = mysqli_fetch_assoc($res)){

        /* Comma separated DNS codes : first code is enough to match on upload */
        $dns_codes = explode(',', $row['dns_customer_code']);

        fputcsv($output, array(
            trim($dns_codes[0]),
            $row['customer_name'],
            $row['cust_type'],
            $row['customer_state'],
            $row['customer_district'],
            $row['customer_block_name'],
            $row['customer_gram_panchayat']
        ));
    }
}

fclose($output);
exit;
?>
