<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];
$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);
$incremental_download=$_REQUEST['incremental_download'];
if($incremental_download=='no')
{
	$login_condition="";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
}


if(employeewise_hierarchy=='yes'){
		$employee_hierarchy=return_employee_hierarchy($emp_code);
		$emp_hierarchy_condition='emp_code IN('.$employee_hierarchy.')';
}
else
{
	$emp_hierarchy_condition="emp_code='".$emp_code."'";
}
$sqlempbranch="SELECT branch_code FROM employee_master WHERE ".$emp_hierarchy_condition;
$rsempbranch=mysqli_query($link,$sqlempbranch);
while($rowempbranch=mysqli_fetch_assoc($rsempbranch))
{
	$branch_value=$rowempbranch['branch_code'];
	$branch_code=$branch_code.$branch_value.',';
}
$branch_value_array=explode(',',$branch_code);
$branch_value_final = "'".implode("','", $branch_value_array)."'";
$condition_branch=" AND branch_code IN (".$branch_value_final.")";

$sqlquery="SELECT * FROM prodqty_custclass_wise_TD WHERE 1 ".$condition_branch.$login_condition;
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$cnt=1;
	$contentsrowcolumn  =$count.'¥'.'6';
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
		while($rowdiscount = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowdiscount['branch_code']!='')?$rowdiscount['branch_code']: ' ')."^";
			$contents  .= (($rowdiscount['prod_code']!='')?$rowdiscount['prod_code']: ' ')."^";
			$contents  .= (($rowdiscount['qty_slab']!='')?$rowdiscount['qty_slab']: ' ')."^";
			$contents  .= (($rowdiscount['TD_percent']!='')?$rowdiscount['TD_percent']: ' ')."^";
			$contents  .= (($rowdiscount['cust_class']!='')?$rowdiscount['cust_class']: ' ')."^";
			$contents  .= (($rowdiscount['acedns']!='')?$rowdiscount['acedns']: ' ');
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

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=prodqty_custclass_wise_TD.txt");
	print "$datacontents"; 	
?>
