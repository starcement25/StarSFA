<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");

$emp_code=$_REQUEST['emp_code'];

if(employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$emp_hierarchy_condition=' AND CM.emp_code IN('.$employee_hierarchy.')';
}
else
{
	$emp_hierarchy_condition=" AND CM.emp_code='".$emp_code."'";
}

if($emp_code!='C0007'){
	$sqlquery="SELECT OA.customer_code,OA.customer_name,OA.outstanding_amount,OA.amount_0_15_days,OA.amount_16_30_days,OA.amount_31_45_days,OA.amount_46_90_days,
			   OA.amount_greater_90_days FROM outstanding_ageing OA,customer_master CM WHERE OA.customer_code=CM.customer_code";
}
else
{
	$sqlquery="SELECT OA.customer_code,OA.customer_name,OA.outstanding_amount,OA.amount_0_15_days,OA.amount_16_30_days,OA.amount_31_45_days,OA.amount_46_90_days,
			   OA.amount_greater_90_days FROM outstanding_ageing OA,customer_master CM WHERE OA.customer_code=CM.customer_code ".$emp_hierarchy_condition."";
}
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$contentsrowcolumn=$count.'¥'.'8';
	if($count>0){
		$date=date('Y-m-d');
		$time=date('H:i:s');
		$contentsdatetime = $date.'€'.$time."\n";
		while($rowoutstanding = mysqli_fetch_assoc($result))
		{
			$contents   = (($rowoutstanding['customer_code']!='')?$rowoutstanding['customer_code']: ' ')."^";
			$contents  .= (($rowoutstanding['customer_name']!='')?$rowoutstanding['customer_name']: ' ')."^";
			$contents  .= (($rowoutstanding['outstanding_amount']!='')?intval($rowoutstanding['outstanding_amount']): ' ')."^";
			$contents  .= (($rowoutstanding['amount_0_15_days']!='')?intval($rowoutstanding['amount_0_15_days']): ' ')."^";
			$contents  .= (($rowoutstanding['amount_16_30_days']>0)?intval($rowoutstanding['amount_16_30_days']): 0)."^";
			$contents  .= (($rowoutstanding['amount_31_45_days']>0)?intval($rowoutstanding['amount_31_45_days']): 0)."^";
			$contents  .= (($rowoutstanding['amount_46_90_days']>0)?intval($rowoutstanding['amount_46_90_days']): 0)."^";
			$contents  .= (($rowoutstanding['amount_greater_90_days']>0)?intval($rowoutstanding['amount_greater_90_days']): 0);

			$linecontents  .= $contents."\n";	
		}
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
	/*$contents .= "</recordset>";			
	echo $contents;*/
	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=outstanding_ageing.txt");
	print "$datacontents";
	mysqli_close($link);		
?>
