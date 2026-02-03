<?php
ob_start();
session_start();
require("adminUtils_tecpl.php");
require ("attribute_selection_kiosk.php");
if($_SESSION['admin_login']=="")  		header("product:index.php");

disphtml("main();");

function main(){
	$type=$_REQUEST['type'];
	if($_SESSION['admin_login']=="admin"){
		$emp_hierarchy_value = '';
		$emp_hierarchy_value_condition = '';
	}
	else{
		$emp_hierarchy_value=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_value_condition = " WHERE emp_code IN(".$emp_hierarchy_value.") ";
	}
		$zone_condition = " WHERE zone != '' ";
	$division_condition = " WHERE division_id != '' ";
	$region_condition = " WHERE region != '' ";
	$CCC_condition = " WHERE kiosk_id != '' ";

	
	/*--------> Check If Zone Exists <--------*/
	$sql_zone = "SELECT DISTINCT SUBSTRING_INDEX(zone, ',', 1) AS zone FROM employee_master".$emp_hierarchy_value_condition.$zone_condition." ORDER BY zone ASC";
	$res_zone = mysqli_query($link,$sql_zone);
	$zone_total = mysqli_num_rows($res_zone);
	
	?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="ajax1.js"></script>
    <!--script language="JavaScript" src="calendar3.js"></script-->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="ajax1.js"></script>
    <script type="text/javascript" src="jquery.highlight.js"></script>
    <script src="http://cdn.jsdelivr.net/webshim/1.12.4/extras/modernizr-custom.js"></script>
    <!-- polyfiller file to detect and load polyfills -->
    <script src="http://cdn.jsdelivr.net/webshim/1.12.4/polyfiller.js"></script>
    <script>
      webshims.setOptions('waitReady', false);
      webshims.setOptions('forms-ext', {types: 'date'});
      webshims.polyfill('forms forms-ext');
    </script>
    <?php
	$hidden = " ";
	
	echo "<center>";
	echo "<table width='100%'><tr><td align='left' valign='top' style='padding-left:10px;'><a href='adminMain.php' style='color:blue; font-weight:bold;'><< Back</a></td><td width='90%' align='center'>";
	attribute_selection($hidden,$create_control,$type);
	echo "</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr></table>";
	echo "<br>";
	?>
    <div id="display" style="max-height: 350px; width:80%; overflow-y: scroll;" align="center"></div><br />
    <div id="display_details" style="max-height: 350px; max-width:1000px; overflow-y: scroll; overflow-x: scroll;" align="center" hidden></div><br />
    <div style="width:100%;" align="right" id="print_export" hidden><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
	<input type="hidden" id="report_name" />
    <?php
	echo "</center>";
	?>
    <script>
	function display_result(type){
		var start_date = document.getElementById("start_date").value;
		var end_date = document.getElementById("end_date").value;
		if(document.getElementById("start_date").value.search(/\S/) == -1){
			alert("Please provide From date");
			return false;
		}
		if(document.getElementById("end_date").value.search(/\S/) == -1){
			alert("Please provide To date");
			return false;
		}
		if(start_date>end_date){
			alert("From date cannot be greater than To date");
			return false;
		}
		if(document.getElementById("zone"))
		{
			var zone=document.getElementById("zone").value;	
			zone = encodeURIComponent(zone);
		}
		else
		{
			var zone ='';
		}
		if(document.getElementById("division_id"))
		{
			var division_id=document.getElementById("division_id").value;	
		}
		else
		{
			var division_id ='';
		}
		if(document.getElementById("region"))
		{
			var region=document.getElementById("region").value;	
		}
		else
		{
			var region='';
		}
		if(document.getElementById("CCC"))
		{
			var CCC=document.getElementById("CCC").value;	
		}
		else
		{
			var CCC='';
		}
	
		document.getElementById("display_details").innerHTML = '';
		document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('kiosk_cash_cheque_report_data.php?zone='+zone+'&start_date='+start_date+'&end_date='+end_date+'&division_id='+division_id+'&region='+region+'&CCC='+CCC+'&type='+type,'display',0);
	}
	function PrintElem(elem)
	{
		var displaydiv = document.getElementById('display').innerHTML;	
		Popup(displaydiv);
	}
	function Popup(data) 
	{
		var mywindow = window.open('', 'No Activity Report', 'height=400,width=600');
		mywindow.document.write('<html><head><title>No Activity Report</title>');
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
		a.download = 'Kiosk Report' + postfix + '.xls';
		//triggering the function
		document.body.appendChild(a);
		a.click();
		document.body.removeChild(a);
		//just in case, prevent default behaviour
		//e.preventDefault();
	}
	
	function show_option(survey_type){
		var survey_type = survey_type;
		if(survey_type == 'KYC'){
			document.getElementById("date_div").hidden = false;
			document.getElementById("month_row").hidden = true;
		}
		else if(survey_type == 'Branding'){
			document.getElementById("date_div").hidden = true;
			document.getElementById("month_row").hidden = false;
		}
		else if(survey_type == 'Site Visit'){
			document.getElementById("date_div").hidden = false;
			document.getElementById("month_row").hidden = true;
		}
		if(survey_type == 'Technical Meets'){
			document.getElementById("date_div").hidden = false;
			document.getElementById("month_row").hidden = true;
		}
	}
	
	function get_visit_details(visit_type,month_data,employee,survey_type){
		//alert(visit_type+month_data+employee);
		document.getElementById("display_details").hidden = false;
		var employee = encodeURIComponent(employee);
		document.getElementById("display_details").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('star_survey_report_branding_details.php?month_data='+month_data+'&visit_type='+visit_type+'&employee='+employee+'&survey_type='+survey_type,'display_details',0);
		//document.getElementById("print_export").hidden = false;
		
	}
	
	function site_visit_details(visit_status,survey_date,employee,survey_type){
		//alert(visit_status+survey_date+employee);
		var employee = encodeURIComponent(employee);
		document.getElementById("display_details").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('star_survey_report_site_visit_data.php?visit_status='+visit_status+'&survey_date='+survey_date+'&employee='+employee+'&survey_type='+survey_type,'display_details',0);
		//document.getElementById("print_export").hidden = false;
		
	}
	</script>
    <?php
}
?>