<?php

header("Content-Type: application/json");

require_once("../sfa_connection.php");

$localDB = new sfa_connection();
$conn = $localDB->conn;

ini_set('memory_limit', '1G');
set_time_limit(0);

date_default_timezone_set('Asia/Kolkata');

/* ================= CONFIG ================= */

$chunkSize = 500;

$updateBatch = [];
$logBatch    = [];

$updated = 0;
$skipped = 0;

/* ================= LOG TABLE ================= */

$logTableSql = "
CREATE TABLE IF NOT EXISTS ship_to_party_lat_long_log (
    id INT NOT NULL AUTO_INCREMENT,
    dns_customer_code VARCHAR(50) NOT NULL DEFAULT '',
    customer_code VARCHAR(50) NOT NULL DEFAULT '',
    old_base_latt VARCHAR(50) NOT NULL DEFAULT '',
    old_base_longi VARCHAR(50) NOT NULL DEFAULT '',
    new_base_latt VARCHAR(50) NOT NULL DEFAULT '',
    new_base_longi VARCHAR(50) NOT NULL DEFAULT '',
    status VARCHAR(30) NOT NULL DEFAULT '',
    remarks VARCHAR(255) NOT NULL DEFAULT '',
    update_type VARCHAR(30) NOT NULL DEFAULT 'excel',
    created_at DATETIME DEFAULT NULL,
    PRIMARY KEY (id),
    KEY idx_dns_customer_code (dns_customer_code),
    KEY idx_created_at (created_at)
)";

mysqli_query($conn, $logTableSql);

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

if($header === FALSE){
    fclose($handle);
    echo json_encode(["status"=>false,"message"=>"Empty CSV file"]);
    exit;
}

$header = array_map('trim', $header);

/* Strip UTF-8 BOM from the first column name */
if(isset($header[0])){
    $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);
}

$customer_index  = array_search("Customer SFA Code", $header);
$latitude_index  = array_search("Latitude", $header);
$longitude_index = array_search("Longitude", $header);

if($customer_index === false || $latitude_index === false || $longitude_index === false){
    fclose($handle);
    echo json_encode(["status"=>false,"message"=>"Invalid CSV format. Required columns : Customer SFA Code, Latitude, Longitude"]);
    exit;
}

/* ================= SHIP TO PARTY CUSTOMER MAP ================= */

$customer_sql = "
SELECT dns_customer_code,
       customer_code,
       base_latt,
       base_longi
FROM customer_master
WHERE UPPER(TRIM(cust_type)) = 'SHIP TO PARTY'
AND dns_customer_code != ''";

$customer_res = mysqli_query($conn, $customer_sql);

$customer_map = [];

while($row = mysqli_fetch_assoc($customer_res)){

    $customer_map[$row['dns_customer_code']] = [
        'customer_code' => $row['customer_code'],
        'base_latt'     => $row['base_latt'],
        'base_longi'    => $row['base_longi']
    ];
}

/* ================= MAIN LOOP ================= */

$created_at = date('Y-m-d H:i:s');

while(($data = fgetcsv($handle)) !== FALSE){

    $dns_customer_code = isset($data[$customer_index]) ? trim($data[$customer_index]) : '';
    $base_latt         = isset($data[$latitude_index]) ? trim($data[$latitude_index]) : '';
    $base_longi        = isset($data[$longitude_index]) ? trim($data[$longitude_index]) : '';

    /* Skip fully blank lines */
    if($dns_customer_code == '' && $base_latt == '' && $base_longi == '') continue;

    if($dns_customer_code == ''){
        $skipped++;
        $logBatch[] = buildLogRow($conn, '', '', '', '', $base_latt, $base_longi,
                                  'skipped', 'Customer SFA Code blank', $created_at);
        continue;
    }

    /* ================= CUSTOMER NOT A SHIP TO PARTY ================= */

    if(!isset($customer_map[$dns_customer_code])){
        $skipped++;
        $logBatch[] = buildLogRow($conn, $dns_customer_code, '', '', '', $base_latt, $base_longi,
                                  'missing', 'Customer code not found under SHIP TO PARTY', $created_at);
        continue;
    }

    $old_latt  = $customer_map[$dns_customer_code]['base_latt'];
    $old_longi = $customer_map[$dns_customer_code]['base_longi'];
    $customer_code = $customer_map[$dns_customer_code]['customer_code'];

    /* ================= LAT LONG VALIDATION ================= */

    if($base_latt == '' || $base_longi == ''){
        $skipped++;
        $logBatch[] = buildLogRow($conn, $dns_customer_code, $customer_code,
                                  $old_latt, $old_longi, $base_latt, $base_longi,
                                  'skipped', 'Latitude / Longitude blank', $created_at);
        continue;
    }

    if(!is_numeric($base_latt) || !is_numeric($base_longi)){
        $skipped++;
        $logBatch[] = buildLogRow($conn, $dns_customer_code, $customer_code,
                                  $old_latt, $old_longi, $base_latt, $base_longi,
                                  'invalid', 'Latitude / Longitude not numeric', $created_at);
        continue;
    }

    if($base_latt < -90 || $base_latt > 90 || $base_longi < -180 || $base_longi > 180){
        $skipped++;
        $logBatch[] = buildLogRow($conn, $dns_customer_code, $customer_code,
                                  $old_latt, $old_longi, $base_latt, $base_longi,
                                  'invalid', 'Latitude / Longitude out of range', $created_at);
        continue;
    }

    /* ================= UPDATE BATCH ================= */

    $updateBatch[$dns_customer_code] = [
        'base_latt'  => $base_latt,
        'base_longi' => $base_longi
    ];

    $logBatch[] = buildLogRow($conn, $dns_customer_code, $customer_code,
                              $old_latt, $old_longi, $base_latt, $base_longi,
                              'updated', '', $created_at);

    $updated++;

    /* ================= CHUNK UPDATE ================= */

    if(count($updateBatch) >= $chunkSize){

        updateCustomerBatch($conn, $updateBatch);
        insertLogBatch($conn, $logBatch);

        $updateBatch = [];
        $logBatch    = [];
    }
}

/* ================= FINAL UPDATE ================= */

if(!empty($updateBatch)){
    updateCustomerBatch($conn, $updateBatch);
}

if(!empty($logBatch)){
    insertLogBatch($conn, $logBatch);
}

fclose($handle);

/* ================= RESPONSE ================= */

echo json_encode([
    "status"  => true,
    "updated" => $updated,
    "skipped" => $skipped,
    "message" => "Upload Completed. Updated : ".$updated." , Skipped : ".$skipped
]);

exit;


/* ================= FUNCTIONS ================= */

function buildLogRow($conn, $dns_customer_code, $customer_code,
                     $old_latt, $old_longi, $new_latt, $new_longi,
                     $status, $remarks, $created_at)
{
    return "(
        '".mysqli_real_escape_string($conn, $dns_customer_code)."',
        '".mysqli_real_escape_string($conn, $customer_code)."',
        '".mysqli_real_escape_string($conn, $old_latt)."',
        '".mysqli_real_escape_string($conn, $old_longi)."',
        '".mysqli_real_escape_string($conn, $new_latt)."',
        '".mysqli_real_escape_string($conn, $new_longi)."',
        '".mysqli_real_escape_string($conn, $status)."',
        '".mysqli_real_escape_string($conn, $remarks)."',
        'excel',
        '".mysqli_real_escape_string($conn, $created_at)."'
    )";
}


function insertLogBatch($conn, $batch)
{
    $sql = "
    INSERT INTO ship_to_party_lat_long_log
    (dns_customer_code,
     customer_code,
     old_base_latt,
     old_base_longi,
     new_base_latt,
     new_base_longi,
     status,
     remarks,
     update_type,
     created_at)
    VALUES " . implode(",", $batch);

    mysqli_query($conn, $sql);
}


function updateCustomerBatch($conn, $batch)
{
    $latt_case  = "";
    $longi_case = "";
    $codes      = [];

    foreach($batch as $dns_customer_code => $value){

        $code  = mysqli_real_escape_string($conn, $dns_customer_code);
        $latt  = mysqli_real_escape_string($conn, $value['base_latt']);
        $longi = mysqli_real_escape_string($conn, $value['base_longi']);

        $latt_case  .= " WHEN '$code' THEN '$latt' ";
        $longi_case .= " WHEN '$code' THEN '$longi' ";

        $codes[] = "'$code'";
    }

    $sql = "
    UPDATE customer_master
    SET base_latt  = CASE dns_customer_code $latt_case END,
        base_longi = CASE dns_customer_code $longi_case END
    WHERE dns_customer_code IN (" . implode(",", $codes) . ")
    AND UPPER(TRIM(cust_type)) = 'SHIP TO PARTY'";

    mysqli_query($conn, $sql);
}
