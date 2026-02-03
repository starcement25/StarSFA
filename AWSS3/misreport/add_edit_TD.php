<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
?>

<script>
/*function validate()
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
}*/
</script>

<?php

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
	
$sql_count_product = "SELECT distinct($group_name) FROM employee_master EM, sauda_allocation SA, $sauda_table_value $acronym WHERE EM.emp_code=SA.emp_code AND SA.product_filter_code=$group_code".$condition." ORDER BY $group_name ASC";
$res_count_product = mysql_query($sql_count_product);
$total_product = mysql_num_rows($res_count_product);

$sql_top_emp="SELECT emp_code FROM employee_master WHERE reporting_to=''";
$rs_top_emp=mysql_query($sql_top_emp);
$row_top_emp=mysql_fetch_array($rs_top_emp);
$top_emp_code=$row_top_emp['emp_code'];
if(vertical_fields=='yes'){
	$sqlempvertical="SELECT vertical_value FROM employee_master WHERE emp_code='".$_GET['emp_code']."'";
	$rsempvertical=mysql_query($sqlempvertical);
	$rowempvertical=mysql_fetch_array($rsempvertical);
	$emp_vertical_value=$rowempvertical['vertical_value'];
	$emp_vertical_value_array=explode(',',$emp_vertical_value);
	//$emp_vertical_value = "'".implode("','", $emp_vertical_value_array)."'";
	$condition_one=" AND (";
	$condition_two='';
	foreach($emp_vertical_value_array as $emp_vertical_values)
	{
		$condition_two.=" FIND_IN_SET( '".$emp_vertical_values."',vertical_value) OR";
	}
	$condition_two=substr($condition_two,0,-2);
	$condition_one.=$condition_two.")";
}
else
{
	$condition_one='';
}
?>


<input type="hidden" name="emp_code" id="emp_code" value="<?php echo $row_get_details['emp_code'];  ?>" />
<input type="hidden" name="mode" id="mode" value="" />
<input type="hidden" name="condition" value="<?php echo $condition;?>" />
<input type="hidden" name="quantity_booked" id="quantity_booked" value="<?php echo $_GET['quantity_booked']; ?>" />
<table border="1" width="600" style="border-collapse:collapse;" class="border" cellpadding="5px">
	<tr class="TDHEAD">
    	<td colspan="2" align="center"><b>TD Allocation</b><div style="float:right;"><a href="TD_main.php"><img src="close.png" width="25" height="25" /></a></div></td>
    </tr>
    <tr>
    	<td>Select Employee</td>
        <td>
        	<select name="emp" id="emp" <?php if($_GET) echo "disabled"; ?>>
            	<option value="">Select</option>
            	<?php
					$sql_select_emp = "SELECT emp_code, emp_name FROM employee_master";
					$res_select_emp = mysql_query($sql_select_emp);
					while($row_select_emp = mysql_fetch_array($res_select_emp))
					{
						if($row_select_emp['emp_code'] == $_GET['emp_code'])
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
        	<select name="product_group" id="product_group" <?php if($_GET) echo "onchange=\"call_ajax();\""; ?>>
            	<option value="">Select</option>
        	<?php
				$sql_select_product_group = "SELECT $field_name1, $field_name2 FROM $sauda_table_value WHERE 1 ".$condition_one." ORDER BY $field_name2 ASC";
				$res_select_product_group = mysql_query($sql_select_product_group);
				while($row_select_product_group = mysql_fetch_array($res_select_product_group))
				{
					echo "<option value=\"$row_select_product_group[$field_name1]\">$row_select_product_group[$field_name2]</option>";
				}
			?>
            </select>
        </td>
    </tr>
	<tr>
		<td>TD</td>
		<td>
        <input type="text" class="INPUT" name="quantity" id="quantity" value=""/>
		</td>
	</tr>
    <tr>
    		
    	<td></td>
        <td align="left">
        
        <?php
		if($_GET)
		{
		?>	
			<input type="submit" name="update" value="Update" id="update" class="inplogin"  <?php if(strtoupper($_SESSION['admin_login'])!='ADMIN' && $_SESSION['admin_login']!=$top_emp_code){?>onclick="updatedata();"<?php }else{?>onclick="updatedata();"<?php }?> />
        <?php   
			echo "&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"submit\" name=\"finish\" value=\"Finish\" id=\"finish\" class=\"inplogin\" onclick=\"finish();\" />";
		}
		else
		echo "<input type=\"submit\" name=\"submit\" value=\"Submit\" id=\"submit\" class=\"inplogin\" onclick=\"submitdata();\" />";
		?>
        </td>
    </tr> 
</table>
<?php
mysql_close($link);
?>