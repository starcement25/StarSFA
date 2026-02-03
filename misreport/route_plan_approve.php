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
	$create_control = "";
	
	echo "<center>";
	echo "<span style=\"font-weight:bold; font-size:14px;\">ROUTE PLAN APPROVAL</span><br><br>";
	echo "<table width='100%'><tr><td align='left' valign='top' style='padding-left:10px;'><a href='adminMain.php' style='color:blue; font-weight:bold;'><< Back</a></td><td width='90%' align='center'>";
	attribute_selection($hidden,$create_control);
	echo "</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr></table>";
	echo "<br>";
	?>
    <div id="display" style="max-height: 350px; width:80%; overflow-y: scroll; " align="center"></div><br />
    <!--<div id="display_details" style="max-height: 350px; max-width:1000px; overflow-y: scroll; overflow-x: scroll;" align="center"></div><br />-->
    
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
		
		document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('route_plan_approve_data.php?employee='+employee,'display',0);
	}
	
	function display_control(visitdateval)
	{
		var visitdate=visitdateval.substring(4, 8)+'-'+visitdateval.substring(2, 4)+'-'+visitdateval.substring(0, 2);
		 var current_date =new Date().toISOString().slice(0,10); 
		 //alert(current_date);
		 
		 if( visitdate > current_date)
		 {
			if(document.getElementById('route_id_display_'+visitdateval).style.display=='block' )
			{
				document.getElementById('route_id_control_'+visitdateval).style.display='block';
				document.getElementById('route_id_display_'+visitdateval).style.display='none';
			}
			else if(document.getElementById('route_id_control_'+visitdateval).style.display=='block' )
			{
				document.getElementById('route_id_control_'+visitdateval).style.display='none';
				document.getElementById('route_id_display_'+visitdateval).style.display='block';
			}
		 }
		 else
		 { 
		   alert(" Visit date must be greater than current date");
		 }
	}
	
	function PrintElem(elem)
	{
		var displaydivval = 'display';
		var displaydiv = document.getElementById(displaydivval).innerHTML;	
		Popup(displaydiv);
	   //Popup($(elem).html());
	}

	function Popup(data) 
	{
		var mywindow = window.open('', 'PJP Report', 'height=400,width=600');
		mywindow.document.write('<html><head><title>PJP Report</title>');
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
			var table_div = document.getElementById('display');
			var table_html = table_div.outerHTML.replace(/ /g, '%20');
			a.href = data_type + ', ' + table_html;
			//setting the file name
			a.download = 'PJP Report' + postfix + '.xls';
			//triggering the function
			a.click();
			//just in case, prevent default behaviour
			e.preventDefault();
	}
	
	</script>
    <?php
}
?>