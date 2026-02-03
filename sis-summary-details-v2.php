<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];

/*if(employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$emp_hierarchy_condition=' emp_code IN('.$employee_hierarchy.')';
}
else
{
	$emp_hierarchy_condition=" emp_code='".$emp_code."'";
}*/
$sqlempdnscode="SELECT dns_emp_code FROM employee_master WHERE emp_code='".$emp_code."'";
$rsempdnscode=mysqli_query($link,$sqlempdnscode);
$rowempdnscode=mysqli_fetch_assoc($rsempdnscode);
$dns_emp_code=$rowempdnscode['dns_emp_code'];

	$sql_summary = "SELECT * FROM SIS_summary where emp_code='".$dns_emp_code."'";
	$res_summary = mysqli_query($link,$sql_summary);
	$count=mysqli_num_rows($res_summary);
	//print_r($vertical_array);
	if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
		$contentsdatetime =$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
		while($row_summary = mysqli_fetch_assoc($res_summary)){
			$name = $row_summary['name'];
			$month_year=$row_summary['month_year'];
			$sales_volume_MT=$row_summary['sales_volume_MT'];
			$sales_volume_TGT=$row_summary['sales_volume_TGT'];
			$sales_volume_ACH=$row_summary['sales_volume_ACH'];
			$sales_volume_ACH_percent=$row_summary['sales_volume_ACH_percent'];
			$sales_volume_WGT_percent=$row_summary['sales_volume_WGT_percent'];
			$sales_volume_SCORE_percent=$row_summary['sales_volume_SCORE_percent'];
			$monthly_unique_visit=$row_summary['monthly_unique_visit'];
			$monthly_unique_visit_TGT=$row_summary['monthly_unique_visit_TGT'];
			$monthly_unique_visit_ACH=$row_summary['monthly_unique_visit_ACH'];
			$monthly_unique_visit_ACH_percent=$row_summary['monthly_unique_visit_ACH_percent'];
			$monthly_unique_visit_WGT_percent=$row_summary['monthly_unique_visit_WGT_percent'];
			$monthly_unique_visit_SCORE_percent=$row_summary['monthly_unique_visit_SCORE_percent'];
			$dealer_appointment=$row_summary['dealer_appointment'];
			$dealer_appointment_TGT=$row_summary['dealer_appointment_TGT'];
			$dealer_appointment_ACH=$row_summary['dealer_appointment_ACH'];
			$dealer_appointment_ACH_percent=$row_summary['dealer_appointment_ACH_percent'];
			$dealer_appointmen_WGT_percent=$row_summary['dealer_appointmen_WGT_percent'];
			$dealer_appointment_SCORE_percent=$row_summary['dealer_appointment_SCORE_percent'];
			$active_dealer_count=$row_summary['active_dealer_count'];
			$active_dealer_count_TGT=$row_summary['active_dealer_count_TGT'];
			$active_dealer_count_ACH=$row_summary['active_dealer_count_ACH'];
			$active_dealer_count_ACH_percent=$row_summary['active_dealer_count_ACH_percent'];
			$active_dealer_count_WGT_percent=$row_summary['active_dealer_count_WGT_percent'];
			$active_dealer_count_SCORE_percent=$row_summary['active_dealer_count_SCORE_percent'];
			
			$paramiter_five=$row_summary['paramiter_five'];
			$five_TGT=$row_summary['five_TGT'];
			$five_ACH=$row_summary['five_ACH'];
			$five_ACH_percent=$row_summary['five_ACH_percent'];
			$five_WGT_percent=$row_summary['five_WGT_percent'];
			$five_SCORE_percent=$row_summary['five_SCORE_percent'];
			
			$paramiter_six=$row_summary['paramiter_six'];
			$six_TGT=$row_summary['six_TGT'];
			$six_ACH=$row_summary['six_ACH'];
			$six_ACH_percent=$row_summary['six_ACH_percent'];
			$six_WGT_percent=$row_summary['six_WGT_percent'];
			$six_SCORE_percent=$row_summary['six_SCORE_percent'];

			$paramiter_seven=$row_summary['paramiter_seven'];
			$seven_TGT=$row_summary['seven_TGT'];
			$seven_ACH=$row_summary['seven_ACH'];
			$seven_ACH_percent=$row_summary['seven_ACH_percent'];
			$seven_WGT_percent=$row_summary['seven_WGT_percent'];
			$seven_SCORE_percent=$row_summary['seven_SCORE_percent'];
			
			
			$earning_score_percent=$row_summary['earning_score_percent'];
			$penalty_percent=$row_summary['penalty_percent'];
			$final_score_percent=$row_summary['final_score_percent'];
			$OTSI=$row_summary['OTSI'];
			$SIS_earning_month=$row_summary['SIS_earning_month'];
			$remarks=$row_summary['remarks'];
			$header_id=$row_summary['header_id'];
			//$countapproval++;
			$res_data[] = array("emp_code"=>$emp_code,"name"=>$name,"month_year"=>$month_year,"sales_volume_MT"=>$sales_volume_MT,"sales_volume_TGT"=>$sales_volume_TGT,"sales_volume_ACH"=>$sales_volume_ACH,"sales_volume_ACH_percent"=>$sales_volume_ACH_percent,"sales_volume_WGT_percent"=>$sales_volume_WGT_percent,"sales_volume_SCORE_percent"=>$sales_volume_SCORE_percent,"monthly_unique_visit"=>$monthly_unique_visit,"monthly_unique_visit_TGT"=>$monthly_unique_visit_TGT,"monthly_unique_visit_ACH"=>$monthly_unique_visit_ACH,"monthly_unique_visit_ACH_percent"=>$monthly_unique_visit_ACH_percent,"monthly_unique_visit_WGT_percent"=>$monthly_unique_visit_WGT_percent,"monthly_unique_visit_SCORE_percent"=>$monthly_unique_visit_SCORE_percent,"dealer_appointment"=>$dealer_appointment,"dealer_appointment_TGT"=>$dealer_appointment_TGT,"dealer_appointment_ACH"=>$dealer_appointment_ACH,"dealer_appointment_ACH_percent"=>$dealer_appointment_ACH_percent,"dealer_appointmen_WGT_percent"=>$dealer_appointmen_WGT_percent,"dealer_appointment_SCORE_percent"=>$dealer_appointment_SCORE_percent,"active_dealer_count"=>$active_dealer_count,"active_dealer_count_TGT"=>$active_dealer_count_TGT,"active_dealer_count_ACH"=>$active_dealer_count_ACH,"active_dealer_count_ACH_percent"=>$active_dealer_count_ACH_percent,"active_dealer_count_WGT_percent"=>$active_dealer_count_WGT_percent,"active_dealer_count_SCORE_percent"=>$active_dealer_count_SCORE_percent,"paramiter_five"=>$paramiter_five,"five_TGT"=>$five_TGT,"five_ACH"=>$five_ACH,"five_ACH_percent"=>$five_ACH_percent,"five_WGT_percent"=>$five_WGT_percent,"five_SCORE_percent"=>$five_SCORE_percent,"paramiter_six"=>$paramiter_six,"six_TGT"=>$six_TGT,"six_ACH"=>$six_ACH,"six_ACH_percent"=>$six_ACH_percent,"six_WGT_percent"=>$six_WGT_percent,"six_SCORE_percent"=>$six_SCORE_percent,"paramiter_seven"=>$paramiter_seven,"seven_TGT"=>$seven_TGT,"seven_ACH"=>$seven_ACH,"seven_ACH_percent"=>$seven_ACH_percent,"seven_WGT_percent"=>$seven_WGT_percent,"seven_SCORE_percent"=>$seven_SCORE_percent,		"earning_score_percent"=>$earning_score_percent,"penalty_percent"=>$penalty_percent,"final_score_percent"=>$final_score_percent,"OTSI"=>$OTSI,"SIS_earning_month"=>$SIS_earning_month,"remarks"=>$remarks,"header_id"=>$header_id);
			
		}
			$countcolumns='45';
			$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
			$res_data_final = array("process_status"=>"YES","process_message"=>"Success.","countrows"=>$count,"countcolumns"=>$countcolumns,"datetime"=>$contentsdatetime,"datavalue"=>$res_data );
		}
		else
		{
			$res_data_final = array("process_status"=>"NO","process_message"=>"Something went wrong" );
		}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/sis-summary-details.php?nick_name=$nick_name&emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	/*header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=tech-meet-approval.txt");
	print "$datacontents"; */
	echo json_encode($res_data_final);
	mysqli_close($link);		
?>
