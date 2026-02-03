<?php

require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];

//$selected_emp="E0555";

         
$sql_qry = "SELECT DISTINCT emp_code FROM lead_generation_master where  assigned_to='".$emp_code."'";


	$res_lg = mysqli_query($link,$sql_qry);
	$count=mysqli_num_rows($res_lg);
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
		
		
		while($row_lg = mysqli_fetch_assoc($res_lg)){
			
			$emp_code1=$row_lg['emp_code'];
		
			$sqlempdnscode="SELECT emp_code,emp_name FROM employee_master WHERE emp_code='".$emp_code1."'";
            $rsempdnscode=mysqli_query($link,$sqlempdnscode);
            $rowempdnscode=mysqli_fetch_assoc($rsempdnscode);
            $emp_name=$rowempdnscode['emp_name'];
            $emp_code2=$rowempdnscode['emp_code'];
	
			//$countapproval++;
			$res_data[] = array("emp_code"=>$emp_code2,"emp_name"=>$emp_name);
			
		}
			$countcolumns='2';
			$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
			$res_data_final = array("process_status"=>"YES","process_message"=>"Success.","countrows"=>$count,"countcolumns"=>$countcolumns,"datetime"=>$contentsdatetime,"datavalue"=>$res_data );
		}
		else
		{
			$res_data_final = array("process_status"=>"NO","process_message"=>"Something went wrong" );
		}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/lead-generation-download-approval-emp.php?nick_name=$nick_name&emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	/*header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=tech-meet-approval.txt");
	print "$datacontents"; */
	echo json_encode($res_data_final);
	mysqli_close($link);


?>