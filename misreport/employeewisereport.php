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
function show_date_div()
{
	document.getElementById("date_div").hidden = false;
}

function hide_date_div()
{
	document.getElementById("date_div").hidden = true;
}
</script>

<body onLoad="GenericAjaxFunction('employeewisedata.php','display',0);">
<center>
<div style="width:95%; text-align:right;"><strong>* UOM:MT</strong></div><br>
<div id="display" style="max-height: 350px; width:80%; overflow-y: scroll; margin-left:10px;" align="center">
<img src="ajax-loader.gif" id="ajaxloader">
</div>

<br>

<div id="customer" style="max-height: 350px; width:90%; overflow-y: scroll; margin-left:10px;" align="center">
</div>

<br>

<div id="product" style="max-height: 350px; width:80%; overflow-y: scroll; margin-left:10px;" align="center">

</div>

<br>

<div style="width:60%; margin-left:10px;">
Today:<input type="radio" name="duration" value="today" id="today" checked onClick="hide_date_div(); show_employeewise();" />
MTD:<input type="radio" name="duration" value="mtd" id="mtd" onClick="hide_date_div(); show_employeewise();" />
Custom:<input type="radio" name="duration" value="custom" id="custom" onClick="show_date_div();" />
</div>

<br>
	
<div id="date_div" style="width:60%;" hidden >
From:<input type="date" name="start_date" id="start_date" style="height:20px;" />
To:<input type="date" name="end_date" id="end_date" style="height:20px;" />
<input type="submit" name="submit" value="Submit" onClick="show_employeewise();" />
</div>

<br>

<div><a href="sauda_report_main.php" style="color:blue;">Back</a></div>

</center>
</body>

<script>
function show_employeewise()
{
	document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	document.getElementById("customer").innerHTML = "";
	document.getElementById("product").innerHTML = "";
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
		GenericAjaxFunction('employeewisedata.php?type='+type,'display',0);
	else 
	{
		if(response1 != 0 && response2 != 0)
			GenericAjaxFunction('employeewisedata.php?type='+type+'&start_date='+start_date+'&end_date='+end_date,'display',0);
	}
	
		
}

function show_customer_details(product_code, emp_code, product_name, condition_value, start_date, end_date)
{
	//alert(product_code+emp_code+condition_value);
	document.getElementById("product").innerHTML = "";
	GenericAjaxFunction('customer_booked_quantity.php?product_code='+product_code+'&emp_code='+emp_code+'&condition_value='+condition_value+'&product_name='+product_name+'&start_date='+start_date+'&end_date='+end_date,'customer',0);
}

function show_customer(product_code, emp_code, emp_name, product_name, condition_value, start_date, end_date)
{
	//alert(emp_code+product_code+condition_value+emp_name+product_name);
	document.getElementById("product").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('customer_product_details.php?emp_code='+emp_code+'&emp_name='+emp_name+'&product_code='+product_code+'&product_name='+product_name+'&condition_value='+condition_value+'&start_date='+start_date+'&end_date='+end_date,'product',0);
}

function show_employee_details(emp_code, condition_value, start_date, end_date)
{
	//alert("hello");
	document.getElementById("product").innerHTML = "";
	GenericAjaxFunction('customer_booked_quantity.php?emp_code='+emp_code+'&condition_value='+condition_value+'&start_date='+start_date+'&end_date='+end_date,'customer',0);
}

</script>

<?php
}
?>