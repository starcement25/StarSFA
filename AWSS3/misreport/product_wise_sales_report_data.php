<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

if(strtoupper($_SESSION['admin_login']) == "ADMIN"){
	$emp_hierarchy = "";
	$emp_hierarchy_condition = "";
}
else{
	$emp_hierarchy = return_employee_hierarchy($_SESSION['admin_login']);
	$emp_hierarchy_condition = " AND SUBSTRING(OD.order_no,2,5) IN (".$emp_hierarchy.") ";
}

function header_value($column_name,$tabl_name,$compare_col,$compare_value){
	$sql = "SELECT $column_name FROM $tabl_name WHERE $compare_col = '".$compare_value."'";
	$res = mysql_query($sql);
	$row = mysql_fetch_array($res);
	$header_value = $row[$column_name];
	return $header_value;
}

$today = date('Ymd');
$type = $_REQUEST['type'];
$get_code = $_REQUEST['get_code'];
$listing_type = $_REQUEST['listing_type'];

if($listing_type == 'customerlist'){
	$listing_table = ", order_header OH ";
	$listing_condition = " AND OH.order_no = OD.order_no AND OH.customer_code = '".$get_code."' ";
}
else if($listing_type == 'emplist'){
	if(tagged_distributor_for_order == 'yes'){
		$get_code_split = explode("^",$get_code);
		$get_emp_code = $get_code_split[0];
		$distributor_code = $get_code_split[1];
		$listing_table = ", order_header OH ";
		$listing_condition = " AND OH.order_no = OD.order_no AND OH.tag_distributor_code = '".$distributor_code."' AND SUBSTRING(OH.order_no,2,5) = '".$get_emp_code."' ";
		//print_r($get_code_split);die;
	}
	else{
		$listing_table = ", order_header OH ";
		$listing_condition = " AND OH.order_no = OD.order_no AND SUBSTRING(OH.order_no,2,5) = '".$get_code."' ";
	}
}
else if($listing_type == 'ditributorlist'){
	$listing_table = ", order_header OH ";
	$listing_condition = " AND OH.order_no = OD.order_no AND (OH.tag_distributor_code = '".$get_code."' OR OH.customer_code = '".$get_code."' ) ";
}
else if($listing_type == ''){
	$listing_table = "";
	$listing_condition = "";
}

if($type == 'prod_group'){
	$prod_group_code = $_REQUEST['prod_group_code'];
	$table_name = ", product_group_master PGM, product_master PM ";
	$table_condition = " AND OD.sku_code = PM.prod_code AND PM.product_group_code = PGM.product_group_code AND PGM.product_group_code = '".$prod_group_code."' ";
	
	$header_value = header_value('product_group_name','product_group_master','product_group_code',$prod_group_code);
}
else if($type == 'prod_sub_group'){
	$prod_sub_group_code = $_REQUEST['prod_sub_group_code'];
	$table_name = ", product_sub_group_master PSGM, product_master PM ";
	$table_condition = " AND OD.sku_code = PM.prod_code AND PM.product_sub_group_code = PSGM.product_sub_group_code AND PSGM.product_sub_group_code = '".$prod_sub_group_code."' ";
	
	$header_value = header_value('product_sub_group_name','product_sub_group_master','product_sub_group_code',$prod_sub_group_code);
}
else if($type == 'prod_brand'){
	$prod_brand_code = $_REQUEST['prod_brand_code'];
	$table_name = ", product_brand_master PBM, product_master PM ";
	$table_condition = " AND OD.sku_code = PM.prod_code AND PM.product_brand_code = PBM.product_brand_code AND PBM.product_brand_code = '".$prod_brand_code."' ";
	
	$header_value = header_value('product_brand_name','product_brand_master','product_brand_code',$prod_brand_code);
}
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
if($start_date != '' && $end_date != '')
	$date_condition = " AND (SUBSTRING(OD.order_no,-14,8) BETWEEN '".str_replace("-","",$start_date)."' AND '".str_replace("-","",$end_date)."') ";
else
	$date_condition = " AND SUBSTRING(OD.order_no,-14,8) = '".$today."' ";

$sql_order_details = "SELECT OD.sku_code, SUM(OD.qty), SUM(OD.amount) FROM order_details OD ".$table_name.$listing_table." WHERE OD.order_no LIKE 'O%'".$date_condition.$table_condition.$emp_hierarchy_condition.$listing_condition." GROUP BY OD.sku_code";
$res_order_details = mysql_query($sql_order_details);
$total_row_check = mysql_num_rows($res_order_details);
$count = 1;
if($total_row_check>0){
	if($listing_type == 'customerlist'){
		$sql_customer_name = "SELECT customer_name FROM customer_master WHERE customer_code = '".$get_code."'";
		$res_customer_name = mysql_query($sql_customer_name);
		$row_customer_name = mysql_fetch_array($res_customer_name);
		$customer_name = $row_customer_name['customer_name'];
		
		echo "<table><tr class='TDHEAD_SUB'><td align='center' width='100%'>$customer_name</td></tr></table><br>";
	}
	else if($listing_type == 'emplist'){
		if(tagged_distributor_for_order == 'yes'){
			$sql_emp = "SELECT emp_name FROM employee_master WHERE emp_code = '".$get_emp_code."'";
			$res_emp = mysql_query($sql_emp);
			$row_emp = mysql_fetch_array($res_emp);
			$emp_name = $row_emp['emp_name'];
			
			$sql_distributor = "SELECT customer_name FROM customer_master WHERE customer_code = '".$distributor_code."'";
			$res_distributor = mysql_query($sql_distributor);
			$row_distributor = mysql_fetch_array($res_distributor);
			$distributor_name = $row_distributor['customer_name'];
		
			echo "<table><tr class='TDHEAD_SUB'><td align='center' width='100%'>$emp_name -> $distributor_name</td></tr></table><br>";
		}
		else{
			$sql_emp = "SELECT emp_name FROM employee_master WHERE emp_code = '".$get_code."'";
			$res_emp = mysql_query($sql_emp);
			$row_emp = mysql_fetch_array($res_emp);
			$emp_name = $row_emp['emp_name'];
			
			echo "<table><tr class='TDHEAD_SUB'><td align='center' width='100%'>$emp_name</td></tr></table><br>";
		}
	}
	else if($listing_type == 'ditributorlist'){
		$sql_distributor = "SELECT customer_name FROM customer_master WHERE customer_code = '".$get_code."'";
		$res_distributor = mysql_query($sql_distributor);
		$row_distributor = mysql_fetch_array($res_distributor);
		$distributor_name = $row_distributor['customer_name'];
				
		echo "<table><tr class='TDHEAD_SUB'><td align='center' width='100%'>$distributor_name</td></tr></table><br>";
	}
	?>
    <table class="border" width="100%" border="1" style="border-collapse:collapse;" cellpadding="4px">
      <tr>
      	<td class="TDHEAD" align="center" colspan="4"><?php echo $header_value; ?></td>
      </tr>
      <tr class="TDHEAD">
      	<td>SL</td>
      	<td>Product Name</td>
        <td>Quantity</td>
        <td>Amount</td>
      </tr>
    <?php
	$res_order_details = mysql_query($sql_order_details);
	while($row_order_details = mysql_fetch_array($res_order_details)){
		$sku_code = $row_order_details['sku_code'];
		$qty = $row_order_details['SUM(OD.qty)'];
		$amt = $row_order_details['SUM(OD.amount)'];
		
		$sql_prod_desc = "SELECT prod_desc FROM product_master WHERE prod_code = '".$sku_code."'";
		$res_prod_desc = mysql_query($sql_prod_desc);
		$row_prod_desc = mysql_fetch_array($res_prod_desc);
		$prod_desc = $row_prod_desc['prod_desc'];
		
		echo "<tr>
				<td>".$count."</td>
				<td>".$prod_desc."</td>
				<td align=\"right\">".$qty."</td>
				<td align=\"right\">".number_format($amt,2)."</td>
			  </tr>";
		
		$total_qty += $qty;
		$total_amt += $amt;
		
		$count++;
	}
	?>
      <tr class="TDHEAD_SUB">
      	<td colspan="2">Total</td>
        <td align="right"><?php echo $total_qty; ?></td>
        <td align="right"><?php echo number_format($total_amt,2); ?></td>
      </tr>
    </table>
    <br />
    <div style="width:100%;" align="right">
    <?php
	echo "<input name=\"export\" type=\"button\" value=\"Print\" id=\"btnExport\" onClick=\"pdffile('".$_SESSION['admin_login']."','".$start_date."','".$end_date."','".$get_code."','".$listing_type."');\" >";
	?>
    <!--<input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">-->&nbsp;
    <!--<input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >-->
    <?php
	echo "<input name=\"export\" type=\"button\" value=\"Export\" id=\"btnExport\" onClick=\"csvexport('".$_SESSION['admin_login']."','".$start_date."','".$end_date."','".$get_code."','".$listing_type."');\" >";
	?>
	</div>
    <?php
}
else{
	echo "<div style=\"color:red;\"><b>No Records Available</b></div>";
}
mysql_close($link);
?>