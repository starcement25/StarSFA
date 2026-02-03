<?php
include "star_connection.php";

$employee_master='employee_master';
$competitor_group_master="competitor_group_master";

$emp_code = $_REQUEST["emp_code"] ? addslashes(trim($_REQUEST["emp_code"])) : "";

$branch_code = $_REQUEST["branch_code"] ? addslashes(trim($_REQUEST["branch_code"])) : "";

$sql = "select * from $employee_master where `emp_code`='$emp_code'";

$query=mysqli_query($link,$sql);

$result=mysqli_fetch_assoc($query);

$new_branch_codes=$result['branch_code'];

$sql1="select * from $competitor_group_master where `branch_code`='$new_branch_codes'";

$query1=mysqli_query($link,$sql1);

$competitor_names = array();

while ($row = mysqli_fetch_assoc($query1)) {
    $competitor_names[] = $row['competitor_name'];
}

if (!empty($competitor_names)) {
    $res_data = array(
        "process_status" => "YES",
        "process_message" => "The Competitor names successfully received.",
        "competitor_names" => $competitor_names
    );
} else {
    $res_data = array("process_status" => "NO", "process_message" => "Failed to retrieve competitor data.");
}

echo json_encode($res_data);

mysqli_close();

?>

