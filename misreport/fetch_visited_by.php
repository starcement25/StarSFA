<?php
require_once("../sfa_connection.php");
$localDB = new sfa_connection();
$conn = $localDB->conn;

$term = isset($_GET['term']) ? trim($_GET['term']) : '';
$data = [];

if (!empty($term)) {
    $stmt = $conn->prepare("
        SELECT emp_code, emp_name 
        FROM employee_master 
        WHERE emp_name LIKE ? OR emp_code LIKE ? 
        ORDER BY emp_name ASC 
        LIMIT 10
    ");

    $search = "%$term%";
    $stmt->bind_param("ss", $search, $search); 
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'label' => $row['emp_name'] . ' (' . $row['emp_code'] . ')', 
            'value' => $row['emp_code'] 
        ];
    }
}
echo json_encode($data);
?>
