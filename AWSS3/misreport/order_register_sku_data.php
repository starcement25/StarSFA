<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

if($_GET['type'] == 'today'){
	$today = date('Y-m-d');
	$order_status = "Today's Order Status";
	$order_header_cond = " SUBSTRING(OH.order_no,-14,8) = '".str_replace("-","",$today)."' ";
	$value = 1;
}
else if($_GET['type'] == 'mtd'){
	@$current_date = date('Y-m-d');
	$month = explode("-",$current_date);
	$year = $month[0];
	$month = $month[1];
	$order_status = "Order Status MTD";
	$order_header_cond = " SUBSTRING(OH.order_no,-14,4) =$year AND substring(OH.order_no,-10,2) =$month ";
	$value = 2;
}
else if($_GET['type'] == 'custom'){
	$start_date = str_replace("-","",$_GET['start_date']);
	$end_date = str_replace("-","",$_GET['end_date']);
	$value = 3;
	$order_status = "Order Status From ".date('d-m-Y',strtotime(''.$_GET['start_date'].''))." To ".date('d-m-Y',strtotime(''.$_GET['end_date'].''));
	$order_header_cond = " (substring(OH.order_no,-14,8) BETWEEN ".$start_date." AND ".$end_date.") ";
}
else{
	$today = date('Y-m-d');
	$order_status = "Today's Sauda Booking Status";
	$order_header_cond = " SUBSTRING(OH.order_no,-14,8) = '".str_replace("-","",$today)."' ";
	$value = 1;
}

if($_GET['emp_name'] == 'all'){
	$emp_cond = " GROUP BY DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%d-%m-%Y') ";
}
else{
	$emp_cond = " AND SUBSTRING(OH.order_no,2,5)='".$_GET['emp_name']."' ";
}

$emp_name_array = array();
$order_date_array = array();

if($_GET['emp_name'] == 'all'){
	$sql_order_header = "SELECT DISTINCT DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%d-%m-%Y') as order_date FROM order_header OH WHERE ".$order_header_cond." AND OH.order_no LIKE 'O%' ORDER BY order_date DESC";
	$res_order_header = mysql_query($sql_order_header);
	$total_rows = mysql_num_rows($res_order_header);
	
	if($total_rows>0){ ?>
        <table border="1" width="100%" style="border-collapse:collapse;" cellpadding="8">
        <tr class="TDHEAD">
        <td align="center" colspan="4"><?php echo $order_status; ?></td>
        </tr>
        <tr class="TDHEAD_SUB">
        <td>SI</td>
        <td>SKU</td>
        <td>Order Qty</td>
        <td>Amount</td>
        </tr>
        <?php
        $count = 1;
        $res_order_header = mysql_query($sql_order_header);
		while($row_order_header = mysql_fetch_array($res_order_header)){
			$order_date = $row_order_header['order_date'];
			
			echo "<tr class=\"TDHEAD\"><td colspan='4' align='center'>".$order_date."</td></tr>";
						
			$sql_order_details = "SELECT PM.prod_desc, SUM(OD.qty) as order_qty, SUM(OD.amount) as order_amt FROM order_details OD, product_master PM WHERE DATE_FORMAT(SUBSTRING(OD.order_no,-14,8),'%d-%m-%Y') = '".$order_date."' AND OD.sku_code = PM.prod_code GROUP BY OD.sku_code ORDER BY PM.prod_desc DESC";
			$res_order_details = mysql_query($sql_order_details);
			while($row_order_details = mysql_fetch_array($res_order_details)){
				$prod_name = $row_order_details['prod_desc'];
				$order_qty = $row_order_details['order_qty'];
				$order_amt = $row_order_details['order_amt'];
				
				$total_qty += $order_qty;
				$total_amt += $order_amt;
				
				echo "<tr>
						<td>".$count."</td>
						<td>".$prod_name."</td>
						<td align=\"right\">".number_format($order_qty,2)."</td>
						<td align=\"right\">".number_format($order_amt,2)."</td>
					  </tr>";
				$count++;
			}
		}
		echo "<tr style=\"font-weight:bold;\">
				<td colspan='2'>Total</td>
				<td align=\"right\">".number_format($total_qty,2)."</td>
				<td align=\"right\">".number_format($total_amt,2)."</td>
			  </tr>";
		echo "</table>";
	}
	else{
	echo "<font color='red'><strong>No records</strong></font>";
	}
}
else{
	$sql_order_header = "SELECT OH.order_no, OH.customer_code, DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%d-%m-%Y') as order_date FROM order_header OH, employee_master EM WHERE ".$order_header_cond." AND OH.order_no LIKE 'O%'".$emp_cond." AND EM.emp_code=SUBSTRING(OH.order_no,2,5) ORDER BY EM.emp_name ASC, order_date DESC";
	$res_order_header = mysql_query($sql_order_header);
	$total_rows = mysql_num_rows($res_order_header);
	
	if($total_rows>0){ ?>
	<table border="1" width="100%" style="border-collapse:collapse;" cellpadding="8">
	<tr class="TDHEAD">
	<td align="center" colspan="5"><?php echo $order_status; ?></td>
	</tr>
	<tr class="TDHEAD_SUB">
	<td>SI</td>
	<td width="20%">Date</td>
	<td>SKU</td>
	<td>Order Qty</td>
	<td>Amount</td>
	</tr>
	<?php
	$count = 1;
	$res_order_header = mysql_query($sql_order_header);
	while($row_order_header = mysql_fetch_array($res_order_header)){
		$order_no = $row_order_header['order_no'];
		$customer_code = $row_order_header['customer_code'];
		$order_date = $row_order_header['order_date'];
		
		$sql_empname = "SELECT emp_name FROM employee_master WHERE emp_code='".substr($order_no,1,5)."'";
		$res_empname = mysql_query($sql_empname);
		$row_empname = mysql_fetch_array($res_empname);
		$emp_name = $row_empname['emp_name'];
		if(!in_array($emp_name,$emp_name_array)){
			array_push($emp_name_array,$emp_name);
			$order_date_array = array();
			echo "<tr class=\"TDHEAD\"><td colspan='5' align='center'>$emp_name</td></tr>";
		}
		
		/*$sql_customer_name = "SELECT customer_name FROM customer_master WHERE customer_code = '".$customer_code."'";
		$res_customer_name = mysql_query($sql_customer_name);
		$row_customer_name = mysql_fetch_array($res_customer_name);
		$customer_name = $row_customer_name['customer_name'];*/
		
		$sql_order_details = "SELECT PM.prod_desc, SUM(OD.qty) as order_qty, SUM(OD.amount) as order_amt FROM order_details OD, product_master PM WHERE OD.order_no = '".$order_no."' AND OD.sku_code = PM.prod_code GROUP BY OD.sku_code ORDER BY PM.prod_desc DESC";
		$res_order_details = mysql_query($sql_order_details);
		while($row_order_details = mysql_fetch_array($res_order_details)){
			$prod_name = $row_order_details['prod_desc'];
			$order_qty = $row_order_details['order_qty'];
			$order_amt = $row_order_details['order_amt'];
			
			$total_qty += $order_qty;
			$total_amt += $order_amt;
			
			echo "<tr>
					<td>".$count."</td>";
					if(!in_array($order_date,$order_date_array)){
						array_push($order_date_array,$order_date);
						echo "<td bgcolor=\"#F2F2F2\" style=\"font-weight:bold;\">".$order_date."</td>";
					}
					else{
					echo "<td></td>";
					}
					echo "<td>".$prod_name."</td>
					<td align=\"right\">".number_format($order_qty,2)."</td>
					<td align=\"right\">".number_format($order_amt,2)."</td>
				  </tr>";
			$count++;
		}
	}
	echo "<tr style=\"font-weight:bold;\">
			<td colspan='3'>Total</td>
			<td align=\"right\">".number_format($total_qty,2)."</td>
			<td align=\"right\">".number_format($total_amt,2)."</td>
		  </tr>";
	echo "</table>";
	}
	else{
	echo "<font color='red'><strong>No records</strong></font>";
	}
}
mysql_close($link);
?>