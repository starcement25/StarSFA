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
$sqlquery="SELECT refresh_date_time FROM pending_contract_data_refresh_log ORDER BY refresh_date_time DESC LIMIT 0,1";
$result = mysqli_query($link,$sqlquery);
$rowquery=mysqli_fetch_assoc($result);
$last_pending_contract_uploading_date=substr($rowquery['refresh_date_time'],0,10);
$cur_date=date('Y-m-d');
$datediff = strtotime($cur_date) - strtotime($last_pending_contract_uploading_date);
$datediffdays=round($datediff / (60 * 60 * 24));
$date_diff_array=array();
if($datediffdays > 0)
{
	for($i=1;$i<=$datediffdays ;$i++)
	{
		$date_array_incremental=date('Y-m-d', strtotime($last_pending_contract_uploading_date ." +$i day"));
		array_push($date_diff_array,$date_array_incremental);
	}
}
if($emp_code!='C0007'){
	$sqlquery="SELECT PCA.branch_code,PCA.product_group_code,PCA.prod_code,PCA.customer_code,PCA.broker_id,PCA.contract_qty,PCA.despatch_qty,PCA.qty_0_15,PCA.qty_16_30,
			   PCA.qty_31_45,PCA.qty_46_60,PCA.qty_greater_60,PCA.greater_60_days,(PCA.qty_0_15+PCA.qty_16_30+PCA.qty_31_45+PCA.qty_46_60+PCA.qty_greater_60) AS pending_qty 
			   FROM pending_contract_ageing PCA,customer_master CM WHERE PCA.customer_code=CM.customer_code ".$emp_hierarchy_condition."";
}
else
{
	$sqlquery="SELECT PCA.branch_code,PCA.product_group_code,PCA.prod_code,PCA.customer_code,PCA.broker_id,PCA.contract_qty,PCA.despatch_qty,PCA.qty_0_15,PCA.qty_16_30,PCA.qty_31_45,
				PCA.qty_46_60,PCA.qty_greater_60,PCA.greater_60_days,(PCA.qty_0_15+PCA.qty_16_30+PCA.qty_31_45+PCA.qty_46_60+PCA.qty_greater_60) AS pending_qty
			   FROM pending_contract_ageing PCA,customer_master CM WHERE PCA.customer_code=CM.customer_code";
}
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);
	$contentsrowcolumn=$count.'¥'.'14';
	if($count>0){
		$date=date('Y-m-d');
		$time=date('H:i:s');
		$contentsdatetime = $date.'€'.$time."\n";
		while($rowpendingcontract = mysqli_fetch_assoc($result))
		{
			if(count($date_diff_array) >0)
			{
				foreach($date_diff_array as $date_diff_val)
				{
				$sqlcustomer_booking_qty="SELECT sum(SD.convert_qty_two) as mt_booked FROM sauda_details SD,sauda_header SH 
										WHERE SD.sauda_no=SH.sauda_no AND SH.customer_code='".$rowpendingcontract['customer_code']."' AND 
										SD.sku_code='".$rowpendingcontract['prod_code']."' 
										AND DATE_FORMAT('%Y-%m-%d',SUBSTRING(SD.sauda_no,-14,8))='".$date_diff_val."'";
				$rscustomer_booking_qty=mysqli_query($link,$sqlcustomer_booking_qty);
				$rowcustomer_booking_qty=mysqli_fetch_assoc($rscustomer_booking_qty);						
				${pending_qty.$rowpendingcontract['customer_code']}=${pending_qty.$rowpendingcontract['customer_code']}+$rowcustomer_booking_qty['mt_booked'];						
				}
			}
			if(${pending_qty.$rowpendingcontract['customer_code']}=='')
			{
				${pending_qty.$rowpendingcontract['customer_code']}=0;
			}
			${final_pending_qty.$rowpendingcontract['customer_code']}=$rowpendingcontract['pending_qty']+${pending_qty.$rowpendingcontract['customer_code']};
			$contents   = (($rowpendingcontract['branch_code']!='')?$rowpendingcontract['branch_code']: ' ')."^";
			$contents  .= (($rowpendingcontract['product_group_code']!='')?$rowpendingcontract['product_group_code']: ' ')."^";
			$contents  .= (($rowpendingcontract['prod_code']!='')?$rowpendingcontract['prod_code']: ' ')."^";
			$contents  .= (($rowpendingcontract['customer_code']!='')?$rowpendingcontract['customer_code']: ' ')."^";
			$contents  .= (($rowpendingcontract['broker_id']!='')?$rowpendingcontract['broker_id']: ' ')."^";
			$contents  .= (($rowpendingcontract['qty_0_15']!='')?$rowpendingcontract['qty_0_15']: ' ')."^";
			$contents  .= (($rowpendingcontract['qty_16_30']!='')?$rowpendingcontract['qty_16_30']: ' ')."^";
			$contents  .= (($rowpendingcontract['qty_31_45']!='')?$rowpendingcontract['qty_31_45']: ' ')."^";
			$contents  .= (($rowpendingcontract['qty_46_60']!='')?$rowpendingcontract['qty_46_60']: ' ')."^";
			$contents  .= (($rowpendingcontract['qty_greater_60']!='')?$rowpendingcontract['qty_greater_60']: ' ')."^";
			$contents  .= (($rowpendingcontract['greater_60_days']!='')?$rowpendingcontract['greater_60_days']: ' ')."^";
			$contents  .= (($rowpendingcontract['contract_qty']!='')?$rowpendingcontract['contract_qty']: ' ')."^";
			$contents  .= (($rowpendingcontract['despatch_qty']!='')?$rowpendingcontract['despatch_qty']: ' ')."^";
			$contents  .= ((${final_pending_qty.$rowpendingcontract['customer_code']}!='')?${final_pending_qty.$rowpendingcontract['customer_code']}: ' ');
			//$contents .= '0.0000000000000000000000001'."^";

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
	mysqli_close($link);		
?>
