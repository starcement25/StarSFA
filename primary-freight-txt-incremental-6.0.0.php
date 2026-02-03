<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

ini_set('memory_limit', '-1');
set_time_limit(1000);
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
$emp_code=$_REQUEST['emp_code'];
//$emp_code='100017206';
$incremental_download=$_REQUEST['incremental_download'];
//$last_update_time='2014-06-06 13:40:25';
$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);
$data_download_time=$_REQUEST['data_download_time'];
$data_download_time=str_replace('€',' ',$data_download_time);

if(vertical_fields=='yes'){
	$sqlempvertical="SELECT vertical_value FROM employee_master WHERE emp_code='".$emp_code."'";
	$rsempvertical=mysqli_query($link,$sqlempvertical);
	$rowempvertical=mysqli_fetch_assoc($rsempvertical);
	$emp_vertical_value=$rowempvertical['vertical_value'];
	$emp_vertical_value_array=explode(',',$emp_vertical_value);
	$condition_one=" AND (";
	$condition_two='';
	foreach($emp_vertical_value_array as $emp_vertical_values)
	{
		$condition_two.=" FIND_IN_SET( '".$emp_vertical_values."',PF.vertical_value) OR";
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
	$login_condition=" AND UNIX_TIMESTAMP(PF.datetime) > UNIX_TIMESTAMP('".$last_update_time."')";
}

$sqlfreight= "SELECT * FROM (SELECT PF.dns_prod_code,DATE_FORMAT(SUBSTRING(PF.datetime,1,10),'%d-%m-%Y') As last_updated_date,
				PF.branch_code,PF.freight_cost,PF.transport_mode,PF.truck_load  FROM freight_cost PF WHERE 1 ".$login_condition.$condition_one." 
				ORDER BY PF.datetime DESC) AS SAT GROUP BY 1,3,5,6 ORDER BY 2 DESC";
$rsfreight = mysqli_query($link,$sqlfreight);
$count=mysqli_num_rows($rsfreight);
	$cnt=1;
	$contentsrowcolumn  =$count.'¥'.'5';
	if($count>0){
		/*$date=date('Y-m-d');
		$time=date('H:i:s');
		$contentsdatetime = $date.'€'.$time."\n";*/
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
		while($rowfreight = mysqli_fetch_assoc($rsfreight))
		{
			$branch_code=$rowfreight['branch_code'];
			$dns_prod_code=$rowfreight['dns_prod_code'];
			$freight_cost=$rowfreight['freight_cost'];
			$transport_mode=$rowfreight['transport_mode'];
			$truck_load=$rowfreight['truck_load'];
			
			$contents  = (($dns_prod_code!='')?$dns_prod_code: ' ')."^";
			$contents  .= (($branch_code!='')?$branch_code: ' ')."^";
			$contents  .= (($freight_cost!='')?$freight_cost: ' ')."^";
			$contents  .= (($transport_mode!='')?$transport_mode: ' ')."^";
			$contents  .= (($truck_load!='')?$truck_load: ' ');
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
			$datacontents = '0'.'¥'.'5';
		}
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url =APICALLLOGURL. "/primary-freight-txt-incremental-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=primary_freight.txt");
	print "$datacontents";	
	mysqli_close($link);
?>
