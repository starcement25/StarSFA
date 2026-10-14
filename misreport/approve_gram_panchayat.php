<?php

header("Content-Type: application/json");

date_default_timezone_set('Asia/Kolkata');

set_time_limit(1000);

ini_set('memory_limit', '512M');

require_once("../sfa_connection.php");
require("../include/config.php");
require("../include/config-setup.php");
require("../include/dbcon.php");
require("../include/functions.php");
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$localDB = new sfa_connection();
$conn = $localDB->conn;

$response = array(
    "status"  => "error",
    "message" => "Something went wrong."
);

// ---------------------------------------------------------
// Read & validate input
// ---------------------------------------------------------
$customer_code = isset($_GET['customer_code']) ? trim($_GET['customer_code']) : '';
$status        = isset($_GET['status']) ? trim($_GET['status']) : '';
$emp_code      = isset($_GET['emp_code']) ? trim($_GET['emp_code']) : '';

if ($customer_code === '' || $status === '' || $emp_code === '') {
    $response['message'] = "customer_code, status and emp_code are required.";
    echo json_encode($response);
    exit;
}

if ($status !== '0' && $status !== '1') {
    $response['message'] = "Invalid status value. Only 0 or 1 is allowed.";
    echo json_encode($response);
    exit;
}

// ---------------------------------------------------------
// Main logic
// ---------------------------------------------------------
try {

    mysqli_begin_transaction($conn);

    if ($status === '0') {
        // -------------------------------------------------
        // CASE 0 : REJECT
        // -------------------------------------------------

        // 1. Clear location data from customer_sfa_ac_type
        $sql1 = "UPDATE customer_sfa_ac_type
                 SET customer_state = NULL,
                     customer_district = NULL,
                     customer_block_name = NULL,
                     customer_gram_panchayat = NULL
                 WHERE customer_code = ?";
        $stmt1 = mysqli_prepare($conn, $sql1);
        if (!$stmt1) {
            throw new Exception("Prepare failed (step1): " . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($stmt1, "s", $customer_code);
        if (!mysqli_stmt_execute($stmt1)) {
            throw new Exception("Execute failed (step1): " . mysqli_stmt_error($stmt1));
        }
        mysqli_stmt_close($stmt1);

        // 2. Get market_feedback_id(s) for this customer_code
        $market_feedback_ids = array();

        $sql2 = "SELECT market_feedback_id
                 FROM market_feedback_details
                 WHERE customer_code = ?";
        $stmt2 = mysqli_prepare($conn, $sql2);
        if (!$stmt2) {
            throw new Exception("Prepare failed (step2): " . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($stmt2, "s", $customer_code);
        if (!mysqli_stmt_execute($stmt2)) {
            throw new Exception("Execute failed (step2): " . mysqli_stmt_error($stmt2));
        }
        $result2 = mysqli_stmt_get_result($stmt2);
        while ($row = mysqli_fetch_assoc($result2)) {
            if (!empty($row['market_feedback_id'])) {
                $market_feedback_ids[] = $row['market_feedback_id'];
            }
        }
        mysqli_stmt_close($stmt2);

        // Dedupe
        $market_feedback_ids = array_values(array_unique($market_feedback_ids));

        // 3. Clear location data in market_feedback_universe_type
        //    for all matching market_feedback_id values
        if (count($market_feedback_ids) > 0) {
            $placeholders = implode(',', array_fill(0, count($market_feedback_ids), '?'));
            $types        = str_repeat('s', count($market_feedback_ids));

            $sql3 = "UPDATE market_feedback_universe_type
                     SET customer_state = NULL,
                         customer_district = NULL,
                         customer_block_name = NULL,
                         customer_gram_panchayat = NULL
                     WHERE market_feedback_id IN ($placeholders)";
            $stmt3 = mysqli_prepare($conn, $sql3);
            if (!$stmt3) {
                throw new Exception("Prepare failed (step3): " . mysqli_error($conn));
            }
            mysqli_stmt_bind_param($stmt3, $types, ...$market_feedback_ids);
            if (!mysqli_stmt_execute($stmt3)) {
                throw new Exception("Execute failed (step3): " . mysqli_stmt_error($stmt3));
            }
            mysqli_stmt_close($stmt3);
        }

        mysqli_commit($conn);

        $response['status']  = "success";
        $response['message'] = "Gram Panchayat details rejected and cleared successfully.";

    } else {
        // -------------------------------------------------
        // CASE 1 : APPROVE
        // -------------------------------------------------
        $sql1 = "UPDATE customer_sfa_ac_type
                 SET approval_status = 1,
                     approved_by = ?,
                     updated_at = NOW()
                 WHERE customer_code = ?";
        $stmt1 = mysqli_prepare($conn, $sql1);
        if (!$stmt1) {
            throw new Exception("Prepare failed: " . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($stmt1, "ss", $emp_code, $customer_code);
        if (!mysqli_stmt_execute($stmt1)) {
            throw new Exception("Execute failed: " . mysqli_stmt_error($stmt1));
        }
        mysqli_stmt_close($stmt1);

        mysqli_commit($conn);

        $response['status']  = "success";
        $response['message'] = "Gram Panchayat details approved successfully.";
    }

} catch (Exception $e) {
    mysqli_rollback($conn);
    $response['status']  = "error";
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
exit;