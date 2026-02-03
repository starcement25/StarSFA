<?php
require("include/config.php");
require("include/dbcon.php");
$emp_code=$_REQUEST['emp_code'];

$sqlquery="SELECT * FROM generic_oil_master WHERE oil_name <>'' ORDER BY oil_name ASC";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$contentsrowcolumn  =$count.'¥'.'2';
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
		
		while($rowgenericoil = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowgenericoil['oil_name']!='')?$rowgenericoil['oil_name']: ' ')."^";
			$contents  .= (($rowgenericoil['competitor_name']!='')?$rowgenericoil['competitor_name']: ' ');
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
			$datacontents = '0'.'¥'.'2';
		}
	}
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/bank-list-txt-incremental-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&data_download_time=$data_download_time&incremental_download=$incremental_download"."\r\n";
	$insertPos=0;  // variable for saving //Users position
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
	header("Content-Disposition: attachment; filename=generic_oil_master.txt");
	print "$datacontents"; 	
	mysqli_close($link);
?>
