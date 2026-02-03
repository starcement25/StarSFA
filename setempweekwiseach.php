<?php
	define("SERVER","localhost");
	define("USER","acedns_dnsprod");
	define("PASSWORD","dnsprod1234#");
	//require("include/config-setup.php");
	define("DB","acedns_RUPA");
	$link=mysqli_connect(SERVER,USER,PASSWORD) or die("Database Connection Error.");
	mysqli_select_db(DB,$link) or die("could not connect the database for invalid nick name");
	//require("include/config-email-setup.php");
	
	$month=gmdate('m',strtotime('+330 minute'));
	$year=gmdate('Y',strtotime('+330 minute'));
	$emp_code_array=array();
	$month_array=array();

	$sqlemplist="SELECT emp_code,emp_name FROM employee_master WHERE acedns='Y' ORDER BY emp_code ASC";
	$rsemplist=mysqli_query($link,$sqlemplist);
	while($rowemplist=mysqli_fetch_assoc($rsemplist))
	{
	$sqlquery="SELECT emp_code,month,year FROM self_appraisal_emp_week_wise WHERE month='".$month."' AND year='".$year."' 
				AND emp_code='".$rowemplist['emp_code']."'";
	$result = mysqli_query($link,$sqlquery);
	$count=mysqli_num_rows($result);
	if($count >0)
	{
		while($rowsaledetails=mysqli_fetch_assoc($result))
		{
			$emp_code=$rowsaledetails['emp_code'];
			$month=$rowsaledetails['month'];
			$year=$rowsaledetails['year'];
		    $sqlachivementval="SELECT SUM(visit_qty) AS total_ach,SUBSTRING(visit_date,1,10) As visit_date
						  		FROM 
						   		prev_order_counting_master WHERE  SUBSTRING(order_no,-19,5)='".$emp_code."'  
						   	    AND SUBSTRING(visit_date,6,2)='".$month."' AND SUBSTRING(visit_date,1,4)='".$year."'
							    AND order_no LIKE 'O%' GROUP BY  SUBSTRING(visit_date,1,10) 
								ORDER BY SUBSTRING(visit_date,1,10) ASC";
		    $rsachivementval=mysqli_query($link,$sqlachivementval);
			while($rowachievementval=mysqli_fetch_assoc($rsachivementval))
			{
				$visit_date=$rowachievementval['visit_date'];
				$visit_date_day=substr($visit_date,8,2);
				if($visit_date_day >='01' && $visit_date_day <='08')
				{
					${week1_ach.$emp_code.$month}=${week1_ach.$emp_code.$month}+$rowachievementval['total_ach'];
				}
				if($visit_date_day >='09' && $visit_date_day <='16')
				{
					${week2_ach.$emp_code.$month}=${week2_ach.$emp_code.$month}+$rowachievementval['total_ach'];
				}
				if($visit_date_day >='17' && $visit_date_day <='24')
				{
					${week3_ach.$emp_code.$month}=${week3_ach.$emp_code.$month}+$rowachievementval['total_ach'];
				}
				if($visit_date_day >='25' && $visit_date_day <='31')
				{
					${week4_ach.$emp_code.$month}=${week4_ach.$emp_code.$month}+$rowachievementval['total_ach'];				
				}
			}
			 /*if(!in_array($emp_code,$emp_code_array))
			{
				array_push($emp_code_array,$emp_code);
			}
			if(!in_array($month,$month_array))
			{
				array_push($month_array,$month);
			}*/
			${month_ach.$emp_code.$month}=${week1_ach.$emp_code.$month}+${week2_ach.$emp_code.$month}+${week3_ach.$emp_code.$month}+${week4_ach.$emp_code.$month};
			$sqlupdateach="UPDATE self_appraisal_emp_week_wise 
							SET week1_ach='".${week1_ach.$emp_code.$month}."',
							week2_ach='".${week2_ach.$emp_code.$month}."',
							week3_ach='".${week3_ach.$emp_code.$month}."',
							week4_ach='".${week4_ach.$emp_code.$month}."',
							month_ach='".${month_ach.$emp_code.$month}."',
							download_time=CURRENT_TIMESTAMP() 
							WHERE 
							emp_code='".$emp_code."' AND month='".$month."' AND year='".$year."'";
			mysqli_query($link,$sqlupdateach);				
		}
	}//End of if Exisits
	else
	{
	   $emp_code=$rowemplist['emp_code'];
	   $sqlachivementval="SELECT SUM(visit_qty) AS total_ach,SUBSTRING(visit_date,1,10) As visit_date
							FROM 
							prev_order_counting_master WHERE  SUBSTRING(order_no,-19,5)='".$emp_code."'  
							AND SUBSTRING(visit_date,6,2)='".$month."' AND SUBSTRING(visit_date,1,4)='".$year."'
							AND order_no LIKE 'O%' GROUP BY  SUBSTRING(visit_date,1,10) 
							ORDER BY SUBSTRING(visit_date,1,10) ASC";
		$rsachivementval=mysqli_query($link,$sqlachivementval);
		while($rowachievementval=mysqli_fetch_assoc($rsachivementval))
		{
			$visit_date=$rowachievementval['visit_date'];
			$visit_date_day=substr($visit_date,8,2);
			if($visit_date_day >='01' && $visit_date_day <='08')
			{
				${week1_ach.$emp_code.$month}=${week1_ach.$emp_code.$month}+$rowachievementval['total_ach'];
			}
			if($visit_date_day >='09' && $visit_date_day <='16')
			{
				${week2_ach.$emp_code.$month}=${week2_ach.$emp_code.$month}+$rowachievementval['total_ach'];
			}
			if($visit_date_day >='17' && $visit_date_day <='24')
			{
				${week3_ach.$emp_code.$month}=${week3_ach.$emp_code.$month}+$rowachievementval['total_ach'];
			}
			if($visit_date_day >='25' && $visit_date_day <='31')
			{
				${week4_ach.$emp_code.$month}=${week4_ach.$emp_code.$month}+$rowachievementval['total_ach'];				
			}
		}
		/* if(!in_array($emp_code,$emp_code_array))
		{
			array_push($emp_code_array,$emp_code);
		}
		if(!in_array($month,$month_array))
		{
			array_push($month_array,$month);
		}*/
		${month_ach.$emp_code.$month}=${week1_ach.$emp_code.$month}+${week2_ach.$emp_code.$month}+${week3_ach.$emp_code.$month}+${week4_ach.$emp_code.$month};
		$sqlinsertach="INSERT INTO self_appraisal_emp_week_wise 
						SET 
						emp_code='".$emp_code."',
						month='".$month."',
						year='".$year."',
						week1_ach='".${week1_ach.$emp_code.$month}."',
						week2_ach='".${week2_ach.$emp_code.$month}."',
						week3_ach='".${week3_ach.$emp_code.$month}."',
						week4_ach='".${week4_ach.$emp_code.$month}."',
						month_ach='".${month_ach.$emp_code.$month}."'";
		mysqli_query($link,$sqlinsertach);
	}
	
 }
	echo "SUCCESS";
	mysqli_close($link);
?>