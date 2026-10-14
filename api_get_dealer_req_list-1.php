
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once("sfa_connection.php");

header('Content-Type: application/json');


$localDB = new sfa_connection();
$conn = $localDB->conn;


/*
|--------------------------------------------------------------------------
| Check emp_code
|--------------------------------------------------------------------------
*/
if (!isset($_GET['emp_code'])) {

    echo json_encode([
        'error' => 'emp_code is required'
    ]);

    exit;
}


$emp_Code = trim($_GET['emp_code']);


/*
|--------------------------------------------------------------------------
| Get dns_emp_code from employee_master
|--------------------------------------------------------------------------
*/
$dns_emp_code = '';

 $sql = "SELECT dns_emp_code
        FROM employee_master
        WHERE emp_code = '$emp_Code'
        LIMIT 1";

$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {

    $row = mysqli_fetch_assoc($result);

    $dns_emp_code = $row['dns_emp_code'];
}


/*
|--------------------------------------------------------------------------
| API URL
|--------------------------------------------------------------------------
*/
$apiUrl = SAATHI_URL .
    "/SAP/api_get_all_exclusive_req_emp.php?emp_code=" .
    urlencode($emp_Code) .
    "&dns_emp_code=" .
    urlencode($dns_emp_code);


/*
|--------------------------------------------------------------------------
| CURL Request
|--------------------------------------------------------------------------
*/
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);


/*
|--------------------------------------------------------------------------
| CURL Error Check
|--------------------------------------------------------------------------
*/
if (curl_errno($ch)) {

    echo json_encode([
        'error' => curl_error($ch)
    ]);

    curl_close($ch);
    exit;
}


curl_close($ch);


/*
|--------------------------------------------------------------------------
| Output API Response
|--------------------------------------------------------------------------
*/
echo $response;

exit;

?>

