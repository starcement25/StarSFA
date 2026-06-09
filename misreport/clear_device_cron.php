<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
date_default_timezone_set('Asia/Kolkata');
$cur_time=date('Y-m-d H:i:s');
class sfa_connection {
    public $conn;
    public $host;
    public $user;
    public $password;
    public $database;

    public function __construct() {
        $this->host = "localhost";
        $this->user = "root";
        $this->password = "Passw0rd123#$";
        $this->database = "acedns_STAR";

        $this->conn = mysqli_connect($this->host, $this->user, $this->password, $this->database);
        if (!$this->conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
    }
}

$localDB = new sfa_connection();
$conn = $localDB->conn;

// Allowed emp_codes
$emp_codes = array(
    'E0807','E0174','E2245','E2416','E2145','E2402',
    'E1114','E2116','E2341','E2290','E1720','E2447',
    'E2140','E1997','E2456','E2121','E2132'
);

// Convert array to comma separated quoted string
$emp_codes_list = "'" . implode("','", $emp_codes) . "'";

mysqli_begin_transaction($conn);

try {

    // Step 1: Store old values in log table
    $log_sql = "INSERT INTO device_clear_log (emp_code, old_deviceid, old_registrationid, cleared_at)
                SELECT emp_code, deviceid, registrationid, '$cur_time'
                FROM changepassword
                WHERE emp_code IN ($emp_codes_list)";

    mysqli_query($conn, $log_sql);

    // Step 2: Update table
    $update_sql = "UPDATE changepassword 
                   SET deviceid = '', registrationid = '' 
                   WHERE emp_code IN ($emp_codes_list)";

    mysqli_query($conn, $update_sql);

    mysqli_commit($conn);

    echo date("Y-m-d H:i:s") . " - Device Cleared Successfully\n";

} catch (Exception $e) {

    mysqli_rollback($conn);
    echo "Error: " . $e->getMessage();
}

mysqli_close($conn);
?>