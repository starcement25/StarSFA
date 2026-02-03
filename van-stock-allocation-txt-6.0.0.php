<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];

$curdate=date('Y-m-d');
	$sqlquery="SELECT emp_code,prod_code,allocated_qty,balance_qty,acedns FROM van_stock_allocation WHERE emp_code='".$emp_code."' AND acedns='Y' and allocation_date='".$curdate."'";
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
		$contentsrowcolumn=$count.'¥'.'5';
		while($rowvanallocation = mysqli_fetch_assoc($result))
		{
			$prod_code=$rowvanallocation['prod_code'];
			$allocated_qty=$rowvanallocation['allocated_qty'];
			$emp_code=$rowvanallocation['emp_code'];
			$acedns=$rowvanallocation['acedns'];
			$balance_qty=$rowvanallocation['balance_qty'];
			
				$contents  = (($emp_code!='')?$emp_code: ' ')."^";
				$contents  .= (($prod_code!='')?$prod_code: ' ')."^";
				$contents  .= (($allocated_qty!='')?$allocated_qty: 0)."^";
				$contents  .= (($balance_qty!='')?$balance_qty: 0)."^";
				$contents  .= (($acedns!='')?$acedns: ' ');
				$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://salesmpower.acedns.in/van-stock-allocation-txt-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=van_stock_allocation.txt");
	print "$datacontents"; 		
?>
