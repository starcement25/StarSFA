<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
//$emp_code=$_REQUEST['emp_code'];

/*if(employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$emp_hierarchy_condition=' emp_code IN('.$employee_hierarchy.')';
}
else
{
	$emp_hierarchy_condition=" emp_code='".$emp_code."'";
}*/


	$sql_summary = "SELECT * FROM SIS_summary_header_new";
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
		     $header_id = $row_summary['header_id'];
		    $emp_code = $row_summary['emp_code'];
			$name = $row_summary['name'];
			$month_year=$row_summary['month_year'];
			$parameter_1=$row_summary['parameter_1'];
			$sales_volume_TGT=$row_summary['sales_volume_TGT'];
			$sales_volume_ACTUAL=$row_summary['sales_volume_ACTUAL'];
			$sis_slab_percent=$row_summary['sis_slab_percent'];
			$premium_sales_conversion_target=$row_summary['Premium_sales_conversion_target'];
			$sales_volume_SCORE_percent=$row_summary['sales_volume_SCORE_percent'];
			$parameter_2=$row_summary['parameter_2'];
			$monthly_unique_visit_TGT=$row_summary['monthly_unique_visit_TGT'];
			$monthly_unique_visit_ACH=$row_summary['monthly_unique_visit_ACH'];
			$activities_sis_slab_percent=$row_summary['activities_sis_slab_percent'];
			$activities=$row_summary['activities'];
			$monthly_unique_visit_SCORE_percent=$row_summary['monthly_unique_visit_SCORE_percent'];
			$parameter_3=$row_summary['parameter_3'];
			$dealer_appointment_ACTUAL=$row_summary['dealer_appointment_ACTUAL'];
			$dealer_appointment_sis_slab_percent=$row_summary['dealer_appointment_sis_slab_percent'];
			$influencer_registration=$row_summary['influencer_registration'];
			$dealer_appointment_SCORE_percent=$row_summary['dealer_appointment_SCORE_percent'];
			$parameter_3_5=$row_summary['parameter_3_5'];
			
			$parameter_4=$row_summary['parameter_4'];
			$active_dealer_count_TGT=$row_summary['active_dealer_count_TGT'];
			$active_dealer_count_ACH=$row_summary['active_dealer_count_ACH'];
			$active_dealer_count_sis_slab_percent=$row_summary['active_dealer_count_sis_slab_percent'];
			$active_dealer_growth=$row_summary['active_dealer_growth'];
			$active_dealer_count_SCORE_percent=$row_summary['active_dealer_count_SCORE_percent'];
			
			$paramiter_five=$row_summary['paramiter_five'];
			$five_TGT=$row_summary['five_TGT'];
			$five_ACH=$row_summary['five_ACH'];
			$five_sis_slab_percent=$row_summary['five_sis_slab_percent'];
			$active_influencer_growth=$row_summary['active_influencer_growth'];
			$five_SCORE_percent=$row_summary['five_SCORE_percent'];
			
			$paramiter_six=$row_summary['paramiter_six'];
			$six_ACTUAL=$row_summary['six_ACTUAL'];
			$six_slab_percent=$row_summary['six_slab_percent'];
			$six_SCORE_percent=$row_summary['six_SCORE_percent'];

				$paramiter_seven=$row_summary['parameter_seven'];
			$seven_TGT=$row_summary['seven_TGT'];
			$seven_ACH=$row_summary['seven_ACH'];
			$seven_sis_slab_percent=$row_summary['seven_sis_slab_percent'];
			$seven_WGT=$row_summary['seven_WGT'];
			$seven_SCORE_percent=$row_summary['seven_SCORE_percent'];
			
			
			$earning_score_percent=$row_summary['earning_score_percent'];
			$penalty_percent=$row_summary['penalty_percent'];
			$final_score_percent=$row_summary['final_score_percent'];
			
			$h6_l1=$row_summary['h6_l1'];
			$h6_l2=$row_summary['h6_l2'];
			$h6_l3=$row_summary['h6_l3'];
			$h6_l4=$row_summary['h6_l4'];
			
			$remarks=$row_summary['remarks'];
			//$countapproval++;
			$res_data[] = array("header_id"=>$header_id,"emp_code"=>$emp_code,"name"=>$name,"month_year"=>$month_year,"parameter_1"=>$parameter_1
,"sales_volume_TGT"=>$sales_volume_TGT,"sales_volume_ACTUAL"=>$sales_volume_ACTUAL,"sis_slab_percent"=>$sis_slab_percent,"premium_sales_conversion_target"=>$premium_sales_conversion_target,"sales_volume_SCORE_percent"=>$sales_volume_SCORE_percent,"parameter_2"=>$parameter_2,"monthly_unique_visit_TGT"=>$monthly_unique_visit_TGT,"monthly_unique_visit_ACH"=>$monthly_unique_visit_ACH,"activities_sis_slab_percent"=>$activities_sis_slab_percent,"activities"=>$activities,"monthly_unique_visit_SCORE_percent"=>$monthly_unique_visit_SCORE_percent,"parameter_3"=>$parameter_3,"dealer_appointment_ACTUAL"=>$dealer_appointment_ACTUAL,"dealer_appointment_sis_slab_percent"=>$dealer_appointment_sis_slab_percent,"influencer_registration"=>$influencer_registration,"dealer_appointment_SCORE_percent"=>$dealer_appointment_SCORE_percent
,"parameter_3_5"=>$parameter_3_5
,"parameter_4"=>$parameter_4,"active_dealer_count_TGT"=>$active_dealer_count_TGT,"active_dealer_count_ACH"=>$active_dealer_count_ACH,"active_dealer_count_sis_slab_percent"=>$active_dealer_count_sis_slab_percent,"active_dealer_growth"=>$active_dealer_growth,"active_dealer_count_SCORE_percent"=>$active_dealer_count_SCORE_percent,"paramiter_five"=>$paramiter_five,"five_TGT"=>$five_TGT,"five_ACH"=>$five_ACH,"five_sis_slab_percent"=>$five_sis_slab_percent,"active_influencer_growth"=>$active_influencer_growth,"five_SCORE_percent"=>$five_SCORE_percent,"paramiter_six"=>$paramiter_six,"six_ACTUAL"=>$six_ACTUAL,"six_slab_percent"=>$six_slab_percent,"six_SCORE_percent"=>$six_SCORE_percent,
"paramiter_seven"=>$paramiter_seven,"seven_TGT"=>$seven_TGT,"seven_ACH"=>$seven_ACH,"seven_sis_slab_percent"=>$seven_sis_slab_percent,"seven_WGT"=>$seven_WGT,"seven_SCORE_percent"=>$seven_SCORE_percent,		"earning_score_percent"=>$earning_score_percent,"penalty_percent"=>$penalty_percent,"final_score_percent"=>$final_score_percent,"h6_l1"=>$h6_l1,"h6_l2"=>$h6_l2,"h6_l3"=>$h6_l3,"h6_l4"=>$h6_l4, "remarks"=>$remarks);
			
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
	$url = APICALLLOGURL."/sis-summary-details-bd.php?nick_name=$nick_name&emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	/*header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=tech-meet-approval.txt");
	print "$datacontents"; */
	echo json_encode($res_data_final);
	mysqli_close($link);		
?>
