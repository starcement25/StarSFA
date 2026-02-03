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

if(employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$emp_hierarchy_condition='CRR.emp_code IN('.$employee_hierarchy.')';
}
else
{
	$emp_hierarchy_condition="CRR.emp_code='".$emp_code."'";
}

if($incremental_download=='no')
{
	$login_condition="";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(BC.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
}
$sqlquery="SELECT BC.broker_id,BC.oil_category,BC.pack_size,BC.UOM,BC.brokerage_cost,BC.acedns FROM 
			brokerage_cost BC,customer_route_emp_relation CRR,customer_broker_relation CBR 
			WHERE ".$emp_hierarchy_condition." AND CBR.customer_code=CRR.customer_code AND CBR.broker_code=BC.broker_id 
			 AND CBR.acedns!='N' AND CRR.acedns='Y' ".$login_condition.""; 
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);

	$contentsrowcolumn=$count.'¥'.'6';
	if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
		
		while($rowbrokeragecost = mysqli_fetch_assoc($result))
		{
			$UOM=$rowbrokeragecost['UOM'];
			if($UOM=='MT') $UOM='Loose';
			else			$UOM=$UOM;
			$contents  = (($rowbrokeragecost['broker_id']!='')?$rowbrokeragecost['broker_id']: ' ')."^";
			$contents  .= (($rowbrokeragecost['oil_category']!='')?$rowbrokeragecost['oil_category']: ' ')."^";
			$contents  .= (($rowbrokeragecost['pack_size']!='')?$rowbrokeragecost['pack_size']: ' ')."^";
			$contents  .= (($UOM!='')?$UOM: ' ')."^";
			$contents  .= (($rowbrokeragecost['brokerage_cost']!='')?$rowbrokeragecost['brokerage_cost']: ' ')."^";
			$contents  .= (($rowbrokeragecost['acedns']!='')?$rowbrokeragecost['acedns']: ' ');
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
			$datacontents = '0'.'¥'.'6';
		}
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://www.acedns.in/acednsproduct/brokerage-cost-txt-incremental-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=brokerage_cost.txt");
	print "$datacontents"; 		
?>
