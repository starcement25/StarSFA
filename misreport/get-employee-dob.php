<?php
header("Content-Type: application/json");
date_default_timezone_set('Asia/Kolkata');
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once("../sfa_connection.php");
$localDB = new sfa_connection();
$conn = $localDB->conn;
$emp_code = isset($_GET['emp_code']) ? trim($_GET['emp_code']) : '';
//echo"<pre>";print_r($emp_code);die;

if ($emp_code == '') {
    echo json_encode(["status"=>false,"message"=>"Employee Code required"]);
    exit;
}

/* ===============================
   GET EMPLOYEE DATA
=================================*/
$sql = "
SELECT emp_code, emp_name, dob 
FROM employee_master 
WHERE emp_code = '$emp_code'
LIMIT 1
";

$data = mysqli_query($conn,$sql);

if(mysqli_num_rows($data)==0){
    echo json_encode(["status"=>false,"message"=>"Employee not found"]);
    exit;
}

$row = mysqli_fetch_assoc($data);

/* ===============================
   VALIDATE DOB
=================================*/
if(empty($row['dob']) || $row['dob']=='00000000' || strlen($row['dob'])!=8){
    echo json_encode(["status"=>false,"message"=>"Birthday Not Found"]);
    exit;
}

/* ===============================
   CHECK TODAY BIRTHDAY
=================================*/
$today_md = date("md");
$emp_md   = date("md", strtotime($row['dob']));

if($today_md != $emp_md){
    echo json_encode(["status"=>false,"message"=>"Today is not birthday"]);
    exit;
}

/* ===============================
   CHECK SEEN STATUS
=================================*/
$current_year = date("Y");

$seen_sql = "
SELECT id FROM birthday_wish_seen_log 
WHERE emp_code='$emp_code' 
AND seen_year='$current_year'
";

$seen_data = mysqli_query($conn,$seen_sql);

if(mysqli_num_rows($seen_data)>0){
    echo json_encode(["status"=>false,"message"=>"Birthday already shown"]);
    exit;
}

/* ===============================
   GET ACTIVE BIRTHDAY CONTENT
=================================*/
$content_sql = "
SELECT type, title, message, img 
FROM birthday_master 
WHERE status = 1 
ORDER BY id DESC 
LIMIT 1
";

$content_data = mysqli_query($conn,$content_sql);

if(mysqli_num_rows($content_data)==0){
    echo json_encode(["status"=>false,"message"=>"Birthday content not configured"]);
    exit;
}

$content = mysqli_fetch_assoc($content_data);

/* ===============================
   FORMAT DOB
=================================*/
$dob_formatted = date("d-m-Y", strtotime($row['dob']));

/* ===============================
   REPLACE DYNAMIC VARIABLE
=================================*/
$message = str_replace(
    "{{employee_name}}",
    $row['emp_name'],
    $content['message']
);

/* ===============================
   FINAL RESPONSE
=================================*/
echo json_encode([
    "status"=>true,
    "emp_code"=>$row['emp_code'],
    "emp_name"=>$row['emp_name'],
    "type"=>$content['type'],
    "title"=>$content['title'],
    "message"=>$message,
    "img"=>$content['img'],
    "dob"=>$dob_formatted
]);
