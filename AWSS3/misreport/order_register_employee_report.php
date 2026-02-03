<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	disphtml("main();");
	
function main()
{
	if($_SESSION['admin_login']=="admin" || $_SESSION['admin_login'] == 'supervisor' || $_SESSION['admin_login'] == 'system'){
			$emp_hierarchy='';
			$emp_hierarchy_condition='';
			$emp_hierarchy_condition_one='';
	}
	else{
		$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_condition=" WHERE emp_code IN(".$emp_hierarchy.") ";
		//$emp_hierarchy_condition_one=' AND substring(SD.sauda_no,3,5) IN ('.$emp_hierarchy.')';
		if(vertical_fields=='yes'){
			$sqlempvertical="SELECT vertical_value FROM employee_master WHERE emp_code='".$_SESSION['admin_login']."'";
			$rsempvertical=mysql_query($sqlempvertical);
			$rowempvertical=mysql_fetch_array($rsempvertical);
			$emp_vertical_value=$rowempvertical['vertical_value'];
			$emp_vertical_value_array=explode(',',$emp_vertical_value);
			//$emp_vertical_value = "'".implode("','", $emp_vertical_value_array)."'";
			$condition_one=" WHERE (";
			$condition_three=" AND (";
			$condition_two='';
			foreach($emp_vertical_value_array as $emp_vertical_values)
			{
				$condition_two.=" FIND_IN_SET( '".$emp_vertical_values."',PGM.vertical_value) OR";
			}
			$condition_two=substr($condition_two,0,-2);
			$condition_one.=$condition_two.")";
			$condition_three .= $condition_two.")";
		}
	}

	$start_date = $_REQUEST['start_date'];
	$end_date = $_REQUEST['end_date'];
	$type = $_REQUEST['type'];
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
</head>

<script>

function employeewisereport(){
	if(document.getElementById("today").checked == true){
		var type = 'today';
	}
	else if(document.getElementById("mtd").checked == true){
		var type = 'mtd';
	}
	else if(document.getElementById("custom").checked == true){
		var type = 'custom';
		var start_date = document.getElementById("start_date").value;
		var end_date = document.getElementById("end_date").value;
	}
	window.open('http://acedns.in/acednsproduct/misreport/employeewisereportchanged.php?type='+type+'&start_date='+start_date+'&end_date='+end_date,'_self');
}

/*function customerwisereport(){
	if(document.getElementById("today").checked == true){
		var type = 'today';
	}
	else if(document.getElementById("mtd").checked == true){
		var type = 'mtd';
	}
	else if(document.getElementById("custom").checked == true){
		var type = 'custom';
		var start_date = document.getElementById("start_date").value;
		var end_date = document.getElementById("end_date").value;
	}
	window.open('http://acedns.in/acednsproduct/misreport/customerwise_report.php?type='+type+'&start_date='+start_date+'&end_date='+end_date,'_self');
}*/

function skuwisereport(){
	if(document.getElementById("today").checked == true){
		var type = 'today';
	}
	else if(document.getElementById("mtd").checked == true){
		var type = 'mtd';
	}
	else if(document.getElementById("custom").checked == true){
		var type = 'custom';
		var start_date = document.getElementById("start_date").value;
		var end_date = document.getElementById("end_date").value;
	}
	window.open('http://acedns.in/acednsproduct/misreport/order_register_sku_report.php?type='+type+'&start_date='+start_date+'&end_date='+end_date+'&emp_name='+emp_name,'_self');
}
function show_date_div(){
	document.getElementById("date_div").hidden = false;
}
function hide_date_div(){
	document.getElementById("date_div").hidden = true;
}
</script>
<body onLoad="loadbodydata('<?php echo $type; ?>','<?php echo $start_date; ?>','<?php echo $end_date; ?>','<?php echo $emp_name; ?>');">
<center>
<br />
Employee:<select id="emp_name" onChange="uncheck_criteria();">
			<option value="all" selected >All</option>
            <?php
		 	$sql_emp = "SELECT emp_code, emp_name FROM employee_master".$emp_hierarchy_condition." ORDER BY emp_name ASC";
			$res_emp = mysql_query($sql_emp);
			while($row_emp = mysql_fetch_array($res_emp)){
				echo "<option value=\"".$row_emp['emp_code']."\">".$row_emp['emp_name']."</option>";
			}
			?>
		 </select>
<br /><br />
<div id="display" style="max-height: 350px; width:60%; overflow-y: scroll; margin-left:10px;" align="center">

</div>
<br><br>
<div style="width:60%; margin-left:10px;">
Today:<input type="radio" name="duration" value="today" id="today" <?php if($type == 'today') echo "checked"; ?> checked onClick="hide_date_div(); show_employeewise();" />
MTD:<input type="radio" name="duration" value="mtd" id="mtd" <?php if($type == 'mtd') echo "checked"; ?> onClick="hide_date_div(); show_employeewise();" />
Custom:<input type="radio" name="duration" value="custom" id="custom" <?php if($type == 'custom') echo "checked"; ?> onClick="show_date_div();" />
</div><br>
<div id="date_div" style="width:60%;" hidden >
From:<input type="date" name="start_date" id="start_date" value="<?php echo $start_date; ?>" style="height:20px;" />
To:<input type="date" name="end_date" id="end_date" value="<?php echo $end_date; ?>" style="height:20px;" />
<input type="submit" name="submit" value="Submit" onClick="show_employeewise();" />
</div>
<br />
<div style="width:60%;" align="right"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
</center>
<br>
<div id="options" align="left" style="margin-left:110px;">
<table style="border-collapse:collapse; font-weight:bold;">
  <tr>
  	<td align="right">Employeewise:</td>
    <td align="left"><input type="radio" name="sauda_option" value="employeewise" onClick="employeewisereport();" checked /></td>
  </tr>
  <tr>
  	<td align="right">Customerwise:</td>
    <td align="left"><input type="radio" name="sauda_option" value="customerwise" onClick="customerwisereport();" /></td>
  </tr>
  <tr>
  	<td align="right">SKU wise:</td>
    <td align="left"><input type="radio" name="sauda_option" value="plantwise" onClick="skuwisereport();" /></td>
  </tr>
</table>
</div>
</body>

<script>
function show_employeewise(){
	document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	if(document.getElementById("today").checked == true){
		var type = 'today';
	}
	else if(document.getElementById("mtd").checked == true){
		var type = 'mtd';
	}
	else if(document.getElementById("custom").checked == true){
		var type = 'custom';
		var start_date = document.getElementById("start_date").value;
		var end_date = document.getElementById("end_date").value;
		
		if(start_date>end_date){
			alert("Start date cannot be greater than end date");
			var response1 = 0;
		}
	
	if(document.getElementById("start_date").value.search(/\S/)==-1 || document.getElementById("end_date").value.search(/\S/)==-1){
			alert("Start date/End date cannot be empty");
			var response2 = 0;
		}
	}
	var emp_name = document.getElementById("emp_name").value;
	
	
	if(type != 'custom')
		GenericAjaxFunction('order_register_employee_data.php?type='+type+'&emp_name='+emp_name,'display',0);
	else{
		if(response1 != 0 && response2 != 0)
			GenericAjaxFunction('order_register_employee_data.php?type='+type+'&start_date='+start_date+'&end_date='+end_date+'&emp_name='+emp_name,'display',0);
	}
}

function loadbodydata(type,start_date,end_date,emp_name){
	<?php
	if($type == 'custom'){?>
		document.getElementById("date_div").hidden = false;<?php
	}
	?>
	GenericAjaxFunction('order_register_employee_data.php?type='+type+'&start_date='+start_date+'&end_date='+end_date+'&emp_name='+emp_name,'display',0);
}

function uncheck_criteria(){
	document.getElementById("today").checked = false;
	document.getElementById("mtd").checked = false;
	document.getElementById("custom").checked = false;
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
	var mywindow = window.open('', 'Order Register', 'height=400,width=600');
	mywindow.document.write('<html><head><title>Order Register</title>');
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
		
		/*var displaydiv = document.getElementById("display").innerHTML;
		var customerdatadiv = document.getElementById("customerdata").innerHTML;
	    var productdiv = document.getElementById("product").innerHTML;
	    var customerwisediv = document.getElementById("customerwise").innerHTML;
		var view = displaydiv+'<br>'+customerdatadiv+'<br>'+productdiv+'<br>'+customerwisediv;
		document.write('<div id=\'view\'>');
		document.write(view);
		document.write('<div>');
        //creating a temporary HTML link element (they support setting file names)*/
        var a = document.createElement('a');
        //getting data from our div that contains the HTML table
        var data_type = 'data:application/vnd.ms-excel';
        var table_div = document.getElementById('display');
        var table_html = table_div.outerHTML.replace(/ /g, '%20');
        a.href = data_type + ', ' + table_html;
        //setting the file name
        a.download = 'Order Register' + postfix + '.xls';
        //triggering the function
        a.click();
        //just in case, prevent default behaviour
        e.preventDefault();
}
</script>
<?php
}
?>