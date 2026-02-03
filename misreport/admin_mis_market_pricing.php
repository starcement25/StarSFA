<?php
//ob_start();
session_start();
require("adminUtils.php");
//require("include/dbcon.php");

/*require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");*/

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
	$hidden = " hidden";
	
	$current_date = date('Y-m-d');
	$month_date = date('Y-m');
	$current_month = date('m');
	if($current_month == '01' || $current_month == '02' || $current_month == '03'){
		$previous_year = date('Y', strtotime('-1 year'));
		$previous_year_date = $previous_year."-04-01";
	}
	else{
		$previous_year_date = date('Y-04-01');
	}
	
	$create_control = "<tr><td align=\"right\">Month:</td><td align=\"left\"><select name=\"month_select\" id=\"month_select\"><option value=\"\">Select</option>";
	if(strtoupper($_SESSION['nick_name']) == 'STAR')
	{
	$sql_month_selection = "SELECT DISTINCT SUBSTRING(date_time,1,7) AS distinct_datetime FROM competitor_pricing ORDER BY date_time ASC";
	$res_month_selection = mysqli_query($link,$sql_month_selection);
	while($row_month_selection = mysqli_fetch_assoc($res_month_selection)){
		$distinct_date = $row_month_selection['distinct_datetime'];
		$year_month_split = explode("-",$distinct_date);
		$monthNum  = $year_month_split[1];
		$monthName = date('M', mktime(0, 0, 0, $monthNum, 10));
		$create_control .= "<option value=\"".$distinct_date."\">".$monthName."</option>";
	}
	$create_control .= "</select></td></tr>";
	}
	else
	{
		$sql_month_selection = "SELECT DISTINCT SUBSTRING(market_feedback_id,-14,6) AS distinct_datetime FROM market_feedback ORDER BY SUBSTRING(market_feedback_id,-14,8) ASC";
		$res_month_selection = mysqli_query($link,$sql_month_selection);
		while($row_month_selection = mysqli_fetch_assoc($res_month_selection)){
			$distinct_date = $row_month_selection['distinct_datetime'];
			
			$monthNum  =substr($distinct_date,4,2);
			$distinct_date_value=substr($distinct_date,0,4).'-'.$monthNum;
			$monthName = date('M', mktime(0, 0, 0, $monthNum, 10));
			$create_control .= "<option value=\"".$distinct_date."\">".$monthName."</option>";
		}
		$create_control .= "</select></td></tr>";
	}
	
	echo "<center>";
	echo "<span style=\"font-weight:bold; font-size:14px;\">MARKET FEEDBACK - PRICE</span><br><br>";
	echo "<table width='100%'><tr><td align='left' valign='top' style='padding-left:10px;'><a href='adminMain.php' style='color:blue; font-weight:bold;'><< Back</a></td><td width='90%' align='center'>";
	attribute_selection($hidden,$create_control);
	echo "</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr></table>";
	echo "<br>";
	?>
    <div id="display" style="max-height: 350px; max-width:1000px; overflow-y: scroll; overflow-x: scroll;" align="center"></div><br />
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
		if(document.getElementById("month_select").value.search(/\S/) == -1){
			alert('Please Select Month');
			return false;
		}
		
		var month_data = document.getElementById("month_select").value;
		var employee = document.getElementById("employee").value;
		document.getElementById("display_details").innerHTML = '';
		document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('admin_mis_market_pricing_data.php?employee='+employee+'&month_data='+month_data,'display',0);
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
		mywindow.document.write('<html><head><title>Market Feedback Report</title>');
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
			var get_report_name = document.getElementById("report_name").value
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
			a.download = 'Market Feedback Report' + postfix + '.xls';
			//triggering the function
			a.click();
			//just in case, prevent default behaviour
			e.preventDefault();
	}
	</script>
    <?php
}
?>