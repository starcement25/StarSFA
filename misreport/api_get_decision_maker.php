<?php

require_once("../sfa_connection.php");
header('Content-Type: application/json');

$localDB = new sfa_connection();
$conn = $localDB->conn;



$route_sql = "SELECT value FROM  table_view_mle WHERE row_id ='RA185'";
$route_result = mysqli_query($conn, $route_sql);
if (!$route_result) {
    echo json_encode([
        "process_status" => "No",
        "process_message" => "Query Failed",
        "error" => mysqli_error($conn)
    ]);
    exit;
}

if (mysqli_num_rows($route_result) > 0) {
    $row = mysqli_fetch_assoc($route_result);
    $val = $row['value'];
  $projectArray = array_map(function ($project) {
        return ['decision_maker' => trim($project)];
    }, explode('/', $val));

    echo json_encode([
        "process_status" => "Yes",
        "process_message" => "Value found",
        "value" => $projectArray
    ]);
} else {
    echo json_encode([
        "process_status" => "No",
        "process_message" => "Failed!",
        "error" => "Not Found!"
    ]);
}