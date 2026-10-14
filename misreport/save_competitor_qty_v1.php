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

/* ================= GET RAW XML ================= */

$xml_data = file_get_contents("php://input");

if(empty($xml_data)){

    echo json_encode([
        "status" => false,
        "message" => "No XML received"
    ]);

    exit;
}

/* ================= PARSE XML ================= */

libxml_use_internal_errors(true);

$xml = simplexml_load_string($xml_data);

if(!$xml){

    echo json_encode([
        "status" => false,
        "message" => "Invalid XML"
    ]);

    exit;
}

$created_at = date('Y-m-d H:i:s');

/* ================= XML MASTER VALUES ================= */

$universeType  = clean($xml->marketfeedback->universetype);
$state         = clean($xml->marketfeedback->state);
$district      = clean($xml->marketfeedback->district);
$block         = clean($xml->marketfeedback->block);
$grampanchayat = clean($xml->marketfeedback->grampanchayat);

/* ================= TRACKING ================= */

$success_count = 0;
$error_count   = 0;
$error_log     = [];

/* ================= LOOP DETAILS ================= */

foreach($xml->marketfeedback->details->item as $item){

    $market_feedback_id      = clean($item->market_feedback_id);
    $customer_code           = clean($item->customer_code);
    $emp_code                = clean($item->emp_code);

    $competitor_quantity_id  = clean($item->competitor_quantity_id);
    $qty                     = clean($item->qty);
    $date_time               = clean($item->date_time);

    if($competitor_quantity_id == ""){
        continue;
    }

    /* =====================================================
       VALIDATE COMPETITOR
    ===================================================== */

    $check_comp = "SELECT id
                   FROM competitor_group_master_potential
                   WHERE id='$competitor_quantity_id'
                   AND status='yes'";

    $res_comp = mysqli_query($link, $check_comp);

    if(!$res_comp || mysqli_num_rows($res_comp) == 0){

        $error_count++;
        $error_log[] = "Invalid competitor ID : $competitor_quantity_id";

        continue;
    }

    /* =====================================================
       INSERT MARKET FEEDBACK DETAILS
    ===================================================== */

    $sql_insert_detail = "INSERT INTO market_feedback_details
    (
        market_feedback_id,
        customer_code,
        emp_code,
        date_time,
        competitor_quantity_id,
        marketfeedbackdetails_qty,
        created_at
    )
    VALUES
    (
        '$market_feedback_id',
        '$customer_code',
        '$emp_code',
        '$date_time',
        '$competitor_quantity_id',
        '$qty',
        '$created_at'
    )";

    if(!mysqli_query($link, $sql_insert_detail)){

        $error_count++;
        $error_log[] = "Detail Insert Error : ".mysqli_error($link);

        continue;
    }

    /* =====================================================
       UPDATE / INSERT customer_sfa_ac_type
    ===================================================== */

    if($customer_code != ""){

        $check_customer_sfa = "SELECT id
                               FROM customer_sfa_ac_type
                               WHERE customer_code='$customer_code'
                               LIMIT 1";

        $res_customer_sfa = mysqli_query($link, $check_customer_sfa);

        if($res_customer_sfa && mysqli_num_rows($res_customer_sfa) > 0){

            /* ================= UPDATE ================= */

            $update_customer_sfa = "UPDATE customer_sfa_ac_type
                                    SET
                                        sfa_ac_type='".mysqli_real_escape_string($link, $universeType)."',
                                        customer_state='".mysqli_real_escape_string($link, $state)."',
                                        customer_district='".mysqli_real_escape_string($link, $district)."',
                                        customer_block_name='".mysqli_real_escape_string($link, $block)."',
                                        customer_gram_panchayat='".mysqli_real_escape_string($link, $grampanchayat)."',
                                        updated_at='$created_at'
                                    WHERE customer_code='$customer_code'";

            if(!mysqli_query($link, $update_customer_sfa)){

                $error_count++;
                $error_log[] = "customer_sfa_ac_type Update Error : ".mysqli_error($link);
            }

        }else{

            /* ================= INSERT ================= */

            $insert_customer_sfa = "INSERT INTO customer_sfa_ac_type
            (
                customer_code,
                sfa_ac_type,
                customer_state,
                customer_district,
                customer_block_name,
                customer_gram_panchayat,
                created_at,
                updated_at
            )
            VALUES
            (
                '$customer_code',
                '".mysqli_real_escape_string($link, $universeType)."',
                '".mysqli_real_escape_string($link, $state)."',
                '".mysqli_real_escape_string($link, $district)."',
                '".mysqli_real_escape_string($link, $block)."',
                '".mysqli_real_escape_string($link, $grampanchayat)."',
                '$created_at',
                '$created_at'
            )";

            if(!mysqli_query($link, $insert_customer_sfa)){

                $error_count++;
                $error_log[] = "customer_sfa_ac_type Insert Error : ".mysqli_error($link);
            }
        }
    }

    /* =====================================================
       INSERT market_feedback_universe_type
    ===================================================== */

    if($market_feedback_id != "" && $universeType != ""){

        $check_universe = "SELECT id
                           FROM market_feedback_universe_type
                           WHERE market_feedback_id='$market_feedback_id'
                           LIMIT 1";

        $res_universe = mysqli_query($link, $check_universe);

        if($res_universe && mysqli_num_rows($res_universe) == 0){

            $insert_universe = "INSERT INTO market_feedback_universe_type
            (
                market_feedback_id,
                universe_type,
                customer_state,
                customer_district,
                customer_block_name,
                customer_gram_panchayat,
                created_at
            )
            VALUES
            (
                '$market_feedback_id',
                '".mysqli_real_escape_string($link, $universeType)."',
                '".mysqli_real_escape_string($link, $state)."',
                '".mysqli_real_escape_string($link, $district)."',
                '".mysqli_real_escape_string($link, $block)."',
                '".mysqli_real_escape_string($link, $grampanchayat)."',
                '$created_at'
            )";

            if(!mysqli_query($link, $insert_universe)){

                $error_count++;
                $error_log[] = "Universe Type Insert Error : ".mysqli_error($link);
            }
        }
    }

    $success_count++;
}

/* ================= RESPONSE ================= */

if($success_count > 0 && $error_count == 0){

    echo json_encode([
        "status" => true,
        "message" => "All records inserted successfully",
        "success_count" => $success_count
    ]);

}elseif($success_count > 0 && $error_count > 0){

    echo json_encode([
        "status" => true,
        "message" => "Partial success",
        "success_count" => $success_count,
        "error_count" => $error_count,
        "errors" => $error_log
    ]);

}else{

    echo json_encode([
        "status" => false,
        "message" => "All records failed",
        "error_count" => $error_count,
        "errors" => $error_log
    ]);
}

exit;

?>