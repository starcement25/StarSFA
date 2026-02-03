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

if($incremental_download=='no')
{
	$login_condition="";
	$login_condition_one="";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(CM.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
	$login_condition_one=" AND UNIX_TIMESTAMP(RM.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
}
    $sqlquery="SELECT DISTINCT CM.customer_code,CM.customer_name,CM.route_code,'' AS emp_code,CM.current_balance,CM.credit_limit,CM.acedns,
				CM.black_list,CM.TD,CM.cust_type,CM.rds_tag,CM.sauda_validity_period,CM.address,CM.pin,CM.phone_no,
				CM.dns_customer_code,CM.landline_no,CM.owner_name,CM.owner_phone,CM.cust_class,CM.weekly_closing_day,CM.coverage_type,
				CM.TIN,CM.PAN,CM.minimum_stock,CM.branch_code,CM.visit_day,CM.email,
				CM.sauda_limit,CM.pending_qty,CM.incoterms,CM.loadability_ton,CM.transport_mode,CM.state_code,CM.sauda_type,CM.zone,
				CM.visit_sequence,CM.base_latt,CM.base_longi 
				FROM customer_master CM,DO_transaction DT WHERE 
			   DT.customer_code= CM.customer_code AND DT.DO_status 
			  IN('approved','vehicle_allotted','invoice_generated','despatch','weighbridge_in','weighbridge_out')";
	$result = mysqli_query($link,$sqlquery);
	$count=mysqli_num_rows($result);
	if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
		$dns_route_code='';
		while($rowcustomer = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowcustomer['customer_code']!='')?$rowcustomer['customer_code']: ' ')."^";
			$contents  .= (($rowcustomer['customer_name']!='')?$rowcustomer['customer_name']: ' ')."^";
			$contents  .= (($rowcustomer['route_code']!='')?$rowcustomer['route_code']: ' ')."^";
			$contents  .= (($rowcustomer['emp_code']!='')?$rowcustomer['emp_code']: ' ')."^";
			$contents  .= (($rowcustomer['current_balance']!='')?$rowcustomer['current_balance']: ' ')."^";
			$contents  .= (($rowcustomer['credit_limit']!='')?$rowcustomer['credit_limit']: ' ')."^";
			$contents  .= (($rowcustomer['acedns']!='')?$rowcustomer['acedns']: ' ')."^";
			$contents  .= (($rowcustomer['black_list']!='')?$rowcustomer['black_list']: ' ')."^";
			$contents  .= (($rowcustomer['TD']!='')?$rowcustomer['TD']: '0')."^";
			$contents  .= (($rowcustomer['cust_type']!='')?$rowcustomer['cust_type']: ' ')."^";
			$contents  .= (($rowcustomer['rds_tag']!='')?$rowcustomer['rds_tag']: '')."^";
			$contents  .= (($rowcustomer['sauda_validity_period']!='')?$rowcustomer['sauda_validity_period']: ' ')."^";
			$contents  .= (($rowcustomer['address']!='')?trim(preg_replace('/[\r\n]+/', '',$rowcustomer['address'])): ' ')."^";
			$contents  .= (($rowcustomer['pin']!='')?$rowcustomer['pin']: ' ')."^";
			$contents  .= (($rowcustomer['phone_no']!='')?$rowcustomer['phone_no']: ' ')."^";
			$contents  .= (($rowcustomer['dns_customer_code']!='')?$rowcustomer['dns_customer_code']: ' ')."^";
			$contents  .= (($rowcustomer['landline_no']!='')?$rowcustomer['landline_no']: ' ')."^";
			$contents  .= (($rowcustomer['owner_name']!='')?$rowcustomer['owner_name']: ' ')."^";
			$contents  .= (($rowcustomer['owner_phone']!='')?$rowcustomer['owner_phone']: ' ')."^";
			$contents  .= (($rowcustomer['cust_class']!='')?$rowcustomer['cust_class']: ' ')."^";
			$contents  .= (($rowcustomer['weekly_closing_day']!='')?$rowcustomer['weekly_closing_day']: ' ')."^";
			$contents  .= (($rowcustomer['coverage_type']!='')?$rowcustomer['coverage_type']: ' ')."^";
			$contents  .= (($rowcustomer['TIN']!='')?$rowcustomer['TIN']: ' ')."^";
			$contents  .= (($rowcustomer['PAN']!='')?$rowcustomer['PAN']: ' ')."^";
			$contents  .= (($rowcustomer['minimum_stock']!='')?$rowcustomer['minimum_stock']: ' ')."^";
			$contents  .= (($rowcustomer['branch_code']!='')?$rowcustomer['branch_code']: ' ')."^";
			$contents  .= (($rowcustomer['visit_day']!='')?$rowcustomer['visit_day']: ' ')."^";
			$contents  .= (($rowcustomer['email']!='')?$rowcustomer['email']: ' ')."^";
			$contents  .= (($rowcustomer['sauda_limit']!='')?$rowcustomer['sauda_limit']: ' ')."^";
			$contents  .= (($rowcustomer['pending_qty']!='')?$rowcustomer['pending_qty']: ' ')."^";
			$contents  .= (($rowcustomer['incoterms']!='')?$rowcustomer['incoterms']: ' ')."^";
			$contents  .= (($rowcustomer['loadability_ton']!='')?$rowcustomer['loadability_ton']: ' ')."^";
			$contents  .= (($rowcustomer['transport_mode']!='')?$rowcustomer['transport_mode']: ' ')."^";
			$contents  .= (($rowcustomer['state_code']!='')?$rowcustomer['state_code']: ' ')."^";
			$contents  .= (($rowcustomer['sauda_type']!='')?$rowcustomer['sauda_type']: ' ')."^";
			$contents  .= (($rowcustomer['zone']!='')?$rowcustomer['zone']: ' ')."^";
			$contents  .= (($rowcustomer['visit_sequence']!='')?$rowcustomer['visit_sequence']: ' ')."^";
			$contents  .= (($rowcustomer['base_latt']!='' && $rowcustomer['base_latt']>0)?$rowcustomer['base_latt']: ' ')."^";
			$contents  .= (($rowcustomer['base_longi']!='' && $rowcustomer['base_longi']>0)?$rowcustomer['base_longi']: ' ');
			$linecontents  .= $contents."\n";
		}
		$contentsrowcolumn=$count.'¥'.'39';
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);	
	}
	else
	{
		$last_update_time=str_replace('?','',$last_update_time);
		$data_download_time=str_replace('?','',$data_download_time);
		if(strtotime($data_download_time)>=strtotime($last_update_time))
		{
			$datacontents = '0'.'¥'.'0';
		}
		else
		{
			$datacontents = '0'.'¥'.'39';
		}
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/OTP-customer-details.php?nick_name=$nick_name&emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=customer_master.txt");
	print "$datacontents"; 
	mysqli_close($link);		
?>