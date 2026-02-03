<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");

$emp_code=$_REQUEST['emp_code'];

$sqlempbranch="SELECT branch_code FROM employee_master WHERE emp_code='".$emp_code."'";
$rsempbranch=mysqli_query($link,$sqlempbranch);
$rowempbranch=mysqli_fetch_assoc($rsempbranch);
$branch_code=$rowempbranch['branch_code'];
$emp_branch_code_array=explode(',',$branch_code);
$branch_code = "'".implode("','", $emp_branch_code_array)."'";
$condition_one=' AND branch_code IN ('.$branch_code.')';

if($emp_code!='C0007'){
	$sqlquery="SELECT branch_code,prod_code,customer_code,broker_id,contract_qty,despatch_qty,pending_qty,sauda_date 
			FROM pending_contract WHERE 1 ".$condition_one." 
			ORDER BY sauda_date ASC";
}
else
{
	$sqlquery="SELECT branch_code,prod_code,customer_code,broker_id,contract_qty,despatch_qty,pending_qty,sauda_date 
			  FROM pending_contract ORDER BY sauda_date ASC";
}
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$contentsrowcolumn=$count.'¥'.'8';
	if($count>0){
		$date=date('Y-m-d');
		$time=date('H:i:s');
		$contentsdatetime = $date.'€'.$time."\n";
		while($rowpendingcontract = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowpendingcontract['branch_code']!='')?$rowpendingcontract['branch_code']: ' ')."^";
			$contents  .= (($rowpendingcontract['prod_code']!='')?$rowpendingcontract['prod_code']: ' ')."^";
			$contents  .= (($rowpendingcontract['customer_code']!='')?$rowpendingcontract['customer_code']: ' ')."^";
			$contents  .= (($rowpendingcontract['broker_id']!='')?$rowpendingcontract['broker_id']: ' ')."^";
			$contents  .= (($rowpendingcontract['contract_qty']!='')?$rowpendingcontract['contract_qty']: ' ')."^";
			$contents  .= (($rowpendingcontract['despatch_qty']!='')?$rowpendingcontract['despatch_qty']: ' ')."^";
			$contents  .= (($rowpendingcontract['pending_qty']!='')?$rowpendingcontract['pending_qty']: ' ')."^";
			$contents  .= (($rowpendingcontract['sauda_date']!='')?$rowpendingcontract['sauda_date']: ' ');
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
	header("Content-Disposition: attachment; filename=pending_contract.txt");
	print "$datacontents";		
?>
