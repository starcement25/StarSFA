<?php
require("include/config.php");
require("include/dbcon.php");

$emp_code=$_REQUEST['emp_code'];

$sqlquery="SELECT scheme_id,freebies_prod_code,freebies_prod_desc,qty,value_percent,value_amount FROM freebies_master";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$contentsrowcolumn  =$count.'¥'.'6';
	if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
		while($rowscheme = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowscheme['scheme_id']!='')?$rowscheme['scheme_id']: ' ')."^";
			$contents  .= (($rowscheme['freebies_prod_code']!='')?$rowscheme['freebies_prod_code']: ' ')."^";
			$contents  .= (($rowscheme['freebies_prod_desc']!='')?$rowscheme['freebies_prod_desc']: ' ')."^";
			$contents  .= (($rowscheme['qty']!='')?$rowscheme['qty']: ' ')."^";
			$contents  .= (($rowscheme['value_percent']!='')?$rowscheme['value_percent']: ' ')."^";
			$contents  .= (($rowscheme['value_amount']!='')?$rowscheme['value_amount']: ' ');
			
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
	/*$contents .= "</recordset>";			
	echo $contents;	*/
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/freebies-master-txt-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=freebies_master.txt");
	print "$datacontents"; 	
	mysqli_close($link);	
