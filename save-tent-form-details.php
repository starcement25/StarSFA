<?php
 ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/config-email-setup.php");
$tent_form_details = "tent_form_details";
$tent_form_customer_detais = "tent_form_customer_detais";
$tent_form_product_details = "tent_form_product_details";

$emp_code=$_REQUEST['emp_code'];

$inputJSON = file_get_contents('php://input');

$sqlinsert_xml_data="INSERT INTO xml_data SET emp_code='".$emp_code."',
					 xml='".addslashes($inputJSON)."',
					insertdate=CURRENT_TIMESTAMP()";
mysqli_query($link,$sqlinsert_xml_data);	

/*$inputJSON='{"tent_form_id":"TFE000220220429161556","starting_date_time":"2022-04-29 16:15:56","end_date_time":"2022-04-29 16:16:38","starting_latt":"23.1210395","starting_longi":"88.8598768","end_latt":"23.1210395","end_longi":"88.8598768","starting_image":"E000220220429161604.jpeg","end_image":"E000220220429161648.jpeg","customer_name":"FHFJ  RH","mobile_no":"2354689765","interested_for_demo":"yes","demo_tentative_date_time":"30-04-2022 16:16","remarks":"FHFH","customerdata":[{"tent_form_id":"TFE000220220429161556","customer_alternate_phone_no":"9998887555","customer_address":"DYDYY","customer_other_details":"","product_type":"RO"},{"tent_form_id":"TFE000220220429161556","customer_alternate_phone_no":"9998887555","customer_address":"DYDYY","customer_other_details":"","product_type":"RO"},{"tent_form_id":"TFE000220220429161556","customer_alternate_phone_no":"9998887555","customer_address":"DYDYY","customer_other_details":"","product_type":"RO"},{"tent_form_id":"TFE000220220429161556","customer_alternate_phone_no":"9998887555","customer_address":"DYDYY","customer_other_details":"","product_type":"RO"},{"tent_form_id":"TFE000220220429161556","customer_alternate_phone_no":"9998887555","customer_address":"DYDYY","customer_other_details":"","product_type":"RO"},{"tent_form_id":"TFE000220220429161556","customer_alternate_phone_no":"9998887555","customer_address":"DYDYY","customer_other_details":"","product_type":"RO"},{"tent_form_id":"TFE000220220429161556","customer_alternate_phone_no":"9998887555","customer_address":"DYDYY","customer_other_details":"","product_type":"RO"},{"tent_form_id":"TFE000220220429161556","customer_alternate_phone_no":"9998887555","customer_address":"DYDYY","customer_other_details":"","product_type":"RO"},{"tent_form_id":"TFE000220220429161556","customer_alternate_phone_no":"9998887555","customer_address":"DYDYY","customer_other_details":"","product_type":"RO"},{"tent_form_id":"TFE000220220429161556","customer_alternate_phone_no":"9998887555","customer_address":"DYDYY","customer_other_details":"","product_type":"RO"},{"tent_form_id":"TFE000220220429161556","customer_alternate_phone_no":"9998887555","customer_address":"DYDYY","customer_other_details":"","product_type":"RO"},{"tent_form_id":"TFE000220220429161556","customer_alternate_phone_no":"9998887555","customer_address":"DYDYY","customer_other_details":"","product_type":"RO"},{"tent_form_id":"TFE000220220429161556","customer_alternate_phone_no":"9998887555","customer_address":"DYDYY","customer_other_details":"","product_type":"RO"},{"tent_form_id":"TFE000220220429161556","customer_alternate_phone_no":"9998887555","customer_address":"DYDYY","customer_other_details":"","product_type":"RO"}],"productdata":[{"tent_form_id":"TFE000220220429161556","product":"Chimney","brand":"ch","life_of_product":"6"},{"tent_form_id":"TFE000220220429161556","product":"Chimney","brand":"ch","life_of_product":"6"},{"tent_form_id":"TFE000220220429161556","product":"Chimney","brand":"ch","life_of_product":"6"},{"tent_form_id":"TFE000220220429161556","product":"Chimney","brand":"ch","life_of_product":"6"},{"tent_form_id":"TFE000220220429161556","product":"Chimney","brand":"ch","life_of_product":"6"},{"tent_form_id":"TFE000220220429161556","product":"Chimney","brand":"ch","life_of_product":"6"},{"tent_form_id":"TFE000220220429161556","product":"Chimney","brand":"ch","life_of_product":"6"},{"tent_form_id":"TFE000220220429161556","product":"Chimney","brand":"ch","life_of_product":"6"},{"tent_form_id":"TFE000220220429161556","product":"Chimney","brand":"ch","life_of_product":"6"},{"tent_form_id":"TFE000220220429161556","product":"Chimney","brand":"ch","life_of_product":"6"},{"tent_form_id":"TFE000220220429161556","product":"Chimney","brand":"ch","life_of_product":"6"},{"tent_form_id":"TFE000220220429161556","product":"Chimney","brand":"ch","life_of_product":"6"}]}';*/
$input= json_decode($inputJSON,true);
//print_r($input);
/*$value='{"tent_form_id":"TFE000120220421115704","starting_date_time":"2022-04-21 11:01:10","end_date_time":"2022-04-21 16:01:10","starting_latt":"18","starting_longi":"22","end_latt":"18.1","end_longi":"22.2",
"starting_image":"test1.jpg","end_image":"test2.jpg","customer_name":"test name","mobile_no":"9999999999","interested_for_demo":"no","demo_tentative_date_time":"","remarks":"remarks","productdata":[{"tent_form_id":"TFE000120220421115704","product":"chimney","brand":"testbrand","life_of_product":"test life"},{"tent_form_id":"TFE000120220421115704","product":"RO","brand":"RO brand","life_of_product":"RO life"}],
"customerdata":[{"tent_form_id":"TFE000120220421115704","customer_alternate_phone_no":"90000000000","customer_address":"testaddress","customer_other_details":"test details","product_type":"chimney"},{"tent_form_id":"TFE000120220421115704","customer_alternate_phone_no":"90000000001","customer_address":"testaddress1","customer_other_details":"test details1","product_type":"ro"}]}';*/

$tent_form_id=$input['tent_form_id'] ??'';
if($tent_form_id!=''){
		$starting_date_time=$input['starting_date_time'];
		$end_date_time=$input['end_date_time'];
		$starting_latt=$input['starting_latt'];
		$starting_longi=$input['starting_longi'];
		$end_latt=$input['end_latt'];
		$end_longi=$input['end_longi'];
		$starting_image=$input['starting_image'];
		$end_image=$input['end_image'];
		$customer_name=$input['customer_name'];
		$mobile_no=$input['mobile_no'];
		$interested_for_demo=$input['interested_for_demo'];
		$demo_tentative_date_time=$input['demo_tentative_date_time'];
		$remarks=$input['remarks'];
		$sqlchktentform="SELECT tent_form_id FROM $tent_form_details WHERE tent_form_id='".addslashes($tent_form_id)."' AND mobile_no='".addslashes($mobile_no)."'";
		$rschktentform=mysqli_query($link,$sqlchktentform);
		$countchktentform=mysqli_num_rows($rschktentform);
		if($countchktentform==0){			
		$sqlinserttentdetails="INSERT INTO $tent_form_details SET tent_form_id='".addslashes($tent_form_id)."',
							starting_date_time='".addslashes($starting_date_time)."',
							end_date_time='".addslashes($end_date_time)."',
							starting_latt='".addslashes($starting_latt)."',
							starting_longi='".addslashes($starting_longi)."',
							end_latt='".addslashes($end_latt)."',
							end_longi='".addslashes($end_longi)."',
							 starting_image='".addslashes($starting_image)."',
							end_image='".addslashes($end_image)."',
							customer_name='".addslashes($customer_name)."',
							mobile_no='".addslashes($mobile_no)."',
							interested_for_demo='".addslashes($interested_for_demo)."',
							demo_tentative_date_time='".addslashes($demo_tentative_date_time)."',
							remarks='".addslashes($remarks)."',
							update_date_time=CURRENT_TIMESTAMP()";
		if(mysqli_query($link,$sqlinserttentdetails))
		{
			$productArray=$input['productdata'];
			$customerArray=$input['customerdata'];
			if(count($productArray) >0){
				foreach($productArray as $product_data_val){
					$tent_form_id = $product_data_val["tent_form_id"];
					$product = $product_data_val["product"];
					$brand = $product_data_val["brand"];
					$life_of_product = $product_data_val["life_of_product"];
					$mobile_no = $product_data_val["mobile_no"];
					$sqlinserttentproduct="INSERT INTO $tent_form_product_details SET tent_form_id='".addslashes($tent_form_id)."',
							product='".addslashes($product)."',
							brand='".addslashes($brand)."',
							mobile_no='".addslashes($mobile_no)."',
							life_of_product='".addslashes($life_of_product)."'";
					mysqli_query($link,$sqlinserttentproduct);
				}
			}
			if(count($customerArray) >0){
				foreach($customerArray as $customer_data_val){
					$tent_form_id = $customer_data_val["tent_form_id"];
					$customer_alternate_phone_no = $customer_data_val["customer_alternate_phone_no"];
					$customer_address = $customer_data_val["customer_address"];
					$customer_other_details = $customer_data_val["customer_other_details"];
					$product_type = $customer_data_val["product_type"];
					$mobile_no = $customer_data_val["mobile_no"];
					$sqlinserttentcustomer="INSERT INTO $tent_form_customer_detais SET tent_form_id='".addslashes($tent_form_id)."',
								customer_alternate_phone_no='".addslashes($customer_alternate_phone_no)."',
								customer_address='".addslashes($customer_address)."',
								customer_other_details='".addslashes($customer_other_details)."',
								mobile_no='".addslashes($mobile_no)."',
								product_type='".addslashes($product_type)."'";
					mysqli_query($link,$sqlinserttentcustomer);
				}
			}
			$res_data = array("process_status"=>"YES","process_message"=>'Tent Form Details saved successfully');
		}
		else
		{
			$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong");
		}
	  }
	  else
	  {
		  $res_data = array("process_status"=>"YES","process_message"=>'Tent Form Details saved successfully');
	  }
	}else{
	   $res_data = array("process_status"=>"NO","process_message"=>"Something went wrong");
	}
	echo json_encode($res_data);
mysqli_close($link);
?>