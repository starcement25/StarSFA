<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

$employee = $_REQUEST['employee'];
$start_date = $_REQUEST['start_date'];
$start_date_search=str_replace('-','',$start_date);
$end_date = $_REQUEST['end_date'];
$end_date_search=str_replace('-','',$end_date);
$date_array = array();

if($employee == 'all'){
	$order_condition = '';
	$emp_condition = ' 1';
}
else{
	$att_condition = " SUBSTRING(trans_id,2,5) IN(".$employee.") AND ";
	$emp_condition = " emp_code IN(".$employee.") ";
}

$sql_att_date = "SELECT SUBSTRING(trans_id,-14,8) AS att_date FROM location WHERE ".$att_condition." (SUBSTRING(trans_id,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') AND trans_id LIKE 'A%'";
$res_att_date = mysql_query($sql_att_date);
while($row_att_date = mysql_fetch_array($res_att_date)){
	$att_date = $row_att_date['att_date'];
	if(!in_array($att_date,$date_array))
		array_push($date_array,$att_date);
}

sort($date_array);
//if(!empty($date_array)){
	$header = "Date"."\t"."Employee Code"."\t"."Employee Name"."\t"."Total Hours"."\t"."Attendence"."\t"."PJP"."\t"."First Check in Time"
	."\t"."Last Check out Time"."\t"."Count of Distributor Visit"."\t"."Count of Dealer Visit"."\t"."Total order amount"."\t"."Total collection amount";
	$sql_emp = "SELECT emp_code, dns_emp_code, emp_name FROM employee_master WHERE ".$emp_condition;
	$res_emp = mysql_query($sql_emp);
	while($row_emp = mysql_fetch_array($res_emp)){
		$dns_emp_code = $row_emp['dns_emp_code'];
		$emp_code = $row_emp['emp_code'];
		$emp_name = $row_emp['emp_name'];
		
		foreach($date_array as $date_array_val)
		{
			//For attendence
			$sqlattendance="SELECT SUBSTRING(date,12,8) AS att_time FROM location WHERE trans_id LIKE 'A%' AND 
							SUBSTRING(trans_id,-14,8)='".$date_array_val."' AND SUBSTRING(trans_id,2,5) = '".$emp_code."'";
			$rsattendance=mysql_query($sqlattendance);
			$countattendance=mysql_num_rows($rsattendance);
			$rowattendance=mysql_fetch_array($rsattendance);	
			$att_time=$rowattendance['att_time'];
			if($countattendance >0){
				${att_time.$date_array_val.$emp_code}=$att_time;
			}
	
			//For route plan			
			$date_array_val_seperator=date('Y-m-d',strtotime($date_array_val));
			$sqlpjp="SELECT RM.route_name FROM route_master RM,route_plan RP WHERE RP.route_code=RM.route_code 
					AND RP.visit_date='".$date_array_val_seperator."' AND RP.emp_code='".$emp_code."'";
			$rspjp=mysql_query($sqlpjp);
			${pjp.$date_array_val.$emp_code}='';
			while($rowpjp=mysql_fetch_array($rspjp))
			{
				${pjp.$date_array_val.$emp_code}=${pjp.$date_array_val.$emp_code}.$rowpjp['route_name'].',';
			}
			if(${att_time.$date_array_val.$emp_code} !='' && ${pjp.$date_array_val.$emp_code}!=''){
				
				//For order amount
			
				$sqlorderamount="SELECT SUM(amount) AS order_amount FROM order_details WHERE 
								SUBSTRING(order_no,-14,8)='".$date_array_val."' AND SUBSTRING(order_no,2,5) = '".$emp_code."'";
				$rsorderamount=mysql_query($sqlorderamount);
				$countorderamount=mysql_num_rows($rsorderamount);
				$roworderamount=mysql_fetch_array($rsorderamount);	
				$order_amount=$roworderamount['order_amount'];
				if($order_amount >0){
					${order_amount.$date_array_val.$emp_code}=$order_amount;
				}
				
				//For collection amount
			
				$sqlcollectionamount="SELECT SUM(amount) AS collection_amount FROM payment_details WHERE 
									SUBSTRING(receipt_id,-14,8)='".$date_array_val."' AND SUBSTRING(receipt_id,2,5) = '".$emp_code."'";
				$rscollectionamount=mysql_query($sqlcollectionamount);
				$countcollectionamount=mysql_num_rows($rscollectionamount);
				$rowcollectionamount=mysql_fetch_array($rscollectionamount);	
				$collection_amount=$rowcollectionamount['collection_amount'];
				if($countcollectionamount >0){
					${collection_amount.$date_array_val.$emp_code}=$collection_amount;
				}

				//For Check in and Check out
				$sqlcheckinout="SELECT check_in_time,check_out_time FROM check_in_out_details 
							  WHERE SUBSTRING(check_in_time,1,10)='".$date_array_val_seperator."' AND SUBSTRING(trans_id,3,5) = '".$emp_code."' 
							  ORDER BY SUBSTRING(check_in_time,1,10) ASC";
				$rscheckinout=mysql_query($sqlcheckinout) or die(mysql_error()." Error in select check in out details ".$sqlcheckinout);
				$countcheckinout=mysql_num_rows($rscheckinout);
				if($countcheckinout >0)
				{
					$time_difference_final=0;
					$cntcheckinout=1;
					while($rowcheckinout=mysql_fetch_array($rscheckinout))
					{
						if($cntcheckinout==1) ${first_checkintime.$date_array_val.$emp_code}=date('H:i:s',strtotime($rowcheckinout['check_in_time']));
						if($cntcheckinout==$countcheckinout) ${last_checkouttime.$date_array_val.$emp_code}=date('H:i:s',strtotime($rowcheckinout['check_out_time']));
	
						$check_in_time=date('d-m-Y H:i:s',strtotime($rowcheckinout['check_in_time']));
						$check_out_time=date('d-m-Y H:i:s',strtotime($rowcheckinout['check_out_time']));
						$time_difference=strtotime(${last_checkouttime.$date_array_val.$emp_code})-strtotime(${first_checkintime.$date_array_val.$emp_code});
						$time_difference_final=$time_difference;
						$cntcheckinout++;
					}
					if($time_difference_final >=3600)
					{
						$hours = floor($time_difference_final / 3600);
						$minutes = floor(($time_difference_final / 60) % 60);
						$seconds = $time_difference_final % 60;
						$time_duration=$hours.' Hour(s) '.$minutes.' Minute(s) '.$seconds.' Second(s)';
					}
					else if($time_difference_final >=60 && $time_difference_final<3600)
					{
						$minutes = floor(($time_difference_final / 60) % 60);
						$seconds = $time_difference_final % 60;
						$time_duration=$minutes.' Minute(s) '.$seconds.' Second(s)';
					}
					else
					{
						$seconds = $time_difference_final % 60;
						$time_duration=$seconds.' Second(s)';
					}
				}
				else
				{
					$time_duration='';
					$time_difference_final='';
				}
				${time_duration.$date_array_val.$emp_code}=$time_duration;
				${time_difference_final.$date_array_val.$emp_code}=$time_difference_final;
				
				$sqldistributorvisit="SELECT DISTINCT CIO.customer_code FROM check_in_out_details CIO,customer_master CM
							  WHERE CM.customer_code=CIO.customer_code  AND SUBSTRING(CIO.trans_id,-14,8)='".$date_array_val."' 
							  AND SUBSTRING(CIO.trans_id,3,5) = '".$emp_code."' AND CM.cust_type='D'";
				$rsdistributorvisit=mysql_query($sqldistributorvisit) or die(mysql_error()." Error in select distributor visit ".$sqldistributorvisit);
				$countdistributorvisit=mysql_num_rows($rsdistributorvisit);
				
				$sqldealervisit="SELECT DISTINCT CIO.customer_code FROM check_in_out_details CIO,customer_master CM
							  WHERE CM.customer_code=CIO.customer_code  AND SUBSTRING(CIO.trans_id,-14,8)='".$date_array_val."' 
							  AND SUBSTRING(CIO.trans_id,3,5) = '".$emp_code."' AND CM.cust_type='R'";
				$rsdealervisit=mysql_query($sqldealervisit) or die(mysql_error()." Error in select dealer visit ".$sqldealervisit);
				$countdealervisit=mysql_num_rows($rsdealervisit);
			}
			else
			{
				${time_duration.$date_array_val.$emp_code}='';
				${first_checkintime.$date_array_val.$emp_code}='';
				${last_checkouttime.$date_array_val.$emp_code}='';
				$countdistributorvisit=0;
				$countdealervisit=0;
			}


			if(${att_time.$date_array_val.$emp_code} !='' || ${pjp.$date_array_val.$emp_code}!=''){
			$table_data .= date('d-m-Y',strtotime($date_array_val))."\t".$dns_emp_code."\t".$emp_name."\t".${time_duration.$date_array_val.$emp_code}."\t".${att_time.$date_array_val.$emp_code}."\t".substr(${pjp.$date_array_val.$emp_code},0,-1)."\t".${first_checkintime.$date_array_val.$emp_code}."\t".${last_checkouttime.$date_array_val.$emp_code}."\t".$countdistributorvisit."\t".$countdealervisit."\t".number_format(${order_amount.$date_array_val.$emp_code},2)."\t".
			number_format(${collection_amount.$date_array_val.$emp_code},2)."\n";
			}

		}
	}
	if($table_data !=''){	
		header("Content-type: application/octet-stream"); 
		header("Content-Disposition: attachment; filename=Daily_Activity_Report_Summary.xls"); 
		header("Pragma: no-cache"); 
		header("Expires: 0"); //It will print all the Table row as Excel file row with selected column name as header. 
		echo ucwords($header)."\n".$table_data;
	}
	else
	{
		echo "<span style=\"font-weight:bold; color:red;\">No Records Found!</span>";
	}
mysql_close($link);
?>


