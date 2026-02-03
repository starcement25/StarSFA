<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];

/*if(employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$emp_hierarchy_condition=' SUBSTRING(survey_id,3,5) IN('.$employee_hierarchy.')';
}
else
{
	$emp_hierarchy_condition=" SUBSTRING(survey_id,3,5)='".$emp_code."'";
}*/
$emp_hierarchy_condition=" SUBSTRING(survey_id,3,5)='".$emp_code."'";
	$sql_approval = "SELECT * FROM survey_output WHERE ".$emp_hierarchy_condition." AND type='Technical Meets' AND is_approved IN('yes','reject') ORDER BY SUBSTRING(survey_id,-14,8) ASC";
	$res_approval = mysqli_query($link,$sql_approval);
	$count=mysqli_num_rows($res_approval);
	//print_r($vertical_array);
	if($count>0){
		$survey_id_array=array();
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
		$countapproval=0;
		while($row_approval = mysqli_fetch_assoc($res_approval)){
			$survey_id = $row_approval['survey_id'];
			$row_id=$row_approval['row_id'];
			$value=$row_approval['value'];
			$is_approved=$row_approval['is_approved'];
			$approved_date_time=$row_approval['approved_date_time'];
			$approved_by=$row_approval['approved_by'];
			if($row_id=='RA032')
			{
				${'meet_date'.$survey_id}=$value;
			}
			/*if($row_id=='RA043')
			{
				${'no_of_mason'.$survey_id}=$value;
			}*/
			if($row_id=='RA030')
			{
				${'dealer_code'.$survey_id}=$value;
			}
			if($row_id=='RA064')
			{
				${'mason_details'.$survey_id}=$value;
				$invitee_value=explode('#',substr($value,0,-1));
				${'no_of_mason'.$survey_id}=count($invitee_value);
			}
			${'is_approved'.$survey_id}=$is_approved;
			if(!in_array($survey_id,$survey_id_array))
			{
				array_push($survey_id_array,$survey_id);
			}

		}
		foreach($survey_id_array as $survey_id_val)
		{
			
			$sqlcustomer="SELECT customer_name FROM customer_master WHERE customer_code='".${'dealer_code'.$survey_id_val}."'";
			$rscustomer=mysqli_query($link,$sqlcustomer);
			$rowcustomer=mysqli_fetch_assoc($rscustomer);
			$dealer_name =$rowcustomer['customer_name'];
			 
			$contents  = (($survey_id_val!='')?$survey_id_val: ' ')."^";
			$contents  .= ((${'meet_date'.$survey_id_val}!='')?trim(preg_replace('/[\r\n]+/', '',${'meet_date'.$survey_id_val})): ' ')."^";
			$contents  .= ((${'no_of_mason'.$survey_id_val}!='')?${'no_of_mason'.$survey_id_val}: ' ')."^";
			$contents  .= ((${'dealer_code'.$survey_id_val}!='')?trim(preg_replace('/[\r\n]+/', '',${'dealer_code'.$survey_id_val})): ' ')."^";
			$contents  .= (($dealer_name!='')?$dealer_name: ' ')."^";
			$contents  .= ((${'mason_details'.$survey_id_val}!='')?trim(preg_replace('/[\r\n]+/', '',${'mason_details'.$survey_id_val})): ' ')."^";
			$contents  .= ((${'is_approved'.$survey_id_val}!='')?${'is_approved'.$survey_id_val}: ' ')."^";
			$contents  .= ' '."^";
			$contents  .= ' ';
			$linecontents  .= $contents."\n";
			$countapproval++;
		}
			$contentsrowcolumn=$countapproval.'¥'.'9';
			$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
		}
		else
		{
			$datacontents = '0'.'¥'.'0';
		}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/tech-meet-approval-meeting-details-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=tech-meet-approval-meet.txt");
	print "$datacontents"; 
	mysqli_close($link);		
?>
