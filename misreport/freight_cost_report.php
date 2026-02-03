<?php
ob_start();
session_start();
if(strtoupper($_SESSION['admin_login'])=='ADMIN' ||strtoupper($_SESSION['admin_login'])=='SUPERVISOR' || strtoupper($_SESSION['admin_login'])=='E0076' || strtoupper($_SESSION['admin_login'])=='GMSFATS'){
		require("adminUtils.php");
	}
	else
	{
		require("adminUtils_HBC_SFATS.php");
	}
if($_SESSION['admin_login']=="")  		header("location:index.php");
disphtml("main();");
function main(){	
	$current_date = date('Y-m-d');
	if($_SESSION['admin_login']=="admin")
	{
		$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_condition=" 1 AND EM.acedns!='N' ";
		$customer_condition=" 1 ";
		$depotcondition=" WHERE BM.acedns='Y' ";
		
	}
	else
	{
		$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_condition=" EM.emp_code IN (".$emp_hierarchy.") AND EM.acedns!='N' ";
		//$customer_condition = " CM.emp_code IN (".$emp_hierarchy.") ";
		$depotcondition = ", employee_master EM WHERE ".$emp_hierarchy_condition."AND FIND_IN_SET( BM.branch_code,EM.branch_code) AND BM.acedns='Y'";
		
		if(vertical_fields=='yes'){
			$sqlempvertical="SELECT vertical_value FROM employee_master WHERE emp_code='".$_SESSION['admin_login']."'";
			$rsempvertical=mysqli_query($link,$sqlempvertical);
			$rowempvertical=mysqli_fetch_assoc($rsempvertical);
			$emp_vertical_value=$rowempvertical['vertical_value'];
			$emp_vertical_value_array=explode(',',$emp_vertical_value);
			//$emp_vertical_value = "'".implode("','", $emp_vertical_value_array)."'";
			$condition_one=" WHERE (";
			//$condition_three=" AND (";
			$condition_two='';
			foreach($emp_vertical_value_array as $emp_vertical_values)
			{
				$condition_two.=" FIND_IN_SET( '".$emp_vertical_values."',PGM.vertical_value) OR";
			}
			$condition_two=substr($condition_two,0,-2);
			$condition_one.=$condition_two.")";
			//$condition_three .= $condition_two.")";
			$emp_cond = ", employee_master EM $condition_one AND EM.emp_code = '".$_SESSION['admin_login']."' ";
		}
	}
	
	
?>
<head>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script type="text/javascript" src="ajax1.js"></script>
    <script src="http://cdn.jsdelivr.net/webshim/1.12.4/extras/modernizr-custom.js"></script>
    <!-- polyfiller file to detect and load polyfills -->
    <script src="http://cdn.jsdelivr.net/webshim/1.12.4/polyfiller.js"></script>
    <script>
      webshims.setOptions('waitReady', false);
      webshims.setOptions('forms-ext', {types: 'date'});
      webshims.polyfill('forms forms-ext');
    </script>
    <!--<script src="tableToExcel.js"></script>-->
    <link rel="stylesheet" href="table.css" type="text/css"/>
</head>
<script>
function show_date_div()
{
	document.getElementById("date_div").hidden = false;
}

function hide_date_div()
{
	document.getElementById("date_div").hidden = true;
}

function PrintElem(elem)
{
	var displaydiv = document.getElementById("display").innerHTML;
	Popup(displaydiv);
   //Popup($(elem).html());
}

function Popup(data) 
{
	var mywindow = window.open('', 'Freight Cost Report', 'height=400,width=600');
	mywindow.document.write('<html><head>');
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
		
		//creating a temporary HTML link element (they support setting file names)
        var a = document.createElement('a');
        //getting data from our div that contains the HTML table
        var data_type = 'data:application/vnd.ms-excel';
        var table_div = document.getElementById('display');
        var table_html = table_div.outerHTML.replace(/ /g, '%20');
        a.href = data_type + ', ' + table_html;
        //setting the file name
        a.download = 'Freight Cost Report' + postfix + '.xls';
        //triggering the function
        a.click();
        //just in case, prevent default behaviour
        e.preventDefault();
}
</script>
<body>
<center>
<br>
<table width="35%" style="border-collapse:collapse;" border="1" cellpadding="4">
  <tr class="TDHEAD_SUB">
  	<td align="right">Depot:</td>
    <td align="left">
    	<select name="depot" id="depot" >
			<option value="">Select</option>
            <?php
			$sql_depot = "SELECT DISTINCT BM.branch_name, BM.branch_code FROM branch_master BM".$depotcondition." ORDER BY BM.branch_name ASC";
			$res_depot = mysqli_query($link,$sql_depot);
			while($row_depot = mysqli_fetch_assoc($res_depot)){
				echo "<option value=\"'".$row_depot['branch_code']."'\">".$row_depot['branch_name']."</option>";
				$branch_code_string .= "'".$row_depot['branch_code']."',";
			}
			$branch_code_string = rtrim($branch_code_string,",");
			?>
            <option value="<?php echo $branch_code_string; ?>">All</option>
		 </select>
    </td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td align="right">Product Group:</td>
    <td align="left">
    	<select name="prod_group" id="prod_group">
            <option value="">Select</option>
            <?php
            $sql_product_group = "SELECT  PGM.product_group_code, PGM.product_group_name FROM product_group_master PGM".$emp_cond." ORDER BY PGM.product_group_name ASC";
			$res_product_group = mysqli_query($link,$sql_product_group);
			while($row_product_group = mysqli_fetch_assoc($res_product_group)){
				echo "<option value=\"'".$row_product_group['product_group_code']."'\">".$row_product_group['product_group_name']."</option>";
				$prod_group_string .= "'".$row_product_group['product_group_code']."',";
			}
			$prod_group_string = rtrim($prod_group_string,",");
            ?>
            <option value="<?php echo $prod_group_string; ?>">All</option>
        </select>
    </td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td align="right">Date</td>
    <td align="left"><input type="date" name="start_date" id="start_date" value="" style="height:20px;" />&nbsp;<strong><font color='red'>*</font></strong></td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td></td>
    <td align="left"><input name="submit" type="button" value="Submit" id="submitdata" onClick="get_data();" ></td>
  </tr>
</table>
<br />
<div id="display" style="max-height: 440px; width:70%; overflow-y: scroll; margin-left:10px;" align="center">
</div>
<br>
<div style="width:70%;" align="right"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
</center>
</body>
<script>

function get_data(){
	var depot = document.getElementById("depot").value;
	var prod_group = document.getElementById("prod_group").value;
	var start_date = document.getElementById("start_date").value;
	
	
	if(document.getElementById("depot").value.search(/\S/) == -1 && document.getElementById("prod_group").value.search(/\S/) == -1){
		alert('Provide product group or depot or both');
		return false;
	}
	
	if(document.getElementById("start_date").value.search(/\S/) == -1){
		alert('Provide date');
		return false;
	}
	
	//alert(emp_code+" "+month);
	document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('freight_cost_data.php?depot='+depot+'&prod_group='+prod_group+'&start_date='+start_date,'display',0);
}

function show_data(){
	document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('freight_cost_data.php','display',0);
}
</script>
<?php } ?>