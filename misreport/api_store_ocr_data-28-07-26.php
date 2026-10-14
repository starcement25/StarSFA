<?php
require_once("../sfa_connection.php");
header('Content-Type: application/json');
date_default_timezone_set('Asia/Kolkata');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$localDB = new sfa_connection();
$conn = $localDB->conn;

/* ================= ALLOW ONLY POST ================= */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        "process_status"  => "No",
        "process_message" => "Failed!",
        "error"           => "Only POST method is allowed"
    ]);
    exit;
}

/* ================= READ JSON BODY ================= */
$data = json_decode(file_get_contents("php://input"), true);

if (!is_array($data)) {
    http_response_code(400);
    echo json_encode([
        "process_status"  => "No",
        "process_message" => "Failed!",
        "error"           => "Invalid or empty JSON body"
    ]);
    exit;
}

/* ================= MANDATORY FIELDS ================= */
$required = ['employee_code', 'dealer_code', 'branch_code'];

foreach ($required as $field) {
    if (!isset($data[$field]) || trim($data[$field]) === '') {
        http_response_code(400);
        echo json_encode([
            "process_status"  => "No",
            "process_message" => "Failed!",
            "error"           => "$field is required"
        ]);
        exit;
    }
}

/* ================= HELPER ================= */
function escapeOrNull($conn, $value)
{
    if (is_null($value) || trim($value) === '' || strtolower(trim($value)) === 'null') {
        return "NULL";
    }
    return "'" . mysqli_real_escape_string($conn, $value) . "'";
}

$created_at = date('Y-m-d H:i:s');

/* ================= MAIN RECORD VALUES ================= */
$m_employee_code  = escapeOrNull($conn, $data['employee_code']  ?? null);
$m_dealer_code    = escapeOrNull($conn, $data['dealer_code']    ?? null);
$m_branch_code    = escapeOrNull($conn, $data['branch_code']    ?? null);
$m_address        = escapeOrNull($conn, $data['address']        ?? null);
$m_tech_meet_type = escapeOrNull($conn, $data['tech_meet_type'] ?? null);
$m_date           = escapeOrNull($conn, $data['date']           ?? null);

/* ================= INSERT (TRANSACTION) ================= */
mysqli_begin_transaction($conn);

$insert_main_sql = "INSERT INTO ocr_main_data
    (employee_code, dealer_code, branch_code, address, tech_meet_type, date, created_at)
    VALUES
    ($m_employee_code, $m_dealer_code, $m_branch_code, $m_address, $m_tech_meet_type, $m_date, '$created_at')";

if (!mysqli_query($conn, $insert_main_sql)) {
    mysqli_rollback($conn);
    http_response_code(500);
    echo json_encode([
        "process_status"  => "No",
        "process_message" => "Failed!",
        "error"           => "Insert ocr_main_data failed",
        "db_error"        => mysqli_error($conn)
    ]);
    exit;
}

$ocr_main_data_id = mysqli_insert_id($conn);

/* ================= PARTICIPANTS (MULTIPLE) ================= */
$participants        = isset($data['participants']) && is_array($data['participants']) ? $data['participants'] : [];
$participants_saved  = 0;

foreach ($participants as $p) {

    if (!is_array($p)) continue;

    // skip fully empty rows
    $has_value = false;
    foreach (['participant_name', 'address', 'contact_no', 'ilp_registered', 'date'] as $k) {
        if (isset($p[$k]) && trim($p[$k]) !== '') { $has_value = true; break; }
    }
    if (!$has_value) continue;

    $p_name       = escapeOrNull($conn, $p['participant_name'] ?? null);
    $p_address    = escapeOrNull($conn, $p['address']          ?? null);
    $p_contact    = escapeOrNull($conn, $p['contact_no']       ?? null);
    $p_ilp        = escapeOrNull($conn, $p['ilp_registered']   ?? null);
    $p_date       = escapeOrNull($conn, $p['date']             ?? null);

    $insert_part_sql = "INSERT INTO ocr_participants_data
        (ocr_main_data_id, participant_name, address, contact_no, ilp_registered, date, created_at)
        VALUES
        (" . intval($ocr_main_data_id) . ", $p_name, $p_address, $p_contact, $p_ilp, $p_date, '$created_at')";

    if (!mysqli_query($conn, $insert_part_sql)) {
        mysqli_rollback($conn);
        http_response_code(500);
        echo json_encode([
            "process_status"  => "No",
            "process_message" => "Failed!",
            "error"           => "Insert ocr_participants_data failed",
            "db_error"        => mysqli_error($conn)
        ]);
        exit;
    }

    $participants_saved++;
}

mysqli_commit($conn);

/* ================= RESPONSE ================= */
echo json_encode([
    "process_status"     => "Yes",
    "process_message"    => "Success",
    "ocr_main_data_id"   => $ocr_main_data_id,
    "participants_saved" => $participants_saved
]);

exit;
