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

<body>
<center>
<div id="display" style="max-height: 600px; width:90%; overflow-y: scroll; margin-left:10px;" align="center">
<!--<img src="ajax-loader.gif" id="ajaxloader">-->
</div>

<br />

<table border="1" width="50%" style="border-collapse:collapse;" class="border" cellpadding="4">
  <tr class="TDHEAD_SUB">
  	<td align="right">Date Range</td>
    <td align="left">From:<input type="date" name="start_date" id="start_date" style="height:20px;" />
To:<input type="date" name="end_date" id="end_date" style="height:20px;" onChange="show_business_name();" /></td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td align="right">Business Name</td>
    <td align="left"><select name="business_name" id="business_name">
	<option value="" selected>Select</option>
</select></td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td></td>
    <td align="left"><input type="submit" name="submit" value="Submit" onClick="show_gallery();" /></td>
  </tr>
</table>
</center>
</body>

<script>
function show_business_name()
{
	var start_date = document.getElementById("start_date").value;
	var end_date = document.getElementById("end_date").value;
	
	if(start_date == '')
	{
		alert('Start date empty');
		document.getElementById("end_date").value = '';
	}
	else if(start_date>end_date)
	{
		alert('Start date greater than end date');
	}
	else
	{
		GenericAjaxFunction('populate_business_name.php?start_date='+start_date+'&end_date='+end_date,'business_name',0);
	}
}

function show_gallery()
{
	var start_date = document.getElementById("start_date").value;
	var end_date = document.getElementById("end_date").value;
	var survey_id = document.getElementById("business_name").value;
	if(document.getElementById("business_name").value.search(/\S/) == -1){
		alert('Provide Business Name');
		return false;
	}
	
	document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('show_survey_image.php?survey_id='+survey_id,'display',0);
}
</script>

<?php
}
?>
