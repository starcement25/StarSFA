<?php
define("SERVER","localhost");
define("USER","acedns_dnsprod");
define("PASSWORD","dnsprod1234#");
define("DB","acedns_ABDOS");

mysqli_connect(SERVER,USER,PASSWORD);
mysqli_select_db(DB);
$sqlquery="SELECT DISTINCT customer_code FROM `prev_order_counting_master` ORDER BY customer_code ASC";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	if($count>0){
			$header = "Customer code"."\t"."Customer name"."\t"."Route Name"."\t"."RDS name"."\t"."Employee"."\t"."acedns";

		while($rowcustomer = mysqli_fetch_assoc($result))
		{
			$customer_code=$rowcustomer['customer_code'];
			$sqlcustomerroute="SELECT CM.customer_name,(SELECT route_name FROM route_master WHERE route_code = CRR.route_code) AS route_name,
								(SELECT customer_name FROM customer_master CMB  WHERE CMB.customer_code = CM.rds_tag
								) AS rds_tag,CRR.acedns,(SELECT emp_name FROM employee_master WHERE emp_code = CRR.emp_code) AS emp_code_name 
								FROM customer_route_emp_relation CRR,customer_master CM WHERE CRR.customer_code=CM.customer_code AND CRR.acedns='Y'
								AND CM.customer_code='".$customer_code."'";
			$rscustomerroute=mysqli_query($link,$sqlcustomerroute);
			$cntcustomerroute=mysqli_num_rows($rscustomerroute);
			if($cntcustomerroute >0)
			{
				while($rowcustomerroute=mysqli_fetch_assoc($rscustomerroute))
				{
				$table_data .= $customer_code."\t".$rowcustomerroute['customer_name']."\t".$rowcustomerroute['route_name']."\t".$rowcustomerroute['rds_tag']
				."\t".$rowcustomerroute['emp_code_name']."\t".$rowcustomerroute['acedns']."\n";
				}
			}
			else
			{
				$sqlcustomerroute="SELECT CM.customer_name,(SELECT route_name FROM route_master WHERE route_code = CRR.route_code) AS route_name,
								(SELECT customer_name FROM customer_master CMB  WHERE CMB.customer_code = CM.rds_tag
								) AS rds_tag,CRR.acedns,(SELECT emp_name FROM employee_master WHERE emp_code = CRR.emp_code) AS emp_code_name 
								FROM customer_route_emp_relation CRR,customer_master CM WHERE CRR.customer_code=CM.customer_code AND CRR.acedns='N'
								AND CM.customer_code='".$customer_code."'";
				$rscustomerroute=mysqli_query($link,$sqlcustomerroute);
				$cntcustomerroute=mysqli_num_rows($rscustomerroute);
				if($cntcustomerroute >0)
				{
					while($rowcustomerroute=mysqli_fetch_assoc($rscustomerroute))
					{
					$table_data .= $customer_code."\t".$rowcustomerroute['customer_name']."\t".$rowcustomerroute['route_name']."\t".$rowcustomerroute['rds_tag']
					."\t".$rowcustomerroute['emp_code_name']."\t".$rowcustomerroute['acedns']."\n";
					}
	
				}
			}
		}
	}
	if($table_data !=''){	
		header("Content-type: application/octet-stream"); 
		header("Content-Disposition: attachment; filename=Prev_order_ABDOS.xls"); 
		header("Pragma: no-cache"); 
		header("Expires: 0"); //It will print all the Table row as Excel file row with selected column name as header. 
		echo ucwords($header)."\n".$table_data;
	}

	echo 'SUCCESS';
?>
