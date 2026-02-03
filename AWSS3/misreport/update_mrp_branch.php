<?php
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234#");
$linksetup=mysql_connect(SERVER,USER,PASSWORD) or die("Setup Database Connection Error.");
mysql_select_db("acedns_DNV",$linksetup) or die("could not connect the setup database");
	
$sql_customer_master = "SELECT rds_tag, branch_code, customer_code, SUBSTRING(customer_code,2,5) AS emp_code FROM `customer_master`  WHERE customer_code  LIKE 'N%'";
$res_customer_master = mysql_query($sql_customer_master);
while($row_customer_master = mysql_fetch_array($res_customer_master)){
	$customer_code = $row_customer_master['customer_code'];
	$emp_code = $row_customer_master['emp_code'];
	$rds_tag = $row_customer_master['rds_tag'];
	$branch_code = $row_customer_master['branch_code'];
	
		
	$sql_update_customer_master = "UPDATE customer_master SET branch_code = '".$branch_code."', download_time = current_timestamp WHERE customer_code = '".$rds_tag."'";
	mysql_query($sql_update_customer_master);
}


echo "Updated";
?>