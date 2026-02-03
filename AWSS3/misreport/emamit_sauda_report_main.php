<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	disphtml("main();");
	
function main()
{
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
function showproductoption(){
	document.getElementById("product_option").hidden = false;
}
function hideproductoption(){
	document.getElementById("groupwise").checked = false;
	document.getElementById("skuwise").checked = false;
	document.getElementById("product_option").hidden = true;
}
function saudawisereport(){
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
	window.open('http://acedns.in/acednsproduct/misreport/emamit_saudawisereport.php?type='+type+'&start_date='+start_date+'&end_date='+end_date,'_self');
}
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
	window.open('http://acedns.in/acednsproduct/misreport/emamit_employeewisereportchanged.php?type='+type+'&start_date='+start_date+'&end_date='+end_date,'_self');
}
function product_groupwise(){
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
	window.open('http://acedns.in/acednsproduct/misreport/emamit_product_groupwise_report.php?type='+type+'&start_date='+start_date+'&end_date='+end_date,'_self');
}
function product_skuwise(){
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
	window.open('http://acedns.in/acednsproduct/misreport/emamit_product_skuwise_report.php?type='+type+'&start_date='+start_date+'&end_date='+end_date,'_self');
}
function customerwisereport(){
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
	window.open('http://acedns.in/acednsproduct/misreport/emamit_customerwise_report.php?type='+type+'&start_date='+start_date+'&end_date='+end_date,'_self');
}
function depotwisereport(){
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
	window.open('http://acedns.in/acednsproduct/misreport/emamit_depotwise_report.php?type='+type+'&start_date='+start_date+'&end_date='+end_date,'_self');
}
function zonewisereport(){
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
	window.open('http://acedns.in/acednsproduct/misreport/emamit_zonewise_report.php?type='+type+'&start_date='+start_date+'&end_date='+end_date,'_self');
}
function statewisereport(){
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
	window.open('http://acedns.in/acednsproduct/misreport/emamit_statewise_report.php?type='+type+'&start_date='+start_date+'&end_date='+end_date,'_self');
}
function plantwisereport(){
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
	window.open('http://acedns.in/acednsproduct/misreport/emamit_plantwise_report.php?type='+type+'&start_date='+start_date+'&end_date='+end_date,'_self');
}
function show_date_div(){
	document.getElementById("date_div").hidden = false;
}
function hide_date_div(){
	document.getElementById("date_div").hidden = true;
}
</script>
<body onLoad="loadbodydata('<?php echo $type; ?>','<?php echo $start_date; ?>','<?php echo $end_date; ?>');">
<center>
<div style="width:95%; text-align:right;"><strong>* UOM:MT</strong></div><br>
<div id="display" style="max-height: 350px; width:40%; overflow-y: scroll; margin-left:10px;" align="center">
<img src="ajax-loader.gif" id="ajaxloader">
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
  <tr>
  	<td align="right">Plantwise:</td>
    <td align="left"><input type="radio" name="sauda_option" value="plantwise" onClick="hideproductoption();plantwisereport();" /></td>
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
	
	
	if(type != 'custom')
		GenericAjaxFunction('emamit_sauda_report.php?type='+type,'display',0);
	else{
		if(response1 != 0 && response2 != 0)
			GenericAjaxFunction('emamit_sauda_report.php?type='+type+'&start_date='+start_date+'&end_date='+end_date,'display',0);
	}
}

function loadbodydata(type,start_date,end_date){
	<?php
	if($type == 'custom'){?>
		document.getElementById("date_div").hidden = false;<?php
	}
	?>
	GenericAjaxFunction('emamit_sauda_report.php?type='+type+'&start_date='+start_date+'&end_date='+end_date,'display',0);
}
</script>
<?php
}
?>