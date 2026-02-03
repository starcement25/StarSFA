<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('memory_limit', '-1');
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
	
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";

	 $sqlsurveyidfetch="SELECT SH.survey_id,SH.mall_id,SH.survey_type FROM survey_header SH,offer_emp_relation OER WHERE 
						SH.mall_id=OER.mall_id AND 
						UNIX_TIMESTAMP(OER.enable_date) > UNIX_TIMESTAMP('".$last_update_time."') AND OER.emp_code='".$emp_code."' AND 
						OER.status='assigned'";
	 $rssurveyidfetch = mysqli_query($link,$sqlsurveyidfetch);
	 $countsurveyidfetch=mysqli_num_rows($rssurveyidfetch);
	 if($countsurveyidfetch >0){
		 $countdata=0;
	 while($rowsurveyidfetch = mysqli_fetch_assoc($rssurveyidfetch))
	  {					
		 $survey_id_fetch=$rowsurveyidfetch['survey_id'];
		 $mall_id_fetch=$rowsurveyidfetch['mall_id'];
		 $survey_type=$rowsurveyidfetch['survey_type'];
		 if($survey_type=='mall')
		 {
		 	$sqlquery="SELECT * from survey_publish  WHERE row_id='RA002' AND survey_id='".$survey_id_fetch."'";
		 }
		 if($survey_type=='hi-street')
		 {
		 	$sqlquery="SELECT * from survey_publish  WHERE row_id IN('RA136','RA143') AND survey_id='".$survey_id_fetch."'";
		 }
		 $result = mysqli_query($link,$sqlquery);
		 $count=mysqli_num_rows($result);
	
		if($count>0){
			while($rowssurvey = mysqli_fetch_assoc($result))
			{
				$value=$rowssurvey['value'];
				$status=$rowssurvey['status'];
				$contents  = (($rowssurvey['survey_id']!='')?$rowssurvey['survey_id']: ' ')."^";
				$contents  .= (($mall_id_fetch!='')?$mall_id_fetch: ' ')."^";
				$contents  .= (($rowssurvey['row_id']!='')?trim(preg_replace('/[\r\n]+/', '',$rowssurvey['row_id'])): ' ')."^";
				$contents  .= (($rowssurvey['action_id']!='')?$rowssurvey['action_id']: ' ')."^";
				$contents  .= (($value!='')?$value: ' ')."^";
				$contents  .= (($status!='')?$status: ' ');
				$linecontents  .= $contents."\n";
				$countdata++;
			}
		}
	  }
	   $contentsrowcolumn=$countdata.'¥'.'6';
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
	$url = "http://www.acedns.in/acednsproduct/offer-publish-download-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	
	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=offer_publish.txt");
	print "$datacontents"; 	
	mysqli_close($link);	
?>
