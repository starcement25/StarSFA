<?php

//require_once("../sfa_connection.php");
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

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$localDB = new sfa_connection();
$conn = $localDB->conn;


mysqli_begin_transaction($conn);

//$sql = "UPDATE new_site_lead_visit_master SET approval_status = 'approved', approval_type = 1,  approval_date_time = NOW() WHERE approval_status = 'pending' AND TIMESTAMPDIFF(HOUR, created_at, NOW()) > 48";
$sql = "UPDATE new_site_lead_visit_master SET approval_status = 'approved (auto)', approval_type = 1, approved_auto= 1, approval_date_time = NOW() WHERE approval_status = 'pending' AND TIMESTAMPDIFF(HOUR, created_at, NOW()) > 48";
if (!mysqli_query($conn, $sql)) {
    mysqli_rollback($conn);
    echo "Auto-approval failed: " . mysqli_error($conn);
    exit;
}

$affectedRows = mysqli_affected_rows($conn);
mysqli_commit($conn);

echo date('Y-m-d H:i:s') . " | Auto-approved visits: $affectedRows\n";
