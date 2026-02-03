<?php
/* ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);*/
 
ob_start();
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
	$hidden = " hidden";
	
	$create_control = "<tr><td align=\"right\">Type:</td><td align=\"left\"><select name=\"survey_type\" id=\"survey_type\" onchange=\"show_option(this.value);\"><option value=\"\">Select</option>";
	$sql_survey_type = "SELECT DISTINCT type FROM survey_output ORDER BY type ASC";
	$res_survey_type = mysqli_query($link,$sql_survey_type);
	while($row_survey_type = mysqli_fetch_assoc($res_survey_type)){
		$survey_type = $row_survey_type['type'];
		if($survey_type != '')
		$create_control .= "<option>".$survey_type."</option>";
	}
	$create_control .= "</select></td></tr>";
	
	$create_control .= "<tr id=\"month_row\" hidden><td align=\"right\">Month:</td><td align=\"left\"><select name=\"month_select\" id=\"month_select\"><option value=\"\">Select</option>";
	/*$sql_month_selection = "SELECT DISTINCT SUBSTRING(LO.date,1,7) AS distinct_date FROM location LO, survey_output SO WHERE SO.survey_id = LO.trans_id ORDER BY LO.date ASC";
	$res_month_selection = mysqli_query($link,$sql_month_selection);
	while($row_month_selection = mysqli_fetch_assoc($res_month_selection)){
		$distinct_date = $row_month_selection['distinct_date'];
		$year_month_split = explode("-",$distinct_date);
		$monthNum  = $year_month_split[1];
		$monthName = date('M', mktime(0, 0, 0, $monthNum, 10));
		$create_control .= "<option value=\"".$distinct_date."\">".$monthName."</option>";
	}*/
	$create_control .= "</select></td></tr>";
	
	echo "<center>";
	echo "<span style=\"font-weight:bold; font-size:14px;\">OTHER REPORTS</span><br><br>";
	echo "<table width='100%'><tr><td align='left' valign='top' style='padding-left:10px;'><a href='adminMain.php' style='color:blue; font-weight:bold;'><< Back</a></td><td width='90%' align='center'>";
	attribute_selection($hidden,$create_control);
	echo "</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr></table>";
	echo "<br>";
	?>
    <div id="display" style="max-height: 350px; max-width:1050px; overflow-y: scroll; overflow-x: scroll;" align="center"></div><br />
    <div id="display_details" style="max-height: 350px; max-width:1000px; overflow-y: scroll; overflow-x: scroll;" align="center"></div><br />
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
		if(document.getElementById("sale_access").value.search(/\S/) == -1){
			alert('Please Select Department');
			return false;
		}
		if(document.getElementById("employee").value.search(/\S/) == -1){
			alert('Please Select Employee');
			return false;
		}
		
		if(document.getElementById("survey_type").value.search(/\S/) == -1){
			alert("Please Select Type");
			return false;
		}
		
		var survey_type = document.getElementById("survey_type").value;
		var zone = document.getElementById("zone").value;
		var state = document.getElementById("state").value;
		var branch = document.getElementById("branch").value;
		var department = document.getElementById("sale_access").value;
		
		if(survey_type == 'KYC' || survey_type == 'Site Visit' || survey_type == 'Technical Meets' || survey_type == 'Branding' || survey_type == 'Lead Generation'){
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
		}
		
		/*if(survey_type == 'Branding'){
			var month_data = document.getElementById("month_select").value;
			if(document.getElementById("month_select").value.search(/\S/) == -1){
				alert('Please Select Month');
				return false;
			}
		}*/
		
		var employee = document.getElementById("employee").value;
		
		if(survey_type == 'Branding'){
			var url = 'star_survey_report_branding.php';
		}
		else if(survey_type == 'KYC'){
			var url = 'star_survey_report_KYC.php';
		}
		else if(survey_type == 'Site Visit'){
			var url = 'star_survey_report_site_visit.php';
		}
		else if(survey_type == 'Technical Meets'){
			var url = 'star_survey_report_technical_meets.php';
			//alert('Work In Progress');
			//return false;
		}else if(survey_type == 'Lead Generation'){
			var url = 'lead_generation_data_new_test.php';
		}
			
		document.getElementById("display_details").innerHTML = '';
		document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		
		GenericAjaxFunction(''+url+'?employee='+employee+'&survey_type='+survey_type+'&start_date='+start_date+'&end_date='+end_date+'&zone='+zone+'&state='+state+'&branch='+branch+'&department='+department,'display',0);
		
		/*if(survey_type == 'KYC' )
		document.getElementById("print_export").hidden = false;*/
	}
	
	function PrintElem(elem)
	{
		var report_name = document.getElementById("survey_type").value;
		if(report_name == 'Branding' || report_name == 'Site Visit')
			var displaydivval = 'display_details';
		else
			var displaydivval = 'display';
		
		var displaydiv = document.getElementById(displaydivval).innerHTML;	
		Popup(displaydiv);
	   //Popup($(elem).html());
	}

	function Popup(data) 
	{
		var report_name = document.getElementById("survey_type").value;
		var mywindow = window.open('', ''+report_name+'', 'height=400,width=600');
		mywindow.document.write('<html><head><title>'+report_name+'</title>');
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
			var report_name = document.getElementById("survey_type").value;
			if(report_name == 'Branding' || report_name == 'Site Visit')
				var display_div = 'display_details';
			else
				var display_div = 'display';
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
			var table_div = document.getElementById(''+display_div+'');
			var table_html = table_div.outerHTML.replace(/ /g, '%20');
			a.href = data_type + ', ' + table_html;
			//setting the file name
			a.download = ''+report_name+'' + postfix + '.xls';
			//triggering the function
			a.click();
			//just in case, prevent default behaviour
			e.preventDefault();
	}
	
	function show_option(survey_type){
		var survey_type = survey_type;
		if(survey_type == 'KYC'){
			document.getElementById("date_div").hidden = false;
			document.getElementById("month_row").hidden = true;
		}
		else if(survey_type == 'Branding'){
			document.getElementById("date_div").hidden = false;
			document.getElementById("month_row").hidden = true;
		}
		else if(survey_type == 'Site Visit'){
			document.getElementById("date_div").hidden = false;
			document.getElementById("month_row").hidden = true;
		}else if(survey_type == 'Lead Generation'){
			document.getElementById("date_div").hidden = false;
			document.getElementById("month_row").hidden = true;
			document.getElementById("date_div").hidden = false;
			document.getElementById("month_row").hidden = true;
			document.getElementById("technical_meet_row").hidden = true;
		}
		if(survey_type == 'Technical Meets'){
			document.getElementById("date_div").hidden = false;
			document.getElementById("month_row").hidden = true;
		}
		
	}
	
	function get_visit_details(visit_type,start_date,end_date,employee,survey_type){
		//alert(visit_type+month_data+employee);
		var zone = document.getElementById("zone").value;
		var state = document.getElementById("state").value;
		var branch = document.getElementById("branch").value;
		var department = document.getElementById("sale_access").value;
		
		var employee = encodeURIComponent(employee);
		//alert(visit_type);
		//alert(survey_type);
		document.getElementById("display_details").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('star_survey_report_branding_details.php?start_date='+start_date+'&end_date='+end_date+'&visit_type='+visit_type+'&employee='+employee+'&survey_type='+survey_type+'&zone='+zone+'&state='+state+'&branch='+branch+'&department='+department,'display_details',0);
		//document.getElementById("print_export").hidden = false;
		
	}
	
	function site_visit_details(visit_status,survey_date,employee,survey_type){
		//alert(visit_status+survey_date+employee);
		var zone = document.getElementById("zone").value;
		var state = document.getElementById("state").value;
		var branch = document.getElementById("branch").value;
		var department = document.getElementById("sale_access").value;
		var start_date = document.getElementById("start_date").value;
		var end_date = document.getElementById("end_date").value;
		
		var employee = encodeURIComponent(employee);
		document.getElementById("display_details").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('star_survey_report_site_visit_data.php?visit_status='+visit_status+'&survey_date='+survey_date+'&employee='+employee+'&survey_type='+survey_type+'&zone='+zone+'&state='+state+'&branch='+branch+'&department='+department+'&start_date='+start_date+'&end_date='+end_date,'display_details',0);
		//document.getElementById("print_export").hidden = false;
		
	}
	
	function site_visit_details_all(visit_status,start_date,end_date,employee,survey_type){
		var employee = encodeURIComponent(employee);
		document.getElementById("display_details").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('star_survey_report_site_visit_data.php?visit_status='+visit_status+'&start_date='+start_date+'&end_date='+end_date+'&employee='+employee+'&survey_type='+survey_type,'display_details',0);
	}
	</script>
    <?php
}
?>