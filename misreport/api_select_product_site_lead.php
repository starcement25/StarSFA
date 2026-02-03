<?php
ob_clean(); // clear any accidental output buffer
ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: text/plain; charset=UTF-8');
require_once("../sfa_connection.php");
$localDB = new sfa_connection();
$conn = $localDB->conn;


$route_sql = "SELECT value FROM table_view_mle WHERE row_id = 'SC016'";
$route_result = mysqli_query($conn, $route_sql);

if (!$route_result) {
    echo "Query Failed: " . mysqli_error($conn);
    exit;
}

$count = mysqli_num_rows($route_result);
$contentsrowcolumn = trim($count . '¥' . '3');

if ($count > 0) {
   
    $datetime = gmdate('Y-m-d€H:i:s', strtotime('+330 minute'));

    $linecontents = '';

    while ($row = mysqli_fetch_assoc($route_result)) {
        $val = preg_replace('/\s+/', ' ', trim($row['value']));
        $persons = explode('/', $val);

        foreach ($persons as $person) {
            $person = trim($person);
            if ($person !== '') {
                $linecontents .= $person . "\n";
            }
        }
    }

    
    $linecontents = trim($linecontents);


    $datacontents = $contentsrowcolumn .  "\n" . $linecontents;

  $datacontents = str_replace('#', '^', $datacontents);
echo $datacontents;
} else {
    echo "No Data Found";
}
?>
