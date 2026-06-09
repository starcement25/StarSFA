<?php
header("Content-Type: application/json");

require_once("../sfa_connection.php");

$localDB = new sfa_connection();
$link = $localDB->conn;

date_default_timezone_set('Asia/Kolkata');

/* ================= GET RAW XML ================= */
$xml_data = file_get_contents("php://input");

if(empty($xml_data)){
    echo json_encode(["status"=>false,"message"=>"No XML received"]);
    exit;
}

/* ================= PARSE XML ================= */
libxml_use_internal_errors(true);
$xml = simplexml_load_string($xml_data);

if(!$xml){
    echo json_encode(["status"=>false,"message"=>"Invalid XML"]);
    exit;
}

/* ================= HEADER DATA ================= */
$market_feedback_id = (string)$xml->header->market_feedback_id;
$customer_code      = (string)$xml->header->customer_code;
$emp_code           = (string)$xml->header->emp_code;

$created_at = date('Y-m-d H:i:s');

/* ================= LOOP DETAILS ================= */
foreach($xml->details->item as $item){

    $competitor_quantity_id = trim((string)$item->competitor_quantity_id);
    $qty                    = trim((string)$item->qty);

    if($competitor_quantity_id == "") continue;

    /* ================= OPTIONAL VALIDATION ================= */
    $check_comp = "SELECT id FROM competitor_group_master_potential 
                   WHERE id='$competitor_quantity_id' AND status='yes'";
    $res_comp = mysqli_query($link, $check_comp);

    if(mysqli_num_rows($res_comp) == 0){
        continue; // invalid competitor id
    }

    /* ================= INSERT INTO NEW TABLE ================= */
    $sql_insert_detail = "INSERT INTO market_feedback_details
                        (market_feedback_id, customer_code, competitor_quantity_id, marketfeedbackdetails_qty, created_at)
                        VALUES
                        ('$market_feedback_id','$customer_code','$competitor_quantity_id','$qty','$created_at')";
    mysqli_query($link, $sql_insert_detail);

    /* ================= CHECK EXIST ================= */
    $sqlcheck = "SELECT customer_code FROM competitor_quantity 
                 WHERE customer_code='$customer_code' 
                 AND competitor_quantity_id='$competitor_quantity_id' 
                 LIMIT 1";

    $rscheck = mysqli_query($link, $sqlcheck);

    if(mysqli_num_rows($rscheck) > 0)
    {
        /* ================= UPDATE ================= */
        $sqlupdate = "UPDATE competitor_quantity 
                      SET qty='$qty',
                          update_type='user',
                          updated_at=NOW()
                      WHERE customer_code='$customer_code' 
                      AND competitor_quantity_id='$competitor_quantity_id'";

        mysqli_query($link, $sqlupdate);
    }
    else
    {
        /* ================= INSERT ================= */
        $sqlinsert = "INSERT INTO competitor_quantity SET
                      customer_code='$customer_code',
                      competitor_quantity_id='$competitor_quantity_id',
                      qty='$qty',
                      update_type='user',
                      created_at=NOW(),
                      updated_at=NOW()";

        mysqli_query($link, $sqlinsert);
    }

    /* ================= LOG TABLE INSERT ================= */
    $log_sql = "INSERT INTO competitor_quantity_log 
                (customer_code, competitor_quantity_id, qty, acedns, created_at, mf_stk_audit_id, emp_code)
                VALUES 
                ('$customer_code','$competitor_quantity_id','$qty','yes','$created_at','$market_feedback_id','$emp_code')";

    mysqli_query($link, $log_sql);
}

/* ================= RESPONSE ================= */
echo json_encode([
    "status" => true,
    "message" => "Market feedback processed successfully"
]);

exit;
?>