<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

$vertical = $_REQUEST['vertical'];
$zone = $_REQUEST['zone'];
$state = $_REQUEST['state'];
$emp_code = $_REQUEST['emp_code'];
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

if(strtoupper($_SESSION['nick_name']) != 'MAITHAN' && strtoupper($_SESSION['nick_name']) != 'STAR' && strtoupper($_SESSION['nick_name']) != 'PARLE' && strtoupper($_SESSION['nick_name']) != 'RUPA'){
	
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
	
	echo $sql_prev_order_counting_master = "SELECT customer_code, cust_type, product_code, visit_qty, rate, amount,  SUBSTRING(order_no,2,5) as emp_code, DATE_FORMAT(SUBSTRING(order_no,-14,8),'%d-%m-%Y') as order_date FROM prev_order_counting_master WHERE (SUBSTRING(order_no,-14,8) BETWEEN '".$startdate."' AND '".$enddate."') AND order_no LIKE 'O%' ".$emp_condition.$vertical_condition;
	exit();
	//exit();
	$res_prev_order_counting_master = mysqli_query($link,$sql_prev_order_counting_master);
	$total_rows = mysqli_num_rows($res_prev_order_counting_master);
	
	if($total_rows > 0){
		?>
		<table id="display_table" width="100%" class="border" border="1" style="border-collapse:collapse;">
		  <tr class="TDHEAD" align="center">
			<td>DNS Customer Code</td>
			<td>Customer Name</td>
            <td>Emp Name</td>
            <?php if(tagged_distributor_for_order=='yes'){?>
            <td>Distributor Name</td>
            <?php }?>
			<td>Order Date</td>
            <td>Prod Group</td>
			<td>SKU Code</td>
			<td>SKU Name</td>
			<td>Qty</td>
            <td>Sale Rate</td>
            <td>Order Type</td>
		  </tr>
		<?php
		$res_prev_order_counting_master = mysqli_query($link,$sql_prev_order_counting_master);
		while($row_prev_counting_master = mysqli_fetch_assoc($res_prev_order_counting_master)){
			
			$customer_code = $row_prev_counting_master['customer_code'];
			$cust_type = $row_prev_counting_master['cust_type'];
			$product_code = $row_prev_counting_master['product_code'];
			$visit_qty = $row_prev_counting_master['visit_qty'];
			$rate = $row_prev_counting_master['rate'];
			$amount = $row_prev_counting_master['amount'];
			$emp_code = $row_prev_counting_master['emp_code'];
			$order_date = $row_prev_counting_master['order_date'];
			
			if($cust_type == 'R')
				$order_type = 'Secondary';
			else if($cust_type == 'D')
				$order_type = 'Primary';
			
			$sql_customer_details = "SELECT dns_customer_code, customer_name,rds_tag FROM customer_master  WHERE customer_code = '".$customer_code."'";
			//exit();
			$res_customer_details = mysqli_query($link,$sql_customer_details);
			$row_customer_details = mysqli_fetch_assoc($res_customer_details);
			$dns_customer_code = $row_customer_details['dns_customer_code'];
			$customer_name = $row_customer_details['customer_name'];
			$rds_tag = $row_customer_details['rds_tag'];
			
			if(tagged_distributor_for_order=='yes'){
			$sql_rds_name = "SELECT customer_name FROM customer_master WHERE customer_code = '".$rds_tag."'";
			$res_rds_name = mysqli_query($link,$sql_rds_name);
			$row_rds_name = mysqli_fetch_assoc($res_rds_name);
			$rds_name = $row_rds_name['customer_name'];
			}
			$sql_emp_details = "SELECT dns_emp_code, emp_name FROM employee_master WHERE emp_code = '".$emp_code."'";
			$res_emp_details = mysqli_query($link,$sql_emp_details);
			$row_emp_details = mysqli_fetch_assoc($res_emp_details);
			$dns_emp_code = $row_emp_details['dns_emp_code'];
			$emp_name = $row_emp_details['emp_name'];
			
			$sql_sku_name = "SELECT dns_prod_code, product_group_code, prod_desc FROM product_master WHERE prod_code = '".$product_code."'";
			$res_sku_name = mysqli_query($link,$sql_sku_name);
			$row_sku_name = mysqli_fetch_assoc($res_sku_name);
			$sku_name = $row_sku_name['prod_desc'];
			$dns_prod_code = $row_sku_name['dns_prod_code'];
			$product_group_code = $row_sku_name['product_group_code'];
			
			$sql_prod_group_name = "SELECT product_group_name FROM product_group_master WHERE product_group_code = '".$product_group_code."'";
			$res_prod_group_name = mysqli_query($link,$sql_prod_group_name);
			$row_prod_group_name = mysqli_fetch_assoc($res_prod_group_name);
			$prod_group_name = $row_prod_group_name['product_group_name'];
			
			if(tagged_distributor_for_order=='yes'){
				echo "<tr>
					<td>".$dns_customer_code."</td>
					<td>".$customer_name."</td>
					<td>".$emp_name."</td>
					<td>".$rds_name."</td>
					<td>".$order_date."</td>
					<td>".$prod_group_name."</td>
					<td>".$dns_prod_code."</td>
					<td>".$sku_name."</td>
					<td align=\"right\">".$visit_qty."</td>
					<td align=\"right\">".$rate."</td>
					<td>".$order_type."</td>
				  </tr>";
				
			}
			else
			{
			echo "<tr>
					<td>".$dns_customer_code."</td>
					<td>".$customer_name."</td>
					<td>".$emp_name."</td>
					<td>".$order_date."</td>
					<td>".$prod_group_name."</td>
					<td>".$dns_prod_code."</td>
					<td>".$sku_name."</td>
					<td align=\"right\">".$visit_qty."</td>
					<td align=\"right\">".$rate."</td>
					<td>".$order_type."</td>
				  </tr>";
			}
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
}
else if(strtoupper($_SESSION['nick_name']) == 'RUPA'){
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
						DATE_FORMAT( LO.date,  '%d-%m-%Y' ) AS order_date
						FROM location LO
						INNER JOIN order_header OH ON LO.trans_id = OH.order_no
						INNER JOIN order_details OD ON OH.order_no = OD.order_no
						INNER JOIN customer_master CM ON OH.customer_code = CM.customer_code
						INNER JOIN product_master PM ON OD.sku_code = PM.prod_code
						INNER JOIN product_group_master PGM ON PM.product_group_code = PGM.product_group_code
						INNER JOIN employee_master EM ON LO.emp_code = EM.emp_code
						WHERE (DATE_FORMAT( LO.date,  '%Y%m%d' ) BETWEEN  '".$startdate."' AND  '".$enddate."')
						AND OH.order_no LIKE  'O%'".$emp_condition_one;
	//exit();					
	$res_order_details = mysqli_query($link,$sql_order_details);					
	$total_rows = mysqli_num_rows($res_order_details);
	
	if($total_rows > 0){
		?>
		<table id="display_table" width="100%" class="border" border="1" style="border-collapse:collapse;">
		  <tr class="TDHEAD" align="center">
			<td>DNS Customer Code</td>
			<td>Customer Name</td>
            <td>Emp Name</td>
            <?php if(tagged_distributor_for_order=='yes'){?>
            <td>Distributor Name</td>
            <?php }?>
			<td>Order Date</td>
            <td>Prod Group</td>
			<td>SKU Code</td>
			<td>SKU Name</td>
			<td>Qty</td>
            <td>Sale Rate</td>
            <td>Order Type</td>
		  </tr>
		<?php
		$header = "DNS Customer Code"."\t"."Customer Name"."\t"."Emp Name"."\t"."Distributor Name"."\t"."Order Date"."\t"."Prod Group"."\t"."SKU Code"."\t"."SKU Name"."\t"."Qty"."\t"."Sale Rate"."\t"."Order Type";
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
			$rds_tag = $row_order_details['rds_tag'];
			//$rds_name='';
			
			if($cust_type == 'R')
				$order_type = 'Secondary';
			else if($cust_type == 'D')
				$order_type = 'Primary';
			
			if(tagged_distributor_for_order=='yes' && $rds_tag!=''){
				$sql_rds_name = "SELECT customer_name FROM customer_master WHERE customer_code = '".$rds_tag."'";
				$res_rds_name = mysqli_query($link,$sql_rds_name);
				$row_rds_name = mysqli_fetch_assoc($res_rds_name);
				$rds_name = $row_rds_name['customer_name'];
			}
			if(tagged_distributor_for_order=='yes'){
				echo "<tr>
					<td>".$dns_customer_code."</td>
					<td>".$customer_name."</td>
					<td>".$emp_name."</td>
					<td>".$rds_name."</td>
					<td>".$order_date."</td>
					<td>".$product_group_name."</td>
					<td>".$dns_prod_code."</td>
					<td>".$sku_name."</td>
					<td align=\"right\">".$visit_qty."</td>
					<td align=\"right\">".$rate."</td>
					<td>".$order_type."</td>
				  </tr>";
			}
			else
			{
			echo "<tr>
					<td>".$dns_customer_code."</td>
					<td>".$customer_name."</td>
					<td>".$emp_name."</td>
					<td>".$order_date."</td>
					<td>".$prod_group_name."</td>
					<td>".$dns_prod_code."</td>
					<td>".$sku_name."</td>
					<td align=\"right\">".$visit_qty."</td>
					<td align=\"right\">".$rate."</td>
					<td>".$order_type."</td>
				  </tr>";
			}
			//$contents.=$dns_customer_code."\t".$customer_name."\t".$emp_name."\t".$rds_name."\t".$order_date."\t".$product_group_name."\t".$dns_prod_code."\t".$sku_name."\t".$visit_qty."\t".$rate."\t".$order_type."\n";
			$count++;
		}
			//$datacontents=$header."\n".$contents;
			/*header("Content-type: application/octet-stream"); 
			header("Content-Disposition: attachment; filename=Order_Download_Report.xls"); 
			print "$datacontents";*/
			//$nick_name=strtoupper($_SESSION['nick_name']);
			//echo $datacontents;	
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
else if(strtoupper($_SESSION['nick_name']) == 'PARLE')
{
	$sql_order_header = "SELECT order_no, SUBSTRING(order_no,2,5) as emp_code, DATE_FORMAT(SUBSTRING(order_no,-14,8),'%d-%m-%Y') as order_date, 
						customer_code,tag_distributor_code,d_instruction FROM order_header WHERE (SUBSTRING(order_no,-14,8) BETWEEN '".$startdate."' 
						AND '".$enddate."') AND order_no LIKE 'O%'";
	$res_order_header = mysqli_query($link,$sql_order_header);
	$order_header_total_rows = mysqli_num_rows($res_order_header);
	if($order_header_total_rows>0){
		?>
		<table width="100%" class="border" border="1" style="border-collapse:collapse;" id="display_table">
		  <tr class="TDHEAD" align="center">
          	<td width="7%">Transaction Id</td>
          	<td width="8%">Emp Name</td>
			<td width="9%">WS Name</td>
            <td width="8%">Route Name</td>
			<td width="9%">Customer Name</td>
			<td width="8%">Order Date</td>
            <td width="8%">Category</td>
			<td width="8%">SKU Name</td>
			<td width="7%">Qty</td>
            <td width="7%">Sale Rate</td>
            <td width="7%">Order Value</td>
            <td width="">Remarks</td>
		  </tr>
		<?php
		$res_order_header = mysqli_query($link,$sql_order_header);
		while($row_order_header = mysqli_fetch_assoc($res_order_header)){
			$order_no = $row_order_header['order_no'];
			$emp_code = $row_order_header['emp_code'];
			$order_date = $row_order_header['order_date'];
			$customer_code = $row_order_header['customer_code'];
			$transaction_type = $row_order_header['transaction_type'];
			$tag_distributor_code=$row_order_header['tag_distributor_code'];
			$d_instruction=str_replace(",",";",$row_order_header['d_instruction']);
			
			$sql_customer_details = "SELECT dns_customer_code, customer_name,route_code FROM customer_master WHERE customer_code = '".$customer_code."'";
			$res_customer_details = mysqli_query($link,$sql_customer_details);
			$row_customer_details = mysqli_fetch_assoc($res_customer_details);
			$dns_customer_code = $row_customer_details['dns_customer_code'];
			$route_code = $row_customer_details['route_code'];
			$customer_name = str_replace(",",";",$row_customer_details['customer_name']);
			
			$sqlroutename="SELECT route_name FROM route_master WHERE route_code='".$route_code."'";
			$rsroutename=mysqli_query($link,$sqlroutename);
			$total_row_check = mysqli_num_rows($rsroutename);
			if($total_row_check>0){
				$rsroutename=mysqli_query($link,$sqlroutename);
				$rowroutename=mysqli_fetch_assoc($rsroutename);
				$route_name=$rowroutename['route_name'];
			}
			else{
				$sql_route_code = "SELECT route_code FROM customer_master WHERE customer_code = '".$tag_distributor_code."'";
				$res_route_code = mysqli_query($link,$sql_route_code);
				$row_route_code = mysqli_fetch_assoc($res_route_code);
				$route_code = $row_route_code['route_code'];
				
				$sqlroutename="SELECT route_name FROM route_master WHERE route_code='".$route_code."'";
				$rsroutename=mysqli_query($link,$sqlroutename);
				$rowroutename=mysqli_fetch_assoc($rsroutename);
				$route_name=$rowroutename['route_name'];
			}
			$route_name = str_replace(",",";",$route_name);

			$sqldistributorname="SELECT customer_name FROM customer_master WHERE customer_code='".$tag_distributor_code."'";
			$rsdistributorname=mysqli_query($link,$sqldistributorname);
			$rowdistributorname=mysqli_fetch_assoc($rsdistributorname);
			$distributor_name=str_replace(",",";",$rowdistributorname['customer_name']);

			$sql_emp_details = "SELECT dns_emp_code, emp_name FROM employee_master WHERE emp_code = '".$emp_code."'";
			$res_emp_details = mysqli_query($link,$sql_emp_details);
			$row_emp_details = mysqli_fetch_assoc($res_emp_details);
			$dns_emp_code = $row_emp_details['dns_emp_code'];
			$emp_name = str_replace(",",";",$row_emp_details['emp_name']);
			
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
				$sku_name = str_replace(",",";",$row_sku_name['prod_desc']);
				$dns_prod_code = $row_sku_name['dns_prod_code'];
				$product_group_code = $row_sku_name['product_group_code'];
				
				$sql_prod_group_name = "SELECT product_group_name FROM product_group_master WHERE product_group_code = '".$product_group_code."'";
				$res_prod_group_name = mysqli_query($link,$sql_prod_group_name);
				$row_prod_group_name = mysqli_fetch_assoc($res_prod_group_name);
				$prod_group_name = str_replace(",",";",$row_prod_group_name['product_group_name']);
				
				echo "<tr>
						<td>".$order_no."</td>
						<td>".$emp_name."</td>
						<td>".$distributor_name."</td>
						<td>".$route_name."</td>
						<td>".$customer_name."</td>
						<td>".$order_date."</td>
						<td>".$prod_group_name."</td>
						<td>".$sku_name."</td>
						<td align=\"right\">".$qty."</td>
						<td align=\"right\">".$sale_rate."</td>
						<td>".$amount."</td>
						<td>".$d_instruction."</td>
					  </tr>";
				$count++;
			}
		}
		echo "</table>";
		echo "<br>
	<div style=\"width:100%;\" align=\"right\"><input name=\"print\" type=\"button\" value=\"Print\" id=\"print\" onClick=\"PrintElem('#display');\">&nbsp;
		<input name=\"export\" type=\"button\" value=\"Export\" id=\"btnExport\" onClick=\"exporttocsv();\" >
	</div>";
	}
	else{
		echo "<strong><font color=\"red\">No Records Found</font></strong>";
	}

}
else if(strtoupper($_SESSION['nick_name']) == 'MAITHAN' || strtoupper($_SESSION['nick_name']) == 'STAR'){
	
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
			$d_instruction=preg_replace('/\s+/', ' ',$row_order_header['d_instruction']);
			
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