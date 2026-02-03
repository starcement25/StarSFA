<?php
ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once("include/config.php");
	include_once("include/config-setup.php");
	include_once("include/dbcon.php");
	include_once("include/config-email-setup.php");
	include_once("include/functions.php");

	$emp_code=$_REQUEST['emp_code'];
	/*$sqlempname="SELECT emp_name FROM employee_master WHERE emp_code='".$emp_code."'";
	$rsempname=mysqli_query($link,$sqlempname);
	$rowempname=mysqli_fetch_assoc($rsempname);
	$emp_name=$rowempname['emp_name'];*/
	if(employeewise_hierarchy=='yes'){
		$employee_hierarchy=return_employee_hierarchy($emp_code);
		$sqlreportinglevel="SELECT COUNT(emp_code) AS total_emp_code FROM employee_master WHERE FIND_IN_SET('".$emp_code."', reporting_to)";
		$rsreportinglevel=mysqli_query($link,$sqlreportinglevel);
		$rowreportinglevel=mysqli_fetch_assoc($rsreportinglevel);
		$reporting_level=$rowreportinglevel['total_emp_code'];
	}
	else
	{
		$reporting_level=0;
	} 
	$rds_list='';
	if($reporting_level >0)
	{
		$sqlrdslist="SELECT rds_code FROM rds_master WHERE emp_code IN(".$employee_hierarchy.")";
		$rsrdslist=mysqli_query($link,$sqlrdslist);
		while($rowrdslist=mysqli_fetch_assoc($rsrdslist))
		{
			$rds_list=$rds_list."'".$rowrdslist['rds_code']."'".',';
		}
		$rds_list=substr($rds_list,0,-1);
		$sqlUpdate="UPDATE activity_log SET
					mis_updated_flag_app='1'
					WHERE (rds_code IN(".$rds_list.") OR SUBSTRING(transaction_id,2,5) IN (".$employee_hierarchy."))";
		if(mysqli_query($link,$sqlUpdate))
		{
			echo "1";
		}
		else
		{
			echo "0";
		}
	}
	else
	{
		$sqlrds="SELECT rds_code FROM rds_master WHERE emp_code='".$emp_code."'";
		$rsrds=mysqli_query($link,$sqlrds);
		$rowrds=mysqli_fetch_assoc($rsrds);
		$rds_code=$rowrds['rds_code'];
	
		$sqlactivitylog="SELECT transaction_id FROM activity_log WHERE 	updated_flag_app='0' AND receiver_code='".$emp_code."'";
		$rsactivitylog=mysqli_query($link,$sqlactivitylog);
		$cntactivitylog=mysqli_num_rows($rsactivitylog);
		if($cntactivitylog >0)
		{
			$sqlUpdate="UPDATE activity_log SET updated_flag_app='1' WHERE receiver_code='".$emp_code."'";
		}
		else
		{
			$sqlUpdate="UPDATE activity_log SET
						updated_flag_app='1'
						WHERE receiver_code IS NULL AND (rds_code='".$rds_code."' OR SUBSTRING(transaction_id,2,5)='".$emp_code."' OR SUBSTRING(transaction_id,3,5)='".$emp_code."')";
		}
		if(mysqli_query($link,$sqlUpdate))
		{
			echo "1";
		}
		else
		{
			echo "0";
		}
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/transactionDeletionConfirmation-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);
	/*$config = 'api_calllog.txt';
	$file=fopen($config,"r+");
	$date = date("F j, Y");
	$time = date("H:i:s");
	$newuser ="[$date $time]"."http://www.acedns.in/acednsproduct/transactionDeletionConfirmation-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code"."\r\n";
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
	mysqli_close($link);
?>
