<?php
ob_start();
	session_start();
	require("adminUtils.php");
	require("datefunction.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	
if(!$_GET)
	disphtml("main();");
	
function main()
{
?><head>
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

<center>
<br /><br /><br /><br />
<div id="display" style="max-height: 500px; width:90%; overflow-y: scroll; margin-left:10px;" align="center"></div>
<br />

<table cellpadding="2px">
    <tr>
        <td align="center"><?php create_date_div(); ?></td>
    </tr>
    <tr>
        <td align="center"><!--Enter Employee Name:<input name="emp_name" type="text" value="" />--></td>
    </tr>
    <tr>
        <td align="right"><img src="searchimage.jpg" style="padding-top:5px;" onclick="show_searchby();"/><!--<input name="search" type="button" value="Search By" onclick="show_searchby();"/>-->&nbsp;&nbsp;<input name="pincode" type="text" placeholder="Enter Pincode" id="pincode" hidden />&nbsp;&nbsp;<input name="oilused" type="text" placeholder="Enter Oil Used" id="oilused" hidden /></td>
    </tr>
    <tr>
    	<td align="center"><input name="submit" type="submit" value="Submit" onclick="return show_data();" /></td>
    </tr>
    <tr>
    	<td align="right"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="export" onClick="exporttocsv();"></td>
    </tr>
</table>
</center>
<script>
function show_data()
{
	var start_date = document.getElementById("start_date").value;
	if(document.getElementById("start_date").value.search(/\S/) == -1)
	{
		alert('Provide start date');
		return false;
	}
	
	var end_date = document.getElementById("end_date").value;
	if(document.getElementById("end_date").value.search(/\S/) == -1)
	{
		alert('Provide end date');
		return false;
	}
	
	if(start_date>end_date)
	{
		alert('Start date cannot be greater than end date');
		return false;
	}
	
	var pincode = document.getElementById("pincode").value;
	var oilused = document.getElementById("oilused").value;
	
	document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('product_promotion_data.php?start_date='+start_date+'&end_date='+end_date+'&pincode='+pincode+'&oilused='+oilused,'display',0);
	//alert('Hi');
}

function PrintElem(elem)
{
   Popup($(elem).html());
}

function Popup(data) 
{
	var mywindow = window.open('', 'Product Promotion Details', 'height=400,width=600');
	mywindow.document.write('<html><head><title>Product Promotion Details</title>');
	/*optional stylesheet*/ //mywindow.document.write('<link rel="stylesheet" href="main.css" type="text/css" />');
	mywindow.document.write('</head><body >');
	mywindow.document.write(data);
	mywindow.document.write('</body></html>');

	mywindow.document.close(); // necessary for IE >= 10
	mywindow.focus(); // necessary for IE >= 10

	mywindow.print();
	mywindow.close();

    return true;
}

function exporttocsv()
{
	var start_date = document.getElementById("start_date").value;
	var end_date = document.getElementById("end_date").value;
	var pincode = document.getElementById("pincode").value;
	var oilused = document.getElementById("oilused").value;
		
	window.open('product_promotion_data.php?start_date='+start_date+'&end_date='+end_date+'&export=true&pincode='+pincode+'&oilused='+oilused,'mywindow')	;
	
}

function show_searchby()
{
	document.getElementById("pincode").hidden = false;
	document.getElementById("oilused").hidden = false;
	//alert('Hello');
}
</script>
<?php
}
?>