<?php
ob_start();
session_start();
require("adminUtils.php");
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
$employee = $_REQUEST['employee'];
if($employee == 'all'){
	$order_condition = '';
	$payment_condition = '';
	$emp_condition = '';
}
else{
	$emp_condition = " AND SUBSTRING(POCM.order_no,2,5) IN('".$employee."') ";
}

//$startdate = str_replace("-","",$start_date);
//$enddate = str_replace("-","",$end_date);
$count = 1;
	
	$sql_order_header = "SELECT POCM.order_no,SUBSTRING(POCM.order_no,2,5) as emp_code,DATE_FORMAT(SUBSTRING(POCM.order_no,-14,8),'%d-%m-%Y') as order_date,DATE_FORMAT(SUBSTRING(POCM.delivery_date,1,10),'%d-%m-%Y') as delivery_date,
					POCM.customer_code,POCM.visit_qty,POCM.rate,POCM.amount,POCM.delivery_qty,POCM.remarks,EM.emp_name,CM.customer_name,POCM.product_code 
					FROM prev_order_counting_master POCM,employee_master EM,customer_master CM 
					WHERE SUBSTRING(POCM.order_no,2,5)=EM.emp_code AND POCM.customer_code=CM.customer_code AND POCM.status='pending' AND(SUBSTRING(POCM.delivery_date,1,10) BETWEEN '".$start_date."' AND '".$end_date."') AND POCM.order_no LIKE 'O%' 
		".$emp_condition."";
	$res_order_header = mysqli_query($link,$sql_order_header);
	$order_header_total_rows = mysqli_num_rows($res_order_header);
	if($order_header_total_rows>0){
		?>
		<table id="display_table" width="100%" class="border" border="1" style="border-collapse:collapse;">
		  <tr class="TDHEAD" align="center">
          	<td>Order Date</td>
			<td>Customer Name</td>
			<td>Emp Name</td>
			<td>SKU Name</td>
			<td>Booked Qty</td>
            <td>Sale Rate</td>
            <td>Booked Value</td>
            <td>Delivery Date</td>
            <td>Delivery Qty</td>
            <td>Delivery Value</td>
            <td>Delivery Remarks</td>
		  </tr>
		<?php
		$res_order_header = mysqli_query($link,$sql_order_header);
		while($row_order_header = mysqli_fetch_assoc($res_order_header)){
			$order_no = $row_order_header['order_no'];
			$emp_code = $row_order_header['emp_code'];
			$order_date = $row_order_header['order_date'];
			$customer_code = $row_order_header['customer_code'];
			$customer_name = $row_order_header['customer_name'];
			$emp_name= $row_order_header['emp_name'];
			$order_date=$row_order_header['order_date'];
			$rate=$row_order_header['rate'];
			$amount=$row_order_header['amount'];
			$visit_qty=$row_order_header['visit_qty'];
			$delivery_qty=$row_order_header['delivery_qty'];
			$delivery_date=$row_order_header['delivery_date'];
			$delivery_amount=$delivery_qty*$rate;
			$sku_code=$row_order_header['product_code'];
			$remarks=$row_order_header['remarks'];
			
			$sql_sku_name = "SELECT dns_prod_code, product_group_code, prod_desc,alias FROM product_master WHERE prod_code = '".$sku_code."'";
			$res_sku_name = mysqli_query($link,$sql_sku_name);
			$row_sku_name = mysqli_fetch_assoc($res_sku_name);
			$sku_name = $row_sku_name['prod_desc'];
			$alias = $row_sku_name['alias'];
			if($alias!='')
			{
				$sku_name=$alias;
			}
			$dns_prod_code = $row_sku_name['dns_prod_code'];
				
				echo "	<td>".$order_date."</td>
						<td>".$customer_name."</td>
						<td>".$emp_name."</td>
						<td>".$sku_name."</td>
						<td align=\"right\">".$visit_qty."</td>
						<td align=\"right\">".$rate."</td>
						<td align=\"right\">".$amount."</td>
						<td align=\"right\">".$delivery_date."</td>
						<td align=\"right\">".$delivery_qty."</td>
						<td align=\"right\">".$delivery_amount."</td>
						<td>".$remarks."</td>
					  </tr>";
				$count++;
		}
		?>
    </table><br />
    <table width="100%">
    <tr>
    	<td align="right">
        <div style="width:70%;" align="right"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
            <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
        </div>
        </td>
    </tr>
    </table>
		<?php
	}
	else{
		echo "<strong><font color=\"red\">No Records Found</font></strong>";
	}

mysqli_close($link);
?>