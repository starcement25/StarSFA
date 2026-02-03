<?php
ini_set('MAX_EXECUTION_TIME', -1);
set_time_limit (0);
ini_set('memory_limit', '-1');
ob_start();
session_start();
require("adminUtils.php");
//if($_SESSION['admin_login']=="")  		header("location:index.php");

$brand = $_REQUEST['brand'];
$ordertype = $_REQUEST['ordertype'];
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
$startdate = str_replace("-","",$start_date);
$enddate = str_replace("-","",$end_date);
$empval=$_REQUEST['empval'];
$count = 1;

if($empval == 'admin'){
	$emp_condition_one="";
}
else{
	$emp_hierarchy_value=return_employee_hierarchy($empval);
	$emp_condition_one=" AND EM.emp_code IN(".$emp_hierarchy_value.") "; 
}

if($ordertype=='primary')
{
	$customer_condition=" AND CM.cust_type='D'";
}
else if($ordertype=='secondary')
{
	$customer_condition=" AND CM.cust_type='R'";
}
else
{
	$customer_condition="";
}
if($brand !='')
{
	$product_condition=" AND PM.product_group_code IN(".$brand.")";
}
else $product_condition="";
$sql_order_details="SELECT CM.dns_customer_code,CM.customer_code,CM.customer_name,CM.rds_tag,EM.emp_name, CM.cust_type, CM.rds_tag, PGM.product_group_name, 
						PM.dns_prod_code,PM.prod_code,PM.prod_desc,PM.UOM1,PM.UOM2,PM.conversion_factor, OD.qty, OD.mrp_code, OD.amount, LO.emp_code, 
						DATE_FORMAT( LO.date,  '%d-%m-%Y' ) AS order_date,OH.d_instruction,OH.TD,OH.freight_component,OD.UOM
						FROM location LO
						INNER JOIN order_header OH ON LO.trans_id = OH.order_no
						INNER JOIN order_details OD ON OH.order_no = OD.order_no
						INNER JOIN customer_master CM ON OH.customer_code = CM.customer_code
						INNER JOIN product_master PM ON OD.sku_code = PM.prod_code
						INNER JOIN product_group_master PGM ON PM.product_group_code = PGM.product_group_code
						INNER JOIN employee_master EM ON LO.emp_code = EM.emp_code
						WHERE (DATE_FORMAT( LO.date,  '%Y%m%d' ) BETWEEN  '".$startdate."' AND  '".$enddate."')
						AND OH.order_no LIKE  'O%'".$emp_condition_one.$customer_condition.$product_condition;
	$res_order_details = mysqli_query($link,$sql_order_details);	
			
	$total_rows = mysqli_num_rows($res_order_details);
	
	if($total_rows > 0){
		$header = "DNS Customer Code"."\t"."Customer Name"."\t"."Emp Code"."\t"."Emp Name"."\t"."Order Date"."\t".
		"Prod Group"."\t"."SKU Code"."\t"."SKU Name"."\t"."Qty1"."\t"."UOM1"."\t"."Conversion Factor"."\t"."Qty2"."\t"."UOM2"."\t"."Sale Rate"."\t"."TD(%)"."\t".
		"Order Value"."\t"."Freight"."\t"."Remarks"."\t";
		//$res_prev_order_counting_master = mysqli_query($link,$sql_prev_order_counting_master);
		while($row_order_details = mysqli_fetch_assoc($res_order_details)){
			$customer_code = $row_order_details['customer_code'];
			$dns_customer_code = $row_order_details['dns_customer_code'];
			$customer_name = $row_order_details['customer_name'];
			$emp_name = $row_order_details['emp_name'];
			$cust_type = $row_order_details['cust_type'];
			$product_code = $row_order_details['prod_code'];
			$dns_prod_code = $row_order_details['dns_prod_code'];
			$product_group_name = $row_order_details['product_group_name'];
			$sku_name = $row_order_details['prod_desc'];
			$visit_qty = $row_order_details['qty'];
			$mrp_code = $row_order_details['mrp_code'];
			$amount = $row_order_details['amount'];
			$emp_code = $row_order_details['emp_code'];
			$order_date = $row_order_details['order_date'];
			$rds_tag = $row_order_details['rds_tag'];
			$UOM1 = $row_order_details['UOM1'];
			$UOM2 = $row_order_details['UOM2'];
			$conversion_factor = $row_order_details['conversion_factor'];
			$d_instruction = $row_order_details['d_instruction'];
			$d_instruction=preg_replace('/[\r\n]+/', '',$d_instruction);
			$freight_component = $row_order_details['freight_component'];
			//$d_instruction=str_replace(",",";",$d_instruction);
			$TD = $row_order_details['TD'];
			$UOM = $row_order_details['UOM'];
			if($UOM == $UOM2)
			{
				$qty1=$visit_qty/$conversion_factor;
				$qty2=$visit_qty;
			}
			else if($UOM == $UOM1)
			{
				$qty1=$visit_qty;
				$qty2=$visit_qty*$conversion_factor;
			}
			else
			{
				$qty1=$visit_qty;
				$qty2=$visit_qty*$conversion_factor;
			}
			/*$sqlmrp="SELECT mrp from mrp WHERE mrp_code='".$mrp_code."'";
			$rsmrp=mysqli_query($link,$sqlmrp);
			$rowmrp=mysqli_fetch_assoc($rsmrp);
			$rate=$rowmrp['mrp'];*/
			$rate=$amount/$qty1;
			$amount=$amount-(($amount*$TD)/100);
			if($cust_type == 'R')
				$order_type = 'Secondary';
			else if($cust_type == 'D')
				$order_type = 'Primary';
			
			$contents.=$dns_customer_code."\t".$customer_name."\t".$emp_code."\t".$emp_name."\t".$order_date."\t".$product_group_name."\t".$dns_prod_code."\t".$sku_name."\t".$qty1."\t".$UOM1."\t".$conversion_factor."\t".$qty2."\t".$UOM2."\t".$rate."\t".$TD."\t".$amount."\t".$freight_component."\t".$d_instruction."\n";
			$count++;
		}
			$datacontents=$header."\n".$contents;
			$ordertype_file=str_replace("'","",$ordertype);
			$ordertype_file=str_replace(",","",$ordertype);
			$filename="order_transaction/SKIPPEROrder_".$ordertype_file."_".$startdate."_".$enddate;
			if (file_exists("/home/acedns/public_html/misreport/$filename.xls")){
				unlink("/home/acedns/public_html/misreport/$filename.xls");
			}
			$fp = fopen("/home/acedns/public_html/misreport/$filename.xls","wb");
			fwrite($fp,$datacontents);
			fclose($fp);
			echo $filename;
			//header("Content-type: application/octet-stream"); 
			//header("Content-Disposition: attachment; filename=Order_Download_Report.xls"); 
			//print "$datacontents";
			//$nick_name=strtoupper($_SESSION['nick_name']);
			//echo $datacontents;	
	}
	else{
		echo "0";
	}	
mysqli_close($link);
?>