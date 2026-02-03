<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once("../sfa_connection.php");
header('Content-Type: application/json');

$localDB = new sfa_connection();
$conn = $localDB->conn;

// if (!isset($_GET['state'])) {
//     $res_data = array("process_status" => "No", "process_message" => "Validation Failed!", 'error' => 'State is required');
//     echo json_encode($res_data);
//     exit;
// }

$route_sql = "SELECT value, dependent_value FROM table_view_mle WHERE row_id = 'SC018'";
$route_result = mysqli_query($conn, $route_sql);

if (!$route_result) {
    echo json_encode([
        "process_status" => "No",
        "process_message" => "Query Failed",
        "error" => mysqli_error($conn)
    ]);
    exit;
}

// $districtArray = [];

// while ($row = mysqli_fetch_assoc($route_result)) {
//     $state = $row['dependent_value'];
//     $districts = explode('/', $row['value']);

//     foreach ($districts as $district) {
//         $districtArray[] = [
//             'state' => $state,
//             'district_name' => trim($district)
//         ];
//     }
// }

// if (!empty($districtArray)) {
//     echo json_encode([
//         "process_status" => "Yes",
//         "process_message" => "Districts fetched successfully",
//         "value" => $districtArray
//     ]);
// } else {
//     echo json_encode([
//         "process_status" => "No",
//         "process_message" => "No districts found",
//         "value" => []
//     ]);
// }


$count = mysqli_num_rows($route_result);
$contentsrowcolumn = trim($count . '¥' . '2');

if ($count > 0) {
   
    $datetime = gmdate('Y-m-d€H:i:s', strtotime('+330 minute'));

    $linecontents = '';

    while ($row = mysqli_fetch_assoc($route_result)) {
        $val = preg_replace('/\s+/', ' ', trim($row['value']));
        $districts = explode('/', $val);
$state = trim($row['dependent_value']);
        foreach ($districts as $district) {
            $district = trim($district);
            if ($district !== '') {
                 $linecontents .= $district . '^' . $state . "\n";
            }
        }
    }

    
    $linecontents = trim($linecontents);


    $datacontents = $contentsrowcolumn ."\n" .$linecontents;

    echo $datacontents;
} else {
    echo "No Data Found";
}
