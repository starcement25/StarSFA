<?php
ob_start();
session_start();
require("adminUtils.php");

$start_date = $_REQUEST['start_date'];
if($_REQUEST['dispmode']=='today' || $_REQUEST['dispmode']==''){
	$date=date('d-m-Y');
}
if($_REQUEST['dispmode']=='yesterday'){
	$curdate=date('d-m-Y');
	$date=date('d-m-Y', strtotime("-1 days,$curdate "));
}
if($_SESSION['admin_login']=="admin"){
		$emp_hierarchy='';
		$emp_hierarchy_condition='';
		$emp_hierarchy_condition_one='';
	}
	else
	{
		$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_condition=' AND LO.emp_code IN('.$emp_hierarchy.')';
		$emp_hierarchy_condition_one=' AND EM.emp_code IN('.$emp_hierarchy.')';
	}
$header ="Attendence Data of ".date('d-m-Y',strtotime($date))."\n";
$header .= strtoupper($_SESSION['nick_name'])." Emp code"."\t"."Emp code"."\t"."Name"."\t"."HQ"."\t"."District"."\t"."Designation"."\t"."Vertical"."\t"."Check-In Time"."\t"."Checkout Time"."\t"."Duration"."\t"."Active/Inactivee";
			if($date!='')
			{
				$date_condition ="  AND DATE_FORMAT(LO.date,'%d-%m-%Y') LIKE '%".$date."%'";
			}
			else
			{
				$date_condition='';
			}
				
			$sqlinformation="SELECT EM.emp_name,EM.emp_code,EM.dns_emp_code,EM.HQ,LO.trans_id,DATE_FORMAT(LO.date,'%T') AS time,EM.acedns,LO.latt,LO.longi,EM.district,EM.designation,EM.vertical_value FROM 
							location LO,employee_master EM WHERE LO.emp_code=EM.emp_code AND LO.trans_id LIKE 'A%' 
							AND SUBSTRING(EM.emp_code,1,1)!='C' ".$emp_hierarchy_condition.$date_condition." 
							ORDER BY EM.emp_code ASC ";
			$resinformation=mysql_query($sqlinformation) or die(mysql_error()." Error in select transaction information: ".$sqlinformation);
			$count=mysql_num_rows($resinformation);
			if($count > 0)
			{ 
				while($rowinformation=mysql_fetch_array($resinformation))
				{
					$trans_id=$rowinformation['trans_id'];
					$latt=$rowinformation['latt'];
					$longi=$rowinformation['longi'];
					$time=$rowinformation['time'];
					$emp_name=$rowinformation['emp_name'];
					$emp_code=$rowinformation['emp_code'];
					$dns_emp_code=$rowinformation['dns_emp_code'];
					$HQ=$rowinformation['HQ'];
					$district=$rowinformation['district'];
					$designation=$rowinformation['designation'];
					$vertical_value=$rowinformation['vertical_value'];
					$acedns=$rowinformation['acedns'];
					
					$sql_checkout = "SELECT SUBSTRING(LO.date,12) AS checkout_time,trans_id,latt,longi FROM location LO WHERE LO.emp_code = '".$emp_code."' 
									AND LO.trans_id LIKE 'CH%'".$date_condition;
					$res_checkout = mysql_query($sql_checkout);
					$row_checkout = mysql_fetch_array($res_checkout);
					$check_out_time = $row_checkout['checkout_time'];
					$chk_out_trans_id=$row_checkout['trans_id'];
					$chk_out_latt=$row_checkout['latt'];
					$chk_out_longi=$row_checkout['longi'];
					if($check_out_time == '')
						$check_out_time = '--';
					if(strtoupper($acedns)=='Y'){
						$active_inactive='ACTIVE';
					}
					else {
						$active_inactive='INACTIVE';
					}
					if($check_out_time != '--')
					{
						$time_difference=strtotime($check_out_time)-strtotime($time);
						$time_difference_final=$time_difference;
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
						$time_duration='--';
					}
					$table_data .=$dns_emp_code."\t".$emp_code."\t".$emp_name."\t".$HQ."\t".$district."\t".$designation."\t".$vertical_value."\t".$time."\t".$check_out_time."\t".$time_duration."\t".$active_inactive."\n";
				}
			}
		if($table_data !=''){
			$datetime = gmdate('Y_m_d_H_i',strtotime('+330 minute'));
			$filename="attendance_tracker".$datetime.'.xls';	
			header("Content-type: application/octet-stream"); 
			header("Content-Disposition: attachment; filename=$filename"); 
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


