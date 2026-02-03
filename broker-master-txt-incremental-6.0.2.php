<?php
require("include/config.php");
require("include/dbcon.php");
$dns_broker_id=$_REQUEST['dns_broker_id'];
/*$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);
$incremental_download=$_REQUEST['incremental_download'];
$data_download_time=$_REQUEST['data_download_time'];
$data_download_time=str_replace('€',' ',$data_download_time);
if($incremental_download=='no')
{
	$login_condition=" AND acedns='Y'";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(BM.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
}*/


$sqlquery="SELECT BM.* FROM broker_master BM WHERE dns_broker_id='".$dns_broker_id."'";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$contentsrowcolumn  =$count.'¥'.'7';
	if($count>0){
		/*$date=date('Y-m-d');
		$time=date('h:i:s');
		$contentsdatetime = $date.'€'.$time."\n";*/
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
		
		while($rowbroker = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowbroker['broker_id']!='')?$rowbroker['broker_id']: ' ')."^";
			$contents  .= (($rowbroker['broker_name']!='')?$rowbroker['broker_name']: ' ')."^";
			$contents  .= (($rowbroker['dns_broker_id']!='')?$rowbroker['dns_broker_id']: ' ')."^";
			$contents  .= (($rowbroker['contact_person']!='')?$rowbroker['contact_person']: ' ')."^";
			$contents  .= (($rowbroker['mail_id']!='')?$rowbroker['mail_id']: ' ')."^";
			$contents  .= (($rowbroker['phone_no']!='')?$rowbroker['phone_no']: ' ')."^";
			$contents  .= (($rowbroker['acedns']!='')?$rowbroker['acedns']: ' ');
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
			$datacontents = '0'.'¥'.'7';
		}
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/broker-master-txt-incremental-6.0.2.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	
	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=broker_master.txt");
	print "$datacontents"; 	
	mysqli_close($link);
?>
