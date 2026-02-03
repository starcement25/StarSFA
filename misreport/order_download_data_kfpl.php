<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");

$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];

$startdate = str_replace("-","",$start_date);
$enddate = str_replace("-","",$end_date);
$count = 1;

if(strtoupper($_SESSION['nick_name']) != 'MAITHAN'){
	$sql_order_header = "SELECT order_no, SUBSTRING(order_no,2,5) as emp_code, DATE_FORMAT(SUBSTRING(order_no,-14,8),'%d-%m-%Y') as order_date, customer_code FROM order_header WHERE (SUBSTRING(order_no,-14,8) BETWEEN '".$startdate."' AND '".$enddate."') AND order_no LIKE 'O%'";
	$res_order_header = mysqli_query($link,$sql_order_header);
	$order_header_total_rows = mysqli_num_rows($res_order_header);
	if($order_header_total_rows>0){
		?>
		<table width="100%" class="border" border="1" style="border-collapse:collapse;">
		  <tr class="TDHEAD" align="center">
			<td>Customer Code</td>
			<td>Customer Name</td>
            <td>Emp Code</td>
			<td>Emp Name</td>
			<td>Order Date</td>
            <td>Prod Group</td>
			<td>SKU Code</td>
			<td>SKU Name</td>
			<td>Qty</td>
            <td>Sale Rate</td>
		  </tr>
		
		<?php
		$res_order_header = mysqli_query($link,$sql_order_header);
		while($row_order_header = mysqli_fetch_assoc($res_order_header)){
			$order_no = $row_order_header['order_no'];
			$emp_code = $row_order_header['emp_code'];
			$order_date = $row_order_header['order_date'];
			$customer_code = $row_order_header['customer_code'];
			$transaction_type = $row_order_header['transaction_type'];
			
			$sql_customer_details = "SELECT dns_customer_code, customer_name FROM customer_master WHERE customer_code = '".$customer_code."'";
			$res_customer_details = mysqli_query($link,$sql_customer_details);
			$row_customer_details = mysqli_fetch_assoc($res_customer_details);
			$dns_customer_code = $row_customer_details['dns_customer_code'];
			$customer_name = $row_customer_details['customer_name'];
			
			$sql_emp_details = "SELECT dns_emp_code, emp_name FROM employee_master WHERE emp_code = '".$emp_code."'";
			$res_emp_details = mysqli_query($link,$sql_emp_details);
			$row_emp_details = mysqli_fetch_assoc($res_emp_details);
			$dns_emp_code = $row_emp_details['dns_emp_code'];
			$emp_name = $row_emp_details['emp_name'];
			
			$sql_order_details = "SELECT sku_code, qty, sale_rate, amount FROM order_details WHERE order_no = '".$order_no."'";
			$res_order_details = mysqli_query($link,$sql_order_details);
			while($row_order_details = mysqli_fetch_assoc($res_order_details)){
				$sku_code = $row_order_details['sku_code'];
				$qty = $row_order_details['qty'];
				$sale_rate = $row_order_details['sale_rate'];
				$amount = $row_order_details['amount'];
				
				/*$sql_get_uom = "SELECT UOM1 FROM product_master WHERE prod_code = '".$sku_code."'";
				$res_get_uom = mysqli_query($link,$sql_get_uom);
				$row_get_uom = mysqli_fetch_assoc($res_get_uom);
				$uom = $row_get_uom['UOM1'];*/
				
				$sql_sku_name = "SELECT dns_prod_code, product_group_code, prod_desc FROM product_master WHERE prod_code = '".$sku_code."'";
				$res_sku_name = mysqli_query($link,$sql_sku_name);
				$row_sku_name = mysqli_fetch_assoc($res_sku_name);
				$sku_name = $row_sku_name['prod_desc'];
				$dns_prod_code = $row_sku_name['dns_prod_code'];
				$product_group_code = $row_sku_name['product_group_code'];
				
				$sql_prod_group_name = "SELECT product_group_name FROM product_group_master WHERE product_group_code = '".$product_group_code."'";
				$res_prod_group_name = mysqli_query($link,$sql_prod_group_name);
				$row_prod_group_name = mysqli_fetch_assoc($res_prod_group_name);
				$prod_group_name = $row_prod_group_name['product_group_name'];
				
				echo "<tr>
						<td>".$dns_customer_code."</td>
						<td>".$customer_name."</td>
						<td>".$dns_emp_code."</td>
						<td>".$emp_name."</td>
						<td>".$order_date."</td>
						<td>".$prod_group_name."</td>
						<td>".$dns_prod_code."</td>
						<td>".$sku_name."</td>
						<td align=\"right\">".$qty."</td>
						<td align=\"right\">".$sale_rate."</td>
					  </tr>";
				$count++;
			}
			
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
}
else if(strtoupper($_SESSION['nick_name']) == 'MAITHAN'){
	
	$sql_order_header = "SELECT order_no, SUBSTRING(order_no,2,5) as emp_code, DATE_FORMAT(SUBSTRING(order_no,-14,8),'%d-%m-%Y') as order_date, customer_code, destination_code, order_type, VAT, d_instruction  FROM order_header WHERE (SUBSTRING(order_no,-14,18) BETWEEN '".$startdate."' AND '".$enddate."') AND order_no LIKE 'O%' ORDER BY DATE_FORMAT(SUBSTRING(order_no,-14,14),'%Y-%m-%d %H:%i:%s') DESC";
	$res_order_header = mysqli_query($link,$sql_order_header);
	$order_header_total_rows = mysqli_num_rows($res_order_header);
	if($order_header_total_rows>0){
		?>
		<table width="100%" class="border" border="1" style="border-collapse:collapse;">
		  <tr class="TDHEAD" align="center">
			<td>Order no</td>
			<td>Date</td>
			<td>Partyname</td>
			<td>Source</td>
			<td>Destination</td>
			<td>Business type</td>
			<td>Tax type</td>
            <td>Transporter</td>
            <td>Item name</td>
            <td>Quantity</td>
            <td>Rate</td>
            <td>Delivery due date</td>
            <td>Amount</td>
		  </tr>
		<?php
		$res_order_header = mysqli_query($link,$sql_order_header);
		while($row_order_header = mysqli_fetch_assoc($res_order_header)){

			$order_no = $row_order_header['order_no'];
			$emp_code = $row_order_header['emp_code'];
			$order_date = $row_order_header['order_date'];
			$customer_code = $row_order_header['customer_code'];
			$destination_code = $row_order_header['destination_code'];
			$order_type = $row_order_header['order_type'];
			$VAT = $row_order_header['VAT'];
			$d_instruction = $row_order_header['d_instruction'];
			$tax_type='';
			$transporter='';
			
			if($VAT == 0)
				$VAT = 'NULL';
			
			$sql_customer_details = "SELECT dns_customer_code, customer_name FROM customer_master WHERE customer_code = '".$customer_code."'";
			$res_customer_details = mysqli_query($link,$sql_customer_details);
			$row_customer_details = mysqli_fetch_assoc($res_customer_details);
			$dns_customer_code = $row_customer_details['dns_customer_code'];
			$customer_name = $row_customer_details['customer_name'];
			
			$sql_emp_details = "SELECT emp_name FROM employee_master WHERE emp_code = '".$emp_code."'";
			$res_emp_details = mysqli_query($link,$sql_emp_details);
			$row_emp_details = mysqli_fetch_assoc($res_emp_details);
			$emp_name = $row_emp_details['emp_name'];
			
			$sql_destination = "SELECT destination_name FROM destination_master WHERE destination_code='".$destination_code."'";
			$res_destination = mysqli_query($link,$sql_destination);
			$row_destination = mysqli_fetch_assoc($res_destination);
			$destination_name = $row_destination['destination_name'];
			
			$sql_order_details = "SELECT sku_code, qty, sale_rate, amount FROM order_details WHERE order_no = '".$order_no."'";
			$res_order_details = mysqli_query($link,$sql_order_details);
			while($row_order_details = mysqli_fetch_assoc($res_order_details)){
				$sku_code = $row_order_details['sku_code'];
				$qty = $row_order_details['qty'];
				$sale_rate = $row_order_details['sale_rate'];
				$amount = $row_order_details['amount'];
				
				$sql_sku_name = "SELECT prod_desc FROM product_master WHERE prod_code = '".$sku_code."'";
				$res_sku_name = mysqli_query($link,$sql_sku_name);
				$row_sku_name = mysqli_fetch_assoc($res_sku_name);
				$sku_name = $row_sku_name['prod_desc'];
								

				echo "<tr>
						<td>".$order_no."</td>
						<td>".$order_date."</td>
						<td>".$customer_name."</td>
						<td>".$emp_name."</td>
						<td>".$destination_name."</td>
						<td>".$order_type."</td>
						<td >".$tax_type."</td>
						<td>".$transporter."</td>
						<td>".$sku_name."</td>
						<td align=\"right\">".$qty."</td>
						<td align=\"right\">".$sale_rate."</td>
						<td>".$d_instruction."</td>
						<td align=\"right\">".$amount."</td>
					  </tr>";
			}
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
}
mysqli_close($link);
?>