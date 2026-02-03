<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
$emp_code=$_REQUEST['emp_code'];
//$emp_code='100017206';
$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);
$incremental_download=$_REQUEST['incremental_download'];
$data_download_time=$_REQUEST['data_download_time'];
$data_download_time=str_replace('€',' ',$data_download_time);

if(vertical_fields=='yes'){
	/*$sqlempvertical="SELECT vertical_value FROM employee_master WHERE emp_code='".$emp_code."'";
	$rsempvertical=mysqli_query($link,$sqlempvertical);
	$rowempvertical=mysqli_fetch_assoc($rsempvertical);
	$emp_vertical_value=$rowempvertical['vertical_value'];
	$emp_vertical_value_array=explode(',',$emp_vertical_value);
	$emp_vertical_value = "'".implode("','", $emp_vertical_value_array)."'";
	$condition_one=' AND MRP.vertical_value IN ('.$emp_vertical_value.')';*/
    $sqlempvertical="SELECT vertical_value FROM employee_master WHERE emp_code='".$emp_code."'";
	$rsempvertical=mysqli_query($link,$sqlempvertical);
	$rowempvertical=mysqli_fetch_assoc($rsempvertical);
	$emp_vertical_value=$rowempvertical['vertical_value'];
	$emp_vertical_value_array=explode(',',$emp_vertical_value);
	foreach($emp_vertical_value_array as $emp_vertical_array_val)
	{
		$final_emp_vertical_value_array[]=ltrim($emp_vertical_array_val);
	}
	$condition_one=" AND (";
	$condition_two='';
	foreach($final_emp_vertical_value_array as $emp_vertical_values)
	{
		$condition_two.=" FIND_IN_SET( '".$emp_vertical_values."',MRP.vertical_value) OR";
	}
	$condition_two=substr($condition_two,0,-2);
	$condition_one.=$condition_two.")";
}
else
{
	$condition_one="";
}

if($incremental_download=='no')
{
	$login_condition="";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(MRP.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
}
/*if(multiple_rate=='yes')
{
	$sqlstate="SELECT state FROM employee_master WHERE emp_code='".$emp_code."'";
	$rsstate=mysqli_query($link,$sqlstate);
	$rowstate=mysqli_fetch_assoc($rsstate);
	$state_name=$rowstate['state'];
	if(strtoupper($nick_name)=='ARCHITA')
	{
		$sqlstatecode="SELECT state_code FROM state_master WHERE state='".$state_name."'";
	}
	else
	{
		$sqlstatecode="SELECT state_code FROM state_master WHERE statename LIKE '%".$state_name."%'";
	}
	$rsstatecode=mysqli_query($link,$sqlstatecode);
	$rowstatecode=mysqli_fetch_assoc($rsstatecode);
	$state_code=$rowstatecode['state_code'];
	$sqlquery="SELECT MRP.* FROM mrp MRP WHERE 1 AND MRP.state_code='".$state_code."' ".$condition_one." ".$login_condition."";
	$result = mysqli_query($link,$sqlquery);
	$count=mysqli_num_rows($result);
	$cnt=1;
	$contentsrowcolumn  =$count.'¥'.'13';
	if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
		while($rowprice = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowprice['product_code']!='')?$rowprice['product_code']: ' ')."^";
			$contents  .= (($rowprice['mrp_code']!='')?$rowprice['mrp_code']: ' ')."^";
			$contents  .= (($rowprice['mrp']!='')?round($rowprice['mrp'],2): ' ')."^";
			$contents  .= (($rowprice['sale_rate']!='')?round($rowprice['sale_rate'],2): ' ')."^";
			$contents  .= (($rowprice['UOM']!='')?$rowprice['UOM']: ' ')."^";
			$contents  .= (($rowprice['branch_code']!='')?$rowprice['branch_code']: ' ')."^";
			$contents  .= (($rowprice['destination_code']!='')?$rowprice['destination_code']: ' ')."^";
			$contents  .= (($rowprice['order_type']!='')?$rowprice['order_type']: ' ')."^";
			$contents  .= (($rowprice['acedns']!='')?$rowprice['acedns']: ' ')."^";
			$contents  .= (($rowprice['ws_rate']!='')?$rowprice['ws_rate']: ' ')."^";
			$contents  .= (($rowprice['distributor_rate']!='')?$rowprice['distributor_rate']: ' ')."^";
			$contents  .= (($rowprice['ss_rate']!='')?$rowprice['ss_rate']: ' ')."^";
			$contents  .= (($rowprice['depot_rate']!='')?$rowprice['depot_rate']: ' ');
			$linecontents  .= $contents."\n";
		}
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
			$datacontents = '0'.'¥'.'13';
		}
	}
}
else
{
$sqlbranches="SELECT branch_code FROM branch_master WHERE 1";
$rsbranches=mysqli_query($link,$sqlbranches);
$countbranches=mysqli_num_rows($rsbranches);*/

if(branch_wise_mrp=='yes')
{
   $sqlquery="SELECT DISTINCT MRP.* FROM mrp MRP,employee_master EM WHERE FIND_IN_SET(MRP.branch_code,EM.branch_code)
			AND EM.emp_code='".$emp_code."' ".$condition_one." ".$login_condition."";
  //$sqlquery="SELECT MRP.* FROM mrp MRP WHERE 1 ".$condition_one." ".$login_condition."";			
}
else if(state_wise_mrp=='yes')
{
	$sqlstate="SELECT state FROM employee_master WHERE emp_code='".$emp_code."'";
	$rsstate=mysqli_query($link,$sqlstate);
	$rowstate=mysqli_fetch_assoc($rsstate);
	$state_name=$rowstate['state'];
	if(strtoupper($nick_name)=='ARCHITA')
	{
		$sqlstatecode="SELECT state_code FROM state_master WHERE dns_state_code='".$state_name."'";
	}
	else
	{
		$sqlstatecode="SELECT state_code FROM state_master WHERE statename LIKE '%".$state_name."%'";
	}
	$rsstatecode=mysqli_query($link,$sqlstatecode);
	$rowstatecode=mysqli_fetch_assoc($rsstatecode);
	$state_code=$rowstatecode['state_code'];
	$sqlquery="SELECT MRP.* FROM mrp MRP WHERE 1 AND MRP.state_code='".$state_code."' ".$condition_one." ".$login_condition."";
}
else
{
	$sqlquery="SELECT MRP.* FROM mrp MRP WHERE 1 ".$condition_one." ".$login_condition."";
}
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$contentsrowcolumn  =$count.'¥'.'13';
	if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
		while($rowprice = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowprice['product_code']!='')?$rowprice['product_code']: ' ')."^";
			$contents  .= (($rowprice['mrp_code']!='')?$rowprice['mrp_code']: ' ')."^";
			$contents  .= (($rowprice['mrp']!='')?round($rowprice['mrp'],2): ' ')."^";
			$contents  .= (($rowprice['sale_rate']!='')?round($rowprice['sale_rate'],2): ' ')."^";
			$contents  .= (($rowprice['UOM']!='')?$rowprice['UOM']: ' ')."^";
			$contents  .= (($rowprice['branch_code']!='')?$rowprice['branch_code']: ' ')."^";
			$contents  .= (($rowprice['destination_code']!='')?$rowprice['destination_code']: ' ')."^";
			$contents  .= (($rowprice['order_type']!='')?$rowprice['order_type']: ' ')."^";
			$contents  .= (($rowprice['acedns']!='')?$rowprice['acedns']: ' ')."^";
			$contents  .= (($rowprice['ws_rate']!='')?round($rowprice['ws_rate'],2): ' ')."^";
			$contents  .= (($rowprice['distributor_rate']!='')?round($rowprice['distributor_rate'],2): ' ')."^";
			$contents  .= (($rowprice['ss_rate']!='')?round($rowprice['ss_rate'],2): ' ')."^";
			$contents  .= (($rowprice['depot_rate']!='')?round($rowprice['depot_rate'],2): ' ');

			$linecontents  .= $contents."\n";
		}
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
			$datacontents = '0'.'¥'.'13';
		}
	}
//}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url =APICALLLOGURL."/mrp-txt-incremental-6.0.5.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=mrp.txt");
	print "$datacontents"; 		
	mysqli_close($link);
?>
