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

<script>
function show_today_data(){
	document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('product_wise_sales_report_data.php','display',0);
}

function show_datewise_data(){
	var start_date = document.getElementById("start_date").value;
	var end_date = document.getElementById("end_date").value;
	if(document.getElementById("start_date").value.search(/\S/) == -1){
		alert('Provide start date');
		return false;
	}
	if(document.getElementById("end_date").value.search(/\S/) == -1){
		alert('Provide end date');
		return false;
	}
	if(start_date>end_date){
		alert("Start date cannot be greater than end date");
		return false;
	}
	document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('product_wise_sales_report_data.php?start_date='+start_date+'&end_date='+end_date,'display',0);
}

function csvexport(admin_login,start_date,end_date){
	window.open('productwise_sales_report_export.php?admin_login='+admin_login+'&start_date='+start_date+'&end_date='+end_date);
}

function pdffile(admin_login,start_date,end_date){
	$.post("productwise_sales_report_pdf.php",
    {
		admin_login: admin_login,
		start_date: start_date,
		end_date: end_date
    },
    function(data, status){
        //alert(data);
		var mywindow = window.open('', 'Productwise Sales Report', 'height=400,width=600');
		mywindow.document.write('<html><head><title>Productwise Sales Report</title>');
		mywindow.document.write('</head><body >');
		mywindow.document.write(data);
		mywindow.document.write('<p align=right><b>Powered By ACEdns</b></p></body></html>');
	
		mywindow.document.close(); // necessary for IE >= 10
		mywindow.focus(); // necessary for IE >= 10
	
		mywindow.print();
		mywindow.close();
	
		return true;
    });
}
</script>
</head>

<body onLoad="show_today_data();">
<center>
<div id="display" style="max-height: 480px; width:50%; overflow-y: scroll;" align="center"></div>
<br>
<div id="date_div" style="width:60%;" >
From:<input type="date" name="start_date" id="start_date" style="height:20px;" />
To:<input type="date" name="end_date" id="end_date" style="height:20px;" />
<input type="submit" name="submit" value="Submit" onClick="show_datewise_data();" />
</div>

</center>
</body>
    <?php
}

?>