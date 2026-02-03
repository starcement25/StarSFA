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
$data = json_decode(file_get_contents("php://input"), true);
$created_at = date('Y-m-d H:i:s');
$emp_code = $data['employee_code'] ?? 'NA';
// echo $emp_code;die;
$start_content =  json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$created_at = date('Y-m-d H:i:s');

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
    'longitude',
    'latitude',
    'customer_name',
    'customer_contact_number',
    'customer_full_address',
    'site_segment',
    'visit_type',
    'project_segment',
    'type_of_construction',
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
}

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

echo json_encode([
    "process_status" => "Yes",
    "process_message" => "Success",
    "site_id" => $site_id,
    "site_master" => $site_data,
    "site_visit_master" => $visit_data
]);
exit;
