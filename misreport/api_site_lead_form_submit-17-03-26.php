<?php
require_once("../sfa_connection.php");
header('Content-Type: application/json');
date_default_timezone_set('Asia/Kolkata');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$localDB = new sfa_connection();
$conn = $localDB->conn;
$created_at = date('Y-m-d H:i:s');
// $sql1 = "INSERT INTO new_site_lead_log (emp_code, content, created_at, updated_at)
//         VALUES ('testing', 'testing', '$created_at', '$created_at')";
//         mysqli_query($conn, $sql1);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        "process_status" => "No",
        "process_message" => "Failed!",
        "error" => "Only POST method is allowed"
    ]);
    exit;
}



use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require __DIR__.'/include/sfa_mail/PHPMailer.php';
require __DIR__.'/include/sfa_mail/SMTP.php';
require __DIR__.'/include/sfa_mail/Exception.php';

$data = json_decode(file_get_contents("php://input"), true);
$created_at = date('Y-m-d H:i:s');
$emp_code = $data['employee_code'] ?? 'NA';
// echo $emp_code;die;
$start_content =  json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$created_at = date('Y-m-d H:i:s');
$start_content = mysqli_real_escape_string($conn, $start_content);
$emp_code = mysqli_real_escape_string($conn, $emp_code);
$sql = "INSERT INTO new_site_lead_log (emp_code, content, created_at, updated_at)
        VALUES ('$emp_code', '$start_content', '$created_at', '$created_at')";

mysqli_query($conn, $sql);

$log_id = mysqli_insert_id($conn); 

// echo $data['requested_date_of_delivery'];die;
$required = [
    'site_transaction_id',
    'site_unique_id',
    'site_visit_date',
    'employee_code',
    'employee_name',
    'state',
    'zone',
    'branch',
    'district',
    'customer_name',
    'customer_contact_number',
    'customer_full_address',
    'site_segment',
    'visit_type',
    'project_segment',
    
    'built_up_area',
    'conversion',
    'site_priority',
    // 'counter_code',
    'is_register_engineer',
    'is_register_contractor',
    'meeting_person',
    'decision_maker',
    'current_stage_of_construction',
    'site_potential',
    'consumed_till_date',
    'balance_potential',
    'site_category',
    'brand_used',
    'price_per_bag',
    // 'counter_type',
    // 'counter_name',
    'weather_shield_demo'
];
/*
    commend from 10-02-26
    'longitude',
    'latitude',
    type_of_construction,
*/

foreach ($required as $field) {
    if (!isset($data[$field]) || trim($data[$field]) === '') {
        http_response_code(400);
        echo json_encode([
            "process_status" => "No",
            "process_message" => "Failed!",
            "error" => "$field is required"
        ]);
        exit;
    }
}
// $checkTxn = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM new_site_lead_visit_master WHERE site_transaction_id = '$site_transaction_id'"); $rowTxn = mysqli_fetch_assoc($checkTxn); 
// if ($rowTxn['cnt'] > 0) { http_response_code(400); echo json_encode([ "process_status" => "No", "process_message" => "Failed!", "error" => "site_transaction_id already exists" ]); exit; }
//  $checkUnique = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM new_site_lead_visit_master WHERE site_unique_id = '$site_unique_id'"); 
//  $rowUnique = mysqli_fetch_assoc($checkUnique); if ($rowUnique['cnt'] > 0) { http_response_code(400); echo json_encode([ "process_status" => "No", "process_message" => "Failed!", "error" => "site_unique_id already exists" ]); exit; } 

if (isset($data['is_register_contractor']) && strtolower(trim($data['is_register_contractor'])) === 'yes') {

    $missing = [];
    foreach (['contractor_name', 'contractor_contact_number'] as $field) {
        if (!isset($data[$field]) || trim($data[$field]) === '') {
            $missing[] = $field;
        }
    }

    // print_r($missing);
    if (!empty($missing)) {
        http_response_code(400);
        echo json_encode([
            "process_status" => "No",
            "process_message" => "Failed!",
            "error" => implode(', ', $missing) . " is required when is_register_contractor is Yes"
        ]);
        exit;
    }
}

if (isset($data['is_register_engineer']) && strtolower($data['is_register_engineer']) === 'yes') {
    foreach (['engineer_name', 'engineer_contact_number'] as $field) {
        if (empty($data[$field])) {
            http_response_code(400);
            echo json_encode(["process_status" => "No", "process_message" => "Failed!", "error" => "$field is required when is_register_engineer is Yes"]);
            exit;
        }
    }
}
if (isset($data['visit_type'], $data['conversion'])) {
    $visit_type = strtolower(trim($data['visit_type']));
    $conversion = strtolower(trim($data['conversion']));

  $skipValidation =
        ($visit_type === 'non star site' && $conversion === 'non converted')
        ||  $data['order_quantity'] === '0';
    if (!$skipValidation) {
    // foreach (['product_name', 'order_quantity', 'requested_date_of_delivery'] as $field) {
    //     if (!isset($data[$field]) || $data[$field] === "") {
    //         http_response_code(400);
    //         echo json_encode([
    //             "process_status" => "No",
    //             "process_message" => "Failed!",
    //             "error" => "$field is required unless visit_type is 'Non Star Site' and conversion is 'Non Converted'"
    //         ]);
    //         exit;
    //     }
    // }
/* commend on 10-02-26
    foreach (['product_name', 'order_quantity','requested_date_of_delivery'] as $field) {
    if (!isset($data[$field]) || trim($data[$field]) === '') {
        http_response_code(400);
        echo json_encode([
            "process_status" => "No",
            "process_message" => "Failed!",
            "error" => "$field is required unless visit_type is 'Non Star Site' and conversion is 'Non Converted'"
        ]);
        exit;
    }
}*/

}



}

$site_visit_date = null;
if (isset($data['site_visit_date']) && strtolower(trim($data['site_visit_date'])) !== 'null' && $data['site_visit_date'] !== '') {
    $date = DateTime::createFromFormat('Y-m-d', $data['site_visit_date']);
    if (!$date || $date->format('Y-m-d') !== $data['site_visit_date']) {
        http_response_code(400);
        echo json_encode(["process_status" => "No", "process_message" => "Failed!", "error" => "Invalid date format in site_visit_date. Expected format: YYYY-MM-DD"]);
        exit;
    }
    $site_visit_date = $date->format('Y-m-d');
}
$requested_date_of_delivery = null;
if (isset($data['requested_date_of_delivery']) && strtolower(trim($data['requested_date_of_delivery'])) !== 'null' && $data['requested_date_of_delivery'] !== '') {
    $date = DateTime::createFromFormat('Y-m-d', $data['requested_date_of_delivery']);
    if (!$date || $date->format('Y-m-d') !== $data['requested_date_of_delivery']) {
        http_response_code(400);
        echo json_encode(["process_status" => "No", "process_message" => "Failed!", "error" => "Invalid date format in requested_date_of_delivery. Expected format: YYYY-MM-DD"]);
        exit;
    }
    $requested_date_of_delivery = $date->format('Y-m-d');
}
$actual_date_of_delivery = null;
if (isset($data['actual_date_of_delivery']) && strtolower(trim($data['actual_date_of_delivery'])) !== 'null' && $data['actual_date_of_delivery'] !== '') {
    $date = DateTime::createFromFormat('Y-m-d', $data['actual_date_of_delivery']);
    if (!$date || $date->format('Y-m-d') !== $data['actual_date_of_delivery']) {
        http_response_code(400);
        echo json_encode(["process_status" => "No", "process_message" => "Failed!", "error" => "Invalid date format in actual_date_of_delivery. Expected format: YYYY-MM-DD"]);
        exit;
    }
    $actual_date_of_delivery = $date->format('Y-m-d');
}


foreach (['customer_contact_number', 'contractor_contact_number', 'engg_contact'] as $field) {
    if (!empty($data[$field])) {
        $cleanedPhone = preg_replace('/\D/', '', $data[$field]);
        if (!preg_match('/^\d{10}$/', $cleanedPhone)) {
            http_response_code(400);
            echo json_encode([
                "process_status" => "No",
                "process_message" => "Failed!",
                "error" => "$field must be a valid 10-digit number"
            ]);
            exit;
        }
    }
}
function escapeOrNull($conn, $value)
{
    if (is_null($value) || trim($value) === '' || strtolower(trim($value)) === 'null') {
        return "NULL";
    }
    return "'" . mysqli_real_escape_string($conn, $value) . "'";
}

// print_r($data);die;
$allfields = [
    "site_transaction_id","site_unique_id","site_creation_date","site_visit_date",
    "employee_code","employee_name","zone","branch","state","district","latitude",
    "longitude","customer_name","customer_contact_number","customer_full_address",
    "is_register_contractor","contractor_name","contractor_contact_number",
    "is_register_engineer","engineer_name","engineer_contact_number","meeting_person",
    "decision_maker","site_segment","visit_type","project_segment",
    "type_of_construction","current_stage_of_construction","built_up_area",
    "site_potential","consumed_till_date","balance_potential",
    "balance_potential_manual","site_category","brand_used","price_per_bag",
    "conversion","product_name","order_quantity","requested_date_of_delivery",
    "counter_type","counter_name","counter_code","reason_for_non_conversion",
    "site_priority","weather_shield_demo","approval_status","date_time",
    "asm_name","asm_employee_id","actual_date_of_delivery","delivery_remarks",
    "reason_for_not_delivery","site_status","floor_count","balance_potential_manual","remarks"
];
foreach ($allfields as $key ) {
    
    ${"escaped_" . $key} = escapeOrNull($conn, $data[$key]);
}


mysqli_begin_transaction($conn);

$site_transaction_id = mysqli_real_escape_string($conn, $data['site_transaction_id']);
$site_unique_id = mysqli_real_escape_string($conn, $data['site_unique_id']);


$checkMaster = mysqli_query($conn, "SELECT id FROM new_site_lead_master WHERE unique_id = '$site_unique_id' AND transaction_id='$site_transaction_id'");
// echo $conn="SELECT id FROM new_site_lead_master WHERE unique_id = '$site_unique_id' AND transaction_id='$site_transaction_id'";die; 
if (!$checkMaster) {
    mysqli_rollback($conn);
    echo json_encode(["process_status" => "No", "error" => mysqli_error($conn)]);
    exit;
}
//  print_r(mysqli_num_rows($checkMaster));die;
if (mysqli_num_rows($checkMaster) > 0) {

    $existing = mysqli_fetch_assoc($checkMaster);
    // print_r( $existing );die;
    $site_id = $existing['id'];
} else {

    $insert_site_sql = "INSERT INTO new_site_lead_master (
        transaction_id, unique_id, visit_date, emp_code, emp_name, zone, branch, district, state,
        latitude, longitude, cust_name, cust_phn_no, address, site_segment,
        project_segment, type_of_const, built_up_area, site_priority, counter_code,visit_type,conversion
    ) VALUES (
        $escaped_site_transaction_id, $escaped_site_unique_id, $escaped_site_visit_date, $escaped_employee_code,
        $escaped_employee_name, $escaped_zone, $escaped_branch, $escaped_district, $escaped_state,
        $escaped_latitude, $escaped_longitude, $escaped_customer_name, $escaped_customer_contact_number,
        $escaped_customer_full_address, $escaped_site_segment, $escaped_project_segment,
        $escaped_type_of_construction, $escaped_built_up_area, 
        $escaped_site_priority, $escaped_counter_code,$escaped_visit_type,$escaped_conversion
    )";

    if (!mysqli_query($conn, $insert_site_sql)) {
        mysqli_rollback($conn);
        echo json_encode([
            "process_status" => "No",
            "error" => "Insert new_site_lead_master failed",
            "db_error" => mysqli_error($conn)
        ]);
        exit;
    }
    $site_id = mysqli_insert_id($conn);
}
 $order_quantity = strtolower(trim($data['order_quantity']));

    // $conversion = strtolower(trim($data['conversion']));
// $approval_status = isset($_POST['approval_status']) ? strtolower(trim($_POST['approval_status'])) : null;

// echo $visit_type.$conversion;die;
// if ($approval_status === null || $approval_status === '') {
//     $approval_status = 'pending';
// }

if ((int)$order_quantity == 0 || $order_quantity === '') {
    $approval_status = 'approved';
}else{
     $approval_status = 'pending';
}

if ($approval_status === 'approved') {
    $delivery_remarks = "delivered";
    $approval_date_time = date("Y-m-d H:i:s");

    $escaped_delivery_remarks = "'" . mysqli_real_escape_string($conn, $delivery_remarks) . "'";
    $escaped_date_time = "'" . mysqli_real_escape_string($conn, $approval_date_time) . "'";
}

$insert_visit_sql = "INSERT INTO new_site_lead_visit_master (
    new_site_lead_id, new_site_lead_unique_id, petty_contractor_registered, head_mason_name, head_mason_contact,
    engg_registered, engg_name, engg_contact, meeting_person, decision_maker,
    current_stage_of_construction, site_potential, consumed_till_date, balance_potential,
    site_category, brand_used, price_per_bag, counter_type, counter_name, weather_shield_demo,approval_status,approval_date_time,asm_name,asm_id,actual_date_of_delivery,delivery_remarks,reason_for_not_delivery,site_status,no_of_bags_ordered,requested_date,select_product,reason_for_non_conversion,visit_type,conversion,floor_count,balance_potential_manual,remarks
) VALUES (
   " . intval($site_id) . ", $escaped_site_unique_id, $escaped_is_register_contractor, $escaped_contractor_name,
    $escaped_contractor_contact_number, $escaped_is_register_engineer, $escaped_engineer_name,
    $escaped_engineer_contact_number, $escaped_meeting_person, $escaped_decision_maker,
    $escaped_current_stage_of_construction, $escaped_site_potential, $escaped_consumed_till_date,
    $escaped_balance_potential, $escaped_site_category, $escaped_brand_used, $escaped_price_per_bag,
    $escaped_counter_type, $escaped_counter_name, $escaped_weather_shield_demo, 
'$approval_status', 
$escaped_date_time, 
$escaped_asm_name, 
$escaped_asm_employee_id, 
$escaped_actual_date_of_delivery, 
$escaped_delivery_remarks, 
$escaped_reason_for_not_delivery, 
$escaped_site_status,$escaped_order_quantity,$escaped_requested_date_of_delivery,$escaped_product_name,$escaped_reason_for_non_conversion,$escaped_visit_type,$escaped_conversion,$escaped_floor_count,$escaped_balance_potential_manual,$escaped_remarks
)";
// echo $insert_visit_sql ;die;
if (!mysqli_query($conn, $insert_visit_sql)) {
    mysqli_rollback($conn);
    echo json_encode([
        "process_status" => "No",
        "error" => "Insert new_site_lead_visit_master failed",
        "db_error" => mysqli_error($conn)
    ]);
    exit;
}

// $end_content = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
// $updated_at = date('Y-m-d H:i:s');

// $end_content = mysqli_real_escape_string($conn, $end_content);

// $sql = "UPDATE new_site_lead_log
//         SET content = '$end_content',
//             updated_at = '$updated_at'
//         WHERE id = $log_id";

// mysqli_query($conn, $sql);
mysqli_commit($conn);


$site_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM new_site_lead_master WHERE id = '$site_id'"));
$visit_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM new_site_lead_visit_master WHERE new_site_lead_id = '$site_id' ORDER BY id DESC LIMIT 1"));

    /* ================= SEND EMAIL TO ASM 27-02-26 sk ================= */

if ($approval_status === 'pending' && !empty($data['asm_employee_id'])) {

    $asm_id = mysqli_real_escape_string($conn, $data['asm_employee_id']);

    // Get ASM Email
    $empQuery = mysqli_query($conn, "SELECT email, emp_name, phone_no as contact_no 
                                     FROM employee_master 
                                     WHERE emp_code = '$asm_id' LIMIT 1");

    if ($empQuery && mysqli_num_rows($empQuery) > 0) {

        $empData = mysqli_fetch_assoc($empQuery);
        $asm_email = $empData['email'];
        //$asm_email ="suman.koley@sbinfowaves.com";
        $asm_name  = $empData['emp_name'];

        if (!empty($asm_email)) {

            $mail = new PHPMailer(true);

            try {

                $mail->isSMTP();
                $mail->Host = "cloudmail2.up99plus.com";
                $mail->Port = 25;
                $mail->SMTPAuth = true;
                $mail->Username = "starcement@cloudmail.up99plus.com";
                $mail->Password = "Nh26sjqgWk";
                $mail->SMTPSecure = false;
                $mail->SMTPAutoTLS = false;

                $mail->setFrom("starcement@cloudmail.up99plus.com", "SFA Application");
                $mail->addAddress($asm_email, $asm_name);

                $mail->isHTML(true);
                $mail->Subject = "Approval Required: Site Lead Submitted";

                $customer_name = $data['customer_name'];
                $customer_address = $data['customer_full_address'];
                $visit_type = $data['visit_type'];
                $dealer_name = $data['counter_name'] ?? '';
                $dealer_code = $data['counter_code'] ?? '';
                $product = $data['product_name'] ?? '';
                $bags = $data['order_quantity'] ?? '';
                $request_date = $data['requested_date_of_delivery'] ?? '';
                $status = ucfirst($approval_status);
                $bde_name = $data['employee_name'];
                $bde_contact = $data['customer_contact_number'] ?? '';
                $submitted_on = date("d-m-Y H:i:s");

                /*$mail->Body = "
                <p>Dear ASM,</p>

                <p>This is an automated notification to inform you that a new Site Lead has been submitted by via SFA Application which needs your review and approval to proceed further in the workflow. If no action is taken within 48 hours of submission, the Site Lead will be automatically approved by the system as per the defined workflow protocol.</p>


                <table border='0'>
                    <tr style='color:#000000'><td>Customer Name:</td><td>$customer_name</td></tr>
                    <tr style='color:#000000'><td>Address:</td><td>$customer_address</td></tr>
                    <tr style='color:#000000'><td>Visit Type:</td><td>$visit_type</td></tr>
                        <br>
                    <tr style='color:#222222'><td>Dealer Name:</td><td>$dealer_name</td></tr>
                    <tr style='color:#222222'><td>Dealer Code:</td><td>$dealer_code</td></tr>
                    <tr style='color:#222222'><td>Product:</td><td>$product</td></tr>
                    <tr style='color:#222222'><td>No of Bags:</td><td>$bags</td></tr>
                    <tr style='color:#222222'><td>Request Date:</td><td>$request_date</td></tr>
                    <tr style='color:#222222'><td>Status:</td><td>$status</td></tr>
<br>
                    <tr style='color:#444444'><td>BDE Name:</td><td>$bde_name</td></tr>
                    <tr style='color:#444444'><td>BDE Contact No:</td><td>$bde_contact</td></tr>
                    <tr style='color:#444444'><td>Lead Submitted On:</td><td>$submitted_on</td></tr>
                </table>

                
                <p>You may initiate the review and provide your approval directly through your SFA Application. This email serves as a parallel communication to ensure timely visibility of pending approvals in your queue.<p>

                <p>Should you require any clarifications or supporting inputs, kindly connect with the submitting BDE.</p>

                <p>Thank you for your prompt attention and continued support in ensuring seamless operational execution.</p>
                <br>
                <p>Regards,<br>
                SFA Application<br>
                Automated Workflow Notification<br>
                Star Cement Limited</p>
                ";*/
                $site_id = urlencode($data['site_unique_id']);

                $approve_link = "https://mis.starcement.co.in/api/approve_site_lead.php?site_id=$site_id&approval_status=approved";
                $reject_link  = "https://mis.starcement.co.in/api/approve_site_lead.php?site_id=$site_id&approval_status=rejected";
                $mail->Body = "
                        <p>Dear ASM,</p>

                        <p>A new Site Lead has been submitted via the SFA App and is pending your review and approval.</p>

                        <p>
                        <b>Customer:</b> $customer_name<br>
                        <b>Dealer:</b> $dealer_name ($dealer_code)<br>
                        <b>Product:</b> $product | <b>Qty:</b> $bags Bags<br>
                        <b>Status:</b> $status
                        </p>

                        <p>
                        <b>BDE Name:</b> $bde_name<br>
                        <b>BDE Contact No:</b> $bde_contact
                        </p>
                        <a href='$approve_link'
                            style='background:#28a745;color:#fff;padding:10px 20px;text-decoration:none;border-radius:5px;margin-right:10px;'>
                            Approve
                            </a>

                            <a href='$reject_link'
                            style='background:#dc3545;color:#fff;padding:10px 20px;text-decoration:none;border-radius:5px;'>
                            Reject
                            </a>

                            <br><br>
                        <p style=''>
                        ⚠️ If no action is taken within 48 hours, the lead will be auto-approved as per workflow.
                        </p>

                        <p><b>
                        Please review and approve through the SFA Application.
                        </b></p>

                        <br>
                        <p>
                        Regards,<br>
                        SFA Application<br>
                        Automated Workflow Notification<br>
                        Star Cement Limited
                        </p>
                        ";

                $mail->send();

            } catch (Exception $e) {
                // Optional: log email error
                error_log("ASM Email Failed: " . $mail->ErrorInfo);
            }
        }
    }
}

echo json_encode([
    "process_status" => "Yes",
    "process_message" => "Success",
    "site_id" => $site_id,
    "site_master" => $site_data,
    "site_visit_master" => $visit_data
]);
exit;
