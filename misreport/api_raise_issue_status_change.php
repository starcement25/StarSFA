<?php
require_once("../sfa_connection.php");

header('Content-Type: application/json');
date_default_timezone_set('Asia/Kolkata');
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
        "error" => "Only POST method is allowed"
    ]);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);

if (json_last_error() !== JSON_ERROR_NONE || empty($input)) {
    $input = $_POST;
}

$id     = isset($input['id']) ? (int) $input['id'] : 0;
$status = isset($input['status']) ? trim($input['status']) : '';

if ($id <= 0) {
    http_response_code(400);
    echo json_encode([
        "process_status" => "No",
        "process_message" => "Failed!",
        "error" => "id is required"
    ]);
    exit;
}

if ($status === '') {
    http_response_code(400);
    echo json_encode([
        "process_status" => "No",
        "process_message" => "Failed!",
        "error" => "status is required"
    ]);
    exit;
}

$allowed_status = ['pending', 'resolved', 'forwarded','closed'];
if (!in_array($status, $allowed_status)) {
    http_response_code(400);
    echo json_encode([
        "process_status" => "No",
        "process_message" => "Failed!",
        "error" => "Invalid status value. Allowed values: " . implode(', ', $allowed_status)
    ]);
    exit;
}

// ======================================
// CHECK: issue exists
// ======================================

$stmt = $conn->prepare("SELECT id, status FROM raise_issue WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$stmt->close();

if (!$row) {
    http_response_code(404);
    echo json_encode([
        "process_status" => "No",
        "process_message" => "Failed!",
        "error" => "No issue found with id $id"
    ]);
    exit;
}

// ======================================
// UPDATE status
// ======================================

$now = date('Y-m-d H:i:s');

$stmt = $conn->prepare("
    UPDATE raise_issue
    SET status = ?,
        status_cnange_date = ?,
        updated_at = ?
    WHERE id = ?
");
$stmt->bind_param("sssi", $status, $now, $now, $id);

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode([
        "process_status" => "No",
        "process_message" => "Failed!",
        "error" => "Update failed: " . $stmt->error
    ]);
    $stmt->close();
    exit;
}

$stmt->close();

echo json_encode([
    "process_status" => "Yes",
    "process_message" => "Success",
    "data" => [
        "id" => $id,
        "old_status" => $row['status'],
        "new_status" => $status,
        "updated_at" => $now
    ]
]);

exit;