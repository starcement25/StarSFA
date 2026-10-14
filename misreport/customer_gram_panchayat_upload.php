<?php

header("Content-Type: application/json");

require_once("../sfa_connection.php");

$localDB = new sfa_connection();
$conn = $localDB->conn;

ini_set('memory_limit', '1G');
set_time_limit(0);

date_default_timezone_set('Asia/Kolkata');

/* ================= CONFIG ================= */

$updated  = 0;
$inserted = 0;
$skipped  = 0;
$logBatch = [];

/* ================= LOG TABLE ================= */

$logTableSql = "
CREATE TABLE IF NOT EXISTS customer_gram_panchayat_upload_log (
    id INT NOT NULL AUTO_INCREMENT,
    excel_customer_code VARCHAR(50) NOT NULL DEFAULT '',
    customer_code VARCHAR(50) NOT NULL DEFAULT '',
    old_state VARCHAR(255) NOT NULL DEFAULT '',
    old_district VARCHAR(255) NOT NULL DEFAULT '',
    old_block_name VARCHAR(255) NOT NULL DEFAULT '',
    old_gram_panchayat VARCHAR(255) NOT NULL DEFAULT '',
    new_state VARCHAR(255) NOT NULL DEFAULT '',
    new_district VARCHAR(255) NOT NULL DEFAULT '',
    new_block_name VARCHAR(255) NOT NULL DEFAULT '',
    new_gram_panchayat VARCHAR(255) NOT NULL DEFAULT '',
    status VARCHAR(30) NOT NULL DEFAULT '',
    remarks VARCHAR(255) NOT NULL DEFAULT '',
    created_at DATETIME DEFAULT NULL,
    PRIMARY KEY (id),
    KEY idx_customer_code (customer_code),
    KEY idx_created_at (created_at)
)";

mysqli_query($conn, $logTableSql);

/* ================= FILE CHECK ================= */

if(!isset($_FILES['csv_file'])){
    echo json_encode(["status"=>false,"message"=>"No file uploaded"]);
    exit;
}

$handle = fopen($_FILES['csv_file']['tmp_name'], "r");

if(!$handle){
    echo json_encode(["status"=>false,"message"=>"File open failed"]);
    exit;
}

/* ================= READ HEADER ================= */

$header = fgetcsv($handle);

if($header === FALSE){
    fclose($handle);
    echo json_encode(["status"=>false,"message"=>"Empty CSV file"]);
    exit;
}

/* Normalise header : strip BOM, collapse line breaks / spaces, uppercase */
if(isset($header[0])){
    $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);
}
$header = array_map(function($h){
    return strtoupper(trim(preg_replace('/\s+/', ' ', $h)));
}, $header);

$code_index     = array_search("CUSTOMER_CODE", $header);
$state_index    = array_search("STATE", $header);
$district_index = array_search("DISTRICT", $header);
$block_index    = array_search("BLOCK", $header);

/* Gram Panchayat header may be "Updated Gram-Panchayat As on dd-mm-yyyy" */
$gp_index = false;
foreach($header as $i => $h){
    if(strpos($h, 'PANCHAYAT') !== false){
        $gp_index = $i;
        break;
    }
}

if($code_index === false || $state_index === false || $district_index === false
   || $block_index === false || $gp_index === false){
    fclose($handle);
    echo json_encode(["status"=>false,"message"=>"Invalid CSV format. Required columns : customer_code, State, District, Block, Gram Panchayat"]);
    exit;
}

/* ================= CUSTOMER MAP ================= */

/* Excel customer_code = customer_master.dns_customer_code (may hold comma separated codes) */
$customer_map = [];

$res = mysqli_query($conn, "SELECT customer_code, dns_customer_code, IFNULL(cust_type,'') AS cust_type FROM customer_master WHERE dns_customer_code != ''");

while($row = mysqli_fetch_assoc($res)){

    foreach(explode(',', $row['dns_customer_code']) as $dns){

        $dns = strtoupper(trim($dns));

        if($dns != '' && !isset($customer_map[$dns])){
            $customer_map[$dns] = [
                'customer_code' => $row['customer_code'],
                'cust_type'     => trim($row['cust_type'])
            ];
        }
    }
}

/* ================= EXISTING SFA AC TYPE ROWS ================= */

$sfa_map = [];

$res = mysqli_query($conn, "
SELECT customer_code,
       IFNULL(customer_state,'')          AS customer_state,
       IFNULL(customer_district,'')       AS customer_district,
       IFNULL(customer_block_name,'')     AS customer_block_name,
       IFNULL(customer_gram_panchayat,'') AS customer_gram_panchayat
FROM customer_sfa_ac_type");

while($row = mysqli_fetch_assoc($res)){
    $sfa_map[$row['customer_code']] = $row;
}

/* ================= MAIN LOOP ================= */

$created_at = date('Y-m-d H:i:s');

/* Blank Excel cell keeps the existing value */
$updateStmt = mysqli_prepare($conn, "
UPDATE customer_sfa_ac_type
SET customer_state          = IF(? = '', customer_state, ?),
    customer_district       = IF(? = '', customer_district, ?),
    customer_block_name     = IF(? = '', customer_block_name, ?),
    customer_gram_panchayat = IF(? = '', customer_gram_panchayat, ?),
    updated_at              = ?
WHERE customer_code = ?");

/* sfa_ac_type = customer_master.cust_type */
$insertStmt = mysqli_prepare($conn, "
INSERT INTO customer_sfa_ac_type
(customer_code,
 sfa_ac_type,
 customer_state,
 customer_district,
 customer_block_name,
 customer_gram_panchayat,
 approval_status,
 created_at,
 updated_at)
VALUES (?, ?, NULLIF(?, ''), NULLIF(?, ''), NULLIF(?, ''), NULLIF(?, ''), 0, ?, ?)");

mysqli_begin_transaction($conn);

while(($data = fgetcsv($handle)) !== FALSE){

    $excel_code = isset($data[$code_index])     ? trim($data[$code_index])     : '';
    $state      = isset($data[$state_index])    ? trim($data[$state_index])    : '';
    $district   = isset($data[$district_index]) ? trim($data[$district_index]) : '';
    $block      = isset($data[$block_index])    ? trim($data[$block_index])    : '';
    $gp         = isset($data[$gp_index])       ? trim($data[$gp_index])       : '';

    /* Skip fully blank lines */
    if($excel_code == '' && $state == '' && $district == '' && $block == '' && $gp == '') continue;

    if($excel_code == ''){
        $skipped++;
        $logBatch[] = buildLogRow($conn, '', '', null, $state, $district, $block, $gp,
                                  'skipped', 'customer_code blank', $created_at);
        continue;
    }

    if($state == '' && $district == '' && $block == '' && $gp == ''){
        $skipped++;
        $logBatch[] = buildLogRow($conn, $excel_code, '', null, $state, $district, $block, $gp,
                                  'skipped', 'State / District / Block / Gram Panchayat all blank', $created_at);
        continue;
    }

    /* ================= CUSTOMER NOT FOUND ================= */

    $key = strtoupper($excel_code);

    if(!isset($customer_map[$key])){
        $skipped++;
        $logBatch[] = buildLogRow($conn, $excel_code, '', null, $state, $district, $block, $gp,
                                  'missing', 'dns_customer_code not found in customer_master', $created_at);
        continue;
    }

    $customer_code = $customer_map[$key]['customer_code'];
    $cust_type     = $customer_map[$key]['cust_type'];

    /* ================= INSERT (not present in customer_sfa_ac_type) ================= */

    if(!isset($sfa_map[$customer_code])){

        mysqli_stmt_bind_param($insertStmt, "ssssssss",
            $customer_code,
            $cust_type,
            $state,
            $district,
            $block,
            $gp,
            $created_at,
            $created_at);

        mysqli_stmt_execute($insertStmt);

        $logBatch[] = buildLogRow($conn, $excel_code, $customer_code, null,
                                  $state, $district, $block, $gp, 'inserted', '', $created_at);

        /* Same customer later in the file goes to UPDATE */
        $sfa_map[$customer_code] = [
            'customer_state'          => $state,
            'customer_district'       => $district,
            'customer_block_name'     => $block,
            'customer_gram_panchayat' => $gp
        ];

        $inserted++;

        if(count($logBatch) >= 500){
            insertLogBatch($conn, $logBatch);
            $logBatch = [];
        }
        continue;
    }

    /* ================= UPDATE ================= */

    mysqli_stmt_bind_param($updateStmt, "ssssssssss",
        $state, $state,
        $district, $district,
        $block, $block,
        $gp, $gp,
        $created_at,
        $customer_code);

    mysqli_stmt_execute($updateStmt);

    $logBatch[] = buildLogRow($conn, $excel_code, $customer_code, $sfa_map[$customer_code],
                              $state, $district, $block, $gp, 'updated', '', $created_at);

    $updated++;

    if(count($logBatch) >= 500){
        insertLogBatch($conn, $logBatch);
        $logBatch = [];
    }
}

if(!empty($logBatch)){
    insertLogBatch($conn, $logBatch);
}

mysqli_commit($conn);

mysqli_stmt_close($updateStmt);
mysqli_stmt_close($insertStmt);
fclose($handle);

/* ================= RESPONSE ================= */

echo json_encode([
    "status"   => true,
    "updated"  => $updated,
    "inserted" => $inserted,
    "skipped"  => $skipped,
    "message"  => "Upload Completed. Updated : ".$updated." , Inserted : ".$inserted." , Skipped : ".$skipped
]);

exit;


/* ================= FUNCTIONS ================= */

function buildLogRow($conn, $excel_code, $customer_code, $old,
                     $state, $district, $block, $gp,
                     $status, $remarks, $created_at)
{
    $values = [
        $excel_code,
        $customer_code,
        $old ? $old['customer_state']          : '',
        $old ? $old['customer_district']       : '',
        $old ? $old['customer_block_name']     : '',
        $old ? $old['customer_gram_panchayat'] : '',
        $state,
        $district,
        $block,
        $gp,
        $status,
        $remarks,
        $created_at
    ];

    $values = array_map(function($v) use ($conn){
        return "'".mysqli_real_escape_string($conn, $v)."'";
    }, $values);

    return "(".implode(",", $values).")";
}


function insertLogBatch($conn, $batch)
{
    $sql = "
    INSERT INTO customer_gram_panchayat_upload_log
    (excel_customer_code,
     customer_code,
     old_state,
     old_district,
     old_block_name,
     old_gram_panchayat,
     new_state,
     new_district,
     new_block_name,
     new_gram_panchayat,
     status,
     remarks,
     created_at)
    VALUES " . implode(",", $batch);

    mysqli_query($conn, $sql);
}
