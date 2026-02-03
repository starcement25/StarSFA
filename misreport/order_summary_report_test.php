<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");
disphtml("main();");

function main(){
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
    <!--<script src="tableToExcel.js"></script>-->
    <link rel="stylesheet" href="table.css" type="text/css"/>
</head>
<script>
function PrintElem(elem)
{
	var displaydiv = document.getElementById("display").innerHTML;
	Popup(displaydiv);
   //Popup($(elem).html());
}

function Popup(data) 
{
	var mywindow = window.open('', 'Sale Register', 'height=400,width=600');
	mywindow.document.write('<html><head>');
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

function exporttocsv(){
	var dt = new Date();
	var day = dt.getDate();
	var month = dt.getMonth() + 1;
	var year = dt.getFullYear();
	var hour = dt.getHours();
	var mins = dt.getMinutes();
	var postfix = day + "." + month + "." + year + "_" + hour + "." + mins;
	
	var tab_text="<table border='2px'><tr bgcolor='#87AFC6'>";
    var textRange; var j=0;
    tab = document.getElementById('display_table'); // id of table

    for(j = 0 ; j < tab.rows.length ; j++) 
    {     
        tab_text=tab_text+tab.rows[j].innerHTML+"</tr>";
    }

    tab_text=tab_text+"</table>";
	tab_text= tab_text.replace(/<A[^>]*>|<\/A>/g, "");//remove if u want links in your table
    tab_text= tab_text.replace(/<img[^>]*>/gi,""); // remove if u want images in your table
    tab_text= tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // reomves input params
		
	var a = document.createElement('a');
	
	a.href = 'data:application/vnd.ms-excel,' + encodeURIComponent(tab_text);
	a.download = 'Sale Register' + postfix + '.xls';
	document.body.appendChild(a);
	a.click();
	document.body.removeChild(a);
}
function show_data()
{
	/*if(document.getElementById("cust_type").value.search(/\S/) == -1){
		alert("Please Select Sale Type");
		return false;
	}
	var cust_type = document.getElementById("cust_type").value;
	
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
		return false;
	}*/
	
	document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('order_summary_report_data_test.php','display',0);
}

function export_to_csv()
{
	var start_date = document.getElementById("start_date").value;
	var end_date = document.getElementById("end_date").value;
	var cust_type = document.getElementById("cust_type").value;
	
	window.open('sale_register_data_export.php?cust_type='+cust_type+'&start_date='+start_date+'&end_date='+end_date,'mywindow')	;
	
}
</script>
<body>
<center>
<br>
<?php
if(tagged_distributor_for_order == 'yes'){
	$custtype_select_data = "<select name=\"cust_type\" id=\"cust_type\">
							  <option value=\"\">Select</option>
							  <option value=\"primary\">Primary</option>
							  <option value=\"secondary\">Secondary</option>
							</select>";
}
else{
	$custtype_select_data = "<select name=\"cust_type\" id=\"cust_type\">
							  <option value=\"\">Select</option>
							  <option value=\"secondary\">Secondary</option>
							</select>";
}
?>
<table width="45%" class="border" style="border-collapse:collapse;" border="1" cellpadding="2">
  <tr class="TDHEAD">
  	<td colspan="2" align="center">Select Criteria</td>
  </tr>
  <tr>
  	<td colspan="2" align="center">
    <input type="submit" name="submit" value="Submit" onClick="show_data();" />
    </td>
  </tr>
</table>
<br />
<div id="display" style="max-height: 440px; width:95%; overflow-y: scroll;" align="center">
</div>
</center>
</body>
<?php } ?>