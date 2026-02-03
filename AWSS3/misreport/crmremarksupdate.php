<?php
ob_start();
session_start();
require("adminUtils.php");
$customercode = $_REQUEST['customercode'];
$restype=$_REQUEST['restype'];
$today=date("Ymd");
$sql=mysql_query("SELECT * FROM CRM_transaction WHERE customer_code='".$customercode."' AND SUBSTRING(call_id,-14,8)='".$today."'");
if(mysql_num_rows($sql)>0)
{
	
	$sqlup="UPDATE CRM_transaction SET response_type='".$_REQUEST['restype']."',update_date_time=NOW() WHERE customer_code='".$customercode."' AND SUBSTRING(call_id,-14,8)='".$today."'";
	
	if(mysql_query($sqlup)){
		echo "Remarks add sucessfully";
	}
	else{
	   echo "Remarks not add sucessfully";
	}
}
else{
	echo "No record found";
}
?>