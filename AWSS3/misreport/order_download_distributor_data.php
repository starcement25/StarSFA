<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
$retailer = $_REQUEST['retailer'];

$startdate = str_replace("-","",$start_date);
$enddate = str_replace("-","",$end_date);
$count = 1;

	if($retailer == 'all'){
		$sql_retailer = "SELECT customer_code, customer_name FROM customer_master WHERE rds_tag='".$_SESSION['admin_login']."' 
					AND customer_code IN(SELECT DISTINCT customer_code FROM order_header) order by customer_name ASC";
		$res_retailer = mysql_query($sql_retailer);
		$customer_code_string='';
		while($row_retailer = mysql_fetch_array($res_retailer)){
			$customer_code = $row_retailer['customer_code'];
			$customer_code_string=$customer_code_string."'".$customer_code."'".',';
		}
		$customer_code_string=substr($customer_code_string,0,-1);
		$retailer_condition = " AND customer_code IN(".$customer_code_string.")";
	}
	else{
		$retailer_condition = " AND customer_code='".$retailer."'";
	}
	
		$sql_order_header = "SELECT order_no, SUBSTRING(order_no,2,5) as emp_code, DATE_FORMAT(SUBSTRING(order_no,-14,8),'%d-%m-%Y') as order_date,TD, customer_code FROM order_header 
		WHERE (SUBSTRING(order_no,-14,8) BETWEEN '".$startdate."' AND '".$enddate."') AND order_no LIKE 'O%' 
		".$retailer_condition."";
	$res_order_header = mysql_query($sql_order_header);
	$order_header_total_rows = mysql_num_rows($res_order_header);
	if($order_header_total_rows>0){
		if(tagged_distributor_for_order=='yes'){
			$distributor_header="\t"."Distributor Name";
		}
		else $distributor_header="";
		if(providing_code=='yes')
		{
		$header = "Customer Code"."\t"."Category of Store"."\t"."Customer Name"."\t"."Emp Code"."\t"."Emp Name".$distributor_header."\t"."Order Date"."\t"."Prod Group"."\t"."SKU Code"."\t"."SKU Name"."\t"."Qty"."\t"."UOM"."\t"."Sale Rate"."\t"."TD(%)"."\t"."Order Value"."\t";
		}
		else
		{
		  $header = "Customer Name"."\t"."Category of Store"."\t"."Emp Code"."\t"."Emp Name".$distributor_header."\t"."Order Date"."\t"."Prod Group"."\t"."SKU Code"."\t"."SKU Name"."\t"."Qty"."\t"."UOM"."\t"."Sale Rate"."\t"."TD(%)"."\t"."Order Value"."\t";
		}
		?>
		<table id="display_table" width="100%" class="border" border="1" style="border-collapse:collapse;">
		  <tr class="TDHEAD" align="center">
          	<td>Order Date</td>
			<td>Customer Name</td>
			<td>Emp Name</td>
			<td>SKU Name</td>
			<td>Qty</td>
            <td>Sale Rate</td>
            <td>Order Value</td>
		  </tr>
		<?php
		$res_order_header = mysql_query($sql_order_header);
		while($row_order_header = mysql_fetch_array($res_order_header)){
			$order_no = $row_order_header['order_no'];
			$emp_code = $row_order_header['emp_code'];
			$order_date = $row_order_header['order_date'];
			$customer_code = $row_order_header['customer_code'];
			$transaction_type = $row_order_header['transaction_type'];
			$creation_type= $row_order_header['creation_type'];
			$TD=$row_order_header['TD'];
			
			$sql_customer_details = "SELECT dns_customer_code, customer_name,rds_tag,category_of_store FROM customer_master WHERE customer_code = '".$customer_code."'";
			$res_customer_details = mysql_query($sql_customer_details);
			$row_customer_details = mysql_fetch_array($res_customer_details);
			$dns_customer_code = $row_customer_details['dns_customer_code'];
			$customer_name = $row_customer_details['customer_name'];
			$rds_tag = $row_customer_details['rds_tag'];
			$category_of_store = $row_customer_details['category_of_store'];
			
			$sql_emp_details = "SELECT dns_emp_code, emp_name,state FROM employee_master WHERE emp_code = '".$emp_code."'";
			$res_emp_details = mysql_query($sql_emp_details);
			$row_emp_details = mysql_fetch_array($res_emp_details);
			$dns_emp_code = $row_emp_details['dns_emp_code'];
			$emp_name = $row_emp_details['emp_name'];
			$state_name=$row_emp_details['state'];
			
			$sql_order_details = "SELECT sku_code, qty, sale_rate, amount,mrp_code,TD,UOM FROM order_details WHERE order_no = '".$order_no."'";
			$res_order_details = mysql_query($sql_order_details);
			while($row_order_details = mysql_fetch_array($res_order_details)){
				$sku_code = $row_order_details['sku_code'];
				$qty = $row_order_details['qty'];
				$sale_rate = $row_order_details['sale_rate'];
				$amount = $row_order_details['amount'];
				$UOM1 = $row_order_details['UOM1'];
				$mrp_code = $row_order_details['mrp_code'];
				$TD = $row_order_details['TD'];
				$UOM_order = $row_order_details['UOM'];
				
				
				if(mrp=='yes')
				{
					$sqlmrpval="SELECT mrp FROM mrp WHERE mrp_code='".$mrp_code."'";
					$rsmrpval=mysql_query($sqlmrpval);
					$rowmrpval=mysql_fetch_array($rsmrpval);
					$mrp_salerate_val=$rowmrpval['mrp'];
				}
				else
				{
				   $mrp_salerate_val=$sale_rate;
				}
				$amount=$mrp_salerate_val*$qty;
				if($TD > 0)
				{
					$amount=($amount-(($amount*$TD)/100));
				}
				
				$sql_sku_name = "SELECT dns_prod_code, product_group_code, prod_desc,alias FROM product_master WHERE prod_code = '".$sku_code."'";
				$res_sku_name = mysql_query($sql_sku_name);
				$row_sku_name = mysql_fetch_array($res_sku_name);
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
						<td align=\"right\">".$qty."</td>
						<td align=\"right\">".$mrp_salerate_val."</td>
						<td align=\"right\">".$amount."</td>
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

mysql_close($link);
?>