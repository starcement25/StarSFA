<?php
ob_clean(); 
ob_start();
if (ob_get_level()) { ob_end_clean(); }
ob_start();
header_remove();

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
header('Content-Type: text/plain; charset=UTF-8');
require_once("../sfa_connection.php");
$localDB = new sfa_connection();
$conn = $localDB->conn;

$route_sql = "SELECT value FROM table_view_mle WHERE row_id = 'SC017'";
$route_result = mysqli_query($conn, $route_sql);

if (!$route_result) {
    echo "Query Failed: " . mysqli_error($conn);
    exit;
}
$count = mysqli_num_rows($route_result);
$contentsrowcolumn = trim($count . '¥' . '1');

if ($count > 0) {
   
    $datetime = gmdate('Y-m-d€H:i:s', strtotime('+330 minute'));

    $linecontents = '';

    while ($row = mysqli_fetch_assoc($route_result)) {
        $val = preg_replace('/\s+/', ' ', trim($row['value']));
        $states = explode('/', $val);

        foreach ($states as $state) {
            $state = trim($state);
            if ($state !== '') {
                $linecontents .= $state . "\n";
            }
        }
    }

    
    $linecontents = trim($linecontents);


    $datacontents = $contentsrowcolumn. "\n" . $linecontents;

    echo $datacontents;
} else {
    echo "No Data Found";
}
?>
