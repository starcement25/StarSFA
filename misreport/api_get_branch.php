<?php

require_once("../sfa_connection.php");
header('Content-Type: application/json');

$localDB = new sfa_connection();
$conn = $localDB->conn;

if (!isset($_GET['route_code'])) {
    $res_data = array("process_status" => "No", "process_message" => "Validation Failed!", 'error' => 'route_code is required');
    echo json_encode($res_data);
    exit;
}

$route = addslashes(trim($_GET['route_code']));


$route_sql = "SELECT * FROM route_master WHERE route_code = '$route' ";
// echo $route_sql;
// die;
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
    $val = $row['branch_code'];


    $branch_sql = "SELECT branch_name, branch_code FROM branch_master WHERE branch_code = '$val'";
    $branch_result = mysqli_query($conn, $branch_sql);

    if (!$branch_result) {
        echo json_encode([
            "process_status" => "No",
            "process_message" => "Branch query failed",
            "error" => mysqli_error($conn)
        ]);
        exit;
    }

    if (mysqli_num_rows($branch_result) > 0) {
        $branch_row[] = mysqli_fetch_assoc($branch_result);
        $branch_name = $branch_row['branch_name'];
        if ($val !== 'B214') {
            $branch_row[] = [
                "branch_name" => "GUWAHATI URBAN",
                "branch_code" => "B214"
            ];
        }
        echo json_encode([
            "process_status" => "Yes",
            "process_message" => "Success!",
            "value" => $branch_row
        ]);
    } else {
        echo json_encode([
            "process_status" => "No",
            "process_message" => "Branch not found for code $branch_code"
        ]);
    }
} else {
    echo json_encode([
        "process_status" => "No",
        "process_message" => "Failed!",
        "error" => "Not Found!"
    ]);
}
