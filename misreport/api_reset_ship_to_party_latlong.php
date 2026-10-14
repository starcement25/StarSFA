<?php
session_start();

if (strtoupper($_SESSION['admin_login'] ?? '') == 'ACCOUNTS' && strtoupper($_SESSION['nick_name'] ?? '') == 'STAR') {
    require("adminUtils_accounts.php");
} else {
    require("adminUtils.php");
}
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require_once("../sfa_connection.php");

header("Content-Type: application/json");

// Basic session gate — reject if no admin session is present at all.
if (empty($_SESSION['admin_login'])) {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Not authorized."]);
    exit;
}

$localDB = new sfa_connection();
$conn = $localDB->conn;

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

if ($id <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid record id."]);
    exit;
}

$sql  = "UPDATE ship_to_party_location SET latitude = 0, longitude = 0, updated_at = NOW() WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    echo json_encode(["success" => false, "message" => mysqli_error($conn)]);
    exit;
}

mysqli_stmt_bind_param($stmt, 'i', $id);
$ok = mysqli_stmt_execute($stmt);

if ($ok) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => mysqli_error($conn)]);
}
mysqli_stmt_close($stmt);