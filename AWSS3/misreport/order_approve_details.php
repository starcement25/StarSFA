<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

$distributor = $_REQUEST['distributor'];
$order_date=$_REQUEST['order_date'];

$order_no_array = array();
$order_no_array_one = array();

/*----> Data fetching query <----*/
$sql_order_exist_check = "SELECT  DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%d-%m-%Y') AS order_date,CM.customer_name,SUM(OD.amount) AS total_amount,CM.customer_code FROM order_header OH,customer_master CM,order_details OD WHERE OH.order_no=OD.order_no AND OH.status ='' AND OH.customer_code=CM.customer_code AND 
(OH.customer_code IN(SELECT customer_code FROM customer_master WHERE cust_type='R' AND rds_tag='".$distributor."') OR OH.customer_code='".$distributor."') AND DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%d-%m-%Y')='".$order_date."' 
GROUP BY DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%d-%m-%Y'),OH.customer_code ORDER BY DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%d-%m-%Y') DESC";
$res_order_exist_check = mysql_query($sql_order_exist_check);
$total_row_check = mysql_num_rows($res_order_exist_check);
if($total_row_check>0){
?>
<table width="70%" class="border" cellpadding="6px" border="1" style="border-collapse:collapse;">
  <tr class="TDHEAD">
  	<td width="7%">Sl no</td>
    <td width="15%">Date</td>
  	<td width="">Retailer Name</td>
	<td width="20%">Total amount</td>
    <td width="15%">Status</td>
  </tr>
<?php
	$count=1;
	$res_order_exist_check = mysql_query($sql_order_exist_check);
	while($row_order_exist_check = mysql_fetch_array($res_order_exist_check)){
		$order_date = $row_order_exist_check['order_date'];
		$order_date_without_hipen=str_replace('-','',$order_date);
		$customer_name = $row_order_exist_check['customer_name'];
		$customer_code = $row_order_exist_check['customer_code'];
		$total_amount = $row_order_exist_check['total_amount'];
		$select_control_val=$order_date_without_hipen.'-'.$customer_code;
		$select_control = "<select id=\"select_$select_control_val\" onChange=\"edit_order_details('$select_control_val');\">
							<option value=\"\">SELECT</option>
							<option value=\"APPROVED\">APPROVED</option>
							<option value=\"NOT APPROVED\">NOT APPROVED</option>
						   </select>";
		echo "<tr><td>$count</td><td>$order_date</td><td>$customer_name</td><td align='right'>".number_format($total_amount,2)."</td><td>$select_control</td></tr>";
		$count++;
	}
}
else{
	echo "<div style=\"font-weight:bold; color:red;\">No records</div>";
}
mysql_close($link);
?>