<?php
//ob_start();
session_start();
require("adminUtils.php");
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require ("attribute_selection.php");
if($_SESSION['admin_login']=="")  		header("product:index.php");

disphtml("main();");

function main(){
    require("include/dbcon.php");
	?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="ajax1.js"></script>
    <?php
	$hidden = "";
	echo "<center>";
	echo "<span style=\"font-weight:bold; font-size:14px;\">MARKET FEEDBACK - PRICING - RSP</span><br><br>";
	echo "<table width='100%'><tr><td align='left' valign='top' style='padding-left:10px;'><a href='adminMain.php' style='color:blue; font-weight:bold;'><< Back</a></td><td width='90%' align='center'>";
	attribute_selection($hidden,"");
	echo "</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr></table>";
	echo "<br>";
	?>
    <div id="display" style="max-height: 350px; max-width:1300px; overflow-y: scroll; overflow-x: scroll;" align="center"></div><br />
    <div id="display_details" style="max-height: 350px; width:95%; overflow-y: scroll;" align="center"></div><br />
    <div style="width:100%;" align="right" id="print_export" hidden><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
	<input type="hidden" id="report_name" />
    <?php
	echo "</center>";
	?>
    <script>
	function display_result(){
		var nickname="<?php echo strtoupper($_SESSION['nick_name']);?>";
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
		if(document.getElementById("sale_access"))
		{
			if(document.getElementById("sale_access").value.search(/\S/) == -1){
				alert('Please Select Department');
				return false;
			}
		}
		if(document.getElementById("employee").value.search(/\S/) == -1){
			alert('Please Select Employee');
			return false;
		}
		var start_date = document.getElementById("start_date").value;
		var end_date = document.getElementById("end_date").value;
		
		if(document.getElementById("start_date").value.search(/\S/) == -1 && document.getElementById("end_date").value.search(/\S/) == -1){
			alert("Please provide start date/end date");
			return false;
		}
		
		if(start_date>end_date){
			alert("Start date cannot be greater than end date");
			return false;
		}
		
		var employee = document.getElementById("employee").value;
		var branch = document.getElementById("branch").value;
		//alert(employee);
		document.getElementById("display_details").innerHTML = '';
		document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		/*if(nickname=='STAR')
			{
		GenericAjaxFunction('admin_mis_market_pricing_data_customize.php?employee='+employee+'&start_date='+start_date+'&end_date='+end_date+'&branch='+branch,'display',0);
			}
			else
			{*/
				GenericAjaxFunction('admin_mis_market_pricing_data_customize_active.php?employee='+employee+'&start_date='+start_date+'&end_date='+end_date+'&branch='+branch,'display',0);

			//}
		document.getElementById("print_export").hidden = false;
	}
	
	function PrintElem(elem)
	{
		var displaydiv = document.getElementById("display").innerHTML;
		Popup(displaydiv);
	   //Popup($(elem).html());
	}

	function Popup(data) 
	{
		var mywindow = window.open('', 'Market Feedback Report', 'height=400,width=600');
		mywindow.document.write('<html><head><title>Market Feedback Pricing</title>');
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
		var nickname="<?php echo strtoupper($_SESSION['nick_name']);?>";
		//alert(divid);
			//getting values of current time for generating the file name
			/*var get_report_name = document.getElementById("report_name").value
			var dt = new Date();
			var day = dt.getDate();
			var month = dt.getMonth() + 1;
			var year = dt.getFullYear();
			var hour = dt.getHours();
			var mins = dt.getMinutes();
			var postfix = day + "." + month + "." + year + "_" + hour + "." + mins;*/
			
			/*document.write('<div id=\'view\'>');
			document.write(view);
			document.write('<div>');*/
			//creating a temporary HTML link element (they support setting file names)*/
			/*var a = document.createElement('a');
			//getting data from our div that contains the HTML table
			var data_type = 'data:application/vnd.ms-excel';
			var table_div = document.getElementById('display');
			var table_html = table_div.outerHTML.replace(/ /g, '%20');
			a.href = data_type + ', ' + table_html;
			//setting the file name
			a.download = 'Market Feedback Pricing' + postfix + '.xls';
			//triggering the function
			a.click();
			//just in case, prevent default behaviour
			e.preventDefault();*/
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
		if(document.getElementById("sale_access"))
		{
			if(document.getElementById("sale_access").value.search(/\S/) == -1){
				alert('Please Select Department');
				return false;
			}
			var department = document.getElementById("sale_access").value;

		}
		else var department='';
		if(document.getElementById("employee").value.search(/\S/) == -1){
			alert('Please Select Employee');
			return false;
		}
		
		var zone = document.getElementById("zone").value;
		var state = document.getElementById("state").value;
		var branch = document.getElementById("branch").value;
		
		var start_date = document.getElementById("start_date").value;
		var end_date = document.getElementById("end_date").value;
		
		var employee = document.getElementById("employee").value;
		/*if(nickname=='STAR')
			{
		window.open('admin_mis_market_pricing_data_customize_export.php?employee='+employee+'&start_date='+start_date+'&end_date='+end_date+'&zone='+zone+'&state='+state+'&branch='+branch,'mywindow')	;
			}
			else
			{*/
			window.open('admin_mis_market_pricing_data_customize_export_active.php?employee='+employee+'&start_date='+start_date+'&end_date='+end_date+'&zone='+zone+'&state='+state+'&branch='+branch,'mywindow')	;

			//}

	}
	</script>
    <?php
}
?>