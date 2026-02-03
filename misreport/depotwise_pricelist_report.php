<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
?>

<?php
if(!$_GET)
{
	disphtml("main();");
}

function main()
{
?>
	<script type="text/javascript" src="ajax1.js"></script>
	<?php
	$sql_sauda_filter = "SELECT sauda_allocation_basedon_filter FROM acedns_acednsproduct.product_details WHERE nick_name='$_SESSION[nick_name]'";
	$res_sauda_filter = mysqli_query($link,$sql_sauda_filter);
	$row_sauda_filter = mysqli_fetch_assoc($res_sauda_filter);
	
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
	
	?>
	<center>
	<br /><br />
	<table border="1" width="60%" class="border" style="border-collapse:collapse;" cellpadding="5px;">
	  <tr class="TDHEAD_SUB">
		<td align="center">
			<select name="branch" id="branch">
				<option selected value="">Select Depot</option>
				<?php
					$sql_select_depot = "SELECT * FROM branch_master WHERE acedns='Y' ORDER BY branch_name ASC";
					$res_select_depot = mysqli_query($link,$sql_select_depot);
					while($row_select_depot = mysqli_fetch_assoc($res_select_depot))
					{
						echo "<option value=\"'$row_select_depot[branch_code]'\">$row_select_depot[branch_name]</option>";
						$branch_all .= "'".$row_select_depot['branch_code']."',";
					}
					$branch_all = rtrim($branch_all,",");
				?>
                <option value="<?php echo $branch_all; ?>">All</option>
			</select>
		&nbsp;&nbsp;
			<select name="product_group" id="product_group">
				<option selected value="">Select Product</option>
				<?php
					$sql_select_product_group = "SELECT $group_code, $group_name FROM $sauda_table_value $acronym";
					$res_select_product_group = mysqli_query($link,$sql_select_product_group);
					while($row_select_product_group = mysqli_fetch_assoc($res_select_product_group))
					{
						echo "<option value=\"'$row_select_product_group[$field_name1]'\">$row_select_product_group[$field_name2]</option>";
						$all .= "'".$row_select_product_group[$field_name1]."',";
					}
					$all = rtrim($all,",");
				?>
                <option value="<?php echo $all; ?>">All</option>
			</select>
		&nbsp;&nbsp;
			<input name="submit" type="button" value="Submit" onclick="return_result(branch.value, product_group.value);" />
		</td>
	  </tr>
   </table>
	<br />
	
	<div id="display" style="max-height: 400px; width:60%; overflow-y: scroll; margin-left:10px;" align="center">
	<img src="ajax-loader.gif" id="ajaxloader" hidden>
	</div>
    <br />
    <div style="width:60%;" align="right"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
	</center>
	
	<script>
	function return_result(branch_code, product_group_code)
	{
		//alert(branch_code+product_group_code);
		if(document.getElementById("branch").value.search(/\S/) == -1 || document.getElementById("product_group").value.search(/\S/) == -1)
		{
			alert("Select both fields");
		}
		else
		{
			document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
			GenericAjaxFunction('depotwise_pricelist_data.php?branch_code='+branch_code+'&product_group_code='+product_group_code,'display',0);
		}
	}
	
function PrintElem(elem)
{
	var displaydiv = document.getElementById("display").innerHTML;
	Popup(displaydiv);
   //Popup($(elem).html());
}

function Popup(data) 
{
	var mywindow = window.open('', 'Depotwise Pricelist', 'height=400,width=600');
	mywindow.document.write('<html><head><title>Depotwise Pricelist</title>');
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
        a.download = 'Depotwise Pricelist' + postfix + '.xls';
        //triggering the function
        a.click();
        //just in case, prevent default behaviour
        e.preventDefault();
}

	</script>
	<?php
}
?>
