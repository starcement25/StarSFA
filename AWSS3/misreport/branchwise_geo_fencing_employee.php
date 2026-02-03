<?php
ob_start();
session_start();
require("adminUtils.php");
require ("attribute_selection.php");
if($_SESSION['admin_login']=="")  		header("product:index.php");

disphtml("main();");

function main(){
	if($_SESSION['admin_login']=="admin"){
		$emp_hierarchy_value = '';
		$emp_hierarchy_value_condition = '';
		$zone_condition = " WHERE zone != '' ";
		$state_condition = " WHERE state != '' ";
		$branch_condition = " WHERE branch_code != '' ";
		$sale_access_condition = " WHERE sale_access != '' ";
		$hq_condition = " WHERE hq != '' ";
		$designation_condition = " WHERE designation != '' ";
	}
	else{
		$emp_hierarchy_value=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_value_condition = " WHERE emp_code IN(".$emp_hierarchy_value.") ";
		$zone_condition = " AND zone != '' ";
		$state_condition = " AND state != '' ";
		$branch_condition = " AND branch_code != '' ";
		$sale_access_condition = " AND sale_access != '' ";
		$hq_condition = " AND hq != '' ";
		$designation_condition = " AND designation != '' ";
	}
	
	/*--------> Check If Zone Exists <--------*/
	$sql_zone = "SELECT DISTINCT SUBSTRING_INDEX(zone, ',', 1) AS zone FROM employee_master".$emp_hierarchy_value_condition.$zone_condition." ORDER BY zone ASC";
	$res_zone = mysql_query($sql_zone);
	$zone_total = mysql_num_rows($res_zone);
	
	/*--------> Check If State Exists <--------*/	
	$sql_state = "SELECT DISTINCT SUBSTRING_INDEX(state, ',', 1) AS state FROM employee_master".$emp_hierarchy_value_condition.$state_condition." ORDER BY state ASC";
	$res_state = mysql_query($sql_state);
	$state_total = mysql_num_rows($res_state);
	
	/*--------> Check If Branch Exists <--------*/
	$sql_branch = "SELECT DISTINCT SUBSTRING_INDEX(branch_code, ',', 1) AS branch_code FROM employee_master".$emp_hierarchy_value_condition.$branch_condition." ORDER BY branch_code ASC";
	$res_branch = mysql_query($sql_branch);
	$branch_total = mysql_num_rows($res_branch);
	
	if(strtoupper($_SESSION['nick_name']) == 'STAR'){
	/*--------> Check If Sale Access Exists <--------*/
	$sql_sale_access = "SELECT DISTINCT sale_access FROM employee_master".$emp_hierarchy_value_condition.$sale_access_condition." ORDER BY sale_access ASC";
	$res_sale_access = mysql_query($sql_sale_access);
	$sale_access_total = mysql_num_rows($res_sale_access);
	}
	
	if(strtoupper($_SESSION['nick_name']) != 'STAR'){
	/*--------> Check If Headquarter Exists <--------*/
	$sql_hq = "SELECT DISTINCT hq FROM employee_master".$emp_hierarchy_value_condition.$hq_condition." ORDER BY hq ASC";
	$res_hq = mysql_query($sql_hq);
	$hq_total = mysql_num_rows($res_hq);
	
	/*--------> Check If Designation Exists <--------*/
	$sql_designation = "SELECT DISTINCT designation FROM employee_master".$emp_hierarchy_value_condition.$designation_condition." ORDER BY designation ASC";
	$res_designation = mysql_query($sql_designation);
	$designation_total = mysql_num_rows($res_designation);
	}
	?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="ajax1.js"></script>
    <script language="JavaScript" src="calendar3.js"></script>
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
	$prev_year=date('Y')-1;
	$date_format=date("$prev_year-04-01");
	$create_control = "<tr><td align=\"right\">Month:</td><td align=\"left\"><select name=\"month_select\" id=\"month_select\" onchange=\"clear_display_div();\"><option value=\"\">Select</option>";
	$sql_month_selection = "SELECT DISTINCT SUBSTRING(date,1,7) AS distinct_datetime FROM location WHERE trans_id LIKE 'A%'  
								AND SUBSTRING(date,1,10) >='".$date_format."' ORDER BY date ASC";
	$res_month_selection = mysql_query($sql_month_selection);
	while($row_month_selection = mysql_fetch_array($res_month_selection)){
		$distinct_date = $row_month_selection['distinct_datetime'];
		$year_month_split = explode("-",$distinct_date);
		$monthNum  = $year_month_split[1];
		$monthName = date('M', mktime(0, 0, 0, $monthNum, 10));
		$create_control .= "<option value=\"".$distinct_date."\">".$monthName."-".$year_month_split[0]."</option>";
	}
	$create_control .= "</select></td></tr>";
	echo "<center>";
	echo "<span style=\"font-weight:bold; font-size:14px;\">GEO FENCING EMPLOYEE DETAILS</span><br><br>";
	echo "<table width='100%'><tr><td align='left' valign='top' style='padding-left:10px;'><a href='adminMain.php' style='color:blue; font-weight:bold;'><< Back</a></td><td width='90%' align='center'>";
	attribute_selection($hidden,$create_control);
	echo "</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr></table>";
	echo "<br>";
	?>
    <div id="display" style="max-height: 350px; width:70%; overflow-y: scroll;" align="center"></div><br />
    <div id="display_details" style="max-height: 350px; max-width:1000px; overflow-y: scroll; overflow-x: scroll;" align="center" hidden></div><br />
    <div style="width:100%;" align="right" id="print_export" hidden><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
	<input type="hidden" id="report_name" />
    <?php
	echo "</center>";
	?>
    <script>
	function display_result(){
		
		<?php if($zone_total>0){ ?>	
		if(document.getElementById("zone").value.search(/\S/) == -1){
			alert('Please Select Zone');
			return false;
		}
		else
			var zone = document.getElementById("zone").value;
		<?php } ?>
		
		<?php if($state_total>0){ ?>		
		if(document.getElementById("state").value.search(/\S/) == -1){
			alert('Please Select State');
			return false;
		}
		else 
			var state = document.getElementById("state").value;
		<?php } ?>
		
		
		<?php if($branch_total>0){ ?>
		if(document.getElementById("branch").value.search(/\S/) == -1){
			alert('Please Select Branch');
			return false;
		}
		else 
			var branch = document.getElementById("branch").value;
		<?php } ?>
		
		/*var start_date = document.getElementById("start_date").value;
		var end_date = document.getElementById("end_date").value;
		if(document.getElementById("start_date").value.search(/\S/) == -1 && document.getElementById("end_date").value.search(/\S/) == -1){
			alert("Please provide start date/end date");
			return false;
		}
		if(start_date>end_date){
			alert("Start date cannot be greater than end date");
			return false;
		}*/
		if(document.getElementById("month_select").value.search(/\S/) == -1){
			alert('Please Select Month');
			return false;
		}
		var month_data = document.getElementById("month_select").value;
			
		
		document.getElementById("display_details").innerHTML = '';
		document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('branchwise_geo_fencing_employee_data.php?branch='+branch+'&month_data='+month_data,'display',0);
	}
	
	function PrintElem(elem)
	{
		var displaydiv = document.getElementById('display').innerHTML;	
		Popup(displaydiv);
	}
	
	function Popup(data) 
	{
		var mywindow = window.open('', 'Geo Fencing Employee Report', 'height=400,width=600');
		mywindow.document.write('<html><head><title>Geo Fencing Employee Report</title>');
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
		a.download = 'Geo Fencing Employee Report' + postfix + '.xls';
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