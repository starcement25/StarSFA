<?php
require_once("../sfa_connection.php");
$localDB = new sfa_connection();
$conn = $localDB->conn;

$term = isset($_GET['term']) ? trim($_GET['term']) : '';
$data = [];

if (!empty($term)) {
    $stmt = $conn->prepare("SELECT route_code, route_name FROM route_master WHERE route_name LIKE ? ORDER BY route_name ASC LIMIT 10");
    $search = "%$term%";
    $stmt->bind_param("s", $search);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'label' => $row['route_name'],
            'value' => $row['route_code']
        ];
    }
}
echo json_encode($data);
?>
