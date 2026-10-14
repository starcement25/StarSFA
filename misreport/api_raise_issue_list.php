<?php
require_once("../sfa_connection.php");
header('Content-Type: application/json');
date_default_timezone_set('Asia/Kolkata');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$localDB = new sfa_connection();
$conn = $localDB->conn;


$emp_code = '';
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $emp_code = isset($_GET['emp_code']) ? trim($_GET['emp_code']) : '';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emp_code = isset($_POST['emp_code']) ? trim($_POST['emp_code']) : '';
} else {
    http_response_code(405);
    echo json_encode([
        "process_status" => "No",
        "process_message" => "Failed!",
        "error" => "Only GET or POST method is allowed"
    ]);
    exit;
}


if ($emp_code === '') {
    http_response_code(400);
    echo json_encode([
        "process_status" => "No",
        "process_message" => "Failed!",
        "error" => "emp_code is required"
    ]);
    exit;
}


$status = isset($_REQUEST['status']) ? trim($_REQUEST['status']) : '';
$allowed_status = ['pending', 'resolved', 'forwarded'];

$limit  = isset($_REQUEST['limit']) ? (int) $_REQUEST['limit'] : 0;  
$offset = isset($_REQUEST['offset']) ? (int) $_REQUEST['offset'] : 0;


$sql = "SELECT
            id,
            emp_code,
            emp_name,
            branch_code,
            branch_name,
            customer_code,
            customer_name,
            category,
            state,
            district,
            esc_dept,
            issue,
            resolve_reason,
            `status`,
            status_cnange_date,
            created_at,
            updated_at
        FROM raise_issue
        WHERE emp_code = ? ";

$types = "s";
$params = [$emp_code];

if ($status !== '' && in_array($status, $allowed_status)) {
    $sql .= " AND status = ?";
    $types .= "s";
    $params[] = $status;
}

$sql .= " ORDER BY created_at DESC";

if ($limit > 0) {
    $sql .= " LIMIT ? OFFSET ?";
    $types .= "ii";
    $params[] = $limit;
    $params[] = $offset;
}


$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode([
        "process_status" => "No",
        "process_message" => "Failed!",
        "error" => "Prepare failed: " . $conn->error
    ]);
    exit;
}


$bindParams = [];
$bindParams[] = &$types;
foreach ($params as $key => $value) {
    $bindParams[] = &$params[$key];
}
call_user_func_array([$stmt, 'bind_param'], $bindParams);

$stmt->execute();
$result = $stmt->get_result();


$issueList = [];
while ($row = $result->fetch_assoc()) {
    $issueList[] = [
        "id"                  => (int) $row['id'],
        "emp_code"            => $row['emp_code'],
        "emp_name"            => $row['emp_name'],
        "branch_code"         => $row['branch_code'],
        "branch_name"         => $row['branch_name'],
        "customer_code"       => $row['customer_code'],
        "customer_name"       => $row['customer_name'],
        "category"            => $row['category'],
        "state"               => $row['state'],
        "district"            => $row['district'],
        "esc_dept"            => $row['esc_dept'],
        "issue"               =>  $row['issue'],
          "remarks"               =>  $row['resolve_reason'],
        "status"              => $row['status'],
        "status_cnange_date"  => $row['status_cnange_date'],
        "created_at"          => $row['created_at'],
        "updated_at"          => $row['updated_at'],
    ];
}

$stmt->close();


if (count($issueList) > 0) {
    echo json_encode([
        "process_status" => "Yes",
        "process_message" => "Success",
        "total_count" => count($issueList),
        "data" => $issueList
    ]);
} else {
    echo json_encode([
        "process_status" => "Yes",
        "process_message" => "No records found",
        "total_count" => 0,
        "data" => []
    ]);
}

exit;