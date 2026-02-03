<?php
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234#");
define("DB","acedns_ABDOS");

mysqli_connect(SERVER,USER,PASSWORD);
mysqli_select_db(DB);
$sqlquery="SELECT customer_code FROM customer_master";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	if($count>0){
		while($rowcustomer = mysqli_fetch_assoc($result))
		{
			$sqlcustomerroute="SELECT route_code FROM customer_route_emp_relation WHERE customer_code='".$rowcustomer['customer_code']."'";
			$rscustomerroute=mysqli_query($link,$sqlcustomerroute);
			$cntcustomerroute=mysqli_num_rows($rscustomerroute);
			if($cntcustomerroute >0)
			{
				while($rowcustomerroute = mysqli_fetch_assoc($rscustomerroute))
				{
					$sqlupdatecustomerroute="UPDATE customer_master SET  route_code='".$rowcustomerroute['route_code']."' WHERE 
											customer_code='".$rowcustomer['customer_code']."'";
					mysqli_query($link,$sqlupdatecustomerroute);						
				}
			}
		}
	}
	echo 'SUCCESS';
?>
