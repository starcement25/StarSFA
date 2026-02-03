<?php

ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);

require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];

$sqlquery="SELECT  prod_code,SUM(reallocation_qty) AS total_reallocation_qty,SUM(balance_qty) AS total_balance_qty FROM stock_reallocation 
			WHERE emp_code='".$emp_code."' AND active_flag='Y' AND emp_code IN(SELECT DISTINCT reporting_to FROM employee_master WHERE acedns='Y') 
			GROUP BY emp_code,prod_code";
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);

$date=gmdate('d',strtotime('+330 minute'));
$month=gmdate('m',strtotime('+330 minute'));
$year=gmdate('Y',strtotime('+330 minute'));

$hour=gmdate('H',strtotime('+330 minute'));
$minute=gmdate('i',strtotime('+330 minute'));
$second=gmdate('s',strtotime('+330 minute'));
$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";

	if($count>0){
		$contentsrowcolumn=$count.'¥'.'3';
		while($rowstockreallocation = mysqli_fetch_assoc($result))
		{
			$total_balance_qty=$rowstockreallocation['total_balance_qty'];
			$total_reallocation_qty=$rowstockreallocation['total_reallocation_qty'];
			$prod_code=$rowstockreallocation['prod_code'];
			
				$contents = (($prod_code!='')?$prod_code: ' ')."^";
				$contents .= (($total_reallocation_qty!='')?$total_reallocation_qty: 0)."^";
				$contents .= (($total_balance_qty!='')?$total_balance_qty: 0);
				$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://salesmpower.acedns.in/stock-reallocation-txt-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=stock_reallocation.txt");
	print "$datacontents"; 		
?>
