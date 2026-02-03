<?php
ini_set('MAX_EXECUTION_TIME', -1);
set_time_limit (0);
ini_set('memory_limit', '-1');
ob_start();
session_start();
require("adminUtils.php");
//if($_SESSION['admin_login']=="")  		header("location:index.php");

$vertical = $_REQUEST['vertical'];
$zone = $_REQUEST['zone'];
$state = $_REQUEST['state'];
$emp_code = $_REQUEST['employee'];
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
$startdate = str_replace("-","",$start_date);
$enddate = str_replace("-","",$end_date);
$count = 1;
if($emp_code == 'all'){
	$emp_condition = '';
	$emp_condition_one="";
}
else{
	$emp_condition = " AND SUBSTRING(order_no,2,5) IN (".$emp_code.") ";
	$emp_condition_one=" AND EM.emp_code IN(".$emp_code.") "; 
}

if($vertical != ''){
	if($vertical == 'MACROMAN')
		$vertical_condition = " AND vertical_value LIKE 'M%' ";
	else if($vertical == 'all')
		$vertical_condition = "";	
	else
		//$vertical_condition = " AND vertical_value = '".$vertical."'";
		$vertical_condition = " AND FIND_IN_SET('".$vertical."',vertical_value)";
}
else{
	$vertical_condition = "";
}

	if(TD=='yes' && TD_type=='order value wise')
	{
		$TD_condition=",OH.TD";
	}
	else if(TD=='yes' && TD_type=='sku wise')
	{
		$TD_condition=",OD.TD";
	}
	else if(TD=='no')
	{
		$TD_condition="";
	}
	
	/*$sql_prev_order_counting_master = "SELECT customer_code, cust_type, product_code, visit_qty, rate, amount,  SUBSTRING(order_no,2,5) as emp_code, DATE_FORMAT(SUBSTRING(order_no,-14,8),'%d-%m-%Y') as order_date FROM prev_order_counting_master WHERE (SUBSTRING(order_no,-14,8) BETWEEN '".$startdate."' AND '".$enddate."') AND order_no LIKE 'O%' ".$emp_condition.$vertical_condition;
	//exit();
	$res_prev_order_counting_master = mysqli_query($link,$sql_prev_order_counting_master);*/
	$sql_order_details="SELECT CM.dns_customer_code,CM.customer_code,CM.customer_name,CM.rds_tag,EM.emp_name, CM.cust_type, CM.rds_tag, PGM.product_group_name, 
						PM.dns_prod_code,PM.prod_code,PM.prod_desc, OD.qty, OD.sale_rate, OD.amount, LO.emp_code, 
						DATE_FORMAT( LO.date,  '%d-%m-%Y' ) AS order_date,SUBSTRING(LO.date,12,8) AS order_time
						FROM location LO
						INNER JOIN order_header OH ON LO.trans_id = OH.order_no
						INNER JOIN order_details OD ON OH.order_no = OD.order_no
						INNER JOIN customer_master CM ON OH.customer_code = CM.customer_code
						INNER JOIN product_master PM ON OD.sku_code = PM.prod_code
						INNER JOIN product_group_master PGM ON PM.product_group_code = PGM.product_group_code
						INNER JOIN employee_master EM ON LO.emp_code = EM.emp_code
						WHERE (DATE_FORMAT( LO.date,  '%Y%m%d' ) BETWEEN  '".$startdate."' AND  '".$enddate."')
						AND OH.order_no LIKE  'O%'".$emp_condition_one;
	$res_order_details = mysqli_query($link,$sql_order_details);					
	$total_rows = mysqli_num_rows($res_order_details);
	
	if($total_rows > 0){
		$header = "DNS Customer Code"."\t"."Customer Name"."\t"."Emp Name"."\t"."Distributor Code"."\t"."Distributor Name"."\t"."Order Date"."\t"."Order Time"."\t"."Prod Group"."\t"."SKU Code"."\t"."SKU Name"."\t"."Qty"."\t"."Sale Rate"."\t"."Order Type";
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
			$rate = $row_order_details['sale_rate'];
			$amount = $row_order_details['amount'];
			$emp_code = $row_order_details['emp_code'];
			$order_date = $row_order_details['order_date'];
			$order_time = $row_order_details['order_time'];
			$rds_tag = $row_order_details['rds_tag'];
			
			if($cust_type == 'R')
				$order_type = 'Secondary';
			else if($cust_type == 'D')
				$order_type = 'Primary';
			$rds_name='';
			if(tagged_distributor_for_order=='yes' && $rds_tag!=''){
				$sql_rds_name = "SELECT customer_name,dns_customer_code FROM customer_master WHERE customer_code = '".$rds_tag."'";
				$res_rds_name = mysqli_query($link,$sql_rds_name);
				$row_rds_name = mysqli_fetch_assoc($res_rds_name);
				$rds_name = $row_rds_name['customer_name'];
				$dns_rds_code = $row_rds_name['dns_customer_code'];
			}
			$contents.=$dns_customer_code."\t".$customer_name."\t".$emp_name."\t".$dns_rds_code."\t".$rds_name."\t".$order_date."\t".$order_time."\t".$product_group_name."\t".$dns_prod_code."\t".$sku_name."\t".$visit_qty."\t".$rate."\t".$order_type."\n";
			$count++;
		}
			$datacontents=$header."\n".$contents;
			$vertical_file=str_replace("'","",$vertical);
			$vertical_file=str_replace(",","",$vertical);
			$zone_file=str_replace(",","",$zone);
			$zone_file=str_replace(",","",$zone);
			$state_file=str_replace(",","",$state);
			$state_file=str_replace(",","",$state);
			$filename="order_transaction/Order_".$vertical_file."_".$zone_file."_".$state_file."_".$startdate."_".$enddate;
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
		echo "<strong><font color=\"red\">No Records Found</font></strong>";
	}	
mysqli_close($link);
?>