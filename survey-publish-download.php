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
	
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";

	 $sqlsurveyidfetch="SELECT SH.survey_id,SH.mall_id,SH.survey_type FROM survey_header SH,mall_emp_audit_relation MEAR WHERE 
						SH.mall_id=MEAR.mall_id AND SH.status='ready to publish' AND 
						UNIX_TIMESTAMP(SH.download_time) > UNIX_TIMESTAMP('".$last_update_time."') AND MEAR.emp_code='".$emp_code."' AND 
						MEAR.status='assigned'";
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
		 	$sqlquery="SELECT * from survey_publish  WHERE row_id IN('RA002','RA004','RA015','RA016','RA018','RA022') AND survey_id='".$survey_id_fetch."'";
		 }
		 if($survey_type=='hi-street')
		 {
		 	$sqlquery="SELECT * from survey_publish  WHERE row_id IN('RA136','RA140','RA141','RA143','RA145','RA149','RA150','RA152','RA159') 
					AND survey_id='".$survey_id_fetch."'";
		 }
		 $result = mysqli_query($link,$sqlquery);
		 $count=mysqli_num_rows($result);
	
		if($count>0){
			while($rowssurvey = mysqli_fetch_assoc($result))
			{
				$status='NOT_DONE';
				$contents  = (($rowssurvey['survey_id']!='')?$rowssurvey['survey_id']: ' ')."^";
				$contents  .= (($mall_id_fetch!='')?$mall_id_fetch: ' ')."^";
				$contents  .= (($rowssurvey['row_id']!='')?trim(preg_replace('/[\r\n]+/', '',$rowssurvey['row_id'])): ' ')."^";
				$contents  .= (($rowssurvey['action_id']!='')?$rowssurvey['action_id']: ' ')."^";
				$contents  .= (($rowssurvey['value']!='')?$rowssurvey['value']: ' ')."^";
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
	$url = APICALLLOGURL."/survey-publish-download.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	
	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=survey_publish.txt");
	print "$datacontents"; 		
?>
