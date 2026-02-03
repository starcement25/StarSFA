<?php
ob_start();
	session_start();
	require("adminUtils.php");
	require("datefunction.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	
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
<br /><br />
<table cellpadding="4px" width="70%" class="border">
	<tr class="TDHEAD_SUB">
    	<td align="center">Market Feedback</td>
    </tr>
    <tr><td align="center">
<div id="display" style="max-height: 500px; width:100%; overflow-y: scroll;" align="center"></div>
<br /><br />
<table cellpadding="4px">
	<tr>
        <td align="center"><?php create_date_div(); ?></td>
    </tr>
    <tr>
        <td align="left">Product Group:
        <select name="product_group" id="product_group" onchange="show_route();">
        	<option value="" selected>Select</option>
            <?php
				$sql_generic_oil_master = "SELECT oil_name FROM generic_oil_master WHERE competitor_name != ''";
				$res_generic_oil_master = mysql_query($sql_generic_oil_master);
				while($row_generic_oil_master = mysql_fetch_array($res_generic_oil_master))
				{
					echo "<option>".$row_generic_oil_master['oil_name']."</option>";
				}
			?>
        </select><font color="#FF0000">*</font>&nbsp;&nbsp;
        Select Route:
        <select name="route" id="route">
        	<option value="">Select</option>
            
        </select><font color="#FF0000">*</font>
        </td>
    </tr>
    <tr>
    	<td align="center"><input name="submit" type="button" value="Submit" onclick="return show_market_feedback();"/></td>
    </tr>
    <tr>
    	<td align="right"><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="export" onClick="exporttocsv();"></td>
    </tr>
</table>
</td></tr></table>
</center>
<script>
function show_market_feedback()
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
	
	var product_group = document.getElementById("product_group").value;
	if(document.getElementById("product_group").value.search(/\S/) == -1)
	{
		alert('Select product group');
		return false;
	}
	
	var route_code = document.getElementById("route").value;
	if(document.getElementById("route").value.search(/\S/) == -1)
	{
		alert('Select route');
		return false;
	}
	
	document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('market_feedback_data.php?start_date='+start_date+'&end_date='+end_date+'&product_group='+product_group+'&route_code='+route_code,'display',0);
	//alert('Hi');
}

function show_route()
{
	var start_date = document.getElementById("start_date").value;
	var end_date = document.getElementById("end_date").value;
	var product_group = document.getElementById("product_group").value;
	
	GenericAjaxFunction('market_feedback_route_selection.php?start_date='+start_date+'&end_date='+end_date+'&product_group='+product_group,'route',0);
}

function PrintElem(elem)
{
   Popup($(elem).html());
}

function Popup(data) 
{
	var mywindow = window.open('', 'Market Feedback Details', 'height=400,width=600');
	mywindow.document.write('<html><head><title>Market Feedback Details</title>');
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
	var product_group = document.getElementById("product_group").value;
	var route_code = document.getElementById("route").value;
			
	window.open('market_feedback_data.php?start_date='+start_date+'&end_date='+end_date+'&product_group='+product_group+'&route_code='+route_code+'&export=true','mywindow');
	
}
</script>
<?php
}
?>