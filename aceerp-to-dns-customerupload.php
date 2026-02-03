<?php	
set_time_limit(1000);
ini_set('memory_limit', '-1');
error_reporting(E_ALL ^ E_NOTICE);
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");

$postdata = file_get_contents("php://input");
//echo $postdata;
if (isset($postdata)) {
$request = json_decode($postdata);
//print_r($request);
$guid_scod = $request->guid_scod;
$dns_customer_code = $request->dns_customer_code;				 
$customer_name	=$request->customer_name;
$phone_no		=$request->phone_no;
				
$sqlcustomernamechk="SELECT * FROM customer_master WHERE guid_scod='".addslashes($guid_scod)."' ";
$rscustomernamechk=mysqli_query($link,$sqlcustomernamechk);
$countcustomernamechk=mysqli_num_rows($rscustomernamechk);
$csv_row_count=$rec_count+1;
if($countcustomernamechk<1)
{
	$sqlmaxcustomercode="SELECT MAX(customer_code) AS max_customer_code FROM  customer_master WHERE customer_code NOT LIKE '%N%'";
	$rsmaxcustomercode=mysqli_query($link,$sqlmaxcustomercode);
	$rowmaxcustomercode=mysqli_fetch_assoc($rsmaxcustomercode);
	$max_customer_code=$rowmaxcustomercode['max_customer_code'];
	
	if($max_customer_code=='')
	{
		$max_customer_code='C/0000001';
	}
	else
	{
		$max_customer_code++;
	}
	$sql  = "insert into customer_master ";
	$sql .= " SET customer_code='".$max_customer_code."'";
	$sql .= " , dns_customer_code='".$dns_customer_code."'";
	$sql .= " , guid_scod='".$guid_scod."'";
	$sql .= " , customer_name='".addslashes($customer_name)."'";
	$sql .= " , phone_no='".$phone_no."'";
	$sql .= " , acedns='Y'";
	$sql .= " , black_list='N'";
	$sql .= " , cust_type='Dealer'";
	$sql .= " , download_time=CURRENT_TIMESTAMP()";
	//exit();
	mysqli_query($link,$sql) or die(mysqli_error()."Error in Insertion.");
	$customer_code=$max_customer_code;
	
}
else
{
	$rowcustomernamechk=mysqli_fetch_assoc($rscustomernamechk);
	$customer_code_db=$rowcustomernamechk['customer_code'];
	$phone_no_db=$rowcustomernamechk['phone_no'];
	$customer_name_db=$rowcustomernamechk['customer_name'];
	
	$sqlupdated  = "update customer_master ";
			$sqlupdated .= " SET customer_name='".addslashes($customer_name_db)."'";
			$sqlupdated .= " , phone_no	='".$phone_no_db."'";
	$sqlupdated .= " ,download_time=CURRENT_TIMESTAMP() 
							 WHERE guid_scod='".addslashes($guid_scod)."' ";
	mysqli_query($link,$sqlupdated) or die(mysqli_error()."Error in updation.");
}
echo 'Success';
}
else
{
	echo 'Failure';
}
?>