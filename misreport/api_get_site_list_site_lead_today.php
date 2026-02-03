<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once("../sfa_connection.php");

// Output plain text
header('Content-Type: text/plain');

$localDB = new sfa_connection();
$conn = $localDB->conn;

if (!$conn) {
    echo "0¥0";
    exit;
}

if (!isset($_GET['emp_code'])) {
    echo "0¥0";
    exit;
}

$emp_code = trim($_GET['emp_code']);
$today= date('Y-m-d');
$stmt = $conn->prepare("
   SELECT m.*, v.*
    FROM new_site_lead_master AS m
    INNER JOIN new_site_lead_visit_master AS v 
        ON v.new_site_lead_id = m.id
       AND DATE(v.created_at) = ?
    WHERE m.emp_code = ?
    ORDER BY v.id DESC
");
$stmt->bind_param("ss", $today, $emp_code); 
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
