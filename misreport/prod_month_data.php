<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

//$months = array ('Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec','Jan','Feb','Mar');
/*------------------------------> Select of month (financial year)<-------------------------------*/
$current_month = date('m');
if($current_month == '01' || $current_month == '02' || $current_month == '03'){
	$previous_year = date('Y', strtotime('-1 year'));
	$current_year = date('Y');
	$months = array ('Apr-'.$previous_year.'','May-'.$previous_year.'','Jun-'.$previous_year.'','Jul-'.$previous_year.'','Aug-'.$previous_year.'','Sep-'.$previous_year.'','Oct-'.$previous_year.'','Nov-'.$previous_year.'','Dec-'.$previous_year.'','Jan-'.$current_year.'','Feb-'.$current_year.'','Mar-'.$current_year.'');
}
else{
	$previous_year = date('Y');
	$current_year = date('Y', strtotime('+1 year'));
	$months = array ('Apr-'.$previous_year.'','May-'.$previous_year.'','Jun-'.$previous_year.'','Jul-'.$previous_year.'','Aug-'.$previous_year.'','Sep-'.$previous_year.'','Oct-'.$previous_year.'','Nov-'.$previous_year.'','Dec-'.$previous_year.'','Jan-'.$current_year.'','Feb-'.$current_year.'','Mar-'.$current_year.'');
}

/*------------------------------> Month Color <-------------------------------*/
function month_color($month){
	if($month == 'Jan'  || $month == 'Jul')
		$month_color = '#FFC125';
			if($month == 'Feb' || $month == 'Aug')
				$month_color = '#CDAA7D';
					if($month == 'Mar' || $month == 'Sep')
						$month_color = '#BFEFFF';
							if($month == 'Apr' || $month == 'Oct')
								$month_color = '#FA8072';
									if($month == 'May' || $month == 'Nov')
										$month_color = '#F0E68C';
											if($month == 'Jun' || $month == 'Dec')
												$month_color = '#AB82FF';
	return $month_color;
				
}
/*------------------------------> Customer code according to customer name <-------------------------------*/
$emp_code = $_REQUEST['emp_code'];
$cust_name = $_REQUEST['cust_name'];
if($emp_code != '' && $cust_name != ''){
	$sql_customer = "SELECT customer_code FROM customer_master WHERE customer_name LIKE '%".$cust_name."%'";
	$res_customer = mysqli_query($link,$sql_customer);
	while($row_customer = mysqli_fetch_assoc($res_customer)){
		$customer_string .= "'".$row_customer['customer_code']."',";
	}
	$customer_string = rtrim($customer_string,",");
	
	$emp_hierarchy = return_employee_hierarchy($emp_code);
	$condition = " AND SUBSTRING(POCM.order_no,2,5) IN (".$emp_hierarchy.") AND POCM.customer_code IN (".$customer_string.") ";
}
else{
	if($_SESSION['admin_login'] == 'admin')
		$condition = "";
	else{
		$emp_hierarchy = return_employee_hierarchy($_SESSION['admin_login']);
		$condition = " AND SUBSTRING(POCM.order_no,2,5) IN (".$emp_hierarchy.") ";
	}
}
/*------------------------------> Check if data exist <-------------------------------*/
$sql_check_record = "SELECT * FROM prev_order_counting_master POCM WHERE SUBSTRING(POCM.visit_date,1,4)='".$previous_year."'".$condition;
$res_check_record = mysqli_query($link,$sql_check_record);
$total_record_check = mysqli_num_rows($res_check_record);
if($total_record_check>0){
?>
<table border="1" width="100%" style="border-collapse:collapse; border-color:#663300;" cellpadding="4">
  <tr align="center" style="font-weight:bold; font-style:italic;">
  	<td bgcolor="#99CC66">&nbsp;</td>
<?php
/*------------------------------> Table first row creation (month)<-------------------------------*/
foreach($months as $value){
	$month_year = explode("-",$value);
	
	$sql_date_count = "SELECT DISTINCT SUBSTRING(POCM.visit_date,1,10) FROM prev_order_counting_master POCM WHERE SUBSTRING(POCM.visit_date,1,4)='".$month_year[1]."' AND SUBSTRING(POCM.visit_date,6,2)='".date('m',strtotime(''.$month_year[0].''))."'".$condition;
	$res_date_count = mysqli_query($link,$sql_date_count);
	$total_date_count = mysqli_num_rows($res_date_count);
	$month_color = month_color($month_year[0]);
	echo "<td colspan='$total_date_count' bgcolor=\"".$month_color."\">$month_year[0]</td>";
}
?>
</tr>
<tr align="center" class="TDHEAD_SUB">
  <td>SKU</td>
<?php
/*---------------------------> Table second row creation (dates according to months)------------------------------*/
	foreach($months as $value){
		$month_year = explode("-",$value);
		$sql_date_count = "SELECT DISTINCT SUBSTRING(POCM.visit_date,1,10) as visit_date FROM prev_order_counting_master POCM WHERE SUBSTRING(POCM.visit_date,1,4)='".$month_year[1]."' AND SUBSTRING(POCM.visit_date,6,2)='".date('m',strtotime(''.$month_year[0].''))."'".$condition;
		$res_date_count = mysqli_query($link,$sql_date_count);
		$total_rows = mysqli_num_rows($res_date_count);
		if($total_rows>0){
			$res_date_count = mysqli_query($link,$sql_date_count);
			while($row_date_count = mysqli_fetch_assoc($res_date_count)){
				$visit_date = date('d-m-Y',strtotime(''.$row_date_count['visit_date'].''));
				echo "<td>".$visit_date."</td>";
			}
		}
		else{
			echo "<td></td>";
		}
	}
?>
</tr>
<?php
/*------------------------------> Select of product group <-------------------------------*/
$product_group_array = array();
foreach($months as $value){
	$month_year = explode("-",$value);
	$sql_product_group_code = "SELECT DISTINCT PGM.product_group_code as product_group_code, PGM.product_group_name as product_group_name FROM product_group_master PGM, prev_order_counting_master POCM, product_master PM WHERE POCM.product_code=PM.prod_code AND PM.product_group_code=PGM.product_group_code AND SUBSTRING(visit_date,1,4)='".$month_year[1]."' AND SUBSTRING(visit_date,6,2)='".date('m',strtotime(''.$month_year[0].''))."'".$condition." ORDER BY PGM.product_group_name";
	$res_product_group_code = mysqli_query($link,$sql_product_group_code);
	while($row_product_group_code = mysqli_fetch_assoc($res_product_group_code)){
		$product_group_code = $row_product_group_code['product_group_code'];
		$product_group_name = $row_product_group_code['product_group_name'];
		$product_group_array[$product_group_code] = $product_group_name;
	}
}
/*------------------------------> Assign color according to group <-------------------------------*/
foreach($product_group_array as $index=>$val){
	if($index == 'BR1')
		$color = '#FFF68F';
			if($index == 'BR2')
				$color = '#7D9EC0';
					if($index == 'BR3')
						$color = '#FFEC8B';
							if($index == 'BR4')
								$color = '#C1FFC1';
									if($index == 'BR5')
										$color = '#AB82FF';
											if($index == 'BR6')
												$color = '#8E8E38';
													if($index == 'BR7')
														$color = '#71C671';
															if($index == 'BR8')
																$color = '#FFE4B5';
	/*------------------> SKU selection according to product group (creates several rows) <------------------------*/
	$sql_product = "SELECT DISTINCT PM.prod_desc, POCM.product_code FROM prev_order_counting_master POCM, product_master PM WHERE POCM.product_code=PM.prod_code AND PM.product_group_code='".$index."'".$condition;
	$res_product = mysqli_query($link,$sql_product);
	while($row_product = mysqli_fetch_assoc($res_product)){
		$prod_name = $row_product['prod_desc'];
		$product_code = $row_product['product_code'];
		
		echo "<tr><td style=\"background:$color;\">$prod_name</td>";
	
	/*------------------------------> Get quantity of SKU <-------------------------------*/	
		foreach($months as $value){
		$month_year = explode("-",$value);
		$sql_date_count = "SELECT DISTINCT SUBSTRING(POCM.visit_date,1,10) as visit_date FROM prev_order_counting_master POCM WHERE SUBSTRING(POCM.visit_date,1,4)='".$month_year[1]."' AND SUBSTRING(POCM.visit_date,6,2)='".date('m',strtotime(''.$month_year[0].''))."'".$condition;
		$res_date_count = mysqli_query($link,$sql_date_count);
		$total_rows = mysqli_num_rows($res_date_count);
		if($total_rows>0){
			$res_date_count = mysqli_query($link,$sql_date_count);
			while($row_date_count = mysqli_fetch_assoc($res_date_count)){
				$visit_date = $row_date_count['visit_date'];
				
				$sql_prod_qty = "SELECT SUM(visit_qty) as tot_qty FROM prev_order_counting_master WHERE SUBSTRING(visit_date,1,10) = '".$visit_date."' AND product_code = '".$product_code."'";
				$res_prod_qty = mysqli_query($link,$sql_prod_qty);
				$tot_rows = mysqli_num_rows($res_prod_qty);
				if($tot_rows>0){
					$res_prod_qty = mysqli_query($link,$sql_prod_qty);
					$row_prod_qty = mysqli_fetch_assoc($res_prod_qty);
					$tot_qty = $row_prod_qty['tot_qty'];
					if($tot_qty!='')
					echo "<td align=\"right\">".$tot_qty."</td>";
					else
					echo "<td align=\"right\">--</td>";
				}
				else{
				echo "<td>--</td>";
				}
			}
		}
		else{
			echo "<td>--</td>";
		}
		
	}
	echo "</tr>";	
	}
}
?>
</table>
<?php } else { echo "<font color=\"red\"><strong>No records found</strong></font>";}
mysqli_close($link);
?>