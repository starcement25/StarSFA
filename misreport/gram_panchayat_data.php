```php
<?php
date_default_timezone_set('Asia/Kolkata');

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

header("Content-Type: text/plain");

require_once("../sfa_connection.php");

$localDB = new sfa_connection();
$conn = $localDB->conn;

/*
    Table Structure:

    gram_panchayat_data
    -------------------
    sr_no
    state_ut
    district
    block_name
    gram_panchayat
*/

$sql = "SELECT 
            sr_no,
            state_ut,
            district,
            block_name,
            gram_panchayat
        FROM gram_panchayat_data";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query Failed : " . mysqli_error($conn));
}

$data = array();

/*
    Store Added Municipality
    Unique By:
    state_ut + district + block_name
*/
$municipality_added = array();

while ($row = mysqli_fetch_assoc($result)) {

    /*
        Original Gram Panchayat Data
    */
    $line = $row['sr_no'] . "^" .
            $row['state_ut'] . "^" .
            $row['district'] . "^" .
            $row['block_name'] . "^" .
            $row['gram_panchayat'];

    $data[] = $line;

    /*
        Create Unique Key
    */
    $unique_key = trim($row['state_ut']) . "|" .
                  trim($row['district']) . "|" .
                  trim($row['block_name']);

    /*
        Add Municipality Only One Time
        Per state_ut + district + block_name
    */
    if (!isset($municipality_added[$unique_key])) {

        $municipality = $row['sr_no'] . "^" .
                        $row['state_ut'] . "^" .
                        $row['district'] . "^" .
                        $row['block_name'] . "^Municipal corporation";

        $data[] = $municipality;

        $municipality_added[$unique_key] = true;
    }
}

/*
    Total Count
*/
echo count($data) . "¥5\n";

/*
    Final Output
*/
echo implode("\n", $data);

mysqli_close($conn);
?>
