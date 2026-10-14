<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");

$emp_code = $_REQUEST['emp_code'];

$sql = "SELECT level 
        FROM employee_master 
        WHERE emp_code = '$emp_code' 
        LIMIT 1";

$rsemphierarchy = mysqli_query($link, $sql);

if ($rsemphierarchy && mysqli_num_rows($rsemphierarchy) > 0) {

    $rowemp = mysqli_fetch_assoc($rsemphierarchy);

    echo json_encode(array(
        "status" => true,
        "emp_code" => $emp_code,
        "level" => $rowemp['level']
    ));

} else {

    echo json_encode(array(
        "status" => false,
        "emp_code" => $emp_code,
        "level" => null,
        "message" => "Employee not found"
    ));
}
?>