<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

header("Content-Type: application/json");

require_once("../sfa_connection.php");

$localDB = new sfa_connection();
$link = $localDB->conn;

date_default_timezone_set('Asia/Kolkata');
/* ================= TRIM FUNCTION ================= */
function clean($value){
    return trim((string)$value);
}

// function updateMonthlyPotential($link, $customer_code){
//     $customer_code = mysqli_real_escape_string($link, $customer_code);
//     $sql = "UPDATE customer_master cm
//             SET cm.monthly_potential = (
//                 SELECT COALESCE(SUM(cq.marketfeedbackdetails_qty), 0)
//                 FROM market_feedback_details cq
//                 WHERE cq.customer_code = '$customer_code'
//             )
//             WHERE cm.customer_code = '$customer_code'";

//     return mysqli_query($link, $sql);
// }
function updateMonthlyPotential($link, $customer_code){
    $customer_code = mysqli_real_escape_string($link, $customer_code);

    $sql = "UPDATE customer_master cm
            SET cm.monthly_potential = (
                SELECT COALESCE(MAX(cq.marketfeedbackdetails_qty), 0)
                FROM market_feedback_details cq
                WHERE cq.customer_code = '$customer_code'
            )
            WHERE cm.customer_code = '$customer_code'";

    return mysqli_query($link, $sql);
}

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

$created_at = date('Y-m-d H:i:s');

/* ================= TRACKING ================= */
$success_count = 0;
$error_count   = 0;
$error_log     = [];


/* ================= GET UNIVERSE TYPE ================= */
$universeType = clean($xml->marketfeedback->universetype);
//echo"<pre>";print_r($universeType);die;


/* ================= LOOP DETAILS ================= */
foreach($xml->marketfeedback->details->item as $item){

    $market_feedback_id = clean($item->market_feedback_id);
    $customer_code      = clean($item->customer_code);
    $emp_code           = clean($item->emp_code);

    $competitor_quantity_id = clean($item->competitor_quantity_id);
    $qty                    = clean($item->qty);
    $date_time              = clean($item->date_time);

    if($competitor_quantity_id == "") continue;

    /* ================= VALIDATION ================= */
    $check_comp = "SELECT id FROM competitor_group_master_potential 
                   WHERE id='$competitor_quantity_id' AND status='yes'";
    $res_comp = mysqli_query($link, $check_comp);

    if(!$res_comp || mysqli_num_rows($res_comp) == 0){
        $error_count++;
        $error_log[] = "Invalid competitor ID: $competitor_quantity_id";
        continue;
    }

    /* ================= INSERT INTO market_feedback_details ================= */
    $sql_insert_detail = "INSERT INTO market_feedback_details
                        (market_feedback_id, customer_code, emp_code, date_time, competitor_quantity_id, marketfeedbackdetails_qty, created_at)
                        VALUES
                        ('$market_feedback_id','$customer_code','$emp_code','$date_time','$competitor_quantity_id','$qty','$created_at')";

    if(!mysqli_query($link, $sql_insert_detail)){
        $error_count++;
        $error_log[] = "Detail Insert Error: ".mysqli_error($link);
        continue;
    }

    /* ================= CHECK EXIST ================= */
    $sqlcheck = "SELECT customer_code FROM competitor_quantity 
                 WHERE customer_code='$customer_code' 
                 AND competitor_quantity_id='$competitor_quantity_id' 
                 LIMIT 1";

    $rscheck = mysqli_query($link, $sqlcheck);

    if(!$rscheck){
        $error_count++;
        $error_log[] = "Check Error: ".mysqli_error($link);
        continue;
    }
    if(mysqli_num_rows($rscheck) > 0)
    {
        /* ================= UPDATE ================= */
        $sqlupdate = "UPDATE competitor_quantity 
                      SET qty='$qty',
                          update_type='user',
                          updated_at=NOW()
                      WHERE customer_code='$customer_code' 
                      AND competitor_quantity_id='$competitor_quantity_id'";

      /*  if(!mysqli_query($link, $sqlupdate)){
            $error_count++;
            $error_log[] = "Update Error: ".mysqli_error($link);
            continue;
        }*/
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

        /*if(!mysqli_query($link, $sqlinsert)){
            $error_count++;
            $error_log[] = "Insert Error: ".mysqli_error($link);
            continue;
        }*/
    }

    /*if(!updateMonthlyPotential($link, $customer_code)){
        $error_count++;
        $error_log[] = "Monthly Potential Update Error: ".mysqli_error($link);
        continue;
    }
*/
    /* ================= LOG TABLE INSERT ================= */
     $log_sql = "INSERT INTO competitor_quantity_log 
                (customer_code, competitor_quantity_id, qty, acedns, created_at, mf_stk_audit_id, emp_code)
                VALUES 
                ('$customer_code','$competitor_quantity_id','$qty','yes','$created_at','$market_feedback_id','$emp_code')";

    if(!mysqli_query($link, $log_sql)){
        $error_count++;
        $error_log[] = "Log Insert Error: ".mysqli_error($link);
        continue;
    }

    $success_count++;
}
/* ================= STORE UNIVERSE TYPE ================= */

if($market_feedback_id != "" && $universeType != ""){

    $check_universe = "SELECT id 
                       FROM market_feedback_universe_type
                       WHERE market_feedback_id='$market_feedback_id'
                       LIMIT 1";

    $res_universe = mysqli_query($link, $check_universe);

    if($res_universe && mysqli_num_rows($res_universe) == 0){

        $insert_universe = "INSERT INTO market_feedback_universe_type
                            (market_feedback_id, universe_type, created_at)
                            VALUES
                            ('$market_feedback_id',
                             '".mysqli_real_escape_string($link, $universeType)."',
                             '$created_at')";

        if(!mysqli_query($link, $insert_universe)){
            $error_log[] = "Universe Type Insert Error: ".mysqli_error($link);
        }
    }
}

/* ================= RESPONSE ================= */
if($success_count > 0 && $error_count == 0){
    echo json_encode([
        "status" => true,
        "message" => "All records inserted successfully",
        "success_count" => $success_count
    ]);
}
elseif($success_count > 0 && $error_count > 0){
    echo json_encode([
        "status" => true,
        "message" => "Partial success",
        "success_count" => $success_count,
        "error_count" => $error_count,
        "errors" => $error_log
    ]);
}
else{
    echo json_encode([
        "status" => false,
        "message" => "All records failed",
        "error_count" => $error_count,
        "errors" => $error_log
    ]);
}

exit;
?>
