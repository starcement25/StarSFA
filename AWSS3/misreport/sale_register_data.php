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
	$emp_hierarchy_condition = " AND SUBSTRING(OH.order_no,2,5) IN (".$emp_hierarchy.") ";
}
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
$cust_type = $_REQUEST['cust_type'];
$state = $_REQUEST['state'];
$employee = $_REQUEST['employee'];
$employee_condition=" AND SUBSTRING(OH.order_no,2,5) IN (".$employee.") ";

if($cust_type == 'primary'){
	$cust_type_condition = " (CM.cust_type = 'D' OR CM.cust_type = 'Dealer') ";
	$cust_type_order = "";
}
else if($cust_type == 'secondary'){
	$cust_type_condition = " (CM.cust_type = 'R' OR CM.cust_type = 'Sub-Dealer') ";
	$cust_type_order = " CM.rds_tag, ";
}

$date_condition = " SUBSTRING(OH.order_no,-14,8) BETWEEN '".str_replace("-",'',$start_date)."' AND '".str_replace("-",'',$end_date)."' ";


if(no_of_filter == 1){
	$table_header = "<td>Prod Desc</td><td>UOM</td>";
	$colspan = '5';
}
if(no_of_filter == 2){
	$table_header = "<td>Prod Group</td>
					<td>Prod Desc</td><td>UOM</td>";
	$colspan = '6';
}
if(no_of_filter == 3){
	$table_header = "<td>Prod Group</td>
					<td>Prod Sub Group</td>
					<td>Prod Desc</td><td>UOM</td>";
	$colspan = '7';
}
if(no_of_filter == 4){
	$table_header = "<td>Prod Group</td>
					<td>Prod Sub Group</td>
					<td>Brand</td>
					<td>Prod Desc</td><td>UOM</td>";
	$colspan = '8';
}

function productdetails($p_code,$tablename,$column,$get_column){
	$sql = "SELECT $get_column FROM $tablename WHERE $column = '$p_code'";
	$res = mysql_query($sql);
	$row = mysql_fetch_array($res);
	return $row[$get_column];
}

$count = 1;
$emp_customer_date_array = array();

if(modified_customer_emp_route=='yes')
{
	$sql_order_header = "SELECT OH.order_no, SUBSTRING(OH.order_no,2,5) AS emp_code, OH.customer_code, DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%d-%m-%Y') AS order_date,OH.TD,EM.emp_name,EM.dns_emp_code,EM.state,
					CM.customer_name,CM.rds_tag,RM.route_name,CM.cust_class,CM.category_of_store FROM `order_header` OH, customer_master CM,employee_master EM,route_master RM WHERE 
					OH.order_no LIKE 'O%' AND ".$date_condition." AND OH.customer_code = CM.customer_code AND CM.route_code=RM.route_code
					AND ".$cust_type_condition.$employee_condition." AND SUBSTRING(OH.order_no,2,5)=EM.emp_code AND EM.state=".$state."
					ORDER BY ".$cust_type_order." SUBSTRING(OH.order_no,2,5), OH.customer_code, SUBSTRING(OH.order_no,-14,8) ASC";
}
else
{
	$sql_order_header = "SELECT OH.order_no, SUBSTRING(OH.order_no,2,5) AS emp_code, OH.customer_code, DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%d-%m-%Y') AS order_date,OH.TD,EM.emp_name,EM.dns_emp_code,EM.state,
					CM.customer_name,CM.rds_tag,RM.route_name,CM.cust_class,CM.category_of_store FROM `order_header` OH, customer_master CM,employee_master EM,route_master RM WHERE 
					OH.order_no LIKE 'O%' AND ".$date_condition." AND OH.customer_code = CM.customer_code AND CM.route_code=RM.route_code
					AND ".$cust_type_condition.$employee_condition." AND SUBSTRING(OH.order_no,2,5)=EM.emp_code AND EM.state=".$state."
					ORDER BY ".$cust_type_order." SUBSTRING(OH.order_no,2,5), OH.customer_code, SUBSTRING(OH.order_no,-14,8) ASC";
}
$res_order_header = mysql_query($sql_order_header);
$total_row_check = mysql_num_rows($res_order_header);
if($total_row_check>0){
	?>
    <table id="display_table" class="border" border="1" style="border-collapse:collapse;" width="100%" cellpadding="3">
      <tr class="TDHEAD" align="center">
        <td>Date</td>
        <?php echo $table_header; ?>
        <td>Quantity</td>
        <td>Sale Rate</td>
        <td>Amount</td>
      </tr>
    <?php
	$res_order_header = mysql_query($sql_order_header);
	while($row_order_header = mysql_fetch_array($res_order_header)){
		$order_no = $row_order_header['order_no'];
		$emp_code = $row_order_header['emp_code'];
		$emp_name = $row_order_header['emp_name'];
		$dns_emp_code = $row_order_header['dns_emp_code'];
		$customer_code = $row_order_header['customer_code'];
		$order_date = $row_order_header['order_date'];
		$TD = $row_order_header['TD'];
		$customer_name = $row_order_header['customer_name'];
		$cust_class = $row_order_header['cust_class'];
		$category_of_store = $row_order_header['category_of_store'];
		$rds_tag = $row_order_header['rds_tag'];
		$state = $row_order_header['state'];
		$route_name = $row_order_header['route_name'];

		$emp_customer_tag = $emp_code."^".$customer_code."^".$order_date;
		if(!in_array($emp_customer_tag,$emp_customer_date_array)){
			array_push($emp_customer_date_array,$emp_customer_tag);
			/*$sql_emp = "SELECT emp_name FROM employee_master WHERE emp_code = '".$emp_code."'";
			$res_emp = mysql_query($sql_emp);
			$row_emp = mysql_fetch_array($res_emp);
			$emp_name = $row_emp['emp_name'];
			
			$sql_customer = "SELECT customer_name, rds_tag FROM customer_master WHERE customer_code = '".$customer_code."'";
			$res_customer = mysql_query($sql_customer);
			$row_customer = mysql_fetch_array($res_customer);
			$customer_name = $row_customer['customer_name'];
			$rds_tag = $row_customer['rds_tag'];*/
			if($cust_type == 'primary'){
				echo "<tr>
						<td colspan=\"".$colspan."\" class=\"TDHEAD_SUB\" align=\"center\">
						<table width=\"100%\">
						  <tr>
						  	<td align=\"center\">Distributor: $customer_name</td>
							<td align=\"center\">Route: $route_name</td>
							<td align=\"center\">Employee: $emp_name (".$emp_code.")</td>
							<td align=\"center\">State: $state </td>
						  </tr>
						</table>
						</td>
					  </tr>";
			}
			else if($cust_type == 'secondary'){
				$sql_rds_tag = "SELECT GROUP_CONCAT(customer_name SEPARATOR ';') AS rds_name FROM customer_master WHERE FIND_IN_SET(customer_code,'".$rds_tag."')";
				$res_rds_tag = mysql_query($sql_rds_tag);
				$row_rds_tag = mysql_fetch_array($res_rds_tag);
			    $rds_name =$row_rds_tag['rds_name'];
				//$rds_name=substr($rds_name,0,-1);
				//$rds_name = $row_rds_tag['customer_name'];
				
				//if($rds_name != '')
				echo "<tr>
						<td colspan=\"".$colspan."\" class=\"TDHEAD_SUB\" align=\"center\">
						<table width=\"100%\">
						  <tr>
						  	<td align=\"center\">Distributor: $rds_name</td>";
							if(strtoupper($_SESSION['nick_name'])=='PALSONS'){
								echo "<td align=\"center\">Retailer: $customer_name - ".$cust_class." - ".$category_of_store."</td>";
							}
							else
							{
								echo "<td align=\"center\">Retailer: $customer_name</td>";
							}
							
					echo "<td align=\"center\">Route: $route_name</td>
							<td align=\"center\">Employee: $emp_name (".$emp_code.")</td>
							<td align=\"center\">State: $state </td>
						  </tr>
						</table>
						</td>
					  </tr>";
			}
		}
		/*if($cust_type == 'secondary' && $rds_name == '')
			continue;*/
		$sql_order_details = "SELECT sku_code, qty, sale_rate, amount,mrp_code,UOM FROM order_details WHERE order_no = '".$order_no."'";
		$res_order_details = mysql_query($sql_order_details);
		while($row_order_details = mysql_fetch_array($res_order_details)){
			$sku_code = $row_order_details['sku_code'];
			$qty = $row_order_details['qty'];
			$UOM_order=$row_order_details['UOM'];
			$sale_rate = $row_order_details['sale_rate'];
			//$amount = $row_order_details['amount'];
			$mrp_code = $row_order_details['mrp_code'];
			if(mrp=='yes'){
			$sqlmrp="SELECT mrp from mrp where mrp_code='".$mrp_code."'";
			$rsmrp=mysql_query($sqlmrp);
			$rowmrp=mysql_fetch_array($rsmrp);
			$mrp=$rowmrp['mrp'];
			$sale_rate=$mrp;
			}
			$amount=$qty*$sale_rate;
			if($TD >0){
				$amount=$amount-(($amount*$TD)/100);
			}

			$sql_prod_details = "SELECT prod_desc, product_group_code, product_sub_group_code, product_brand_code,alias,UOM1,conversion_factor FROM product_master 
								WHERE prod_code = '".$sku_code."'";
			$res_prod_details = mysql_query($sql_prod_details);
			$row_prod_details = mysql_fetch_array($res_prod_details);
			$prod_desc = $row_prod_details['prod_desc'];
			$alias = $row_prod_details['alias'];
			$UOM = $row_prod_details['UOM1'];
			$conversion_factor = $row_prod_details['conversion_factor'];
			
			if($alias!='')
			{
				$prod_desc=$alias;
			}
			$product_group_code = $row_prod_details['product_group_code'];
			$product_sub_group_code = $row_prod_details['product_sub_group_code'];
			$product_brand_code = $row_prod_details['product_brand_code'];
			
			if(no_of_filter == '1'){
				$table_data = "<td>".$prod_desc."</td><td>".$UOM."</td>";
			}
			else if(no_of_filter == '2'){
				$prod_group_name = productdetails($product_group_code,'product_group_master','product_group_code','product_group_name');
				$table_data = "<td>".$prod_group_name."</td>
							   <td>".$prod_desc."</td><td>".$UOM."</td>";
			}
			else if(no_of_filter == '3'){
				$prod_group_name = productdetails($product_group_code,'product_group_master','product_group_code','product_group_name');
				$prod_sub_group_name = productdetails($product_sub_group_code,'product_sub_group_master','product_sub_group_code','product_sub_group_name');
				$table_data = "<td>".$prod_group_name."</td>
							   <td>".$prod_sub_group_name."</td>
							   <td>".$prod_desc."</td><td>".$UOM."</td>";
			}
			else if(no_of_filter == '4'){
				$prod_group_name = productdetails($product_group_code,'product_group_master','product_group_code','product_group_name');
				$prod_sub_group_name = productdetails($product_sub_group_code,'product_sub_group_master','product_sub_group_code','product_sub_group_name');
				$prod_brand_name = productdetails($product_brand_code,'product_brand_master','product_brand_code','product_brand_name');
				$table_data = "<td>".$prod_group_name."</td>
							   <td>".$prod_sub_group_name."</td>
							   <td>".$prod_brand_name."</td>
							   <td>".$prod_desc."</td><td>".$UOM."</td>";
			}
			if(strtoupper($_SESSION['nick_name'])=='ABDOST' || strtoupper($_SESSION['nick_name'])=='ABDOS')
				{
					//echo strtoupper($UOM);
					if(strtoupper($UOM_order)!='CASE' || $UOM_order=='')
					{
						$qtyconverted=$qty/$conversion_factor;
						$sqlselmrp="SELECT mrp FROM mrp WHERE product_code='".$sku_code."' AND UPPER(UOM)='CASE'";
						$rsselmrp=mysql_query($sqlselmrp);
						$rowselmrp=mysql_fetch_array($rsselmrp);
						$mrp=$rowselmrp['mrp'];
						$sale_rate=$mrp;
						$amount=$qtyconverted*$mrp;
						$qty=$qtyconverted;
					}
					
					//$qty=number_format($qtyconverted,2);
				}
			echo "<tr>
					<td>".$order_date."</td>
					".$table_data."
					<td align=\"right\">".$qty."</td>
					<td align=\"right\">".$sale_rate."</td>
					<td align=\"right\">".$amount."</td>
				  </tr>";
			$count++;
		}
	}
	?>
    </table><br /><br />
    <p>
    <div style="width:90%;" align="right"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="export_to_csv();" >
</div>
    </p>
    <?php
}
else{
	echo "<span style=\"font-weight:bold; color:red;\">No Records Found!</span>";
}
?>