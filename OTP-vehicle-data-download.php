<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");

$emp_code=$_REQUEST['emp_code'];
$operation_type=$_REQUEST['operation_type'];
$sqlmenuaccess="SELECT accessible_menu FROM OTP_menu_access WHERE emp_code='".$emp_code."'";
$rsmenuaccess=mysqli_query($link,$sqlmenuaccess);
$countmenuaccess=mysqli_num_rows($rsmenuaccess);
$menu_access_array=array();
if($countmenuaccess >0)
{
	while($rowmenuaccess=mysqli_fetch_assoc($rsmenuaccess))
	{
		array_push($menu_access_array,$rowmenuaccess['accessible_menu']);
	}
}
//print_r($menu_access_array);
if(in_array('gate_keeper_in',$menu_access_array) && $operation_type=='gate_keeper_in') // Start of Gate Keeper 1
{
	$sqlquery="SELECT customer_code,destination,DO_no,vehicle_no,trans_response_date,trans_response_id FROM DO_tracking WHERE 
				arrival_date_gate='0000-00-00 00:00:00'";
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
		while($rowvehicle = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowvehicle['customer_code']!='')?$rowvehicle['customer_code']: ' ')."^";
			$contents .= (($rowvehicle['destination']!='')?$rowvehicle['destination']: ' ')."^";
			$contents .= (($rowvehicle['DO_no']!='')?$rowvehicle['DO_no']: ' ')."^";
			$contents .= (($rowvehicle['vehicle_no']!='')?$rowvehicle['vehicle_no']: ' ')."^";
			$contents .= (($rowvehicle['trans_response_id']!='')?$rowvehicle['trans_response_id']: ' ')."^";
			$contents .= (($rowvehicle['trans_response_date']!='0000-00-00 00:00:00')?'DONE': ' ')."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' ';
			
			$linecontents  .= $contents."\n";
		}
		$contentsrowcolumn=$count.'¥'.'30';
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
			$datacontents = '0'.'¥'.'30';
		}
	}
}
if(in_array('despatch_in',$menu_access_array) && $operation_type=='despatch_in') // Start of Despatch in
{
	$sqlquery="SELECT customer_code,destination,DO_no,vehicle_no,trans_response_date,arrival_date_gate,trans_response_id,arrival_gate_id FROM 
				DO_tracking WHERE 
				despatch_approval_date='0000-00-00 00:00:00' AND arrival_date_gate!='0000-00-00 00:00:00'";
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
		while($rowvehicle = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowvehicle['customer_code']!='')?$rowvehicle['customer_code']: ' ')."^";
			$contents .= (($rowvehicle['destination']!='')?$rowvehicle['destination']: ' ')."^";
			$contents .= (($rowvehicle['DO_no']!='')?$rowvehicle['DO_no']: ' ')."^";
			$contents .= (($rowvehicle['vehicle_no']!='')?$rowvehicle['vehicle_no']: ' ')."^";
			$contents .= (($rowvehicle['trans_response_id']!='')?$rowvehicle['trans_response_id']: ' ')."^";
			$contents .= (($rowvehicle['trans_response_date']!='0000-00-00 00:00:00')?'DONE': ' ')."^";
			$contents .= (($rowvehicle['arrival_gate_id']!='')?$rowvehicle['arrival_gate_id']: ' ')."^";
			$contents .= (($rowvehicle['arrival_date_gate']!='0000-00-00 00:00:00')?'DONE': ' ')."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' ';
			
			$linecontents  .= $contents."\n";
		}
		$contentsrowcolumn=$count.'¥'.'30';
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
			$datacontents = '0'.'¥'.'30';
		}
	}
}
/*if(in_array('gate_keeper2',$menu_access_array) && $operation_type=='gate_keeper2') // Start of Gate Keeper 2
{
	$sqlquery="SELECT customer_code,destination,DO_no,vehicle_no,trans_response_date,arrival_date_gate,trans_response_id,arrival_gate_id
			,despatch_approval_date,despatch_approval_id 
				FROM DO_tracking WHERE 
				gate_vehicle_in_date='0000-00-00 00:00:00' AND despatch_approval_date!='0000-00-00 00:00:00'";
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
		while($rowvehicle = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowvehicle['customer_code']!='')?$rowvehicle['customer_code']: ' ')."^";
			$contents .= (($rowvehicle['destination']!='')?$rowvehicle['destination']: ' ')."^";
			$contents .= (($rowvehicle['DO_no']!='')?$rowvehicle['DO_no']: ' ')."^";
			$contents .= (($rowvehicle['vehicle_no']!='')?$rowvehicle['vehicle_no']: ' ')."^";
			$contents .= (($rowvehicle['trans_response_id']!='')?$rowvehicle['trans_response_id']: ' ')."^";
			$contents .= (($rowvehicle['trans_response_date']!='0000-00-00 00:00:00')?'DONE': ' ')."^";
			$contents .= (($rowvehicle['arrival_gate_id']!='')?$rowvehicle['arrival_gate_id']: ' ')."^";
			$contents .= (($rowvehicle['arrival_date_gate']!='0000-00-00 00:00:00')?'DONE': ' ')."^";
			$contents .= (($rowvehicle['despatch_approval_id'])?$rowvehicle['despatch_approval_id']: ' ')."^";
			$contents .= (($rowvehicle['despatch_approval_date']!='0000-00-00 00:00:00')?'DONE': ' ')."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' ';
			
			$linecontents  .= $contents."\n";
		}
		$contentsrowcolumn=$count.'¥'.'30';
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
			$datacontents = '0'.'¥'.'30';
		}
	}
}*/
if(in_array('weighbridge_in',$menu_access_array) && $operation_type=='weighbridge_in') // Start of Weighbridge in
{
	/*$sqlquery="SELECT customer_code,destination,DO_no,vehicle_no,trans_response_date,arrival_date_gate,trans_response_id,arrival_gate_id
			,despatch_approval_date,despatch_approval_id,gate_vehicle_in_id,gate_vehicle_in_date 
				FROM DO_tracking WHERE 
				wb_in_date='0000-00-00 00:00:00' AND gate_vehicle_in_date!='0000-00-00 00:00:00'";*/
	$sqlquery="SELECT customer_code,destination,DO_no,vehicle_no,trans_response_date,arrival_date_gate,trans_response_id,arrival_gate_id
			,despatch_approval_date,despatch_approval_id,gate_vehicle_in_id,gate_vehicle_in_date 
				FROM DO_tracking WHERE 
				wb_in_date='0000-00-00 00:00:00' AND despatch_approval_date!='0000-00-00 00:00:00'";			
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
		while($rowvehicle = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowvehicle['customer_code']!='')?$rowvehicle['customer_code']: ' ')."^";
			$contents .= (($rowvehicle['destination']!='')?$rowvehicle['destination']: ' ')."^";
			$contents .= (($rowvehicle['DO_no']!='')?$rowvehicle['DO_no']: ' ')."^";
			$contents .= (($rowvehicle['vehicle_no']!='')?$rowvehicle['vehicle_no']: ' ')."^";
			$contents .= (($rowvehicle['trans_response_id']!='')?$rowvehicle['trans_response_id']: ' ')."^";
			$contents .= (($rowvehicle['trans_response_date']!='0000-00-00 00:00:00')?'DONE': ' ')."^";
			$contents .= (($rowvehicle['arrival_gate_id']!='')?$rowvehicle['arrival_gate_id']: ' ')."^";
			$contents .= (($rowvehicle['arrival_date_gate']!='0000-00-00 00:00:00')?'DONE': ' ')."^";
			$contents .= (($rowvehicle['despatch_approval_id'])?$rowvehicle['despatch_approval_id']: ' ')."^";
			$contents .= (($rowvehicle['despatch_approval_date']!='0000-00-00 00:00:00')?'DONE': ' ')."^";
			$contents .= (($rowvehicle['gate_vehicle_in_id'])?$rowvehicle['gate_vehicle_in_id']: ' ')."^";
			$contents .= (($rowvehicle['gate_vehicle_in_date']!='0000-00-00 00:00:00')?'DONE': ' ')."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' ';
			
			$linecontents  .= $contents."\n";
		}
		$contentsrowcolumn=$count.'¥'.'30';
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
			$datacontents = '0'.'¥'.'30';
		}
	}
}
if(in_array('loading',$menu_access_array) && $operation_type=='loading') // Start of Loading
{
	$sqlquery="SELECT customer_code,destination,DO_no,vehicle_no,trans_response_date,arrival_date_gate,trans_response_id,arrival_gate_id
			,despatch_approval_date,despatch_approval_id,gate_vehicle_in_id,gate_vehicle_in_date,wb_in_id,wb_in_date,tare_weight
				FROM DO_tracking WHERE 
				loading_date='0000-00-00 00:00:00' AND wb_in_date!='0000-00-00 00:00:00'";
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
		while($rowvehicle = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowvehicle['customer_code']!='')?$rowvehicle['customer_code']: ' ')."^";
			$contents .= (($rowvehicle['destination']!='')?$rowvehicle['destination']: ' ')."^";
			$contents .= (($rowvehicle['DO_no']!='')?$rowvehicle['DO_no']: ' ')."^";
			$contents .= (($rowvehicle['vehicle_no']!='')?$rowvehicle['vehicle_no']: ' ')."^";
			$contents .= (($rowvehicle['trans_response_id']!='')?$rowvehicle['trans_response_id']: ' ')."^";
			$contents .= (($rowvehicle['trans_response_date']!='0000-00-00 00:00:00')?'DONE': ' ')."^";
			$contents .= (($rowvehicle['arrival_gate_id']!='')?$rowvehicle['arrival_gate_id']: ' ')."^";
			$contents .= (($rowvehicle['arrival_date_gate']!='0000-00-00 00:00:00')?'DONE': ' ')."^";
			$contents .= (($rowvehicle['despatch_approval_id'])?$rowvehicle['despatch_approval_id']: ' ')."^";
			$contents .= (($rowvehicle['despatch_approval_date']!='0000-00-00 00:00:00')?'DONE': ' ')."^";
			$contents .= (($rowvehicle['gate_vehicle_in_id'])?$rowvehicle['gate_vehicle_in_id']: ' ')."^";
			$contents .= (($rowvehicle['gate_vehicle_in_date']!='0000-00-00 00:00:00')?'DONE': ' ')."^";
			$contents .= (($rowvehicle['wb_in_id'])?$rowvehicle['wb_in_id']: ' ')."^";
			$contents .= (($rowvehicle['wb_in_date']!='0000-00-00 00:00:00')?'DONE': ' ')."^";
			$contents .= (($rowvehicle['tare_weight']!='')?$rowvehicle['tare_weight']: ' ')."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' ';
			
			$linecontents  .= $contents."\n";
		}
		$contentsrowcolumn=$count.'¥'.'30';
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
			$datacontents = '0'.'¥'.'30';
		}
	}
}
if(in_array('weighbridge_out',$menu_access_array) && $operation_type=='weighbridge_out') // Start of Weighbridge out
{
	$sqlquery="SELECT customer_code,destination,DO_no,vehicle_no,trans_response_date,arrival_date_gate,trans_response_id,arrival_gate_id
			,despatch_approval_date,despatch_approval_id,gate_vehicle_in_id,gate_vehicle_in_date,wb_in_id,wb_in_date,tare_weight,
			loading_id,loading_date,bay_no,GRN_attachment,GRN_attached_date FROM DO_tracking WHERE 
				wb_out_date='0000-00-00 00:00:00' AND loading_date!='0000-00-00 00:00:00'";
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
		while($rowvehicle = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowvehicle['customer_code']!='')?$rowvehicle['customer_code']: ' ')."^";
			$contents .= (($rowvehicle['destination']!='')?$rowvehicle['destination']: ' ')."^";
			$contents .= (($rowvehicle['DO_no']!='')?$rowvehicle['DO_no']: ' ')."^";
			$contents .= (($rowvehicle['vehicle_no']!='')?$rowvehicle['vehicle_no']: ' ')."^";
			$contents .= (($rowvehicle['trans_response_id']!='')?$rowvehicle['trans_response_id']: ' ')."^";
			$contents .= (($rowvehicle['trans_response_date']!='0000-00-00 00:00:00')?'DONE': ' ')."^";
			$contents .= (($rowvehicle['arrival_gate_id']!='')?$rowvehicle['arrival_gate_id']: ' ')."^";
			$contents .= (($rowvehicle['arrival_date_gate']!='0000-00-00 00:00:00')?'DONE': ' ')."^";
			$contents .= (($rowvehicle['despatch_approval_id'])?$rowvehicle['despatch_approval_id']: ' ')."^";
			$contents .= (($rowvehicle['despatch_approval_date']!='0000-00-00 00:00:00')?'DONE': ' ')."^";
			$contents .= (($rowvehicle['gate_vehicle_in_id'])?$rowvehicle['gate_vehicle_in_id']: ' ')."^";
			$contents .= (($rowvehicle['gate_vehicle_in_date']!='0000-00-00 00:00:00')?'DONE': ' ')."^";
			$contents .= (($rowvehicle['wb_in_id'])?$rowvehicle['wb_in_id']: ' ')."^";
			$contents .= (($rowvehicle['wb_in_date']!='0000-00-00 00:00:00')?'DONE': ' ')."^";
			$contents .= (($rowvehicle['tare_weight']!='')?$rowvehicle['tare_weight']: ' ')."^";
			$contents .= (($rowvehicle['loading_id'])?$rowvehicle['loading_id']: ' ')."^";
			$contents .= (($rowvehicle['loading_date']!='0000-00-00 00:00:00')?'DONE': ' ')."^";
			$contents .= (($rowvehicle['bay_no'])?$rowvehicle['bay_no']: ' ')."^";
			$contents .= (($rowvehicle['GRN_attachment'])?$rowvehicle['GRN_attachment']: ' ')."^";
			$contents .= (($rowvehicle['GRN_attachment_date'])?$rowvehicle['GRN_attachment_date']: ' ')."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' ';
			
			$linecontents  .= $contents."\n";
		}
		$contentsrowcolumn=$count.'¥'.'30';
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
			$datacontents = '0'.'¥'.'30';
		}
	}
}
if(in_array('despatch_out',$menu_access_array) && $operation_type=='despatch_out') // Start of Despatch out
{
	$sqlquery="SELECT customer_code,destination,DO_no,vehicle_no,trans_response_date,arrival_date_gate,trans_response_id,arrival_gate_id
			,despatch_approval_date,despatch_approval_id,gate_vehicle_in_id,gate_vehicle_in_date,wb_in_id,wb_in_date,tare_weight,
			loading_id,loading_date,bay_no,GRN_attachment,GRN_attached_date,wb_out_id,wb_out_date 
				FROM DO_tracking WHERE 
			invoice_date='0000-00-00' AND wb_out_date!='0000-00-00 00:00:00'";
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
		while($rowvehicle = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowvehicle['customer_code']!='')?$rowvehicle['customer_code']: ' ')."^";
			$contents .= (($rowvehicle['destination']!='')?$rowvehicle['destination']: ' ')."^";
			$contents .= (($rowvehicle['DO_no']!='')?$rowvehicle['DO_no']: ' ')."^";
			$contents .= (($rowvehicle['vehicle_no']!='')?$rowvehicle['vehicle_no']: ' ')."^";
			$contents .= (($rowvehicle['trans_response_id']!='')?$rowvehicle['trans_response_id']: ' ')."^";
			$contents .= (($rowvehicle['trans_response_date']!='0000-00-00 00:00:00')?'DONE': ' ')."^";
			$contents .= (($rowvehicle['arrival_gate_id']!='')?$rowvehicle['arrival_gate_id']: ' ')."^";
			$contents .= (($rowvehicle['arrival_date_gate']!='0000-00-00 00:00:00')?'DONE': ' ')."^";
			$contents .= (($rowvehicle['despatch_approval_id'])?$rowvehicle['despatch_approval_id']: ' ')."^";
			$contents .= (($rowvehicle['despatch_approval_date']!='0000-00-00 00:00:00')?'DONE': ' ')."^";
			$contents .= (($rowvehicle['gate_vehicle_in_id'])?$rowvehicle['gate_vehicle_in_id']: ' ')."^";
			$contents .= (($rowvehicle['gate_vehicle_in_date']!='0000-00-00 00:00:00')?'DONE': ' ')."^";
			$contents .= (($rowvehicle['wb_in_id'])?$rowvehicle['wb_in_id']: ' ')."^";
			$contents .= (($rowvehicle['wb_in_date']!='0000-00-00 00:00:00')?'DONE': ' ')."^";
			$contents .= (($rowvehicle['tare_weight']!='')?$rowvehicle['tare_weight']: ' ')."^";
			$contents .= (($rowvehicle['loading_id'])?$rowvehicle['loading_id']: ' ')."^";
			$contents .= (($rowvehicle['loading_date']!='0000-00-00 00:00:00')?'DONE': ' ')."^";
			$contents .= (($rowvehicle['bay_no'])?$rowvehicle['bay_no']: ' ')."^";
			$contents .= (($rowvehicle['GRN_attachment'])?$rowvehicle['GRN_attachment']: ' ')."^";
			$contents .= (($rowvehicle['GRN_attachment_date'])?$rowvehicle['GRN_attachment_date']: ' ')."^";
			$contents .= (($rowvehicle['wb_out_id'])?$rowvehicle['wb_out_id']: ' ')."^";
			$contents .= (($rowvehicle['wb_out_date']!='0000-00-00 00:00:00')?'DONE': ' ')."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' ';
			
			$linecontents  .= $contents."\n";
		}
		$contentsrowcolumn=$count.'¥'.'30';
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
			$datacontents = '0'.'¥'.'30';
		}
	}
}
if(in_array('gate_keeper_out',$menu_access_array) && $operation_type=='gate_keeper_out') // Start of Gate Keeper2 out
{
	$sqlquery="SELECT customer_code,destination,DO_no,vehicle_no,trans_response_date,arrival_date_gate,trans_response_id,arrival_gate_id
			,despatch_approval_date,despatch_approval_id,gate_vehicle_in_id,gate_vehicle_in_date,wb_in_id,wb_in_date,tare_weight,
			loading_id,loading_date,bay_no,GRN_attachment,GRN_attached_date,wb_out_id,wb_out_date,despatch_trans_id,invoice_date 
			FROM DO_tracking WHERE exit_approval_date='0000-00-00 00:00:00' AND invoice_date!='0000-00-00'";
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
		while($rowvehicle = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowvehicle['customer_code']!='')?$rowvehicle['customer_code']: ' ')."^";
			$contents .= (($rowvehicle['destination']!='')?$rowvehicle['destination']: ' ')."^";
			$contents .= (($rowvehicle['DO_no']!='')?$rowvehicle['DO_no']: ' ')."^";
			$contents .= (($rowvehicle['vehicle_no']!='')?$rowvehicle['vehicle_no']: ' ')."^";
			$contents .= (($rowvehicle['trans_response_id']!='')?$rowvehicle['trans_response_id']: ' ')."^";
			$contents .= (($rowvehicle['trans_response_date']!='0000-00-00 00:00:00')?'DONE': ' ')."^";
			$contents .= (($rowvehicle['arrival_gate_id']!='')?$rowvehicle['arrival_gate_id']: ' ')."^";
			$contents .= (($rowvehicle['arrival_date_gate']!='0000-00-00 00:00:00')?'DONE': ' ')."^";
			$contents .= (($rowvehicle['despatch_approval_id'])?$rowvehicle['despatch_approval_id']: ' ')."^";
			$contents .= (($rowvehicle['despatch_approval_date']!='0000-00-00 00:00:00')?'DONE': ' ')."^";
			$contents .= (($rowvehicle['gate_vehicle_in_id'])?$rowvehicle['gate_vehicle_in_id']: ' ')."^";
			$contents .= (($rowvehicle['gate_vehicle_in_date']!='0000-00-00 00:00:00')?'DONE': ' ')."^";
			$contents .= (($rowvehicle['wb_in_id'])?$rowvehicle['wb_in_id']: ' ')."^";
			$contents .= (($rowvehicle['wb_in_date']!='0000-00-00 00:00:00')?'DONE': ' ')."^";
			$contents .= (($rowvehicle['tare_weight']!='')?$rowvehicle['tare_weight']: ' ')."^";
			$contents .= (($rowvehicle['loading_id'])?$rowvehicle['loading_id']: ' ')."^";
			$contents .= (($rowvehicle['loading_date']!='0000-00-00 00:00:00')?'DONE': ' ')."^";
			$contents .= (($rowvehicle['bay_no'])?$rowvehicle['bay_no']: ' ')."^";
			$contents .= (($rowvehicle['GRN_attachment'])?$rowvehicle['GRN_attachment']: ' ')."^";
			$contents .= (($rowvehicle['GRN_attachment_date'])?$rowvehicle['GRN_attachment_date']: ' ')."^";
			$contents .= (($rowvehicle['wb_out_id'])?$rowvehicle['wb_out_id']: ' ')."^";
			$contents .= (($rowvehicle['wb_out_date']!='0000-00-00 00:00:00')?'DONE': ' ')."^";
			$contents .= (($rowvehicle['despatch_trans_id'])?$rowvehicle['despatch_trans_id']: ' ')."^";
			$contents .= (($rowvehicle['invoice_date']!='0000-00-00')?'DONE': ' ')."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' ';
			
			$linecontents  .= $contents."\n";
		}
		$contentsrowcolumn=$count.'¥'.'30';
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
			$datacontents = '0'.'¥'.'30';
		}
	}
}
if(in_array('security_out',$menu_access_array) && $operation_type=='security_out') // Start of security out
{
	$sqlquery="SELECT customer_code,destination,DO_no,vehicle_no,trans_response_date,arrival_date_gate,trans_response_id,arrival_gate_id
			,despatch_approval_date,despatch_approval_id,gate_vehicle_in_id,gate_vehicle_in_date,wb_in_id,wb_in_date,tare_weight,
			loading_id,loading_date,bay_no,GRN_attachment,GRN_attached_date,wb_out_id,wb_out_date,despatch_trans_id,invoice_date,
			exit_approval_id,exit_approval_date FROM DO_tracking WHERE security_chk_date='0000-00-00 00:00:00' AND exit_approval_date!='0000-00-00 00:00:00'";
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
		while($rowvehicle = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowvehicle['customer_code']!='')?$rowvehicle['customer_code']: ' ')."^";
			$contents .= (($rowvehicle['destination']!='')?$rowvehicle['destination']: ' ')."^";
			$contents .= (($rowvehicle['DO_no']!='')?$rowvehicle['DO_no']: ' ')."^";
			$contents .= (($rowvehicle['vehicle_no']!='')?$rowvehicle['vehicle_no']: ' ')."^";
			$contents .= (($rowvehicle['trans_response_id']!='')?$rowvehicle['trans_response_id']: ' ')."^";
			$contents .= (($rowvehicle['trans_response_date']!='0000-00-00 00:00:00')?'DONE': ' ')."^";
			$contents .= (($rowvehicle['arrival_gate_id']!='')?$rowvehicle['arrival_gate_id']: ' ')."^";
			$contents .= (($rowvehicle['arrival_date_gate']!='0000-00-00 00:00:00')?'DONE': ' ')."^";
			$contents .= (($rowvehicle['despatch_approval_id'])?$rowvehicle['despatch_approval_id']: ' ')."^";
			$contents .= (($rowvehicle['despatch_approval_date']!='0000-00-00 00:00:00')?'DONE': ' ')."^";
			$contents .= (($rowvehicle['gate_vehicle_in_id'])?$rowvehicle['gate_vehicle_in_id']: ' ')."^";
			$contents .= (($rowvehicle['gate_vehicle_in_date']!='0000-00-00 00:00:00')?'DONE': ' ')."^";
			$contents .= (($rowvehicle['wb_in_id'])?$rowvehicle['wb_in_id']: ' ')."^";
			$contents .= (($rowvehicle['wb_in_date']!='0000-00-00 00:00:00')?'DONE': ' ')."^";
			$contents .= (($rowvehicle['tare_weight']!='')?$rowvehicle['tare_weight']: ' ')."^";
			$contents .= (($rowvehicle['loading_id'])?$rowvehicle['loading_id']: ' ')."^";
			$contents .= (($rowvehicle['loading_date']!='0000-00-00 00:00:00')?'DONE': ' ')."^";
			$contents .= (($rowvehicle['bay_no'])?$rowvehicle['bay_no']: ' ')."^";
			$contents .= (($rowvehicle['GRN_attachment'])?$rowvehicle['GRN_attachment']: ' ')."^";
			$contents .= (($rowvehicle['GRN_attachment_date'])?$rowvehicle['GRN_attachment_date']: ' ')."^";
			$contents .= (($rowvehicle['wb_out_id'])?$rowvehicle['wb_out_id']: ' ')."^";
			$contents .= (($rowvehicle['wb_out_date']!='0000-00-00 00:00:00')?'DONE': ' ')."^";
			$contents .= (($rowvehicle['despatch_trans_id'])?$rowvehicle['despatch_trans_id']: ' ')."^";
			$contents .= (($rowvehicle['invoice_date']!='0000-00-00')?'DONE': ' ')."^";
			$contents .= (($rowvehicle['security_chk_id'])?$rowvehicle['security_chk_id']: ' ')."^";
			$contents .= (($rowvehicle['security_chk_date']!='0000-00-00 00:00:00')?'DONE': ' ')."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' '."^";
			$contents .=' ';
			
			$linecontents  .= $contents."\n";
		}
		$contentsrowcolumn=$count.'¥'.'30';
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
			$datacontents = '0'.'¥'.'30';
		}
	}
}
$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
$url = APICALLLOGURL."/OTP-vehicle-data-download.php?nick_name=$nick_name&emp_code=$emp_code&operation_type=$operation_type";
insertapilog($datetime,$emp_code,$url,$nick_name);
header("Content-type: application/text"); 
header("Content-Disposition: attachment; filename=vehicle_list.txt");
print "$datacontents"; 
mysqli_close($link);		
?>
