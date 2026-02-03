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


	$sql_accessibility = "SELECT * FROM prop_form_accessibility";
	$res_accessibility = mysqli_query($link,$sql_accessibility);
	$count=mysqli_num_rows($res_accessibility);
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
		while($row_accessibility = mysqli_fetch_assoc($res_accessibility)){
			$hierarchy = $row_accessibility['hierarchy'];
			$knocking_sheet=$row_accessibility['knocking_sheet'];
			$tent_sheet=$row_accessibility['tent_sheet'];
			$demo_sheet=$row_accessibility['demo_sheet'];
			$sales_closure_form=$row_accessibility['sales_closure_form'];
			$booking_closure_form=$row_accessibility['booking_closure_form'];
			$group_leader_form=$row_accessibility['group_leader_form'];
			$telecaller_form=$row_accessibility['telecaller form'];
			//$countapproval++;
			$res_data[] = array("hierarchy"=>$hierarchy,"tent_sheet"=>$tent_sheet,"knocking_sheet"=>$knocking_sheet,"demo_sheet"=>$demo_sheet,"sales_closure_form"=>$sales_closure_form,"booking_closure_form"=>$booking_closure_form,"group_leader_form"=>$group_leader_form,"telecaller_form"=>$telecaller_form);
			
		}
			$countcolumns='7';
			$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
			$res_data_final = array("process_status"=>"YES","process_message"=>"Success.","countrows"=>$count,"countcolumns"=>$countcolumns,"datetime"=>$contentsdatetime,"datavalue"=>$res_data );
		}
		else
		{
			$res_data_final = array("process_status"=>"NO","process_message"=>"	" );
		}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/prop_form_accessibility.php?nick_name=$nick_name&emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	/*header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=tech-meet-approval.txt");
	print "$datacontents"; */
	echo json_encode($res_data_final);
	mysqli_close($link);		
?>
