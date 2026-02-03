<?php
require("include/config.php");
require("include/dbcon.php");

$emp_code=$_REQUEST['emp_code'];
$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);
$incremental_download=$_REQUEST['incremental_download'];
$data_download_time=$_REQUEST['data_download_time'];
$data_download_time=str_replace('€',' ',$data_download_time);

if($incremental_download=='no')
{
	$login_condition="";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
}

$sqlquery="SELECT * FROM loyalty_card_holder_master WHERE 1 ".$login_condition." ORDER BY loyalty_card_holder_name ASC";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$string = trim(preg_replace('/\s+/', ' ', $string));
	$contentsrowcolumn  =$count.'¥'.'10';
	if($count>0){
		$date=gmdate('d',strtotime('+329 minute'));
		$month=gmdate('m',strtotime('+329 minute'));
		$year=gmdate('Y',strtotime('+329 minute'));
		
		$hour=gmdate('H',strtotime('+329 minute'));
		$minute=gmdate('i',strtotime('+329 minute'));
		$second=gmdate('s',strtotime('+329 minute'));
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
		while($rowloyalty = mysqli_fetch_assoc($result))
		{
				$contents  = (($rowloyalty['loyalty_card_holder_code']!='')?$rowloyalty['loyalty_card_holder_code']: ' ')."^";
				$contents  .= (($rowloyalty['loyalty_card_holder_name']!='')?$rowloyalty['loyalty_card_holder_name']: ' ')."^";
				$contents  .= (($rowloyalty['loyalty_card_no']!='')?trim(preg_replace('/\s+/', '',$rowloyalty['loyalty_card_no'])): ' ')."^";
				$contents  .= (($rowloyalty['card_type']!='')?$rowloyalty['card_type']: ' ')."^";
				$contents  .= (($rowloyalty['total_purchase_value']!='')?$rowloyalty['total_purchase_value']: ' ')."^";
				$contents  .= (($rowloyalty['total_reward_point']!='')?$rowloyalty['total_reward_point']: ' ')."^";
				$contents  .= (($rowloyalty['last_update_on']!='')?$rowloyalty['last_update_on']: ' ')."^";
				$contents  .= (($rowloyalty['redeemed']!='')?$rowloyalty['redeemed']: ' ')."^";
				$contents  .= (($rowloyalty['phone_no']!='')?$rowloyalty['phone_no']: ' ')."^";
				$contents  .= (($rowloyalty['address']!='')?$rowloyalty['address']: ' ');
				
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
			$datacontents = '0'.'¥'.'10';
		}
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://www.acedns.in/acednsproduct/loyalty-card-holder-txt-4.0.4.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$contents .= "</recordset>";			
	echo $contents;	*/
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/loyalty-card-holder-txt-4.0.4.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download"."\r\n";
	$insertPos=0;  // variable for saving 
	while (!feof($file)) {
		$line=fgets($file);
		if (strpos($line, 'http://')!==false) {
			$insertPos=ftell($file);
			$newline =  $newuser;
		}
		else
		{
			$newline.=$line;   // append existing data with new data of user
		}

	}
	fseek($file,$insertPos);   // move pointer to the file position where we saved above 
	fwrite($file, $newline);
	fclose($file);*/	

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=loyalty_card_holder_master.txt");
	print "$datacontents"; 		
?>
