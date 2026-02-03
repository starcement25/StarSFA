<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");

$emp_code=$_REQUEST['emp_code'];
$json_array = array();

$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);
$incremental_download=$_REQUEST['incremental_download'];
$data_download_time=$_REQUEST['data_download_time'];
$data_download_time=str_replace('€',' ',$data_download_time);
if($incremental_download=='no')
{
	$login_condition=" acedns='Y'";
}
else
{
	$login_condition=" UNIX_TIMESTAMP(creation_date) > UNIX_TIMESTAMP('".$last_update_time."') ";
}
$sqlsampledetails = "SELECT refference_no,sample_photo,acedns FROM sample_master WHERE ".$login_condition;	
$rssampledetails= mysqli_query($link,$sqlsampledetails);
$count=mysqli_num_rows($rssampledetails);		

if($count >0){		
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
		while($rowsample = mysqli_fetch_assoc($rssampledetails))
		{
			$sample_url='/sample/thumbnail/thumb_'.$rowsample['sample_photo'];
			$response[$rowsample['refference_no']]= array(
					"refference_no"=>$rowsample['refference_no'],
					"sample_photo"=>$sample_url,
					"acedns"=>$rowsample['acedns']
				);
			$json_array['sample_details'] = $response;		
		}
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
header("Content-type: application/text"); 
header("Content-Disposition: attachment; filename=sample_info.txt");
echo json_encode($json_array); 		
?>
