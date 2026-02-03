<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once("../sfa_connection.php");
header('Content-Type: application/json');

$localDB = new sfa_connection();
$conn = $localDB->conn; 

if (!$conn) {
    echo json_encode([
        "process_status" => "No",
        "process_message" => "Database connection failed"
    ]);
    exit;
}


if (!isset($_GET['asm_id'])) {
    echo json_encode([
        "process_status" => "No",
        "process_message" => "Validation Failed!",
        "error" => "ASM Code is required"
    ]);
    exit;
}

$emp_code = trim($_GET['asm_id']);

$stmt = $conn->prepare("
    SELECT m.*, v.*
    FROM new_site_lead_master AS m
    LEFT JOIN new_site_lead_visit_master AS v 
        ON v.new_site_lead_id = m.id
        AND v.id = (
            SELECT vv.id 
            FROM new_site_lead_visit_master vv
            WHERE vv.new_site_lead_id = m.id
            ORDER BY vv.id DESC 
            LIMIT 1
        )
    WHERE v.asm_id = ?
");
$stmt->bind_param("s", $emp_code);
$stmt->execute();
$result = $stmt->get_result();
$total_rows = $result->num_rows;
if ($total_rows == 0) {
    echo "0¥0";
    exit;
}

$rows_output = [];
$columns = [];
$total_cols = 0;

while ($row = $result->fetch_assoc()) {
    if (empty($columns)) {
        $columns = array_keys($row);
        $total_cols = count($columns);
    }
    $values = array_map(function($v) {
        return $v === null ? '' : $v;
    }, $row);
    $rows_output[] = implode('^', $values);
}


echo $total_rows . "¥" . $total_cols . "\n";


echo implode('#', $columns) . "\n";

echo implode("\n", $rows_output) . "\n";

exit;
?>
