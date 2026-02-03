<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/config-email-setup.php");
$telecaller_form_details = "telecaller_form_details";
$emp_code=$_REQUEST['emp_code'];
$inputJSON = file_get_contents('php://input');
$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
					 xml='".addslashes($inputJSON)."',
					insertdate=CURRENT_TIMESTAMP()";
mysqli_query($link,$sqlinsert_xml_data);	
//$inputJSON='{"telecaller_form_id":"TCE000220220517183848","customer_name":"S","mobile":"1234567890","is_connected":"yes","not_connected_reason":"","demo_existing_product":"Chimney#chi#6,RO#ro#2","demo_achieved":"yes","demo_appointment_datetime":"2022-5-18 0:0","allocated_user":"GL","next_appointment_date_time":"","service_interest":"yes","service_type":"s t","collected_amount":"400","service_date":"2022-08-17","remarks":"re","update_by":"E0002"}';

$input= json_decode($inputJSON,true);
//print_r($input);
/*$value='{"telecaller_form_id":"TCE000420220421115704","customer_name":"test","mobile":"9999999999","is_connected":"yes","not_connected_reason":"",
"demo_existing_product":"chimney,ro","demo_achieved":"yes","demo_appointment_datetime":"2022-04-25 14:29:10","allocated_user":"GL","next_appointment_date_time": "2022-04-27 14:29:10",
"service_interest":"yes","service_type":"","collected_amount":"18000","service_date":"2022-04-28","remarks":"","update_by":"E0004"};*/

$telecaller_form_id=$input['telecaller_form_id'];
if($telecaller_form_id!=''){
		$tent_knocking_form_id=$input['tent_knocking_form_id'];
		$customer_name=$input['customer_name'];
		$mobile=$input['mobile'];
		$is_connected=$input['is_connected'];
		$not_connected_reason=$input['not_connected_reason'];
		$demo_existing_product=$input['demo_existing_product'];
		$demo_achieved=$input['demo_achieved'];
		$demo_appointment_datetime=$input['demo_appointment_datetime'];
		$allocated_user=$input['allocated_user'];
		$next_appointment_date_time=$input['next_appointment_date_time'];
		$service_interest=$input['service_interest'];
		$service_type=$input['service_type'];
		$collected_amount=$input['collected_amount'];
		$service_date=$input['service_date'];
		$remarks=$input['remarks'];
		$update_by=$input['update_by'];
		$demo_interested_product=$input['demo_interested_product'];
		
		$sqlchktelecallerform="SELECT telecaller_form_id FROM $telecaller_form_details WHERE telecaller_form_id='".addslashes($telecaller_form_id)."'";
		$rschktelecallerform=mysqli_query($link,$sqlchktelecallerform);
		$countchktelecallerform=mysqli_num_rows($rschktelecallerform);
		if($countchktelecallerform==0){
		$sqlinserttelecallerdetails="INSERT INTO $telecaller_form_details SET telecaller_form_id='".addslashes($telecaller_form_id)."',
							tent_knocking_form_id='".addslashes($tent_knocking_form_id)."',
							customer_name='".addslashes($customer_name)."',
							mobile='".addslashes($mobile)."',
							is_connected='".addslashes($is_connected)."',
							not_connected_reason='".addslashes($not_connected_reason)."',
							demo_existing_product='".addslashes($demo_existing_product)."',
							 demo_achieved='".addslashes($demo_achieved)."',
							demo_appointment_datetime='".addslashes($demo_appointment_datetime)."',
							allocated_user='".addslashes($allocated_user)."',
							service_interest='".addslashes($service_interest)."',
							next_appointment_date_time='".addslashes($next_appointment_date_time)."',
							service_type='".addslashes($service_type)."',
							collected_amount='".addslashes($collected_amount)."',
							service_date='".addslashes($service_date)."',
							remarks='".addslashes($remarks)."',
							demo_interested_product='".addslashes($demo_interested_product)."',
							update_by='".addslashes($update_by)."',
							update_date_time=CURRENT_TIMESTAMP()";
		if(mysqli_query($link,$sqlinserttelecallerdetails))
		{
			$res_data = array("process_status"=>"YES","process_message"=>'Tele Caller Form Details saved successfully');
		}
		else
		{
			$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong");
		}
	  }
	  else
	  {
		  $res_data = array("process_status"=>"YES","process_message"=>'Tele Caller Form Details saved successfully');
	  }
	}else{
	   $res_data = array("process_status"=>"NO","process_message"=>"Something went wrong");
	}
	echo json_encode($res_data);
mysqli_close();
?>