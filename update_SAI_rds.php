<?php
require("include/config.php");
require("include/dbcon.php");

$sqladminlogin="select customer_code,customer_name,route_code,rds_tag FROM customer_master  WHERE cust_type!='S'";
$rsadminlogin=mysqli_query($link,$sqladminlogin);
while($rowcustomer=mysqli_fetch_assoc($rsadminlogin))
{
		$customer_code=$rowcustomer['customer_code'];
		$customer_name=$rowcustomer['customer_name'];
		$route_code=$rowcustomer['route_code'];
		$rds_tag=$rowcustomer['rds_tag'];
		
		$sqrdstag="SELECT customer_code  FROM customer_master WHERE route_code='".$route_code."' AND cust_type='S'";
		$rsrdstag=mysqli_query($link,$sqrdstag);
		$rowrdstag=mysqli_fetch_assoc($rsrdstag);
		$rds_to_be_mapped=$rowrdstag['customer_code'];
		
		if($rds_tag!=$rds_to_be_mapped)
		{
			echo $sqlUpdate="UPDATE customer_master SET rds_tag='".$rds_to_be_mapped."'
				WHERE customer_code='".$customer_code."'";
			mysqli_query($link,$sqlUpdate);
		}
	
}
?>
