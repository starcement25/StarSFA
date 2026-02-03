<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/config-email-setup.php");
$demo_form_details = "demo_form_details";
$emp_code=$_REQUEST['emp_code'];

$inputJSON = file_get_contents('php://input');
$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
					 xml='".addslashes($inputJSON)."',
					insertdate=CURRENT_TIMESTAMP()";
mysqli_query($link,$sqlinsert_xml_data);	
$input= json_decode($inputJSON,true);
//print_r($input);
/*$value='{"demo_form_id":"DFE000120220421115704","demo_latt":"18.22","demo_longi":"22.23","customer_name":"test1","prod_interested":"chimney,ro","demo_achieved":"yes","future_appointment_date":"2022-04-23","demo_given_by":"A Rao","sales_achieved":"yes",
"ask_details":"test details","full_payment":full","sale_model":"test model","sale_price":"1200.50","sale_exchange":"test exchange","sale_payment_details":"test payment","booking_done":"yes","booking_model":"test booking","booking_sale_price":"1300.20","booking_adv_amount":"1000.00","booking_full_payment":"full","balance_due":"300.20"};*/

$demo_form_id=$input['demo_form_id'];
if($demo_form_id!=''){
		$demo_latt=$input['demo_latt'];
		$demo_longi=$input['demo_longi'];
		$customer_name=$input['customer_name'];
		$prod_interested=$input['prod_interested'];
		$demo_achieved=$input['demo_achieved'];
		$future_appointment_date=$input['future_appointment_date'];
		$demo_given_by=$input['demo_given_by'];
		$sales_achieved=$input['sales_achieved'];
		$ask_details=$input['ask_details'];
		$full_payment=$input['full_payment'];
		$sale_model=$input['sale_model'];
		$sale_price=$input['sale_price'];
		$sale_exchange=$input['sale_exchange'];
		$sale_payment_details=$input['sale_payment_details'];
		$booking_done=$input['booking_done'];
		$booking_model=$input['booking_model'];
		$booking_sale_price=$input['booking_sale_price'];
		$booking_adv_amount=$input['booking_adv_amount'];
		$booking_full_payment=$input['booking_full_payment'];
		$balance_due=$input['balance_due'];
		
		$sqlchkdemoform="SELECT demo_form_id FROM $demo_form_details WHERE demo_form_id='".addslashes($demo_form_id)."'";
		$rschkdemoform=mysqli_query($link,$sqlchkdemoform);
		$countchkdemoform=mysqli_num_rows($rschkdemoform);
		if($countchkdemoform==0){
		$sqlinsertdemodetails="INSERT INTO $demo_form_details SET demo_form_id='".addslashes($demo_form_id)."',
							demo_latt='".addslashes($demo_latt)."',
							demo_longi='".addslashes($demo_longi)."',
							customer_name='".addslashes($customer_name)."',
							prod_interested='".addslashes($prod_interested)."',
							demo_achieved='".addslashes($demo_achieved)."',
							future_appointment_date='".addslashes($future_appointment_date)."',
							 demo_given_by='".addslashes($demo_given_by)."',
							sales_achieved='".addslashes($sales_achieved)."',
							ask_details='".addslashes($ask_details)."',
							full_payment='".addslashes($full_payment)."',
							sale_model='".addslashes($sale_model)."',
							sale_price='".addslashes($sale_price)."',
							sale_exchange='".addslashes($sale_exchange)."',
							sale_payment_details='".addslashes($sale_payment_details)."',
							booking_done='".addslashes($booking_done)."',
							booking_model='".addslashes($booking_model)."',
							booking_sale_price='".addslashes($booking_sale_price)."',
							booking_adv_amount='".addslashes($booking_adv_amount)."',
							booking_full_payment='".addslashes($booking_full_payment)."',
							balance_due='".addslashes($balance_due)."',
							update_date_time=CURRENT_TIMESTAMP()";
		if(mysqli_query($link,$sqlinsertdemodetails))
		{
			$res_data = array("process_status"=>"YES","process_message"=>'Demo Form Details saved successfully');
		}
		else
		{
			$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong");
		}
	  }
	  else
	  {
		  $res_data = array("process_status"=>"YES","process_message"=>'Demo Form Details saved successfully');
	  }
	}else{
	   $res_data = array("process_status"=>"NO","process_message"=>"Something went wrong");
	}
	echo json_encode($res_data);
//mysqli_close();
?>