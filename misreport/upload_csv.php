<?php

header("Content-Type: application/json");

require_once("../sfa_connection.php");

$localDB = new sfa_connection();
$conn = $localDB->conn;

ini_set('memory_limit', '1G');
set_time_limit(0);

date_default_timezone_set('Asia/Kolkata');

/* ================= CONFIG ================= */

$chunkSize = 1000;

$logBatch = [];
$mainBatch = [];

$inserted = 0;
$updated  = 0;
$skipped  = 0;

/* ================= SFA A/C TYPE TABLE ================= */

$sfaAcTypeTableSql = "
CREATE TABLE IF NOT EXISTS customer_sfa_ac_type (
    id INT NOT NULL AUTO_INCREMENT,
    customer_code VARCHAR(50) NOT NULL,
    sfa_ac_type VARCHAR(100) NOT NULL DEFAULT '',
    created_at DATETIME DEFAULT NULL,
    updated_at DATETIME DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uniq_customer_code (customer_code)
)";

mysqli_query($conn, $sfaAcTypeTableSql);

/* ================= FILE CHECK ================= */

if(!isset($_FILES['csv_file'])){
    echo json_encode(["status"=>false,"message"=>"No file uploaded"]);
    exit;
}

$file = $_FILES['csv_file']['tmp_name'];
$handle = fopen($file, "r");

if(!$handle){
    echo json_encode(["status"=>false,"message"=>"File open failed"]);
    exit;
}

/* ================= READ HEADER ================= */

$header = fgetcsv($handle);

$customer_index = array_search("Customer SFA Code", $header);
$type_index     = array_search("Type", $header);
$sfa_ac_type_index = array_search("SFA A/C Type", $header);
$active_index   = array_search("Active", $header);

if($customer_index === false || $type_index === false || $sfa_ac_type_index === false || $active_index === false){
    echo json_encode(["status"=>false,"message"=>"Invalid CSV format"]);
    exit;
}

/* ================= CUSTOMER MAP ================= */

$customer_sql = "
SELECT customer_code, dns_customer_code 
FROM customer_master 
WHERE dns_customer_code !=''";

$customer_res = mysqli_query($conn, $customer_sql);

$customer_map = [];

while($row = mysqli_fetch_assoc($customer_res)){
    $customer_map[$row['dns_customer_code']] 
        = $row['customer_code'];
}

/* ================= COMPETITOR MAP ================= */

$comp_sql = "
SELECT id, competitor_name, competitor_type 
FROM competitor_group_master_potential 
WHERE status='yes'";

$comp_res = mysqli_query($conn, $comp_sql);

$competitor_map = [];

while($row = mysqli_fetch_assoc($comp_res)){
    $competitor_map
        [$row['competitor_name']]
        [$row['competitor_type']]
        = $row['id'];
}

/* ================= MAIN LOOP ================= */

$rowCount = 0;

while(($data = fgetcsv($handle)) !== FALSE){

    $rowCount++;

    $sap_customer_code = isset($data[$customer_index]) ? trim($data[$customer_index]) : '';
    $type   = isset($data[$type_index]) ? strtoupper(trim($data[$type_index])) : '';
    $sfa_ac_type = isset($data[$sfa_ac_type_index]) ? trim($data[$sfa_ac_type_index]) : '';
    $active = isset($data[$active_index]) ? strtolower(trim($data[$active_index])) : '';

    if(!isset($customer_map[$sap_customer_code])){
        $skipped++;
        /* ================= LOG INSERT ================= */
        $log_sql_missing = "INSERT INTO competitor_quantity_log_missing 
                    (customer_code, created_at)
                    VALUES 
                    ('$sap_customer_code','".date('Y-m-d H:i:s')."')";

        mysqli_query($conn, $log_sql_missing);
        continue;
    }

    if($type != 'NE' && $type != 'ROE'){
        $skipped++;
        continue;
    }

    $customer_code = mysqli_real_escape_string(
        $conn,
        $customer_map[$sap_customer_code]
    );

    $acedns = ($active == 'yes') ? 'yes' : 'no';
    $created_at = date('Y-m-d H:i:s');

    upsertSfaAcType($conn, $customer_code, $sfa_ac_type, $created_at);

    /* ================= LOOP COMPETITORS ================= */

    foreach($header as $index => $col_name){

        if(in_array($col_name,
            ["Customer SFA Code","Type","SFA A/C Type","Active"]
        )) continue;

        $competitor_name = trim($col_name);
        //$qty = trim($data[$index]);
        $qty = isset($data[$index])
            ? floatval(preg_replace('/[^\d.]/', '', trim($data[$index])))
            : 0;

        if($qty === "") continue;

        if(!isset(
            $competitor_map
            [$competitor_name]
            [$type]
        )) continue;

        $competitor_id =
            $competitor_map
            [$competitor_name]
            [$type];

        /* ================= LOG BATCH ================= */

        $logBatch[] = "(
            '$customer_code',
            '$competitor_id',
            '$qty',
            '$acedns',
            '$created_at'
        )";

        /* ================= MAIN UPSERT ================= */

        $mainBatch[] = "(
            '$customer_code',
            '$competitor_id',
            '$qty',
            '$acedns',
            'excel',
            '$created_at',
            '$created_at'
        )";

        $inserted++;

        /* ================= CHUNK INSERT ================= */

        if(count($logBatch) >= $chunkSize){

            insertLogBatch($conn, $logBatch);
            upsertMainBatch($conn, $mainBatch);

            $logBatch  = [];
            $mainBatch = [];

            echo "Processed $rowCount rows<br>";
        }
    }
}

/* ================= FINAL INSERT ================= */

if(!empty($logBatch)){
    insertLogBatch($conn, $logBatch);
}

if(!empty($mainBatch)){
    upsertMainBatch($conn, $mainBatch);
}

fclose($handle);

/* ================= RESPONSE ================= */

echo json_encode([
    "status"   => true,
    "inserted" => $inserted,
    "updated"  => $updated,
    "skipped"  => $skipped,
    "message"  => "Upload Completed"
]);

exit;


/* ================= FUNCTIONS ================= */

function insertLogBatch($conn, $batch)
{
    $sql = "
    INSERT INTO competitor_quantity_log
    (customer_code,
     competitor_quantity_id,
     qty,
     acedns,
     created_at)
    VALUES " . implode(",", $batch);

    mysqli_query($conn, $sql);
}


function upsertMainBatch($conn, $batch)
{
    $sql = "
    INSERT INTO competitor_quantity
    (customer_code,
     competitor_quantity_id,
     qty,
     acedns,
     update_type,
     created_at,
     updated_at)
    VALUES " . implode(",", $batch) . "

    ON DUPLICATE KEY UPDATE

        qty = VALUES(qty),
        acedns = VALUES(acedns),
        update_type='excel',
        updated_at = VALUES(updated_at)
    ";

    mysqli_query($conn, $sql);
}


function upsertSfaAcType($conn, $customer_code, $sfa_ac_type, $created_at)
{
    $sfa_ac_type = mysqli_real_escape_string($conn, $sfa_ac_type);

    $sql = "
    INSERT INTO customer_sfa_ac_type
    (customer_code,
     sfa_ac_type,
     created_at,
     updated_at)
    VALUES
    ('$customer_code',
     '$sfa_ac_type',
     '$created_at',
     '$created_at')

    ON DUPLICATE KEY UPDATE

        sfa_ac_type = VALUES(sfa_ac_type),
        updated_at = VALUES(updated_at)
    ";

    mysqli_query($conn, $sql);
}
