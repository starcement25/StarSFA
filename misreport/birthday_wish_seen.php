<?php
header("Content-Type: application/json");
require_once("../sfa_connection.php");

$localDB = new sfa_connection();
$conn = $localDB->conn;

if(isset($_POST['emp_code']))
{
    $emp_code = mysqli_real_escape_string($conn, $_POST['emp_code']);

    $today = date("Y-m-d");
    $current_year = date("Y");

    $sql = "
    INSERT INTO birthday_wish_seen_log
    (emp_code, seen_date, seen_year)
    VALUES ('$emp_code', '$today', '$current_year')
    ON DUPLICATE KEY UPDATE seen_date='$today'
    ";

    if(mysqli_query($conn, $sql))
    {
        echo json_encode([
            "status"=>true,
            "message"=>"Birthday marked as seen"
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
    echo json_encode([
        "status"=>false,
        "message"=>"emp_code required"
    ]);
}
?>
