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

<body onLoad="GenericAjaxFunction('survey_name_data.php','display',0);">
<center>
<div id="display" style="max-height: 350px; width:90%; overflow-y: scroll; margin-left:10px;" align="center">
<img src="ajax-loader.gif" id="ajaxloader">
</div>

<br />

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
<select name="empl" id="empl" onChange="remove_checked();">
	<option value="all" selected>All</option>
<?php
$sql_empl = "SELECT emp_code, emp_name FROM employee_master ORDER BY emp_name ASC";
$res_empl = mysqli_query($link,$sql_empl);
while($row_empl = mysqli_fetch_assoc($res_empl))
{
	echo "<option value='".$row_empl['emp_code']."'>".$row_empl['emp_name']."</option>";
}
?>
</select>

</center>
</body>

<script>
function show_employeewise()
{
	document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	var emp_code = document.getElementById("empl").value;
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
		GenericAjaxFunction('survey_name_data.php?type='+type+'&emp_code='+emp_code,'display',0);
	else 
	{
		if(response1 != 0 && response2 != 0)
			GenericAjaxFunction('survey_name_data.php?type='+type+'&start_date='+start_date+'&end_date='+end_date+'&emp_code='+emp_code,'display',0);
	}
	
		
}

function remove_checked()
{
	document.getElementById("today").checked = false;
	document.getElementById("mtd").checked = false;
	document.getElementById("custom").checked = false;
}
</script>

<?php
}
?>