<?php
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
?>

<?php

if(!$_GET)
	disphtml("main();");
	

?>

<?php
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

$sql_count_product = "SELECT distinct($group_name) FROM $sauda_table_value $acronym ORDER BY $group_name ASC";
$res_count_product = mysql_query($sql_count_product);
$total_product = mysql_num_rows($res_count_product);
$td_width = round(70/$total_product,2);
?>
<head>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script type="text/javascript" src="ajax1.js"></script>
<script src="js/jquery-1.9.1.js"></script>
<script type="text/javascript" src="js/jquery.freezeheader.js"></script>
<link href="css/style.css" rel="stylesheet" type="text/css">
</head>

<body>
<center>
<div style="height:30px; width:90%; text-align:center;">
Employee:<input class="INPUT" type="text" name="employee_name" id="employee_name" value="" />&nbsp;&nbsp;
<input type="submit" class="inplogin" name="search" value="Search" onClick="search_record(employee_name.value);" />
</div>
<div style="height:30px; width:90%; text-align:right;"><input type="submit" class="inplogin" name="submit" value="Add" onClick="GenericAjaxFunction('add_edit_sauda.php','edit_form',0);"></div>
<div style="height:30px; width:90%; text-align:right;"><b>* all measurements are in MT</b></div>
</center>
<div id="display" style="width:100%; padding:100px;"></div><br />
<center><div id="edit_form" style="width:60%;"></div></center>

<script>
$(document).ready(function () {
	$("#display").html('<table id="demo" width="800px" border="1" style="border-collapse:collapse;"><thead><tr><th colspan="<?php echo (($total_product*2)+3); ?>" class="TDHEAD" align="center"><b>Sauda Details&nbsp;&nbsp;<?php echo $sauda_date; ?></b></th></tr><tr class="TDHEAD_SUB" align="center"><th rowspan="2"><b>Employee</b></th><?php $res_count_product = mysql_query($sql_count_product); while($row_count_product = mysql_fetch_array($res_count_product))echo "<th align=\"center\"><b>$row_count_product[$field_name2]</b></th>"; ?><th rowspan="2">Edit</th></tr><tr class="TDHEAD_SUB" align="center"><?php for($i=1;$i<=$total_product;$i++) echo "<th align=\"center\"><b>Alloted</b></th>"; ?></tr></thead>');
	$("#demo").freezeHeader({ 'height': '300px' });
	$.ajax({url: "saudatest-bkup.php", success: function(result){
			$("#demo").append(result);
		}});
	$("#demo").append('</table>');
	})
	
function show_data(quantity,emp_code,product_code,quantity_booked,old_quantity)
{
	//alert(quantity+" "+emp_code+" "+product_code+" "+quantity_booked+" "+old_quantity);
	//var qty = document.getElementById("quantity").value;
	//alert(qty);
	if($('#'+quantity+'').html()<quantity_booked && $('#'+quantity+'').html()!='')
	{
		$('#'+quantity+'').html(old_quantity);
		alert("Allocation Quantity cannot be less than Booked Quantity")
	}
	
	else
	{
		if($('#'+quantity+'').html()!='')
		{
			$.post("sauda_update.php",
			{
				emp_code: emp_code,
				quantity: $('#'+quantity+'').html(),
				product_code: product_code,
				quantity_booked: quantity_booked
			},
			function(data, status){
				if(data.search('Allot') != -1)
					$('#'+quantity+'').html(old_quantity);
				alert("Data: " + data);
			});
		}
	}
}

function submitdata()
{
	var employee_code = document.getElementById("emp").value;
	var product_group_code = document.getElementById("product_group").value;
	var quantityalloted = document.getElementById("quantity").value;
	
	if(document.getElementById("emp").value.search(/\S/) == -1 || document.getElementById("product_group").value.search(/\S/) == -1 || document.getElementById("quantity").value.search(/\S/) == -1)
	alert("Fields cannot be empty");
	else
	{
	
	$.post("sauda_add.php",
    {
		emp_code: employee_code,
        quantity: quantityalloted,
		product_code: product_group_code
    },
    function(data, status){
        alert("Data: " + data);
    });
	}
	
}

function call_ajax()
{
	var emp_code = document.getElementById("emp").value;
	var product_group = document.getElementById("product_group").value;
	//var quantity = document.getElementById("qty").value;
	
	GenericAjaxFunctionText('sauda_quantity.php?emp_code='+emp_code+'&product_code='+product_group+'','quantity',0);
}

function updatedata()
{
	var employee_code = document.getElementById("emp").value;
	var product_group_code = document.getElementById("product_group").value;
	var quant = document.getElementById("quantity").value;
	var quantity_booked = document.getElementById("quantity_booked").value;
	
	var search_empl_name = document.getElementById("employee_name").value;
		
	$.post("sauda_update.php",
    {
		emp_code: employee_code,
        quantity: quant,
		product_code: product_group_code,
		quantity_booked: quantity_booked
    },
    function(data, status){
        alert("Data: " + data);
    });
	
		
	if(search_empl_name!='')
		GenericAjaxFunction('saudatest.php?search_empl_name='+search_empl_name,'display',0);
	else
		GenericAjaxFunction('saudatest.php','display',0);
}

function finish()
{
	window.open('http://acedns.in/acednsproduct/misreport/sauda_main.php','_self');
}

function search_record(empl_name_value)
{
	GenericAjaxFunction('saudatest.php?search_empl_name='+empl_name_value,'display',0);
}

</script>

<?php
}
?>
</body>