<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
if(!$_GET)
	disphtml("main();");
	
ob_end_flush();
?><head>
<!--script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script-->
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="ajax1.js"></script>
</head>

<?php
function main()
{
	/*$sql_top_emp="SELECT emp_code FROM employee_master WHERE reporting_to=''";
	$rs_top_emp=mysql_query($sql_top_emp);
	$row_top_emp=mysql_fetch_array($rs_top_emp);
	$top_emp_code=$row_top_emp['emp_code'];*/
?>
<head>
	<!--script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script-->
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
function exporttocsv(divid)
{
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
	//var table_html = encodeURIComponent(table_div.outerHTML.replace(/ /g, '%20'));
	//alert(table_html);return false;
	a.href = data_type + ', ' + table_html;
	//setting the file name
	a.download = 'Van stock allocation' + postfix + '.xls';
	//triggering the function
	document.body.appendChild(a);
	a.click();
	document.body.removeChild(a);
	//just in case, prevent default behaviour
	//e.preventDefault();
}
</script>
<body onLoad="GenericAjaxFunction('van_stock_allocation_show.php','display',0);">
<center>
<!--div style="height:30px; width:90%; text-align:center;">
Employee:<input class="INPUT" type="text" name="employee_name" id="employee_name" value="" />&nbsp;&nbsp;
<input type="submit" class="inplogin" name="search" value="Search" onClick="search_record(employee_name.value);" /></div-->
<br />
<div id="display" style="max-height: 450px; overflow-y: scroll; width:95%;">
<img src="ajax-loader.gif" id="ajaxloader">

<br />
<div id="edit_form" style="width:60%;">
</div>
</center>
</body>

<?php
}
?>