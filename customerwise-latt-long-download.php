<?php
include "school_connection.php";
$emp_code=$_REQUEST['emp_code'];
$sql_district = "select district from employee_master where emp_code='$emp_code'";
$res_district = mysqli_query($link,$sql_district);
$row_district=mysqli_fetch_assoc($res_district);
$district=$row_district['district'];
$totres_district = mysqli_num_rows($res_district);

$sqlquery="SELECT DISTINCT c1.customer_code,c1.dns_customer_code,c1.customer_name, c1.route_code,c1.emp_code,c1.current_balance,c1.credit_limit,c1.black_list,c1.acedns,
			   c1.TD,c1.cust_type,c1.rds_tag,c1.sauda_validity_period,c1.address,c1.phone_no,c1.pin,c1.landline_no,
			   c1.owner_name,c1.owner_phone,
			  c1.cust_class,c1.weekly_closing_day,c1.coverage_type,c1.TIN,c1.PAN,c1.minimum_stock,c1.branch_code,c1.visit_day,c1.email,
				c1.sauda_limit,c1.incoterms,
			 c1.pending_qty,c1.loadability_ton,c1.transport_mode,c1.state_code,c1.sauda_type,c1.zone,c1.district,c1.visit_sequence,c1.activated,
			 c1.activated_customer_code,c1.retailer_app,c1.base_latt,c1.base_longi,c1.need_location_update,c1.image,c1.category_of_store,c1.instore_activity,
			 c1.is_new_customer,c1.owner_image,c1.firm_name,c1.firm_image,c1.GST_image,c1.aadhar,c1.aadhar_image FROM customer_master c1 WHERE c1.district='".$district."' ORDER BY c1.customer_name ASC";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
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
	while($rowcustomer = mysqli_fetch_assoc($result))
	{
		$customer_code=$rowcustomer['customer_code'];
		$route_code=$rowcustomer['route_code'];
		$emp_code_db=$rowcustomer['emp_code'];
				
				$customer_name=$rowcustomer['customer_name'];
				$acedns=$rowcustomer['acedns'];
				$dns_customer_code=$rowcustomer['dns_customer_code'];
				$district=$rowcustomer['district'];
				$block=$rowcustomer['zone'];
				$address=$rowcustomer['address'];
				$latt=$rowcustomer['base_latt'];
				$longi=$rowcustomer['base_longi'];

		$res_data[] = array("customer_code"=>$customer_code,"customer_name"=>$customer_name,"acedns"=>$acedns,"dns_customer_code"=>$dns_customer_code,"district"=>$district,"block"=>$block,"address"=>$address,"latt"=>$latt,"longi"=>$longi);
	}
	$countcolumns='9';
	$res_data_final = array("process_status"=>"YES","process_message"=>"Success.","countrows"=>$count,"countcolumns"=>$countcolumns,"datetime"=>$contentsdatetime,"datavalue"=>$res_data );
	}
	else
	{
		$res_data_final = array("process_status"=>"NO","process_message"=>"Something Went Wrong." );
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/customerwise-latt-long-download.php";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	echo json_encode($res_data_final);
	mysqli_close($link);		
?>
