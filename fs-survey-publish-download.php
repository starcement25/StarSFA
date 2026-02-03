<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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

$date=date('Y-m-d');
$time=date('H:i:s');
$contentsdatetime = $date.'€'.$time."\n";


	 $sqlmallidfetch="SELECT MER.mall_id,MM.mall_name,MM.area FROM mall_emp_relation MER,mall_master MM WHERE MM.mall_id=MER.mall_id AND 
	 			MER.status='assigned' AND MER.emp_code='".$emp_code."' ";
	 $rsmallidfetch = mysqli_query($link,$sqlmallidfetch);
	 $countmallidfetch=mysqli_num_rows($rsmallidfetch);
	 if($countmallidfetch >0){
		 $countdata=0;
	 while($rowmallidfetch = mysqli_fetch_assoc($rsmallidfetch))
	  {					
		 $mall_id_fetch=$rowmallidfetch['mall_id'];
		 $mall_name_fetch=$rowmallidfetch['mall_name'];
		 $area_fetch=$rowmallidfetch['area'];
		 if(substr($mall_id_fetch,0,1)=='M')
		 {
			 //$condition=" mall_name='".$mall_name_fetch."'";
			 $condition=" mall_id='".$mall_id_fetch."'";
		 }
		 else
		 {
			  $condition=" mall_id='".$mall_id_fetch."'";
		 }
		 $sqlquery="SELECT * from foot_soldier  WHERE ".$condition." AND DCE_status='NOT DONE' AND 
		 		UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
		 $result = mysqli_query($link,$sqlquery);
		 $count=mysqli_num_rows($result);
	
		if($count>0){
			while($rowssurvey = mysqli_fetch_assoc($result))
			{
				
				$contents  = (($rowssurvey['foot_soldier_id']!='')?$rowssurvey['foot_soldier_id']: ' ')."^";
				$contents  .= (($mall_id_fetch!='')?$mall_id_fetch: ' ')."^";
				$contents  .= (($rowssurvey['mall_name']!='')?trim(preg_replace('/[\r\n]+/', '',$rowssurvey['mall_name'])): ' ')."^";
				$contents  .= (($rowssurvey['pin_code']!='')?$rowssurvey['pin_code']: ' ')."^";
			    $contents  .= (($rowssurvey['business_name']!='')?trim(preg_replace('/[\r\n]+/', '',$rowssurvey['business_name'])): ' ')."^";
				$contents  .= (($rowssurvey['type']!='')?$rowssurvey['type']: ' ')."^";
				$contents  .= (($rowssurvey['DCE_status']!='')?$rowssurvey['DCE_status']: ' ');
				$linecontents  .= $contents."\n";
				$countdata++;
			}
		}
	  }
	   $contentsrowcolumn=$countdata.'¥'.'7';
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
	$url = APICALLLOGURL."/fs-survey-publish-download.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	
	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=fs_survey_publish.txt");
	print "$datacontents"; 	
	mysqli_close($link);	
?>
