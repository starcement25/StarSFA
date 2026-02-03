<?php
  define("SERVER","localhost");
  define("USER","acedns_dnsprod");
  define("PASSWORD","dnsprod1234#");
  require("include/config-setup.php");
  require("include/functions.php");
  define("DB","acedns_HALDIRAM");

//require("include/dbcon.php");
$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");

$rds_tag_array=array();
$sqlselTSI="SELECT emp_code FROM employee_master WHERE designation='TSI' ORDER BY emp_code ASC";
$rsselTSI=mysqli_query($link,$sqlselTSI);
while($rowselTSI=mysqli_fetch_assoc($rsselTSI))
{
	$emp_code=$rowselTSI['emp_code'];
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$emp_hierarchy_condition=' CRER.emp_code IN('.$employee_hierarchy.')';
	
	$sqlcust="SELECT CM.dns_customer_code, CM.customer_name, CM.phone_no, (SELECT route_code FROM route_master
			WHERE route_code = CRER.route_code) AS route_code, (SELECT route_name FROM route_master WHERE route_code = CRER.route_code
			) AS route_name, (SELECT emp_name FROM employee_master WHERE emp_code = CRER.emp_code) AS emp_code_name, CRER.acedns, 
			CM.credit_limit, CM.credit_days, CM.current_balance, CM.black_list, CM.TD, (SELECT branch_name FROM branch_master
			WHERE branch_code = CM.branch_code) AS branch_code, CM.cust_type, CM.rds_tag, CM.sauda_validity_period, CM.address, CM.landline_no, CM.owner_name, CM.owner_phone, CM.cust_class, CM.weekly_closing_day, CM.coverage_type, CM.TIN, CM.PAN, CM.district, CM.minimum_stock, CM.bank_name, CM.bank_account_number
            FROM customer_master CM, customer_route_emp_relation CRER WHERE CM.route_code = CRER.route_code AND CM.customer_code = CRER.customer_code 
			AND CM.cust_type='R' AND ".$emp_hierarchy_condition;
	$rscust=mysqli_query($link,$sqlcust);
	while($rowcust=mysqli_fetch_assoc($rscust))
	{
		$dns_customer_code=$rowcust['dns_customer_code'];
		$customer_name=$rowcust['customer_name'];
		$route_name=$rowcust['route_name'];
		$emp_name=$rowcust['emp_code_name'];
		$rds_tag=$rowcust['rds_tag'];
		
		$sqldistrouterelation="SELECT CRER.customer_code FROM customer_route_emp_relation CRER WHERE CRER.customer_code='".$rds_tag."' 
								AND ".$emp_hierarchy_condition;
		$rsdistrouterelation=mysqli_query($link,$sqldistrouterelation);
		$cntdistrouterelation=mysqli_num_rows($rsdistrouterelation);	
		
		if($cntdistrouterelation==0)	
		{
			$sqlrds_tag_name="SELECT customer_name,dns_customer_code FROM customer_master WHERE customer_code='".$rds_tag."'";
			$rsrds_tag_name=mysqli_query($link,$sqlrds_tag_name);
			$rowrds_tag_name=mysqli_fetch_assoc($rsrds_tag_name);
			$rds_tag_name=$rowrds_tag_name['customer_name'];
			//$contents.=$rowrdsdet['']."\t".$customer_name."\t".$route_name."\t".$emp_name."\t".$rds_tag_name."\n";
			if($rds_tag !='' && !in_array($rds_tag,$rds_tag_array)){
				$sqlrdsdet="SELECT CM.dns_customer_code, CM.customer_name,  (SELECT route_name FROM route_master WHERE route_code = CRER.route_code
				) AS route_name, (SELECT emp_name FROM employee_master WHERE emp_code = CRER.emp_code) AS emp_code_name,
				(SELECT designation FROM employee_master WHERE emp_code = CRER.emp_code) AS emp_code_designation, 
				CM.cust_type, (SELECT CMB.customer_name FROM customer_master CMB WHERE CMB.customer_code = CM.rds_tag
				) AS rds_tag FROM customer_master CM, 
				customer_route_emp_relation CRER WHERE CM.route_code = CRER.route_code AND CM.customer_code = CRER.customer_code 
				AND CM.customer_code='".$rds_tag."'";
				$rsrdsdet=mysqli_query($link,$sqlrdsdet);
				$cntdsdet=mysqli_num_rows($rsrdsdet);
				if($cntdsdet >0){
					while($rowrdsdet=mysqli_fetch_assoc($rsrdsdet))
					{
						$contents.=$rowrdsdet['dns_customer_code']."\t".$rowrdsdet['customer_name']."\t".$rowrdsdet['route_name']."\t".$rowrdsdet['emp_code_name']."\t".$rowrdsdet['emp_code_designation']."\t".$rowrdsdet['rds_tag']."\n";
					}
			   }
			   else{
				   $contents.=''."\t".$rds_tag_name."\t".''."\t".''."\t".''."\t".''."\t".''."\n";
			   }
			   array_push($rds_tag_array,$rds_tag);
			}
		}
	}
}
	$filename="HALDIRAM-CP-mapping-present.xls";
	if (file_exists("/home/acedns/public_html/misreport/$filename")){
		unlink("/home/acedns/public_html/misreport/$filename");
	}
	$fp = fopen("/home/acedns/public_html/misreport/$filename","wb");
	fwrite($fp,$contents);
	fclose($fp);
	echo $filename;
?>