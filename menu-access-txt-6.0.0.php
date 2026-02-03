<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");

$emp_code=$_REQUEST['emp_code'];
$sqlquery="select * from menu_access WHERE emp_code='".$emp_code."'";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	
	$contentsrowcolumn  =$count.'¥'.'1';
	if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
		while($rowaccess = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowaccess['not_accessible_menu']!='')?$rowaccess['not_accessible_menu']: ' ');
			$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);	
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://www.acedns.in/acednsproduct/menu-access-txt-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/user-access-txt-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code"."\r\n";
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
	header("Content-Disposition: attachment; filename=menu_access.txt");
	print "$datacontents"; 		
?>
