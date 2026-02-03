<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
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
    <?php
	$start_date = $_REQUEST['start_date'];
	$end_date = $_REQUEST['end_date'];
	$type = $_REQUEST['type'];
	?>
<script>
function show_date_div(){
	document.getElementById("date_div").hidden = false;
}

function hide_date_div(){
	document.getElementById("date_div").hidden = true;
}

function areawisereport(){
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
	window.open('http://salesmpower.acedns.in/misreport/areawise_report.php?type='+type+'&start_date='+start_date+'&end_date='+end_date,'_self');
}

function districtwisereport(){
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
	window.open('http://salesmpower.acedns.in/misreport/customerwise_report.php?type='+type+'&start_date='+start_date+'&end_date='+end_date,'_self');
}

function distributorwisereport(){
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
	window.open('http://salesmpower.acedns.in/misreport/customerwise_report.php?type='+type+'&start_date='+start_date+'&end_date='+end_date,'_self');
}

function beatwisereport(){
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
	window.open('http://salesmpower.acedns.in/misreport/customerwise_report.php?type='+type+'&start_date='+start_date+'&end_date='+end_date,'_self');
}

function brandwisereport(){
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
	window.open('http://salesmpower.acedns.in/misreport/customerwise_report.php?type='+type+'&start_date='+start_date+'&end_date='+end_date,'_self');
}
</script>
<body onLoad="loadbodydata('<?php echo $type; ?>','<?php echo $start_date; ?>','<?php echo $end_date; ?>');">
<center>
<div id="display" style="max-height: 350px; width:40%; overflow-y: scroll; margin-left:10px;" align="center">
<!--<img src="ajax-loader.gif" id="ajaxloader">-->
</div>
<br>
<div style="width:60%; margin-left:10px;">
Today:<input type="radio" name="duration" value="today" id="today" <?php if($type == 'today') echo "checked"; ?> checked onClick="hide_date_div(); show_employeewise();" />
MTD:<input type="radio" name="duration" value="mtd" id="mtd" <?php if($type == 'mtd') echo "checked"; ?> onClick="hide_date_div(); show_employeewise();" />
Custom:<input type="radio" name="duration" value="custom" id="custom" <?php if($type == 'custom') echo "checked"; ?> onClick="show_date_div();" />
<br>
</div>
<div id="date_div" style="width:60%;" hidden >
From:<input type="date" name="start_date" id="start_date" value="<?php echo $start_date; ?>" style="height:20px;" />
To:<input type="date" name="end_date" id="end_date" value="<?php echo $end_date; ?>" style="height:20px;" />
<input type="submit" name="submit" value="Submit" onClick="show_employeewise();" />
</div>
</center>
<br>
<div id="options" align="left" style="margin-left:110px;">
<table style="border-collapse:collapse; font-weight:bold;">
  <tr>
  	<td align="right">Areawise:</td>
    <td align="left"><input type="radio" name="select_option" value="areawise" onClick="areawisereport();" /></td>
  </tr>
  <tr>
  	<td align="right">Districtwise:</td>
    <td align="left"><input type="radio" name="select_option" value="districtwise" onClick="districtwisereport();" /></td>
  </tr>
  <tr>
  	<td align="right">Distributorwise:</td>
    <td align="left"><input type="radio" name="select_option" value="distributorwise" onClick="distributorwisereport();" /></td>
  </tr>
  <tr>
  	<td align="right">Beatwise:</td>
    <td align="left"><input type="radio" name="select_option" value="beatwise" onClick="beatwisereport();" /></td>
  </tr>
  <tr>
  	<td align="right">Brandwise:</td>
    <td align="left"><input type="radio" name="select_option" value="brandwise" onClick="brandwisereport();" /></td>
  </tr>
</table>
</div>
</body>
    <?php
}
?>