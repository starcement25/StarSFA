<?php

require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];
$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);
$incremental_download=$_REQUEST['incremental_download'];
$data_download_time=$_REQUEST['data_download_time'];
$data_download_time=str_replace('€',' ',$data_download_time);

$date=date('Y-m-d');
$time=date('H:i:s');
$contentsdatetime = $date.'€'.$time."\n";

$action_on_lead =  $_REQUEST['action_on_lead'];
$selected_emp =  $_REQUEST['selected_emp'];

$from_date = $_REQUEST['from'];
$to_date = $_REQUEST['to'];
$from_dateArray = explode('/', $from_date);
$to_dateArray = explode('/', $to_date);
$from_date = $from_dateArray[2].$from_dateArray[1].$from_dateArray[0];
$to_date = $to_dateArray[2].$to_dateArray[1].$to_dateArray[0];
$from_date = date('Y-m-d', strtotime($from_date));
$to_date = date('Y-m-d', strtotime($to_date));


$sql_survey_output = "SELECT survey_id,row_id,value FROM survey_output where survey_id IN(SELECT survey_id FROM survey_output WHERE row_id ='RA417' AND value='".$emp_code."')";

            if ($from_date != '' && $to_date != '') {
                $date_condition = " AND DATE_FORMAT(SUBSTRING(SO.survey_id,-14,8),'%Y-%m-%d') >='" . $from_date . "' AND 
					  	DATE_FORMAT(SUBSTRING(SO.survey_id,-14,8),'%Y-%m-%d') <='" . $to_date . "'";
					  	
					  	
					  	
                $sql_survey_output = "SELECT survey_id,row_id,value FROM survey_output SO where survey_id IN(SELECT survey_id FROM survey_output WHERE row_id ='RA417' AND value='".$emp_code."' $date_condition)";
					  	
            }
  $date_condition="";          
            if ($from_date != '' && $to_date != '') {
                $date_condition = " AND DATE_FORMAT(SUBSTRING(LG.lead_generation_id,-14,8),'%Y-%m-%d') >='" . $from_date . "' AND 
					  	DATE_FORMAT(SUBSTRING(LG.lead_generation_id,-14,8),'%Y-%m-%d') <='" . $to_date . "'";
					  	
            }  
            
            
            
$sql_qry = "SELECT *,DATE_FORMAT(SUBSTRING(LG.lead_generation_id,-14,8),'%Y-%m-%d') as lg_date FROM lead_generation_master LG where action_on_lead='".$action_on_lead."' AND emp_code='".$selected_emp."' AND  assigned_to='".$emp_code."' $date_condition";

//echo $sql_qry;exit();
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
			$lead_generation_id = $row_lg['lead_generation_id'];
			$lg_date = $row_lg['lg_date'];
			$lg_date1 = date("F", strtotime($lg_date));
			$month_year=$lg_date1;
			$party_name=$row_lg['party_name'];
			$emp_code1=$row_lg['emp_code'];
			$next_visit_date=$row_lg['next_visit_date'];
			$category_type_construction=$row_lg['category_type_construction'];
			$lead_status=$row_lg['lead_status'];
			
			$sqlempdnscode="SELECT emp_name FROM employee_master WHERE emp_code='".$emp_code1."'";
            $rsempdnscode=mysqli_query($link,$sqlempdnscode);
            $rowempdnscode=mysqli_fetch_assoc($rsempdnscode);
            $emp_name=$rowempdnscode['emp_name'];
            
            
          /*  $sql_survey_output = "SELECT survey_id FROM survey_output WHERE  `value` LIKE '%".$lead_generation_id."%' ORDER by SUBSTRING(survey_id,-14,14) DESC LIMIT 1";
		    $res_survey_output = mysqli_query($link,$sql_survey_output);
		    $row_survey_output = mysqli_fetch_assoc($res_survey_output);
            $survey_id = $row_survey_output['survey_id'];*/
            
            
            
	
			//$countapproval++;
			$res_data[] = array("emp_name"=>$emp_name,"party_name"=>$party_name,"month_year"=>$month_year,"lead_generation_id"=>$lead_generation_id,"s_id"=>$lead_generation_id,"next_visit_date"=>$next_visit_date,"category_type_construction"=>$category_type_construction,"lead_status"=>$lead_status
			);
			
		}
			$countcolumns='7';
			$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
			$res_data_final = array("process_status"=>"YES","process_message"=>"Success.","countrows"=>$count,"countcolumns"=>$countcolumns,"datetime"=>$contentsdatetime,"datavalue"=>$res_data );
		}
		else
		{
			$res_data_final = array("process_status"=>"NO","process_message"=>"Something went wrong" );
		}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/lead-generation-download-approval.php?nick_name=$nick_name&emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	/*header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=tech-meet-approval.txt");
	print "$datacontents"; */
	echo json_encode($res_data_final);
	mysqli_close($link);


?>