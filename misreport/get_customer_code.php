<?php

header('Content-Type: application/json');

require_once("../sfa_connection.php");

$localDB = new sfa_connection();
$conn = $localDB->conn;
$response=array();

if(empty($_REQUEST['customer_code'])){
    $response['process_status']="NO";
    $response['process_message']="customer_code required";
    echo json_encode($response);
    exit;
}

$customer_code=mysqli_real_escape_string($conn,$_REQUEST['customer_code']);

$sql="
SELECT
customer_code,
SAP_customer_code
FROM customer_master
WHERE customer_code='$customer_code'
LIMIT 1
";

$res=mysqli_query($conn,$sql);

if(mysqli_num_rows($res)==0){

    echo json_encode(array(
        "process_status"=>"NO",
        "process_message"=>"Customer not found"
    ));
    exit;
}

$row=mysqli_fetch_assoc($res);

$sap_customer_code=$row['SAP_customer_code'];

$url="https://starsaathi.com/SAP/get_customer_code_by_sap.php?customer_id=".$sap_customer_code;

$ch=curl_init();

curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);

$result=curl_exec($ch);

curl_close($ch);

$saathi=json_decode($result,true);

echo json_encode(array(
    "process_status"=>"YES",
    "sfa_customer_code"=>$customer_code,
    "sap_customer_code"=>$sap_customer_code,
    "saathi_response"=>$saathi
));
?>