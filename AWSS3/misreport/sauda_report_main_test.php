<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
?>


<?php
if(!$_GET)
	disphtml("main();");
	
function main()
{
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
function showproductoption()
{
	document.getElementById("product_option").hidden = false;
}
function hideproductoption()
{
	document.getElementById("groupwise").checked = false;
	document.getElementById("skuwise").checked = false;
	document.getElementById("product_option").hidden = true;
}
function saudawisereport()
{
	window.open('http://acedns.in/acednsproduct/misreport/saudawisereport.php','_self');
}
function employeewisereport()
{
	window.open('http://acedns.in/acednsproduct/misreport/employeewisereportchanged.php','_self');
}
function product_groupwise()
{
	window.open('http://acedns.in/acednsproduct/misreport/product_groupwise_report.php','_self');
}
function product_skuwise()
{
	window.open('http://acedns.in/acednsproduct/misreport/product_skuwise_report.php','_self');
}
function customerwisereport()
{
	window.open('http://acedns.in/acednsproduct/misreport/customerwise_report.php','_self');
}
function depotwisereport()
{
	window.open('http://acedns.in/acednsproduct/misreport/depotwise_report.php','_self');
}
function zonewisereport()
{
	window.open('http://acedns.in/acednsproduct/misreport/zonewise_report.php','_self');
}
function statewisereport()
{
	window.open('http://acedns.in/acednsproduct/misreport/statewise_report.php','_self');
}
</script>

<script>
function show_date_div()
{
	document.getElementById("date_div").hidden = false;
}

function hide_date_div()
{
	document.getElementById("date_div").hidden = true;
}
</script>

<body onLoad="GenericAjaxFunction('sauda_report.php','display',0);">
<center>
<div style="width:95%; text-align:right;"><strong>* UOM:MT</strong></div><br>
<div id="display" style="max-height: 350px; width:40%; overflow-y: scroll; margin-left:10px;" align="center">
<img src="ajax-loader.gif" id="ajaxloader">
</div>

<br>

<div style="width:60%; margin-left:10px;">
Today:<input type="radio" name="duration" value="today" id="today" checked onClick="hide_date_div(); show_employeewise();" />
MTD:<input type="radio" name="duration" value="mtd" id="mtd" onClick="hide_date_div(); show_employeewise();" />
Custom:<input type="radio" name="duration" value="custom" id="custom" onClick="show_date_div();" />
<br>
</div>

<div id="date_div" style="width:60%;" hidden >
From:<input type="date" name="start_date" id="start_date" style="height:20px;" />
To:<input type="date" name="end_date" id="end_date" style="height:20px;" />
<input type="submit" name="submit" value="Submit" onClick="show_employeewise();" />
</div>

</center>

<br>

<div id="options" align="left" style="margin-left:110px;">
<table style="border-collapse:collapse; font-weight:bold;">
  <tr>
  	<td align="right">Saudawise:</td>
    <td align="left"><input type="radio" name="sauda_option" value="saudawise" onClick="hideproductoption();saudawisereport();"/></td>
  </tr>
  <tr>
  	<td align="right">Employeewise:</td>
    <td align="left"><input type="radio" name="sauda_option" value="employeewise" onClick="hideproductoption();employeewisereport();" /></td>
  </tr>
<tr>
  	<td align="right">Productwise:</td>
    <td align="left"><input type="radio" name="sauda_option" value="productwise" onClick="showproductoption();" /></td>
  </tr>
  <tr id="product_option" hidden>
    <td></td>
    <td>
    &nbsp;&nbsp;Groupwise:<input type="radio" name="sauda_productwise_option" id="groupwise" value="groupwise" onClick="product_groupwise();" />
         SKU wise:<input type="radio" name="sauda_productwise_option" id="skuwise" value="skuwise" onClick="product_skuwise();" />
    </td>
  </tr>
  <tr>
  	<td align="right">Customerwise:</td>
    <td align="left"><input type="radio" name="sauda_option" value="customerwise" onClick="hideproductoption();customerwisereport();" /></td>
  </tr>
  <tr>
  	<td align="right">Depotwise:</td>
    <td align="left"><input type="radio" name="sauda_option" value="depotwise" onClick="hideproductoption();depotwisereport();" /></td>
  </tr>
  <tr>
  	<td align="right">Zonewise:</td>
    <td align="left"><input type="radio" name="sauda_option" value="zonewise" onClick="hideproductoption();zonewisereport();" /></td>
  </tr>
  <tr>
  	<td align="right">Statewise:</td>
    <td align="left"><input type="radio" name="sauda_option" value="statewise" onClick="hideproductoption();statewisereport();" /></td>
  </tr>
</table>
</div>
</body>

<script>
function show_employeewise()
{
	document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	if(document.getElementById("today").checked == true)
	{
		var type = 'today';
	}
	else if(document.getElementById("mtd").checked == true)
	{
		var type = 'mtd';
	}
	else if(document.getElementById("custom").checked == true)
	{
		var type = 'custom';
		var start_date = document.getElementById("start_date").value;
		var end_date = document.getElementById("end_date").value;
		
		if(start_date>end_date)
		{
			alert("Start date cannot be greater than end date");
			var response1 = 0;
		}
	
	if(document.getElementById("start_date").value.search(/\S/)==-1 || document.getElementById("end_date").value.search(/\S/)==-1)
		{
			alert("Start date/End date cannot be empty");
			var response2 = 0;
		}
	}
	
	
	if(type != 'custom')
		GenericAjaxFunction('sauda_report.php?type='+type,'display',0);
	else 
	{
		if(response1 != 0 && response2 != 0)
			GenericAjaxFunction('sauda_report.php?type='+type+'&start_date='+start_date+'&end_date='+end_date,'display',0);
	}
	
		
}
</script>
<?php
}
?>