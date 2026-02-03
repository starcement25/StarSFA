<?php
require_once("../sfa_connection.php");
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$localDB = new sfa_connection();
$conn = $localDB->conn;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        "process_status" => "No",
        "process_message" => "Failed!",
        'error' => 'Only POST method is allowed'
    ]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$required = ['route_code', 'cust_name', 'meeting_person_type', 'meeting_person_phone', 'site_name', 'cust_phone', 'address', 'site_segment', 'project_segment', 'type_of_construction', 'site_potential', 'construction_stage', 'cement_brand', 'estimated_req', 'built_up_area', 'visit_type', 'visit_sub_type','latitude','longitude'];

foreach ($required as $field) {
    if (empty($data[$field])) {
        http_response_code(400);
        echo json_encode([
            "process_status" => "No",
            "process_message" => "Failed!",
            'error' => "$field is required"
        ]);
        exit;
    }

    if (in_array($field, ['cust_phone', 'meeting_person_phone'])) {
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

$date_of_delivery = null;
if (isset($data['date_of_delivery']) && strtolower(trim($data['date_of_delivery'])) !== 'null' && $data['date_of_delivery'] !== '') {
    $date = DateTime::createFromFormat('Y-m-d', $data['date_of_delivery']);
    if (!$date || $date->format('Y-m-d') !== $data['date_of_delivery']) {
        http_response_code(400);
        echo json_encode([
            "process_status" => "No",
            "process_message" => "Failed!",
            "error" => "Invalid date format in date_of_delivery. Expected format: YYYY-MM-DD"
        ]);
        exit;
    }
    $date_of_delivery = $date->format('Y-m-d');
}

function escapeOrNull($conn, $value) {
    if (is_null($value) || trim($value) === '' || strtolower(trim($value)) === 'null') {
        return "NULL";
    }
    return "'" . mysqli_real_escape_string($conn, $value) . "'";
}

// Assign raw and escaped variables
$fields = [
    'route_code', 'cust_name', 'meeting_person_type', 'meeting_person_phone', 'site_name', 'cust_phone', 'address',
    'site_segment', 'project_segment', 'type_of_construction', 'site_potential', 'construction_stage', 'cement_brand',
    'estimated_req', 'built_up_area', 'visit_type', 'visit_sub_type', 'latitude', 'longitude', 'branch_code',
    'state', 'district', 'contractor_name', 'contractor_phone', 'engineer_name', 'engineer_phone',
    'engg_reg_star_stellar', 'price_per_bag', 'consumed_till_date', 'decision_maker', 'product_demo',
    'remarks', 'bags_ordered', 'rssd', 'approved_by', 'visited_by'
];

foreach ($fields as $field) {
    ${$field} = isset($data[$field]) ? $data[$field] : null;
    ${"escaped_$field"} = escapeOrNull($conn, ${$field});
}

// Check for existing site
$site_sql = "SELECT * FROM site_master WHERE route_code = {$escaped_route_code} AND cust_phone = {$escaped_cust_phone}";
$site_result = mysqli_query($conn, $site_sql);

if (!$site_result || mysqli_num_rows($site_result) == 0) {
    mysqli_begin_transaction($conn);
    $count_sql = "SELECT COUNT(*) AS site_count FROM site_master WHERE cust_phone = {$escaped_cust_phone}";
    $count_result = mysqli_query($conn, $count_sql);
    $count_row = mysqli_fetch_assoc($count_result);
    $site_number = (int)$count_row['site_count'] + 1;
    $site_code = $cust_phone . '-' . $site_number;

    $insert_site_sql = "INSERT INTO site_master (site_code, route_code, cust_phone, cust_name, site_name, address, branch_code, state, district, latitude, longitude)
        VALUES ('$site_code', {$escaped_route_code}, {$escaped_cust_phone}, {$escaped_cust_name}, {$escaped_site_name}, {$escaped_address}, {$escaped_branch_code}, {$escaped_state}, {$escaped_district}, {$escaped_latitude}, {$escaped_longitude})";

    if (!mysqli_query($conn, $insert_site_sql)) {
        mysqli_rollback($conn);
        echo json_encode(["process_status" => "No", "process_message" => "Insert site_master failed", "error" => mysqli_error($conn)]);
        exit;
    }
    $site_id = mysqli_insert_id($conn);
} else {
    $existing_site = mysqli_fetch_assoc($site_result);
    $site_id = $existing_site['id'];
    $site_code = $existing_site['site_code'];
    mysqli_begin_transaction($conn);
    $update_sql = "UPDATE site_master SET cust_name = {$escaped_cust_name}, address = {$escaped_address}, branch_code = {$escaped_branch_code}, state = {$escaped_state}, district = {$escaped_district}, latitude = {$escaped_latitude}, longitude = {$escaped_longitude} WHERE id = '$site_id'";
    if (!mysqli_query($conn, $update_sql)) {
        mysqli_rollback($conn);
        echo json_encode(["process_status" => "No", "process_message" => "Update site_master failed", "error" => mysqli_error($conn)]);
        exit;
    }
}

$insert_visit_sql = "INSERT INTO site_visit_master (
    site_id, site_code, meeting_person_type, meeting_person_phone, site_segment, project_segment,
    type_of_construction, site_potential, cement_brand, estimated_req, built_up_area, visit_type, visit_sub_type,
    contractor_name, contractor_phone, engineer_name, engineer_phone, engg_reg_star_stellar, construction_stage,
    price_per_bag, consumed_till_date, decision_maker, product_demo, remarks, date_of_delivery,
    bags_ordered, rssd, approved_by, visited_by
) VALUES (
    '$site_id', '$site_code', {$escaped_meeting_person_type}, {$escaped_meeting_person_phone}, {$escaped_site_segment}, {$escaped_project_segment},
    {$escaped_type_of_construction}, {$escaped_site_potential}, {$escaped_cement_brand}, {$escaped_estimated_req}, {$escaped_built_up_area},
    {$escaped_visit_type}, {$escaped_visit_sub_type}, {$escaped_contractor_name}, {$escaped_contractor_phone}, {$escaped_engineer_name}, {$escaped_engineer_phone},
    {$escaped_engg_reg_star_stellar}, {$escaped_construction_stage}, {$escaped_price_per_bag}, {$escaped_consumed_till_date},
    {$escaped_decision_maker}, {$escaped_product_demo}, {$escaped_remarks}, " . ($date_of_delivery ? "'$date_of_delivery'" : "NULL") . ",
    {$escaped_bags_ordered}, {$escaped_rssd}, {$escaped_approved_by}, {$escaped_visited_by}
)";

if (!mysqli_query($conn, $insert_visit_sql)) {
    mysqli_rollback($conn);
    echo json_encode(["process_status" => "No", "process_message" => "Insert site_visit_master failed", "error" => mysqli_error($conn)]);
    exit;
}

mysqli_commit($conn);
$site_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM site_master WHERE id = '$site_id'"));
$visit_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM site_visit_master WHERE site_id = '$site_id' ORDER BY id DESC LIMIT 1"));

echo json_encode([
    "process_status" => "Yes",
    "process_message" => "Success",
    "site_id" => $site_id,
    "site_master" => $site_data,
    "site_visit_master" => $visit_data
]);
exit;
