<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	
	$start_date = $_GET['start_date'];
	$end_date = $_GET['end_date'];
	$customer_code=$_GET['customer_code'];
	$date_condition_allocation = " SUBSTRING(CPA.download_time,1,10) BETWEEN '".$start_date."' AND '".$end_date."'";

	$count = 1;
	
	if($customer_code=='all')
	{
		$customer_code_condition="";
	}
	else
	{
		$customer_code_condition=" AND CPA.customer_code='".$customer_code."'";
	}

$sql_get_allocation_details = "SELECT DATE_FORMAT(SUBSTRING(CPA.allocation_id,-14,8),'%d-%m-%Y') AS allocation_date,CPA.prod_code,CPA.customer_code,
							SUM(CPA.qty) AS allocate_qty,SUM(RD.requisition_qty) AS requisition_qty,COUNT(IMEI) AS billing_qty,DATE_FORMAT(SUBSTRING(RD.requisition_id,-14,8),'%d-%m-%Y') AS requisition_date,DATE_FORMAT(CPB.invoice_date,'%d-%m-%Y') AS billing_date
						FROM customer_product_allocation CPA LEFT JOIN requisition_details RD ON CPA.customer_code=REPLACE(SUBSTRING(RD.allocation_id,3,9),'#','/') AND CPA.prod_code=RD.prod_code AND 
						CPA.allocation_id=RD.allocation_id
						LEFT JOIN customer_product_billing CPB ON REPLACE(SUBSTRING(RD.allocation_id,3,9),'#','/') =CPB.customer_code AND RD.prod_code=CPB.prod_code AND RD.requisition_id=CPB.requisition_id  WHERE
						 ".$date_condition_allocation.$customer_code_condition." AND CPA.qty > 0 GROUP BY DATE_FORMAT(SUBSTRING(CPA.allocation_id,-14,8),'%Y-%m-%d'),CPA.customer_code,CPA.prod_code
						ORDER BY DATE_FORMAT(SUBSTRING(CPA.allocation_id,-14,8),'%Y-%m-%d') DESC";

$res_get_allocation_details = mysqli_query($link,$sql_get_allocation_details);
$total_rows = mysqli_fetch_assoc($res_get_allocation_details);
if($total_rows>0){
	echo "<table width='100%' border='1' style='border-collapse:collapse;' class='border' cellpadding='6px'>";
	echo "<tr class='TDHEAD'><td colspan='11' align='center'>Stock Balance Report</td></tr>";
	echo "<tr class='TDHEAD_SUB' align=\"center\">
			<td>SI</td>
			<td>Allocation Date</td>
			<td>Party Name</td>
			<td>Product</td>
			<td>Allocation Qty</td>
			<td>Requisition Date</td>
			<td>Requisition Qty</td>
			<td>Billing Date</td>
			<td>Billing Qty</td>
			<td>Balance (Allocation - Requisition)</td>
			<td>Balance Yet to be Billed</td>
		  </tr>";
	
	while($row_get_allocation_details = mysqli_fetch_assoc($res_get_allocation_details)){
		$allocation_date = $row_get_allocation_details['allocation_date'];
		$customer_code = $row_get_allocation_details['customer_code'];
		$prod_code = $row_get_allocation_details['prod_code'];
		$allocate_qty = $row_get_allocation_details['allocate_qty'];
		$requisition_qty = $row_get_allocation_details['requisition_qty'];
		$billing_qty = $row_get_allocation_details['billing_qty'];
		$requisition_date = $row_get_allocation_details['requisition_date'];
		$billing_date = $row_get_allocation_details['billing_date'];
		
		$sqlproddesc="SELECT prod_desc FROM product_master WHERE prod_code='".$prod_code."'";
		$rsproddesc=mysqli_query($link,$sqlproddesc);	
		$rowproddesc=mysqli_fetch_assoc($rsproddesc);
		$prod_desc=$rowproddesc['prod_desc'];
		
		$sqlcustomer="SELECT customer_name FROM customer_master WHERE customer_code='".$customer_code."'";
		$rscustomer=mysqli_query($link,$sqlcustomer);	
		$rowcustomer=mysqli_fetch_assoc($rscustomer);
		$customer_name=$rowcustomer['customer_name'];
		
		echo "<tr>
					<td>".$count."</td>
					<td >".$allocation_date."</td>
					<td >".$customer_name."</td>
					<td >".$prod_desc."</td>
					<td align='right'>".$allocate_qty."</td>
					<td >".$requisition_date."</td>
					<td align='right'>".$requisition_qty."</td>
					<td >".$billing_date."</td>
					<td align='right'>".$billing_qty."</td>
					<td align='right'>".($allocate_qty - $requisition_qty)."</td>
					<td align='right'>".($requisition_qty - $billing_qty)."</td>
				  </tr>";
				  
		$count++;
	}
}
else{
	echo "<font color='red'><strong>No records found</strong></font>";
}
mysqli_close($link);
?>
