<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
?>

<?php
if($_POST['mode']=="edit")
{
	disphtml("edit_saudadetails();");
}
else if($_POST['mode']=="add")
{
	disphtml("add_saudadetails();");
}
else 
{
	disphtml("main();");
}
ob_end_flush();


function add_saudadetails()
{
	$sql_insert_sauda_allocation = "INSERT INTO sauda_allocation SET 
															   emp_code = '$_POST[emp]', 
													product_filter_code = '$_POST[product_group]', 
																	qty = '$_POST[quantity]'";
	$res_insert_sauda_allocation = mysql_query($sql_insert_sauda_allocation);
	
	$sql_insert_sauda_log = "INSERT into sauda_allocation_log SET 
															   emp_code = '$_POST[emp]', 
													product_filter_code = '$_POST[product_group]', 
																	qty = '$_POST[quantity]'";
	$res_insert_sauda_log = mysql_query($sql_insert_sauda_log);
	
	header('location:saudaallocation.php?insert=success');
}

function edit_saudadetails()
{
	$sql_update_sauda_allocation = "UPDATE sauda_allocation SET 
														emp_code = '$_POST[emp]', 
											 product_filter_code = '$_POST[product_group]', 
															 qty = '$_POST[quantity]'
									WHERE emp_code = '$_GET[emp_code]' AND product_filter_code = '$_GET[product_code]';";
	$res_update_sauda_allocation = mysql_query($sql_update_sauda_allocation);
	
	$sql_insert_sauda_log = "INSERT into sauda_allocation_log SET 
															   emp_code = '$_POST[emp]', 
													product_filter_code = '$_POST[product_group]', 
																	qty = '$_POST[quantity]'";
	$res_insert_sauda_log = mysql_query($sql_insert_sauda_log);
	
	$condition = $_POST['condition'];
	
	header('location:saudaallocation.php?update=success&condition='.$condition.'');
}


function main()
{
date_default_timezone_set("Asia/Kolkata"); 
$sauda_date = date('d-m-Y');
$today = date('Y-m-d');

$today = str_replace("-","",$today);

$sql_sauda_filter = "SELECT sauda_allocation_basedon_filter FROM acedns_acednsproduct.product_details WHERE nick_name='$_SESSION[nick_name]'";
$res_sauda_filter = mysql_query($sql_sauda_filter);
$row_sauda_filter = mysql_fetch_array($res_sauda_filter);

$sauda_filter_value = $row_sauda_filter['sauda_allocation_basedon_filter'];

if($sauda_filter_value == 1)
{
	$sauda_table_value = 'product_group_master';
	$field_name1 = 'product_group_code';
	$field_name2 = 'product_group_name';
	$acronym = "PGM";
}
else if($sauda_filter_value == 2)
{
	$sauda_table_value = 'product_sub_group_master';
	$field_name1 = 'product_sub_group_code';
	$field_name2 = 'product_sub_group_name';
	$acronym = "PSGM";
}
else if($sauda_filter_value == 3)
{
	$sauda_table_value = 'product_brand_master';
	$field_name1 = 'product_brand_code';
	$field_name2 = 'product_brand_name';
	$acronym = "PBM";
}
else if($sauda_filter_value == 4)
{
	$sauda_table_value = 'product_master';
	$field_name1 = 'product_code';
	$field_name2 = 'product_name';
	$acronym = "PM";
}

$group_name = $acronym.".".$field_name2;
$group_code = $acronym.".".$field_name1;

if($_POST['product_name'] != '' && $_POST['employee_name'] == '')
	$condition = " AND $group_name LIKE '%$_POST[product_name]%'";
else if($_POST['product_name'] == '' && $_POST['employee_name'] != '')
	$condition = " AND EM.emp_name LIKE '%$_POST[employee_name]%'";
else if($_POST['product_name'] != '' && $_POST['employee_name'] != '')
	$condition = " AND $group_name LIKE '%$_POST[product_name]%' AND EM.emp_name LIKE '%$_POST[employee_name]%'";
else if($_GET)
	$condition = $_GET['condition'];
else
	$condition = "";
	
echo $sql_sauda_details = "SELECT EM.emp_code, EM.emp_name, $group_name, $group_code, SA.emp_code, SA.product_filter_code, SA.qty FROM employee_master EM, sauda_allocation SA, $sauda_table_value $acronym WHERE EM.emp_code=SA.emp_code AND SA.product_filter_code=$group_code".$condition." ORDER BY $group_name ASC, EM.emp_name ASC";
$res_sauda_details = mysql_query($sql_sauda_details);

echo "<center>";

echo "<div style=\"height:30px; width:90%; text-align:center;\"><form name=\"search_data\" method=\"POST\" action=\"\" onsubmit=\"return search_validate();\">Product:<input class=\"INPUT\" type=\"text\" name=\"product_name\" value=\"$_POST[product_name]\" />&nbsp;&nbsp;Employee:<input class=\"INPUT\" type=\"text\" name=\"employee_name\" value=\"$_POST[employee_name]\" />&nbsp;&nbsp;<input type=\"submit\" class=\"inplogin\" name=\"search\" value=\"Search\" /></form>&nbsp;&nbsp;</div>
<div style=\"height:30px; width:80%; text-align:right;\"><input type=\"submit\" class=\"inplogin\" name=\"submit\" value=\"Add\" onclick=\"show_form();\"></div>";

echo "<div id=\"display\" style=\"max-height: 300px; width:80%; overflow-y: scroll;\">";

echo "<div style=\"position:fixed;  width:inherit;\">";

echo "<div style=\"position:relative; width:100%;\">";
echo "<table style=\"border-collapse:collapse; width:100%;\" class=\"border\">
		<tr>
			<td class=\"TDHEAD\" align=\"center\"><b>Sauda Details&nbsp;&nbsp;$sauda_date</b></td>
		</tr>
	  </table>";
echo "</div>";

echo "<div style=\"position:relative; width:100%;\">
		<table style=\"border-collapse:collapse; width:100%;\" class=\"border\">  
		<tr class=\"TDHEAD_SUB\">
			<td style=\"width:20%;\"><b>Name</b></td>
			<td style=\"width:20%;\"><b>Product Type</b></td>
			<td style=\"width:20%;\"><b>Quantity Alloted</b></td>
			<td style=\"width:20%;\"><b>Quantity Booked</b></td>
			<td style=\"width:20%;\"></td>
		</tr>
		</table>
	  </div>";
	  
echo "</div>";

echo "<br><br>";

echo "<table border=\"1\" style=\"border-collapse:collapse; width:100%;\" class=\"border\">";

while($row_sauda_details = mysql_fetch_array($res_sauda_details))
{
	$sql_quantity_booked = "SELECT sum(OD.qty) FROM order_details OD, product_master PM WHERE substring(OD.order_no,2,5)='$row_sauda_details[emp_code]' AND substring(OD.order_no,7,8)='$today' AND OD.sku_code=PM.prod_code AND PM.$field_name1='$row_sauda_details[$field_name1]'";
	$res_quantity_booked = mysql_query($sql_quantity_booked);
	$row_quantity_booked = mysql_fetch_array($res_quantity_booked);
	$quantity_booked = $row_quantity_booked['sum(OD.qty)'];
	echo "<tr>
			<td style=\"width:20%; height:22px;\">$row_sauda_details[emp_name]</td>
			<td style=\"width:20%\">$row_sauda_details[$field_name2]</td>
			<td style=\"width:20%\">$row_sauda_details[qty]</td>
			<td style=\"width:20%\">$quantity_booked</td>
			<td style=\"width:20%;\"><a href=\"saudaallocation.php?emp_code=$row_sauda_details[emp_code]&product_code=$row_sauda_details[$field_name1]&condition=$condition\" style=\"color:blue;\">Edit</a></td>
		  </tr>";
}
echo "</table>";

echo "</div>";

echo "<center>";

echo "<br><br>";


if($_GET['emp_code'])
{
	$sql_get_details = "SELECT * FROM sauda_allocation WHERE emp_code = '$_GET[emp_code]' AND product_filter_code = '$_GET[product_code]'";
	$res_get_details = mysql_query($sql_get_details);
	$row_get_details = mysql_fetch_array($res_get_details);
}
?>

<script>
function validate()
{
	//alert("Hello");
	var emp = document.sauda_allocation.emp.value;
	if(document.sauda_allocation.emp.value.search(/\S/) == -1)
	{
		alert("Select employee");
		return false;
	}
	
	var product_group = document.sauda_allocation.product_group.value;
	if(document.sauda_allocation.product_group.value.search(/\S/) == -1)
	{
		alert("Select Product Type");
		return false;
	}
	
	var quantity = document.sauda_allocation.quantity.value;
	if(document.sauda_allocation.quantity.value.search(/\S/) == -1)
	{
		alert("Select Quantity");
		return false;
	}
	
	if(document.getElementById("emp_code").value != "")
		document.getElementById("mode").value = 'edit';
	else
		document.getElementById("mode").value = 'add';
	
	return true;
}

function search_validate()
{
	var product_name = document.search_data.product_name.value;
	var employee_name = document.search_data.employee_name.value;
	if(document.search_data.product_name.value.search(/\S/) == -1 && document.search_data.employee_name.value.search(/\S/) == -1)
	{
		alert("Enter either or both data");
		return false;
	}
	return true;
}
</script>

<center>
<form name="sauda_allocation" id="sauda_allocation" method="POST" action="" onsubmit="return validate();" hidden>
<input type="hidden" name="emp_code" id="emp_code" value="<?php echo $row_get_details['emp_code'];  ?>" />
<input type="hidden" name="mode" id="mode" value="" />
<input type="hidden" name="condition" value="<?php echo $condition;?>" />
<table border="1" width="600" style="border-collapse:collapse;" class="border" cellpadding="5px">
	<tr class="TDHEAD">
    	<td colspan="2" align="center"><b>Sauda Allocation</b><div style="float:right;"><a href="saudaallocation.php"><img src="close.png" width="25" height="25" /></a></div></td>
    </tr>
    <tr>
    	<td>Select Employee</td>
        <td>
        	<select name="emp" id="emp">
            	<option value="">Select</option>
            	<?php
					$sql_select_emp = "SELECT emp_code, emp_name FROM employee_master";
					$res_select_emp = mysql_query($sql_select_emp);
					while($row_select_emp = mysql_fetch_array($res_select_emp))
					{
						if($row_select_emp['emp_code'] == $row_get_details['emp_code'])
							echo "<option value=\"$row_select_emp[emp_code]\" selected>$row_select_emp[emp_name]</option>";
						else
							echo "<option value=\"$row_select_emp[emp_code]\">$row_select_emp[emp_name]</option>";
					}
					
				?>
            </select>
        </td>
    </tr>
    <tr>
    	<td>Select Product Type</td>
        <td>
        	<select name="product_group" id="product_group">
            	<option value="">Select</option>
        	<?php
				$sql_select_product_group = "SELECT $field_name1, $field_name2 FROM $sauda_table_value";
				$res_select_product_group = mysql_query($sql_select_product_group);
				while($row_select_product_group = mysql_fetch_array($res_select_product_group))
				{
					if($row_select_product_group[$field_name1] == $row_get_details['product_filter_code'])
						echo "<option value=\"$row_select_product_group[$field_name1]\" selected>$row_select_product_group[$field_name2]</option>";
					else
						echo "<option value=\"$row_select_product_group[$field_name1]\">$row_select_product_group[$field_name2]</option>";
				}
			?>
            </select>
        </td>
    </tr>
	<tr>
		<td>Quantity</td>
		<td><input type="text" class="INPUT" name="quantity" value="<?php echo $row_get_details['qty']; ?>" /></td>
	</tr>
    <tr>
    	<td></td>
        <td align="left"><input type="submit" name="submit" value="Submit" id="submit" class="inplogin" /></td>
    </tr> 
</table>
</form>

<?php
if($_GET['insert'] == 'success')
	echo "Data inserted successfully";
	
if($_GET['update'] == 'success')
	echo "Data updated successfully";

}
?>
</center>

<script>
function show_form()
{
	document.getElementById("sauda_allocation").hidden = false;
}
<?php
if($_GET['emp_code'])
{
	echo "document.getElementById(\"sauda_allocation\").hidden = false;";
	echo "document.getElementById(\"submit\").value='Update';";
}
?>
</script>

