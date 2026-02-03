<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];

/*$sqlempfunctionality="SELECT functionality,functionality_rel_val FROM employee_master WHERE emp_code='".$emp_code."'";
$rsempfunctionality=mysqli_query($link,$sqlempfunctionality);
$rowempfunctionality=mysqli_fetch_assoc($rsempfunctionality);
$functionality_rel_val=$rowempfunctionality['functionality_rel_val'];*/

$sqlempcust="SELECT CM.customer_code,CM.retailer_app,CM.cust_type,CM.rds_tag FROM customer_route_emp_relation CRR,customer_master CM
				WHERE CM.customer_code=CRR.customer_code AND CRR.emp_code='".$emp_code."' AND CRR.acedns='Y'";
$rsempcust=mysqli_query($link,$sqlempcust); 
$rowempcust=mysqli_fetch_assoc($rsempcust);
$customer_code=$rowempcust['customer_code'];
$retailer_app=$rowempcust['retailer_app'];
$cust_type=$rowempcust['cust_type'];
$rds_tag=$rowempcust['rds_tag'];
$sqlcustomerleafcount="SELECT COUNT(customer_code) AS total_leaf FROM customer_master WHERE cust_type='R' AND rds_tag='".$customer_code."'";
$rscustomerleafcount=mysqli_query($link,$sqlcustomerleafcount);
$total_leaf=mysqli_num_rows($rscustomerleafcount);

$curdate=date('Y-m-d');
if($rds_tag==''){
	if($retailer_app=='yes' && $rds_tag=='' && $cust_type=='D' && $total_leaf > 0)
	{
	$sqlquery="SELECT  allocation_id_consolidated As allocation_id,prod_code,SUM(qty) AS qty,from_date,to_date,acedns	FROM customer_product_allocation WHERE customer_code IN(SELECT customer_code FROM customer_master WHERE rds_tag='".$customer_code."' AND acedns='Y' AND cust_type='R') AND allocation_id_consolidated NOT IN(SELECT DISTINCT allocation_id FROM requisition_details) AND acedns='Y' GROUP BY prod_code";
	$result = mysqli_query($link,$sqlquery);
	$count=mysqli_num_rows($result);
	}
	else if($retailer_app=='yes' && $rds_tag=='' && $cust_type=='R')
	{
	$sqlquery="SELECT * FROM customer_product_allocation WHERE customer_code='".$customer_code."' AND allocation_id NOT IN(SELECT DISTINCT allocation_id FROM requisition_details) AND acedns='Y'";
	$result = mysqli_query($link,$sqlquery);
	$count=mysqli_num_rows($result);
	}
	
	
	$date=gmdate('d',strtotime('+330 minute'));
	$month=gmdate('m',strtotime('+330 minute'));
	$year=gmdate('Y',strtotime('+330 minute'));
	
	$hour=gmdate('H',strtotime('+330 minute'));
	$minute=gmdate('i',strtotime('+330 minute'));
	$second=gmdate('s',strtotime('+330 minute'));
	$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
	
	if($count>0){
		$contentsrowcolumn=$count.'¥'.'7';
		while($rowstockallocation = mysqli_fetch_assoc($result))
		{
			$allocation_id=$rowstockallocation['allocation_id'];
			if($cust_type=='D')
			{
				$customer_code_allocation=$customer_code;
			}
			else $customer_code_allocation=$rowstockallocation['customer_code'];
			$prod_code=$rowstockallocation['prod_code'];
			$qty=$rowstockallocation['qty'];
			$from_date=$rowstockallocation['from_date'];
			$to_date=$rowstockallocation['to_date'];
			$acedns=$rowstockallocation['acedns'];
			
				$contents  = (($allocation_id!='')?$allocation_id: ' ')."^";
				$contents  .= (($customer_code_allocation!='')?$customer_code_allocation: ' ')."^";
				$contents  .= (($prod_code!='')?$prod_code: ' ')."^";
				$contents  .= (($qty!='')?$qty: 0)."^";
				$contents  .= (($from_date!='')?$from_date: ' ')."^";
				$contents  .= (($to_date!='')?$to_date: ' ')."^";
				$contents  .= (($acedns!='')?$acedns: ' ');
				$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
}
else
{
	$datacontents = '0'.'¥'.'0';
}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://salesmpower.acedns.in/stock-allocation-txt-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=stock_allocation.txt");
	print "$datacontents"; 		
?>
