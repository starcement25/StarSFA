<?php
header("Content-Type: application/json");

require_once("../sfa_connection.php");

$localDB = new sfa_connection();
$conn = $localDB->conn;

ini_set('memory_limit', '1G');

date_default_timezone_set('Asia/Kolkata');

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

$inserted = 0;
$updated  = 0;
$skipped  = 0;

/* ================= IDENTIFY COLUMN INDEX ================= */
$customer_index = array_search("Customer SFA Code", $header);
$type_index     = array_search("Type", $header);
$active_index   = array_search("Active", $header);

if($customer_index === false || $type_index === false || $active_index === false){
    echo json_encode(["status"=>false,"message"=>"Invalid CSV format"]);
    exit;
}

/* ================= CUSTOMER MAP (SAP → INTERNAL) ================= */
$customer_sql = "SELECT customer_code, dns_customer_code FROM customer_master WHERE `dns_customer_code` !='';";
$customer_res = mysqli_query($conn, $customer_sql);

$customer_map = [];
while($row = mysqli_fetch_assoc($customer_res)){
    $customer_map[$row['dns_customer_code']] = $row['customer_code'];
}

/* ================= COMPETITOR MAP (NAME + TYPE → ID) ================= */
$comp_sql = "SELECT id, competitor_name, competitor_type 
             FROM competitor_group_master_potential 
             WHERE status='yes'";

$comp_res = mysqli_query($conn, $comp_sql);

$competitor_map = [];
while($row = mysqli_fetch_assoc($comp_res)){
    $competitor_map[$row['competitor_name']][$row['competitor_type']] = $row['id'];
}

/* ================= LOOP CSV ================= */
while(($data = fgetcsv($handle)) !== FALSE){

    $sap_customer_code = trim($data[$customer_index]);
    $type              = strtoupper(trim($data[$type_index])); // NE / ROE
    $active            = strtolower(trim($data[$active_index]));

    /* ================= VALIDATE CUSTOMER ================= */
    if(!isset($customer_map[$sap_customer_code])){
        $skipped++;
        continue;
    }

    $customer_code = mysqli_real_escape_string($conn, $customer_map[$sap_customer_code]);

    /* ================= VALIDATE TYPE ================= */
    if($type != 'NE' && $type != 'ROE'){
        $skipped++;
        continue;
    }

    $acedns = ($active == 'yes') ? 'yes' : 'no';
    $created_at = date('Y-m-d H:i:s');

    /* ================= LOOP DYNAMIC COMPETITOR COLUMNS ================= */
    foreach($header as $index => $col_name){

        // Skip fixed columns
        if(in_array($col_name, ["Customer SFA Code","Type","Active"])) continue;

        $competitor_name = trim($col_name);
        $qty = trim($data[$index]);

        // Allow 0 qty, skip only empty
        if($qty === "") continue;

        /* ================= VALIDATE COMPETITOR + TYPE ================= */
        if(!isset($competitor_map[$competitor_name][$type])){
            continue;
        }

        $competitor_id = $competitor_map[$competitor_name][$type];

        /* ================= LOG INSERT ================= */
        $log_sql = "INSERT INTO competitor_quantity_log 
                    (customer_code, competitor_quantity_id, qty, acedns, created_at)
                    VALUES 
                    ('$customer_code','$competitor_id','$qty','$acedns','$created_at')";

        mysqli_query($conn, $log_sql);

        /* ================= CHECK EXIST ================= */
        $check_sql = "SELECT competitor_quantity_id 
                      FROM competitor_quantity
                      WHERE customer_code='$customer_code'
                      AND competitor_quantity_id='$competitor_id'";

        $res = mysqli_query($conn, $check_sql);

        if(mysqli_num_rows($res) > 0){

            /* ================= UPDATE ================= */
            $update_sql = "UPDATE competitor_quantity SET
                            qty='$qty',
                            acedns='$acedns',
                            update_type='excel',
                            updated_at='$created_at'
                           WHERE customer_code='$customer_code'
                           AND competitor_quantity_id='$competitor_id'";

            mysqli_query($conn, $update_sql);
            $updated++;

        } else {

            /* ================= INSERT ================= */
            $insert_sql = "INSERT INTO competitor_quantity
                            (customer_code, competitor_quantity_id, qty, acedns, update_type, created_at, updated_at)
                           VALUES
                            ('$customer_code','$competitor_id','$qty','$acedns','excel','$created_at','$created_at')";

            mysqli_query($conn, $insert_sql);
            $inserted++;
        }
    }
}

fclose($handle);

/* ================= FINAL RESPONSE ================= */
echo json_encode([
    "status"   => true,
    "inserted" => $inserted,
    "updated"  => $updated,
    "skipped"  => $skipped,
    "message" => "Upload Completed | Log Inserted: $inserted | Updated: $updated | Skipped: $skipped"
]);

exit;
?>