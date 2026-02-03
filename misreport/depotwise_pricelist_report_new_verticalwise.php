<?php
ob_start();
	session_start();
	if(strtoupper($_SESSION['admin_login'])=='ADMIN' ||strtoupper($_SESSION['admin_login'])=='SUPERVISOR' || strtoupper($_SESSION['admin_login'])=='E0042' ||strtoupper($_SESSION['admin_login'])=='E0076'){
		require("adminUtils.php");
	}
	else
	{
		require("adminUtils_HBC_SFATS.php");
	}
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
	if(strtoupper($_SESSION['admin_login'])=="ADMIN" || strtoupper($_SESSION['admin_login'])=="SUPERVISOR" || strtoupper($_SESSION['admin_login'])=="SYSTEM" 
		|| strtoupper($_SESSION['admin_login'])=="PRICEHBC" || strtoupper($_SESSION['admin_login'])=="PRICESFATS"  
		|| strtoupper($_SESSION['admin_login'])=="HBC" || strtoupper($_SESSION['admin_login'])=="SFATS" || strtoupper($_SESSION['admin_login'])=="ED01"){
		$branchcode='';
		
		if(strtoupper($_SESSION['admin_login'])=="ADMIN" || strtoupper($_SESSION['admin_login'])=="SUPERVISOR" || strtoupper($_SESSION['admin_login'])=="SYSTEM" ){
			$condition='';
		}
		if(strtoupper($_SESSION['admin_login'])=="PRICEHBC" || strtoupper($_SESSION['admin_login'])=="PRICESFATS" || strtoupper($_SESSION['admin_login'])=="HBC" || strtoupper($_SESSION['admin_login'])=="SFATS" || strtoupper($_SESSION['admin_login'])=="ED01")
		{
			$condition='WHERE '.$acronym.'.vertical_value ="'.$_SESSION['vertical_value'].'"';
		}
	}
	else
	{
		//$condition="AND ".$acronym.".vertical_value ='".$_SESSION['vertical_value']."'";
		$condition='WHERE '.$acronym.'.vertical_value ="'.$_SESSION['vertical_value'].'"';
		$sql_branch=mysqli_fetch_assoc(mysqli_query($link,"SELECT branch_code FROM employee_master WHERE emp_code='".$_SESSION['admin_login']."'"));
		$bcode=str_replace(",","','",$sql_branch['branch_code']);
		$branchcode="AND branch_code IN('$bcode')";
	}
	?>
	<center>
	<br /><br />
  <form name ="frmSearch" method="post" action="<?=$_SERVER['PHP_SELF']?>" >
	<table border="1" width="60%" class="border" style="border-collapse:collapse;" cellpadding="5px;">
	   <tr class="TDHEAD_SUB" >
            <td align="center">Choose Date:
                    <?php $from_date=$_REQUEST['from_date'];?>
                  <input type="text" value="<?php echo str_replace('/','-',$from_date);?>" name="from_date" id="from_date"></input>&nbsp;
                    <a href="javascript:cal5.popup();"><img style="cursor:hand;position:absolute;bsauda:0;" bsauda="0" src="images/cal.gif" 
                    width="20" height="18" ></a>
                </label>
                <script language="JavaScript" type="text/javascript">
                    <!-- // create calendar object(s) just after form tag closed
                     // specify form element as the only parameter (document.forms['formname'].elements['inputname']);
                     // note: you can have as many calendar objects as you need for your application
                    var cal5 = new calendar3(document.forms['frmSearch'].elements['from_date']);
                    cal5.year_scroll = true;
                    cal5.time_comp = false;
                    //-->
                </script>
            </td>
       </tr>     
      <tr class="TDHEAD_SUB">
		<td align="center">
			<select name="branch" id="branch">
				<option selected value="">Select Depot</option>
				<?php
					$sql_select_depot = "SELECT * FROM branch_master WHERE acedns='Y' $branchcode ORDER BY branch_name ASC";
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
					$sql_select_product_group = "SELECT $group_code, $group_name FROM $sauda_table_value $acronym  $condition
												ORDER BY PGM.product_group_name ASC";
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
			<input name="submit" type="button" value="Submit" onclick="return_result(branch.value, product_group.value,from_date.value);" />
		</td>
	  </tr>
   </table>
   </form>
	<br />
	
	<div id="display" style="max-height: 400px; max-width: 1000px;  overflow-y: scroll; overflow-x: scroll; " align="center">
	<img src="ajax-loader.gif" id="ajaxloader" hidden>
	</div>
    <br />
    <div style="width:60%;" align="right"><span style="display:none;" id="ndenotes"><b><font color="#FF0000">* N Denotes For Discontinued Product</font></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
	</center>
	
	<script>
	function return_result(branch_code, product_group_code,from_date)
	{
		//alert(branch_code+product_group_code);
		if(document.getElementById("from_date").value.search(/\S/) == -1)
		{
			alert("Please choose date");
		}
		else if(document.getElementById("branch").value.search(/\S/) == -1 || document.getElementById("product_group").value.search(/\S/) == -1)
		{
			alert("Select both fields");
		}
		else
		{
			document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
			GenericAjaxFunction('depotwise_pricelist_data_new_verticalwise.php?branch_code='+branch_code+'&product_group_code='+product_group_code+'&from_date='+from_date,'display',0);
			document.getElementById('ndenotes').style.display='';
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
		document.body.appendChild(a);
        a.click();
		document.body.appendChild(a);
        //just in case, prevent default behaviour
        e.preventDefault();
}

	</script>
	<?php
}
?>
