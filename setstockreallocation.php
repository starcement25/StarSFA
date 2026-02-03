<?php
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	//require("include/config-setup.php");
	define("DB","acedns_VCONNECT");
	$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");
	//require("include/config-email-setup.php");
		
	//$date_val=date('Y-m-d', strtotime(date('Y-m-d') ." -1 day"));
	$sqlcustomer="SELECT customer_code,prod_code,SUM(allocation_qty) AS total_allocation,SUM(requisition_qty) AS total_requisition FROM 
				 stock_balance_details WHERE active_flag='Y' AND requisition_id <> '' 
				 GROUP BY customer_code,prod_code ORDER BY customer_code ASC,prod_code ASC";
	$rscustomer=mysqli_query($link,$sqlcustomer);
	$emp_code_array=array();
	$prod_code_array=array();
	$customer_code_array=array();
	while($rowcustomer=mysqli_fetch_assoc($rscustomer))
	{
		$customer_code=$rowcustomer['customer_code'];
		$prod_code=$rowcustomer['prod_code'];
		$total_allocation=$rowcustomer['total_allocation'];
		$total_requisition=$rowcustomer['total_requisition'];
		
		$sqlemdetails="SELECT CRR.emp_code,EM.reporting_to FROM customer_route_emp_relation CRR,employee_master EM WHERE CRR.emp_code=EM.emp_code 
						AND CRR.customer_code='".$customer_code."'";
		$rsempdetails=mysqli_query($link,$sqlemdetails);
		while($rowempdetails=mysqli_fetch_assoc($rsempdetails))
		{
			$emp_code=$rowempdetails['emp_code'];
			$reporting_to=$rowempdetails['reporting_to'];
			if(!in_array($reporting_to,$emp_code_array))
			{
				array_push($emp_code_array,$reporting_to);
			}
		}
		if(!isset(${customercount.$reporting_to}))
		{
			${customercount.$reporting_to}=0;
		}
		if(!in_array($customer_code,$customer_code_array))
		{
		   ${customercount.$reporting_to}= ${customercount.$reporting_to}+1;
		   array_push($customer_code_array,$customer_code);
		}
		if(!in_array($prod_code,$prod_code_array))
		{
		   array_push($prod_code_array,$prod_code);
		}
		if(!isset(${reallocationbalanceqty.$reporting_to.$prod_code}))
		{
			${reallocationbalanceqty.$reporting_to.$prod_code}=0;
		}
		${reallocationbalanceqty.$reporting_to.$prod_code}=${reallocationbalanceqty.$reporting_to.$prod_code}+($total_allocation-$total_requisition);
	}
	//print_r($emp_code_array);
	foreach($emp_code_array as $emp_code_val)
	{
		//$sqlemplowerleaves="SELECT emp_code FROM employee_master WHERE FIND_IN_SET( '".$emp_code_val."',reporting_to) AND acedns='Y'";
		$sqlemplowerleaves="SELECT DISTINCT SBD.customer_code FROM customer_route_emp_relation CRR,stock_balance_details SBD 
						WHERE CRR.customer_code=SBD.customer_code AND SBD.active_flag='Y' AND CRR.emp_code IN(SELECT emp_code FROM employee_master WHERE FIND_IN_SET( '".$emp_code_val."',reporting_to) AND acedns='Y')";
		$rsemplowerleaves=mysqli_query($link,$sqlemplowerleaves);
		$cntemplowerleaves=mysqli_num_rows($rsemplowerleaves);
		//$cntemplowerleaves=1;
		//echo ${customercount.$emp_code_val};
		//if($cntemplowerleaves==${customercount.$emp_code_val})
		if($cntemplowerleaves > 0)
		{
			foreach($prod_code_array as $prod_code_val)
			{
				$sqlupdatereallocation="UPDATE stock_reallocation SET active_flag='N',inactivation_date=CURRENT_TIMESTAMP() 
										WHERE emp_code='".$emp_code_val."' AND prod_code='".$prod_code_val."'";
				if(mysqli_query($link,$sqlupdatereallocation) && ${reallocationbalanceqty.$emp_code_val.$prod_code_val} >0)
				{
					$sqlinsertreallocation="INSERT INTO stock_reallocation SET emp_code='".$emp_code_val."',
											prod_code='".$prod_code_val."',	
											balance_qty='".${reallocationbalanceqty.$emp_code_val.$prod_code_val}."',
											reallocation_qty='".${reallocationbalanceqty.$emp_code_val.$prod_code_val}."',
											reallocation_date=CURRENT_TIMESTAMP(),active_flag='Y'";
					$rsinsertreallocation=mysqli_query($link,$sqlinsertreallocation);						
				}
			}
		}
	}
	mysqli_close($link);
?>