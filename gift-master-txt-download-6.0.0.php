<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];

	$sql_gift = "SELECT * FROM gift_master WHERE  1 ORDER BY gift_name ASC";
	$res_gift = mysqli_query($link,$sql_gift);
	$count=mysqli_num_rows($res_gift);
	//print_r($vertical_array);
	if($count>0){
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));
		
		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
		//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
		$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
		$countappraisal=0;
		while($row_gift = mysqli_fetch_assoc($res_gift)){
		$gift_id = $row_gift['gift_id'];
		$gift_name = $row_gift['gift_name'];
		$cust_type = $row_gift['cust_type'];
		$acedns = $row_gift['acedns'];
		$from_date = $row_gift['from_date'];
		$to_date = $row_gift['to_date'];
			 
			$contents  = (($gift_id!='')?$gift_id: ' ')."^";
			$contents  .= (($gift_name!='')?trim(preg_replace('/[\r\n]+/', '',$gift_name)): ' ')."^";
			$contents  .= (($cust_type!='')?$cust_type: ' ')."^";
			$contents  .= (($acedns!='')?$acedns: ' ')."^";
			$contents  .= (($from_date!='')?$from_date: ' ')."^";
			$contents  .= (($to_date!='')?$to_date: ' ');
			$linecontents  .= $contents."\n";
		}
			$contentsrowcolumn=$count.'¥'.'6';
			$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
		}
		else
		{
			$datacontents = '0'.'¥'.'0';
		}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/gift-master-txt-download-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=gift_master.txt");
	print "$datacontents"; 
	mysqli_close($link);		
?>
