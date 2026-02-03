<?php
ob_start();
session_start();

require("adminUtils.php");
require ("attribute_selection.php");
if($_SESSION['admin_login']=="")  		header("product:index.php");

disphtml("main();");

function main(){
	?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="ajax1.js"></script>
    <?php
	$hidden = " hidden";
	echo "<center>";
	echo "<table width='100%'><tr><td align='left' valign='top' style='padding-left:10px;'><a href='adminMain.php' style='color:blue; font-weight:bold;'><< Back</a></td><td width='90%' align='center'>";
	attribute_selection($hidden);
	echo "</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr></table>";
	echo "<br>";
	?>
    <div id="display" style="max-height: 350px; width:100%; overflow-y: scroll; overflow-x: scroll;" align="center"></div><br />
    <div id="display_details" style="max-height: 350px; width:100%; overflow-y: scroll;" align="center"></div><br />
    <input type="hidden" id="report_name" />
    <?php
	echo "</center>";
	?>
    <script>
	function display_result(){
		if(document.getElementById("zone").value.search(/\S/) == -1){
			alert('Please Select Zone');
			return false;
		}
		if(document.getElementById("state").value.search(/\S/) == -1){
			alert('Please Select State');
			return false;
		}
		if(document.getElementById("branch").value.search(/\S/) == -1){
			alert('Please Select Branch');
			return false;
		}
		if(document.getElementById("sale_access").value.search(/\S/) == -1){
			alert('Please Select Department');
			return false;
		}
		if(document.getElementById("employee").value.search(/\S/) == -1){
			alert('Please Select Employee');
			return false;
		}
		var employee = document.getElementById("employee").value;
		document.getElementById("display_details").innerHTML = '';
		document.getElementById("display").innerHTML = '<img src="please_wait.gif" id="ajaxloader">';
		GenericAjaxFunction('admin_mis_report_data_star.php?employee='+employee,'display',0);
		document.getElementById("print_export").hidden = true;
		
	}
	
	function show_mis_details(val,employee){
		var employee = encodeURIComponent(employee);
		document.getElementById("display_details").innerHTML = '<img src="please_wait.gif" id="ajaxloader">';
		GenericAjaxFunction('admin_mis_report_data_details_star.php?employee='+employee+'&val='+val,'display_details',0);
		//document.getElementById("print_export").hidden = false;
	}
	
	function show_date_range_control(){
		document.getElementById("custom_date_div").hidden = false;
	}
	
	function show_datewisedata(employee){
		
		var mode = 'datewise';
		var start_date = document.getElementById("start_date_val").value;
		var end_date = document.getElementById("end_date_val").value;
				
		if(start_date>end_date)
		{
			alert("Start date cannot be greater than end date");
			return false;
		}
	
	if(document.getElementById("start_date_val").value.search(/\S/)==-1 && document.getElementById("end_date_val").value.search(/\S/)==-1)
		{
			alert("Start date/End date cannot be empty");
			return false;
		}
		
		document.getElementById("display").innerHTML = '<img src="please_wait.gif" id="ajaxloader">';
		GenericAjaxFunction('admin_mis_report_data_star.php?employee='+encodeURIComponent(employee)+'&mode=datewise&start_date='+start_date+'&end_date='+end_date,'display',0);
	}
	
	function show_emp_data_details(mode,employee,start_date,end_date){
		var employee = encodeURIComponent(employee);
		document.getElementById("display_details").innerHTML = '<img src="please_wait.gif" id="ajaxloader">';
		GenericAjaxFunction('admin_mis_report_data_details_star.php?employee='+employee+'&mode='+mode+'&start_date='+start_date+'&end_date='+end_date,'display_details',0);
	}
	
	function PrintElem(elem)
	{
		var displaydiv = document.getElementById("display_details").innerHTML;
		Popup(displaydiv);
	   //Popup($(elem).html());
	}

	function Popup(data) 
	{
		var mywindow = window.open('', 'MIS Report Details', 'height=400,width=600');
		mywindow.document.write('<html><head><title>MIS Report Details</title>');
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
	
	function exporttocsv(divid)
	{
		//alert(divid);
		//getting values of current time for generating the file name
		var dt = new Date();
		var day = dt.getDate();
		var month = dt.getMonth() + 1;
		var year = dt.getFullYear();
		var hour = dt.getHours();
		var mins = dt.getMinutes();
		var postfix = day + "." + month + "." + year + "_" + hour + "." + mins;
		
		/*document.write('<div id=\'view\'>');
		document.write(view);
		document.write('<div>');*/
		//creating a temporary HTML link element (they support setting file names)*/
		var a = document.createElement('a');
		//getting data from our div that contains the HTML table
		var data_type = 'data:application/vnd.ms-excel';
		var table_div = document.getElementById('display_details');
		var table_html = table_div.outerHTML.replace(/ /g, '%20');
		a.href = data_type + ', ' + table_html;
		//setting the file name
		a.download = 'MIS Report Details' + postfix + '.xls';
		//triggering the function
		document.body.appendChild(a);
		a.click();
		document.body.removeChild(a);
		//just in case, prevent default behaviour
		//e.preventDefault();
	}
	</script>
    <?php
}
?>