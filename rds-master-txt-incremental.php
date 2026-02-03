<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];
$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);
$incremental_download=$_REQUEST['incremental_download'];

if(employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$emp_hierarchy_condition='RM.emp_code IN('.$employee_hierarchy.')';
}
else
{
	$emp_hierarchy_condition="RM.emp_code='".$emp_code."'";
}

if($incremental_download=='no')
{
	$login_condition="";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(RM.download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
}

 if($emp_code!='C0007'){
 $sqlquery="SELECT RM.rds_code, RM.rds_name, RM.emp_code,RM.rds_type
			FROM rds_master RM  WHERE ".$emp_hierarchy_condition." ".$login_condition." ORDER BY RM.rds_name ASC";
 }
 else
 {
		$sqlquery="SELECT RM.rds_code, RM.rds_name, RM.emp_code,RM.rds_type FROM rds_master RM WHERE 1  ".$login_condition." ORDER BY RM.rds_name ASC";
 }

$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);

	$contentsrowcolumn=$count.'¥'.'4';
	if($count>0){
		$date=date('Y-m-d');
		$time=date('H:i:s');
		$contentsdatetime = $date.'€'.$time."\n";
		while($rowsrds = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowsrds['rds_code']!='')?$rowsrds['rds_code']: ' ')."^";
			$contents  .= (($rowsrds['rds_name']!='')?$rowsrds['rds_name']: ' ')."^";
			$contents  .= (($rowsrds['emp_code']!='')?$rowsrds['emp_code']: ' ')."^";
			$contents  .= (($rowsrds['rds_type']!='')?$rowsrds['rds_type']: ' ');
			$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://www.acedns.in/acednsproduct/rds-master-txt-incremental.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&incremental_download=$incremental_download";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/rds-master-txt-incremental.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time&incremental_download=$incremental_download"."\r\n";
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
	header("Content-Disposition: attachment; filename=rds_master.txt");
	print "$datacontents"; 		
?>
