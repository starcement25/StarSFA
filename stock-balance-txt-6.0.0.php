<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");
$emp_code=$_REQUEST['emp_code'];
$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);
$incremental_download=$_REQUEST['incremental_download'];
$data_download_time=$_REQUEST['data_download_time'];
$data_download_time=str_replace('€',' ',$data_download_time);
if(employeewise_hierarchy=='yes'){
	$employee_hierarchy=return_employee_hierarchy($emp_code);
	//$emp_hierarchy_condition='emp_code IN('.$employee_hierarchy.')';
}
if($incremental_download=='no')
{
	$login_condition=" ";
}
else
{
	$login_condition=" AND UNIX_TIMESTAMP(SBD.download_time) > UNIX_TIMESTAMP('".$last_update_time."') ";
}
/*$sqlstockbalancedetails = "SELECT SBD.sl_no,SBD.customer_code,SBD.prod_code,SBD.allocation_id,SBD.allocation_date,SBD.allocation_qty,SBD.requisition_id,
 				SBD.requisition_date,SBD.requisition_qty,SBD.billed_qty,SBD.active_flag,SBD.stock_out_qty  FROM customer_route_emp_relation CRR,
				employee_master EM,stock_balance_details SBD WHERE  
				CRR.emp_code=EM.emp_code AND SBD.customer_code=CRR.customer_code AND CRR.emp_code 
				IN(SELECT emp_code FROM employee_master WHERE acedns='Y' AND FIND_IN_SET( '".$emp_code."',reporting_to) UNION SELECT emp_code FROM employee_master WHERE acedns='Y' AND emp_code='".$emp_code."') ".$login_condition;*/
/*$sqlempcust="SELECT CM.customer_code,CM.retailer_app,CM.cust_type,CM.rds_tag FROM customer_route_emp_relation CRR,customer_master CM
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
if($retailer_app=='yes' && $rds_tag=='' && $cust_type=='D' && $total_leaf > 0)
	{
		   $sqlstockbalancedetails = "SELECT SBD.sl_no,SBD.customer_code,SBD.prod_code,SBD.allocation_id,SBD.allocation_date,SBD.allocation_qty,
		   SBD.requisition_id,SBD.requisition_date,SBD.requisition_qty,SBD.billed_qty,SBD.active_flag,SBD.stock_out_qty  FROM stock_balance_details SBD 
		   	WHERE  SBD.customer_code IN(SELECT customer_code FROM customer_master WHERE rds_tag='".$customer_code."' AND acedns='Y' AND cust_type='R') ".$login_condition;	
	}
	else{*/				
   $sqlstockbalancedetails = "SELECT SBD.sl_no,SBD.customer_code,SBD.prod_code,SBD.allocation_id,SBD.allocation_date,SBD.allocation_qty,SBD.requisition_id,
 				SBD.requisition_date,SBD.requisition_qty,SBD.billed_qty,SBD.active_flag,SBD.stock_out_qty  FROM customer_route_emp_relation CRR,
				employee_master EM,stock_balance_details SBD WHERE  
				CRR.emp_code=EM.emp_code AND SBD.customer_code=CRR.customer_code AND CRR.emp_code 
				IN(".$employee_hierarchy.") ".$login_condition;	
	//}
$resultstockbalancedetails = mysqli_query($link,$sqlstockbalancedetails);
$count=mysqli_num_rows($resultstockbalancedetails);

$date=gmdate('d',strtotime('+330 minute'));
$month=gmdate('m',strtotime('+330 minute'));
$year=gmdate('Y',strtotime('+330 minute'));

$hour=gmdate('H',strtotime('+330 minute'));
$minute=gmdate('i',strtotime('+330 minute'));
$second=gmdate('s',strtotime('+330 minute'));
$contentsdatetime =$year.'-'.$month.'-'.$date.'€'.$hour.':'.$minute.':'.$second."\n";

	if($count>0){
		$contentsrowcolumn=$count.'¥'.'12';
		while($rowstockbalancedetails = mysqli_fetch_assoc($resultstockbalancedetails))
		{
			$customer_code=$rowstockbalancedetails['customer_code'];
			$sl_no=$rowstockbalancedetails['sl_no'];
			$prod_code=$rowstockbalancedetails['prod_code'];
			$allocation_id=$rowstockbalancedetails['allocation_id'];
			$allocation_date=$rowstockbalancedetails['allocation_date'];
			$allocation_qty=$rowstockbalancedetails['allocation_qty'];
			$requisition_id=$rowstockbalancedetails['requisition_id'];
			$requisition_date=$rowstockbalancedetails['requisition_date'];
			$requisition_qty=$rowstockbalancedetails['requisition_qty'];
			$billed_qty=$rowstockbalancedetails['billed_qty'];
			$active_flag=$rowstockbalancedetails['active_flag'];
			$stock_out_qty=$rowstockbalancedetails['stock_out_qty'];
			if($billed_qty > 0)
			{
				$allocation_qty=0;
				$requisition_qty=0;
			}
			
				$contents = (($sl_no!='')?$sl_no: ' ')."^";
				$contents .= (($customer_code!='')?$customer_code: ' ')."^";
				$contents .= (($prod_code!='')?$prod_code: ' ')."^";
				$contents .= (($allocation_id!='')?$allocation_id: ' ')."^";
				$contents .= (($allocation_date!='')?$allocation_date: ' ')."^";
				$contents .= (($allocation_qty!='')?$allocation_qty: 0)."^";
				$contents .= (($requisition_id!='')?$requisition_id: ' ')."^";
				$contents .= (($requisition_date!='')?$requisition_date: ' ')."^";
				$contents .= (($requisition_qty!='')?$requisition_qty: 0)."^";
				$contents .= (($billed_qty!='')?$billed_qty: 0)."^";
				$contents .= (($stock_out_qty!='')?$stock_out_qty: 0)."^";
				$contents .= (($active_flag!='')?$active_flag: ' ');
				$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".$contentsdatetime.str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = "http://salesmpower.acedns.in/stock-balance-txt-6.0.0.php?nick_name=$nick_name&emp_code=$emp_code&last_update_time=$last_update_time";
	insertapilog($datetime,$emp_code,$url,$nick_name);

	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=stock_balance.txt");
	print "$datacontents"; 		
?>
