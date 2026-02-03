<?php

require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");

$emp_code=$_REQUEST['emp_code'];

	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$emp_hierarchy_condition=' AND CRR.emp_code IN('.$employee_hierarchy.')';
	$sqlcustlist="SELECT DISTINCT CC.dns_customer_code FROM customer_route_emp_relation CRR,customer_master CC WHERE CC.customer_code=CRR.customer_code 
					AND CRR.acedns='Y' $emp_hierarchy_condition";
	$rscustlist=mysqli_query($link,$sqlcustlist);
	$customer_string='';
	while($rowcustlist=mysqli_fetch_assoc($rscustlist))
	{
		$customer_string=$customer_string."'".$rowcustlist['dns_customer_code']."'".',';
	}
	$customer_string=substr($customer_string,0,-1);
	
	//SAATHI data fetch
/*define("SERVERREMOTE","103.87.174.95");
define("USERREMOTE","starsaat_dnsprod");
define("PASSWORDREMOTE","dnsprod1234#");
//define("DBREMOTE","starsaat_START");
define("DBREMOTE","starsaathi_STARS");

$link=mysqli_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE,DBREMOTE) or die("Database Connection Error.");*/
	
/*$link=mysqli_connect(SERVERREMOTE,USERREMOTE,PASSWORDREMOTE,TRUE) or die(mysqli_error()."Database Connection Error.");
mysqli_select_db(DBREMOTE,$link) or die(mysqli_error()."could not connect the database");*/
$ledger = "ledger";
$ledger_balance = "ledger_balance";
$customer_master = "customer_master";

/*$sqlquerycustlist="SELECT DISTINCT CM.customer_code,CM.dns_customer_code, CM.customer_name
				FROM ledger_balance LB, customer_master CM WHERE 
				LB.dns_customer_code = CM.dns_customer_code AND LB.dns_customer_code IN(".$customer_string.")
				ORDER BY CM.customer_name ASC";*/
$sqlquerycustlist="SELECT DISTINCT CM.customer_code,CM.dns_customer_code, CM.customer_name,CM.cust_type
				FROM  customer_master CM WHERE  CM.dns_customer_code IN(".$customer_string.")
				ORDER BY CM.cust_type DESC,CM.customer_name ASC LIMIT 0,1500";				
$resquerycustlist = mysqli_query($link,$sqlquerycustlist);
$totcustlist = mysqli_num_rows($resquerycustlist);

if($totcustlist>0){
	while($rowcustlist=mysqli_fetch_assoc($resquerycustlist)){
		$customer_code = $rowcustlist["customer_code"];
		$dns_customer_code = $rowcustlist["dns_customer_code"];
		$customer_name = $rowcustlist["customer_name"];
		$customer_name = preg_replace('/[[:^print:]]/', ' ', $customer_name);
		$customer_data[] =array("customer_code"=>$customer_code,"dns_customer_code"=>$dns_customer_code,"customer_name"=>$customer_name);
	}
	$res_data = array("process_status"=>"YES","process_message"=>"Success.","customer_data"=>$customer_data);
}
else{	
	$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong.");
}	
echo json_encode($res_data);
mysqli_close($link);
?>