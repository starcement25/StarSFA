<?php
header("Content-Type: application/json");
require_once("../sfa_connection.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

$localDB = new sfa_connection();
$conn = $localDB->conn;

if(isset($_POST['emp_code']) && isset($_POST['dob']))
{
    $emp_code = mysqli_real_escape_string($conn, $_POST['emp_code']);
    $dob_input = mysqli_real_escape_string($conn, $_POST['dob']); // dd-mm-YYYY

    /* Validate format */
    if(!preg_match("/^\d{2}-\d{2}-\d{4}$/", $dob_input))
    {
        echo json_encode(["status"=>false,"message"=>"Invalid DOB format (dd-mm-YYYY)"]);
        exit;
    }

    $dob_obj = DateTime::createFromFormat('d-m-Y', $dob_input);

    if(!$dob_obj || $dob_obj > new DateTime())
    {
        echo json_encode(["status"=>false,"message"=>"Invalid DOB"]);
        exit;
    }

    $dob_store = $dob_obj->format('Ymd'); // 19980629

    /* Check employee exists */
    $check_sql = "SELECT emp_code FROM employee_master WHERE emp_code='$emp_code'";
    $check = mysqli_query($conn, $check_sql);

    if(mysqli_num_rows($check)==0)
    {
        echo json_encode(["status"=>false,"message"=>"Employee not found"]);
        exit;
    }

    /* Update DOB */
    $update_sql = "
    UPDATE employee_master 
    SET dob='$dob_store' 
    WHERE emp_code='$emp_code'
    ";

    if(mysqli_query($conn, $update_sql))
    {
        echo json_encode([
            "status"=>true,
            "message"=>"DOB Updated Successfully"
        ]);
    }
    else
    {
        echo json_encode([
            "status"=>false,
            "message"=>"Database Error"
        ]);
    }
}
else
{
    echo json_encode(["status"=>false,"message"=>"Required parameters missing"]);
}
?>
