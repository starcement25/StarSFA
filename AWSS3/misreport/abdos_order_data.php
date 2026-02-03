<?php
ob_start();
session_start();
require("adminUtils.php");
require ("attribute_selection.php");
if($_SESSION['admin_login']=="")  		header("product:index.php");

disphtml("main();");

function main(){
	?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="ajax1.js"></script>
    <?php
	echo "<center>";
	$sql_order_header = "SELECT EM.emp_name, OH.order_no, OH.customer_code, DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%d-%m-%Y') AS order_date FROM order_header OH, employee_master EM WHERE OH.order_no LIKE 'O%' AND SUBSTRING(OH.order_no,2,5) = EM.emp_code ORDER BY EM.emp_name, order_date DESC LIMIT 2000,2500";
	$res_order_header = mysql_query($sql_order_header);
	$total_rows = mysql_num_rows($res_order_header);
	if($total_rows>0){
		?>
        <div id="display" style="max-height: 350px; width:100%; overflow-y: scroll;" align="center">
        <table class="border" border="1" style="border-collapse:collapse;" width="100%">
          <tr class="TDHEAD_SUB">
          	<td>Date</td>
          	<td>Employee Name</td>
          	<td>Beat Name</td>
            <td>Customer Type</td>
            <td>Customer Name</td>
            <td>Coverage Type</td>
            <td>Brand</td>
            <td>Prod Description</td>
            <td>Quantity</td>
            <td>Rate</td>
            <td>Amount</td>
          </tr>
        <?php
		$res_order_header = mysql_query($sql_order_header);
		while($row_order_header = mysql_fetch_array($res_order_header)){
			$order_no = $row_order_header['order_no'];
			$customer_code = $row_order_header['customer_code'];
			$order_date = $row_order_header['order_date'];
			$emp_name = $row_order_header['emp_name'];
			
			$sql_customer_details = "SELECT customer_name, cust_type, coverage_type, route_code FROM cust_master WHERE customer_code = '".$customer_code."'";
			$res_customer_details = mysql_query($sql_customer_details);
			$row_customer_details = mysql_fetch_array($res_customer_details);
			$customer_name = $row_customer_details['customer_name'];
			$cust_type = $row_customer_details['cust_type'];
			$coverage_type = $row_customer_details['coverage_type'];
			$route_code = $row_customer_details['route_code'];
			
			$sql_route_name = "SELECT route_name FROM route_master WHERE route_code = '".$route_code."'";
			$res_route_name = mysql_query($sql_route_name);
			$row_route_name = mysql_fetch_array($res_route_name);
			$route_name = $row_route_name['route_name'];
			
			$sql_order_details = "SELECT sku_code, qty, sale_rate, amount FROM order_details WHERE order_no = '".$order_no."'";
			$res_order_details = mysql_query($sql_order_details);
			while($row_order_details = mysql_fetch_array($res_order_details)){
				$sku_code = $row_order_details['sku_code'];
				$qty = $row_order_details['qty'];
				$sale_rate = $row_order_details['sale_rate'];
				$amount = $row_order_details['amount'];
				
				$sql_product_details = "SELECT product_group_code, prod_desc FROM product_master WHERE prod_code = '".$sku_code."'";
				$res_product_details = mysql_query($sql_product_details);
				$row_product_details = mysql_fetch_array($res_product_details);
				$product_group_code = $row_product_details['product_group_code'];
				$prod_desc = $row_product_details['prod_desc'];
				
				$sql_product_group_name = "SELECT product_group_name FROM product_group_master WHERE product_group_code = '".$product_group_code."'";
				$res_product_group_name = mysql_query($sql_product_group_name);
				$row_product_group_name = mysql_fetch_array($res_product_group_name);
				$product_group_name = $row_product_group_name['product_group_name'];
				
				echo "<tr>
						<td>".$order_date."</td>
						<td>".$emp_name."</td>
						<td>".$route_name."</td>
						<td>".$cust_type."</td>
						<td>".$customer_name."</td>
						<td>".$coverage_type."</td>
						<td>".$product_group_name."</td>
						<td>".$prod_desc."</td>
						<td align=\"right\">".$qty."</td>
						<td align=\"right\">".$sale_rate."</td>
						<td align=\"right\">".number_format($amount,2)."</td>
					  </tr>";
			}
		}
		echo "</table>";
		echo "</div>";
		?>
        <div style="width:90%;" align="right" id="print_export" ><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
        <?php
		echo "<center>";
	}
	else{
		echo "No records found";
	}
?>
<script>
function PrintElem(elem)
	{
		var displaydiv = document.getElementById("display").innerHTML;	
		Popup(displaydiv);
	   //Popup($(elem).html());
	}

	function Popup(data) 
	{
		var mywindow = window.open('', 'Order Details', 'height=400,width=600');
		mywindow.document.write('<html><head><title>Order Details</title>');
		/*optional stylesheet*/ //mywindow.document.write('<link rel="stylesheet" href="main.css" type="text/css" />');
		mywindow.document.write('</head><body >');
		mywindow.document.write(data);
		mywindow.document.write('<p align=right><b>Powered By ACEdns</b></p></body></html>');
	
		mywindow.document.close(); // necessary for IE >= 10
		mywindow.focus(); // necessary for IE >= 10
	
		mywindow.print();
		mywindow.close();
	
		return true;
	}
	
	function exporttocsv(divid)
	{
		//alert(divid);
			//getting values of current time for generating the file name
			var dt = new Date();
			var day = dt.getDate();
			var month = dt.getMonth() + 1;
			var year = dt.getFullYear();
			var hour = dt.getHours();
			var mins = dt.getMinutes();
			var postfix = day + "." + month + "." + year + "_" + hour + "." + mins;
			
			/*document.write('<div id=\'view\'>');
			document.write(view);
			document.write('<div>');*/
			//creating a temporary HTML link element (they support setting file names)*/
			var a = document.createElement('a');
			//getting data from our div that contains the HTML table
			var data_type = 'data:application/vnd.ms-excel';
			var table_div = document.getElementById('display');
			var table_html = table_div.outerHTML.replace(/ /g, '%20');
			a.href = data_type + ', ' + table_html;
			//setting the file name
			a.download = 'Order Details' + postfix + '.xls';
			//triggering the function
			a.click();
			//just in case, prevent default behaviour
			e.preventDefault();
	}
</script>
<?php	
}
?>