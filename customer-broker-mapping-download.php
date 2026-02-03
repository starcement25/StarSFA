<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$dns_broker_id=$_REQUEST['dns_broker_id'];
$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);
$incremental_download=$_REQUEST['incremental_download'];
$data_download_time=$_REQUEST['data_download_time'];
$data_download_time=str_replace('€',' ',$data_download_time);

$sqlbrokerchk="SELECT broker_id FROM broker_master WHERE dns_broker_id='".addslashes($dns_broker_id)."'";
$rsbrokerchk=mysqli_query($link,$sqlbrokerchk);
$rowbrokerchk=mysqli_fetch_assoc($rsbrokerchk);
$broker_code=$rowbrokerchk['broker_id'];

if($incremental_download=='no')
{
	$login_condition=" AND acedns='Y'";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(CBR.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
}


$sqlquery="SELECT  DISTINCT customer_code,broker_code,acedns,mapped_broker FROM customer_broker_relation WHERE broker_code='".$broker_code."'"; 
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);

	$contentsrowcolumn=$count.'¥'.'4';
	if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
		
		while($rowcustomerrds = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowcustomerrds['customer_code']!='')?$rowcustomerrds['customer_code']: ' ')."^";
			$contents  .= (($rowcustomerrds['broker_code']!='')?$rowcustomerrds['broker_code']: ' ')."^";
			$contents  .= (($rowcustomerrds['acedns']!='')?$rowcustomerrds['acedns']: ' ')."^";
			$contents  .= (($rowcustomerrds['mapped_broker']!='')?$rowcustomerrds['mapped_broker']: ' ');
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
			$datacontents = '0'.'¥'.'4';
		}
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://www.acedns.in/acednsproduct/customer-broker-mapping-download.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/customer-branch-relational-txt-incremental.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download"."\r\n";
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
	header("Content-Disposition: attachment; filename=customer_broker_relation.txt");
	print "$datacontents"; 		
?>
