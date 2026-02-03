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
	$payment_condition = '';
	$emp_condition = ' 1';
}
else{
	$att_condition = " SUBSTRING(trans_id,2,5) IN(".$employee.") AND ";
	$checkinout_condition = " SUBSTRING(trans_id,3,5) IN(".$employee.") AND ";
	$emp_condition = " emp_code IN(".$employee.") ";
}

$sql_att_date = "SELECT SUBSTRING(trans_id,-14,8) AS att_date FROM location WHERE ".$att_condition." (SUBSTRING(trans_id,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') AND trans_id LIKE 'A%'";
$res_att_date = mysql_query($sql_att_date);
while($row_att_date = mysql_fetch_array($res_att_date)){
	$att_date = $row_att_date['att_date'];
	if(!in_array($att_date,$date_array))
		array_push($date_array,$att_date);
}

$sql_checkin_out_date = "SELECT SUBSTRING(trans_id,-14,8) AS checkinout_date FROM check_in_out_details WHERE ".$checkinout_condition." (SUBSTRING(trans_id,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') AND trans_id LIKE 'C%'";
$res_checkin_out_date = mysql_query($sql_checkin_out_date);
while($row_checkin_out_date = mysql_fetch_array($res_checkin_out_date)){
	$checkinout_date = $row_checkin_out_date['checkinout_date'];
	if(!in_array($checkinout_date,$date_array))
		array_push($date_array,$checkinout_date);
}

sort($date_array);
//if(!empty($date_array)){
	$header = "Date"."\t"."Customer Name"."\t"."Employee Code"."\t"."Employee Name"."\t"."Total Hours"."\t"."Attendence"."\t"."PJP";
	$sql_emp = "SELECT emp_code, dns_emp_code, emp_name FROM employee_master WHERE ".$emp_condition;
	$res_emp = mysql_query($sql_emp);
	while($row_emp = mysql_fetch_array($res_emp)){
		$dns_emp_code = $row_emp['dns_emp_code'];
		$emp_code = $row_emp['emp_code'];
		$emp_name = $row_emp['emp_name'];
		
		/*$sql_location_check = "SELECT trans_id FROM location WHERE (trans_id LIKE 'O%' OR trans_id LIKE 'P%' OR trans_id LIKE 'CI%') AND 
							emp_code = '".$emp_code."' AND (SUBSTRING(date,1,10) BETWEEN '".$start_date."' AND '".$end_date."') ";
		$res_location_check = mysql_query($sql_location_check);
		$location_row_check = mysql_num_rows($res_location_check);*/
		${customer_code_array.$emp_code}=array();
		$sqlcustomercheck="SELECT DISTINCT customer_code,customer_name FROM customer_master WHERE customer_code IN(SELECT customer_code FROM check_in_out_details WHERE (SUBSTRING(trans_id,-14,8)  BETWEEN '".$start_date_search."' AND '".$end_date_search."') AND SUBSTRING(trans_id,3,5) = '".$emp_code."')";
		$rscustomercheck=mysql_query($sqlcustomercheck);
		$customer_row_check=mysql_num_rows($rscustomercheck);
		
		if($customer_row_check>0){
			
			while($rowcustomercheck=mysql_fetch_array($rscustomercheck)){
				$customer_code=$rowcustomercheck['customer_code'];
				$customer_name=$rowcustomercheck['customer_name'];
				foreach($date_array as $date_array_val)
				{
						$sqlattendance="SELECT SUBSTRING(date,12,8) AS att_time FROM location WHERE trans_id LIKE 'A%' AND 
										SUBSTRING(trans_id,-14,8)='".$date_array_val."' AND SUBSTRING(trans_id,2,5) = '".$emp_code."'";
						$rsattendance=mysql_query($sqlattendance);
						$countattendance=mysql_num_rows($rsattendance);
						$rowattendance=mysql_fetch_array($rsattendance);	
						$att_time=$rowattendance['att_time'];
						if($countattendance >0){
							${att_time.$date_array_val.$emp_code}=$att_time;
						}
	
						
						$date_array_val_seperator=date('Y-m-d',strtotime($date_array_val));
						$sqlpjp="SELECT RM.route_name FROM route_master RM,route_plan RP WHERE RP.route_code=RM.route_code 
								AND RP.visit_date='".$date_array_val_seperator."' AND RP.emp_code='".$emp_code."'";
						$rspjp=mysql_query($sqlpjp);
						${pjp.$date_array_val.$emp_code}='';
						while($rowpjp=mysql_fetch_array($rspjp))
						{
							${pjp.$date_array_val.$emp_code}=${pjp.$date_array_val.$emp_code}.$rowpjp['route_name'].',';
						}
						//For Check in and Check out
						$sqlcheckinout="SELECT check_in_time,check_out_time,remarks FROM check_in_out_details 
									  WHERE customer_code='".$customer_code."' AND SUBSTRING(trans_id,-14,8)='".$date_array_val."' 
									 AND SUBSTRING(trans_id,3,5) = '".$emp_code."'";
						$rscheckinout=mysql_query($sqlcheckinout) or die(mysql_error()." Error in select check in out details ".$sqlcheckinout);
						$countcheckinout=mysql_num_rows($rscheckinout);
						if($countcheckinout >0)
						{
							$time_difference_final=0;
							while($rowcheckinout=mysql_fetch_array($rscheckinout))
							{
								$check_in_time=date('d-m-Y H:i:s',strtotime($rowcheckinout['check_in_time']));
								$check_out_time=date('d-m-Y H:i:s',strtotime($rowcheckinout['check_out_time']));
								$time_difference=strtotime($rowcheckinout['check_out_time'])-strtotime($rowcheckinout['check_in_time']);
								$time_difference_final=$time_difference_final+$time_difference;
								$checkinout_remarks=$rowcheckinout['remarks'];
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
							${customer_name.$customer_code.$date_array_val.$emp_code}=$customer_name;
							${time_duration.$customer_code.$date_array_val.$emp_code}=$time_duration;
							${time_difference_final.$customer_code.$date_array_val.$emp_code}=$time_difference_final;
						}
						else
						{
							$time_duration='';
							$time_difference_final='';
							$checkinout_remarks='';
						}
					  }
					  	array_push(${customer_code_array.$emp_code},$customer_code);
					}
					$current_val_array=array();
					$dateval_attendence_array=array();
					foreach($date_array as $date_array_val)
					{
						$attendance_val=$date_array_val.$emp_code.${att_time.$date_array_val.$emp_code};
						foreach(${customer_code_array.$emp_code} as $customer_code_val)
						{
							if(${customer_name.$customer_code_val.$date_array_val.$emp_code}!='')
							{
								$table_data .= date('d-m-Y',strtotime($date_array_val))."\t".${customer_name.$customer_code_val.$date_array_val.$emp_code}."\t".$dns_emp_code."\t".$emp_name."\t".${time_duration.$customer_code_val.$date_array_val.$emp_code}."\t".${att_time.$date_array_val.$emp_code}."\t".substr(${pjp.$date_array_val.$emp_code},0,-1)."\n";
								 if(!in_array($attendance_val,$dateval_attendence_array))
									{ 
									   array_push($dateval_attendence_array,$attendance_val);
									}
							}
						}
						if(!in_array($attendance_val,$dateval_attendence_array))
						{
						$table_data .= date('d-m-Y',strtotime($date_array_val))."\t".''."\t".$dns_emp_code."\t".$emp_name."\t".''."\t".${att_time.$date_array_val.$emp_code}."\t".substr(${pjp.$date_array_val.$emp_code},0,-1)."\n";

						  array_push($dateval_attendence_array,$attendance_val);
						}
					}
				}
			}
	if($table_data !=''){	
		header("Content-type: application/octet-stream"); 
		header("Content-Disposition: attachment; filename=Daily_Activity_Report_Summary_SKIPPER.xls"); 
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


