<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];

if(employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$emp_hierarchy_condition=' emp_code IN('.$employee_hierarchy.')';
}
else
{
	$emp_hierarchy_condition=" emp_code='".$emp_code."'";
}


	$sql_weightage = "SELECT * FROM sis_app_visibility";
	$res_weightage = mysqli_query($link,$sql_weightage);
	$count=mysqli_num_rows($res_weightage);
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
		while($row_weightage = mysqli_fetch_assoc($res_weightage)){
			$sis_parameter = $row_weightage['sis_parameter'];
			$ROE_L2=$row_weightage['ROE_L2'];
			$ROE_L3_above=$row_weightage['ROE_L3_above'];
			$NE_L2=$row_weightage['NE_L2'];
			$NE_L3_above=$row_weightage['NE_L3_above'];
			//$countapproval++;
			$res_data[] = array("sis_parameter"=>$sis_parameter,"ROE_L2"=>$ROE_L2,"ROE_L3_above"=>$ROE_L3_above,"NE_L2"=>$NE_L2,"NE_L3_above"=>$NE_L3_above);
			
		}
			$countcolumns='5';
			$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
			$res_data_final = array("process_status"=>"YES","process_message"=>"Success.","countrows"=>$count,"countcolumns"=>$countcolumns,"datetime"=>$contentsdatetime,"datavalue"=>$res_data );
		}
		else
		{
			$res_data_final = array("process_status"=>"NO","process_message"=>"	" );
		}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/sis-app-visibility-json.php?nick_name=$nick_name&emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	/*header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=tech-meet-approval.txt");
	print "$datacontents"; */
	echo json_encode($res_data_final);
	mysqli_close($link);		
?>
