<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];
if(employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	$emp_hierarchy_condition=' AND CRR.emp_code IN('.$employee_hierarchy.')';
	//$employee_hierarchy_array=explode(",",$employee_hierarchy);
}
else
{
	$emp_hierarchy_condition=" AND CRR.emp_code='".$emp_code."'";
}
$own_customer_array=array();

$sqlcustomercodelist="SELECT customer_code FROM customer_route_emp_relation CRR WHERE 1 ".$emp_hierarchy_condition;
$rscustomercodelist=mysqli_query($link,$sqlcustomercodelist);
while($rowcustomercodelist=mysqli_fetch_assoc($rscustomercodelist))
{
	array_push($own_customer_array,$rowcustomercodelist['customer_code']);
}

$sqlsaledetails="SELECT CM.customer_code,CM.customer_name,CM.phone_no,CM.rds_tag,
					SUM(CASE WHEN CPB.stock_out_date!='0000-00-00' THEN 1 ELSE 0 END) AS stock_out_qty,CPB.prod_code,PM.prod_desc
					FROM
				 	customer_product_billing CPB INNER JOIN customer_master CM INNER JOIN customer_route_emp_relation CRR INNER JOIN product_master PM
				 	ON CPB.stock_out_customer_code=CRR.customer_code AND CRR.customer_code=CM.customer_code AND CPB.prod_code=PM.prod_code
					GROUP BY CM.customer_code,CPB.prod_code ORDER BY CM.customer_name ASC";
$rs=mysqli_query($link,$sqlsaledetails) or die(mysqli_error()." Error in main: ".$sql);
$count=mysqli_num_rows($rs);

$date=gmdate('d',strtotime('+330 minute'));
$month=gmdate('m',strtotime('+330 minute'));
$year=gmdate('Y',strtotime('+330 minute'));

$hour=gmdate('H',strtotime('+330 minute'));
$minute=gmdate('i',strtotime('+330 minute'));
$second=gmdate('s',strtotime('+330 minute'));
$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";

	if($count>0){
		$concatenatestringarray=array();
		$datacount=0;
		while($rec = mysqli_fetch_assoc($rs))
		{
			$customer_code=$rec['customer_code'];
			$customer_name=$rec['customer_name'];
			$prod_code=$rec['prod_code'];
			$prod_desc=$rec['prod_desc'];
			$phone_no=$rec['phone_no'];
			$stock_out_qty=$rec['stock_out_qty'];
			$rds_tag=$rec['rds_tag'];
			
			if($rds_tag=='')
			{
				if(in_array($customer_code,$own_customer_array))
				{
					$sqlselbilling="SELECT COUNT(IMEI) as billed_qty,SUM(CASE WHEN stock_out_date='0000-00-00' AND activation_date!='0000-00-00 00:00:00' 
							THEN 1 ELSE 0 END) AS stock_out_qty_unregistered FROM customer_product_billing  
									WHERE customer_code='".$customer_code."' AND prod_code='".$prod_code."'";
					$rsselbilling=mysqli_query($link,$sqlselbilling);
					$rowselbilling=mysqli_fetch_assoc($rsselbilling);
					$billed_qty=$rowselbilling['billed_qty'];
					$stock_out_qty_unregistered=$rowselbilling['stock_out_qty_unregistered'];

					$closing_stock=$billed_qty-($stock_out_qty+$stock_out_qty_unregistered);
					if($closing_stock==0)
					{
					  $concatenatestring= $customer_code.'-'.$prod_code;
					  if(!in_array($concatenatestring,$concatenatestringarray))
					  {
						   $contents  = (($customer_code!='')?$customer_code: ' ')."^";
						   $contents  .= (($customer_name!='')?$customer_name: ' ')."^";
						   $contents  .= (($prod_code!='')?$prod_code: ' ')."^";
						   $contents  .= (($phone_no!='')?$phone_no: ' ')."^";
						   $contents  .= (($prod_desc!='')?$prod_desc: ' ');
						   $linecontents  .= $contents."\n";
						   array_push($concatenatestringarray,$concatenatestring);
						   $datacount++;
					  }
					}
				}
			}
			else
			{
				$sqlselbilling="SELECT COUNT(IMEI) as consolidated_billed_qty,SUM(CASE WHEN stock_out_date='0000-00-00' 
							AND activation_date!='0000-00-00 00:00:00' THEN 1 ELSE 0 END) AS consolidated_stock_out_qty_unregistered 
							FROM customer_product_billing  
							WHERE customer_code IN(SELECT customer_code FROM customer_master WHERE rds_tag='".$rds_tag."' AND acedns='Y') 
							AND prod_code='".$prod_code."'";
				$rsselbilling=mysqli_query($link,$sqlselbilling);
				$rowselbilling=mysqli_fetch_assoc($rsselbilling);
				$consolidated_billed_qty=$rowselbilling['consolidated_billed_qty'];
				$consolidated_stock_out_qty_unregistered=$rowselbilling['consolidated_stock_out_qty_unregistered'];
				$sqlstockout="SELECT SUM(CASE WHEN stock_out_date!='0000-00-00' THEN 1 ELSE 0 END) AS consolidated_stock_out_qty  FROM 
							customer_product_billing  
							WHERE stock_out_customer_code IN(SELECT customer_code FROM customer_master WHERE rds_tag='".$rds_tag."' AND acedns='Y') 
							AND prod_code='".$prod_code."'";	
				$rsstockout=mysqli_query($link,$sqlstockout);
				$rowstockout=mysqli_fetch_assoc($rsstockout);
				$consolidated_stock_out_qty=$rowstockout['consolidated_stock_out_qty'];
				$closing_stock=$consolidated_billed_qty-($consolidated_stock_out_qty+$consolidated_stock_out_qty_unregistered);
				
				if($closing_stock==0)
				{
					$sqlselbranchoutlet="SELECT CM.customer_code,CM.customer_name,CM.phone_no FROM customer_master CM,customer_route_emp_relation CRR 
					WHERE CRR.customer_code=CM.customer_code AND CM.rds_tag='".$rds_tag."' AND CM.acedns='Y'";
					$rsselbranchoutlet=mysqli_query($link,$sqlselbranchoutlet);
					while($rowselbranchoutlet=mysqli_fetch_assoc($rsselbranchoutlet))
					{
					   $concatenatestring= $rowselbranchoutlet['customer_code'].'-'.$prod_code;
					  if(!in_array($concatenatestring,$concatenatestringarray))
					  {
					   if(in_array($rowselbranchoutlet['customer_code'],$own_customer_array))
						{
						   $contents  = (($rowselbranchoutlet['customer_code']!='')?$rowselbranchoutlet['customer_code']: ' ')."^";
						   $contents  .= (($rowselbranchoutlet['customer_name']!='')?$rowselbranchoutlet['customer_name']: ' ')."^";
						   $contents  .= (($prod_code!='')?$prod_code: ' ')."^";
						   $contents  .= (($rowselbranchoutlet['phone_no']!='')?$rowselbranchoutlet['phone_no']: ' ')."^";
						   $contents  .= (($prod_desc!='')?$prod_desc: ' ');
						   $linecontents  .= $contents."\n";
						   array_push($concatenatestringarray,$concatenatestring);
						   $datacount++;
						}
					  }
					}
				}
			}
			
		}
		$contentsrowcolumn=$datacount.'¥'.'5';
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
	//$datacontents = '0'.'¥'.'0';
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://salesmpower.acedns.in/zerooutlet-6.0.0.php?emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=Zero_outlet.txt");
	print "$datacontents"; 		
?>
