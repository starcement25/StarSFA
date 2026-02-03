<?php
error_reporting(0);
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	
?>

<?php
$branch_code = $_REQUEST['branch_code'];
$product_group_code = $_REQUEST['product_group_code'];
$sql_prodgroup_name = "SELECT product_group_name FROM product_group_master WHERE product_group_code=".$product_group_code;
$res_prodgroup_name = mysql_query($sql_prodgroup_name);
$row_prodgroup_name = mysql_fetch_array($res_prodgroup_name);
$prodgroupname = $row_prodgroup_name['product_group_name'];

if($_SESSION['admin_login'] != 'admin' && $_SESSION['admin_login'] != 'supervisor')
{
 $sql_employee_branch_code = "SELECT emp_name FROM employee_master WHERE emp_code = '$_SESSION[admin_login]' AND FIND_IN_SET( ".$branch_code.", branch_code )";
$res_employee_branch_code = mysql_query($sql_employee_branch_code);
$total_emp_rows = mysql_num_rows($res_employee_branch_code);
}
else
$total_emp_rows = 1;

if($total_emp_rows>0)
{
	$branch_name_array = array();
$sql_product_details = "SELECT PM.prod_desc, SMR.sale_rate, BM.branch_name FROM product_master PM, sauda_mrp SMR, branch_master BM WHERE PM.product_group_code IN ($product_group_code) AND PM.branch_code IN ($branch_code) AND PM.prod_code = SMR.product_code AND PM.branch_code = SMR.branch_code AND PM.acedns='Y' AND PM.branch_code=BM.branch_code ORDER BY BM.branch_name ASC";
$res_product_details = mysql_query($sql_product_details);
$total_rows = mysql_num_rows($res_product_details);

if($total_rows>0)
{ ?>
	<table class="border" width="100%" border="1" style="border-collapse:collapse;" cellpadding="5px">
      <tr>
        <td colspan="3" class="TDHEAD" align="center">Product Sale Rate - <?php echo $prodgroupname; ?></td>
      </tr>
      <tr class="TDHEAD_SUB">
      	<td>SI</td>
        <td>Product Description</td>
        <td>Sale Rate</td>
      </tr>
      <?php
	/*$sql_select_branch_name = "SELECT branch_name FROM branch_master WHERE branch_code = '$branch_code'";
	$res_select_branch_name = mysql_query($sql_select_branch_name);
	$row_select_branch_name = mysql_fetch_array($res_select_branch_name);*/

	$count = 1;
	$res_product_details = mysql_query($sql_product_details);
	while($row_product_details = mysql_fetch_array($res_product_details))
	{
		$branch_name = $row_product_details['branch_name'];
		if(!in_array($branch_name, $branch_name_array)){
			array_push($branch_name_array,$branch_name);
			echo "<tr>
        			<td colspan=\"3\" class=\"TDHEAD\" align=\"center\">Product Sale Rate - $branch_name</td>
      			</tr>";
			
		}
		echo "<tr>
				<td>".$count."</td>
				<td>".$row_product_details['prod_desc']."</td>
				<td align=\"right\">".number_format($row_product_details['sale_rate'],2)."</td>
		</tr>";
		$count++;
	}
	echo "</table>";
}
else
{
	echo "<strong><font color=\"red\">No records</font></strong>";
}
}
else
{
	echo "<font color=\"red\"><strong>You are not tagged with this branch</strong></font>";
}
mysql_close($link);
?>
