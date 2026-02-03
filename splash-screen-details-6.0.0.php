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
$date=gmdate('d',strtotime('+330 minute'));
$month=gmdate('m',strtotime('+330 minute'));
$year=gmdate('Y',strtotime('+330 minute'));
$hour=gmdate('H',strtotime('+330 minute'));
$minute=gmdate('i',strtotime('+330 minute'));
$second=gmdate('s',strtotime('+330 minute'));
$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";
$current_date_time =$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
$current_date =$year.'-'.$month.'-'.$date;
//For attendance Today
	$sqlattendance  = "SELECT emp_code,trans_id,date FROM location WHERE 
				(trans_id LIKE 'A%'  OR trans_id LIKE 'WO%' OR trans_id LIKE 'LR%') AND emp_code IN(".$employee_hierarchy.") AND 
				SUBSTRING(date,1,10)='".$current_date."'";
	$resultattendance = mysqli_query($link,$sqlattendance);
	$countattendance=mysqli_num_rows($resultattendance);
	$attendanc_today=$countattendance;
//For sale MTD
	$sqlstockout="SELECT SUM(CASE WHEN IMEI!='' AND stock_out_date!='0000-00-00' THEN 1 ELSE 0 END) AS total_stock_out_qty  FROM customer_product_billing  
				 WHERE stock_out_customer_code IN(SELECT customer_code FROM customer_route_emp_relation WHERE emp_code IN(".$employee_hierarchy.") AND acedns='Y' ) AND SUBSTRING(stock_out_date,1,7)=SUBSTRING('".$current_date."',1,7)";
	$rsstockout=mysqli_query($link,$sqlstockout);
	$rowstockout=mysqli_fetch_assoc($rsstockout);
	$total_sale_MTD=$rowstockout['total_stock_out_qty'];
//For Unregistered MTD
	$sqlunregisteredsaleMTD="SELECT SUM(CASE WHEN stock_out_date='0000-00-00' AND activation_date!='0000-00-00 00:00:00' 
					THEN 1 ELSE 0 END) AS stock_out_qty_unregistered_MTD
					 FROM customer_product_billing  
					WHERE customer_code IN(SELECT customer_code FROM customer_route_emp_relation WHERE emp_code IN(".$employee_hierarchy.") AND acedns='Y') 
					AND SUBSTRING(activation_date,1,7)=SUBSTRING('".$current_date."',1,7)";
	$rsunregisteredsaleMTD=mysqli_query($link,$sqlunregisteredsaleMTD);
	$rowunregisteredsaleMTD=mysqli_fetch_assoc($rsunregisteredsaleMTD);
	$stock_out_qty_unregistered_MTD=$rowunregisteredsaleMTD['stock_out_qty_unregistered_MTD'];
	$total_sale_MTD=$total_sale_MTD+$stock_out_qty_unregistered_MTD;
//For sale FTD
	$sqlstockoutFTD="SELECT SUM(CASE WHEN IMEI!='' AND stock_out_date!='0000-00-00' THEN 1 ELSE 0 END) AS total_stock_out_qty  FROM customer_product_billing  
				 WHERE stock_out_customer_code IN(SELECT customer_code FROM customer_route_emp_relation WHERE emp_code IN(".$employee_hierarchy.") AND acedns='Y') AND SUBSTRING(stock_out_date,1,10)='".$current_date."'";
	$rsstockoutFTD=mysqli_query($link,$sqlstockoutFTD);
	$rowstockoutFTD=mysqli_fetch_assoc($rsstockoutFTD);
	$total_sale_FTD=$rowstockoutFTD['total_stock_out_qty'];
//For Unregistered FTD
	$sqlunregisteredsaleFTD="SELECT SUM(CASE WHEN stock_out_date='0000-00-00' AND activation_date!='0000-00-00 00:00:00' 
					THEN 1 ELSE 0 END) AS stock_out_qty_unregistered_FTD
					 FROM customer_product_billing  
					WHERE customer_code IN(SELECT customer_code FROM customer_route_emp_relation WHERE emp_code IN(".$employee_hierarchy.") AND acedns='Y') 
					AND SUBSTRING(activation_date,1,10)='".$current_date."'";
	$rsunregisteredsaleFTD=mysqli_query($link,$sqlunregisteredsaleFTD);
	$rowunregisteredsaleFTD=mysqli_fetch_assoc($rsunregisteredsaleFTD);
	$stock_out_qty_unregistered_FTD=$rowunregisteredsaleFTD['stock_out_qty_unregistered_FTD'];
	$total_sale_FTD=$total_sale_FTD+$stock_out_qty_unregistered_FTD;
//For closing stock
	$sqlselbilling="SELECT COUNT(IMEI) as total_billed_qty FROM customer_product_billing  
					WHERE customer_code IN(SELECT customer_code FROM customer_route_emp_relation WHERE emp_code IN(".$employee_hierarchy.") AND acedns='Y')";
	$rsselbilling=mysqli_query($link,$sqlselbilling);
	$rowselbilling=mysqli_fetch_assoc($rsselbilling);
	$total_billed_qty=$rowselbilling['total_billed_qty'];
	$sqloverallstockout="SELECT SUM(CASE WHEN IMEI!='' AND stock_out_date!='0000-00-00' THEN 1 ELSE 0 END) AS overall_stock_out_qty  
						FROM customer_product_billing  
				 WHERE stock_out_customer_code IN(SELECT customer_code FROM customer_route_emp_relation WHERE emp_code IN(".$employee_hierarchy.") AND acedns='Y')";
	$rsoverallstockout=mysqli_query($link,$sqloverallstockout);
	$rowoverallstockout=mysqli_fetch_assoc($rsoverallstockout);
	$overall_stock_out_qty=$rowoverallstockout['overall_stock_out_qty'];
	
	$sqlunregisteredsale="SELECT SUM(CASE WHEN stock_out_date='0000-00-00' AND activation_date!='0000-00-00 00:00:00' 
					THEN 1 ELSE 0 END) AS stock_out_qty_unregistered
					 FROM customer_product_billing  
					WHERE customer_code IN(SELECT customer_code FROM customer_route_emp_relation WHERE emp_code IN(".$employee_hierarchy.") AND acedns='Y')";
	$rsunregisteredsale=mysqli_query($link,$sqlunregisteredsale);
	$rowunregisteredsale=mysqli_fetch_assoc($rsunregisteredsale);
	$stock_out_qty_unregistered_overall=$rowunregisteredsale['stock_out_qty_unregistered'];
    $closing_stock_overall=$total_billed_qty-($overall_stock_out_qty+$stock_out_qty_unregistered_overall);
//For zero outlet	
$own_customer_array=array();
$sqlcustomercodelist="SELECT customer_code FROM customer_route_emp_relation CRR WHERE 1 ".$emp_hierarchy_condition;
$rscustomercodelist=mysqli_query($link,$sqlcustomercodelist);
while($rowcustomercodelist=mysqli_fetch_assoc($rscustomercodelist))
{
	array_push($own_customer_array,$rowcustomercodelist['customer_code']);
}
$sqlsaledetails="SELECT CM.customer_code,CM.customer_name,CM.phone_no,CM.rds_tag,
					SUM(CASE WHEN CPB.stock_out_date!='0000-00-00' THEN 1 ELSE 0 END) AS stock_out_qty,CPB.prod_code 
					FROM
				 	customer_product_billing CPB INNER JOIN customer_master CM ON CPB.stock_out_customer_code=CM.customer_code 
					GROUP BY CM.customer_code,CPB.prod_code ORDER BY CM.customer_name ASC";
$rs=mysqli_query($link,$sqlsaledetails) or die(mysqli_error()." Error in main: ".$sql);
$count=mysqli_num_rows($rs);
	if($count>0){
		$concatenatestringarray=array();
		$zerooutletarray=array();
		$zerooutletnamearray=array();
		$datacount=0;
		while($rec = mysqli_fetch_assoc($rs))
		{
			$customer_code=$rec['customer_code'];
			$customer_name=$rec['customer_name'];
			$prod_code=$rec['prod_code'];
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
					  if(!in_array($customer_code,$zerooutletarray))
					  {
						   array_push($zerooutletarray,$customer_code);
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
					$sqlselbranchoutlet="SELECT customer_code FROM customer_master WHERE rds_tag='".$rds_tag."' AND acedns='Y'";
					$rsselbranchoutlet=mysqli_query($link,$sqlselbranchoutlet);
					while($rowselbranchoutlet=mysqli_fetch_assoc($rsselbranchoutlet))
					{
					  if(!in_array($rowselbranchoutlet['customer_code'],$zerooutletarray))
					  {
					   if(in_array($rowselbranchoutlet['customer_code'],$own_customer_array))
						{
						   array_push($zerooutletarray,$rowselbranchoutlet['customer_code']);
						}
					  }
					}
				}
			}
		}
		$total_zero_outlet=count($zerooutletarray);
		//$total_zero_outlet='';
		/*echo '<pre>';
		print_r($zerooutletnamearray);
		echo '</pre>';*/
	}
	else
	{
		$total_zero_outlet=='0';
	}
	$contentsrowcolumn='1'.'¥'.'5';
	$contents  = (($attendanc_today!='')?$attendanc_today: 0)."^";
	$contents  .= (($total_sale_MTD!='')?$total_sale_MTD: ' ')."^";
	$contents  .= (($closing_stock_overall!='')?$closing_stock_overall: ' ')."^";
	$contents  .= (($total_zero_outlet!='')?$total_zero_outlet: 0)."^";
	$contents  .= (($total_sale_FTD!='')?$total_sale_FTD: 0);
	$linecontents  .= $contents."\n";

	$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://salesmpower.acedns.in/splash-screen-details-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=splash_screen_details.txt");
	print "$datacontents"; 		
?>
