<?php
ob_start();
session_start();
require("adminUtils.php");
//require ("attribute_selection.php");
if($_SESSION['admin_login']=="")  		header("product:index.php");

disphtml("main();");

function main(){
	if($_SESSION['admin_login']=="admin"){
		$emp_hierarchy_value = '';
		$emp_hierarchy_value_condition = '';
	}
	else{
		$emp_hierarchy_value=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_value_condition = " WHERE emp_code IN(".$emp_hierarchy_value.") ";
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
<center>
    <div id="display" style="max-height: 350px; width:80%; overflow-y: scroll;" align="center"></div><br />
    <div id="display_details" style="max-height: 350px; max-width:1000px; overflow-y: scroll; overflow-x: scroll;" align="center" hidden></div><br />
    <div style="width:100%;" align="right" id="print_export" hidden><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div>
	<input type="hidden" id="report_name" />
    
    <table class="border" width="40%" style="border-collapse:collapse;" cellpadding="6px">
       <tr class="TDHEAD_SUB">
      	<td align="right" width="25%">Month:</td>
        <td align="left" width="25%">
        	<?php
			$formattedMonthArray = array(
								"1" => "January", "2" => "February", "3" => "March", "4" => "April",
								"5" => "May", "6" => "June", "7" => "July", "8" => "August",
								"9" => "September", "10" => "October", "11" => "November", "12" => "December",
							);
			?>
			<!-- displaying the dropdown list -->
			<select name="month" id="month" onchange="populate_date();">
				<option value="">Select Month</option>
				<?php
				foreach ($formattedMonthArray as  $key => $month) {
					$key = str_pad($key, 2, "0", STR_PAD_LEFT);
					echo '<option '.$selected.' value="'.$key.'">'.$month.'</option>';
				}
				?>
			</select>
        </td>
        <td align="left" width="10%">Year:</td>
        <td align="left" width="">
			<select name="year" id="year" onchange="populate_date();">
				<option value="">Select Year</option>
				<?php
				$year=gmdate('Y',strtotime('+330 minute'));
				$prev_year=($year-1);
					echo '<option  value="'.$prev_year.'">'.$prev_year.'</option>';
					echo '<option  value="'.$year.'">'.$year.'</option>';
				?>
			</select>
        </td>
      </tr>
      <tr class="TDHEAD_SUB">
      	<td align="right">Date:</td>
        <td align="left" colspan="3"><div id="date_select_div"></div></td>
      </tr>
      <tr class="TDHEAD_SUB"ss>
        <td align="right" >Employee Type:</td> 
        <td align="left" colspan="3">                 	
        <input type="radio" value="ASM" name="radio_type" id="emp_type" onChange="search_emp('2');" /> ASM
        <input type="radio" value="DSM" name="radio_type" id="emp_type" onChange="search_emp('3');" /> DSM
        <input type="radio" value="ZSM" name="radio_type" id="emp_type" onChange="search_emp('4');" /> ZSM
		</td>
      </tr>
      <tr class="TDHEAD_SUB">
      	<td align="right">Employee:</td>
        <td align="left" colspan="3"><div id="emp_select_div"></div>
        </td>
        </tr>
        <tr class="TDHEAD_SUB">
        
      <tr class="TDHEAD_SUB">
      <td colspan="4" align="center">
      <input type="submit" name="submit" value="Submit" onClick="display_result();" />
      </td>
      </tr>
    </table>
    
</center>
    <script>
	function populate_date(){
		if(document.getElementById("month").value.search(/\S/) == -1)
			return false;
		if(document.getElementById("year").value.search(/\S/) == -1)
			return false;	
		var month=document.getElementById("month").value;
		var year=document.getElementById("year").value;
		document.getElementById("date_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_monthwise_date.php?month='+month+'&year='+year,'date_select_div',0);
	}
	function search_emp(val){
		if(document.getElementById("month").value.search(/\S/) == -1)
			return false;
		if(document.getElementById("year").value.search(/\S/) == -1)
			return false;	
		document.getElementById("emp_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_emp_level_data.php?emp_level='+val,'emp_select_div',0);
	}

	function display_result(){
		
		if(document.getElementById("employee").value.search(/\S/) == -1){
			alert('Please Select Employee');
			return false;
		}
		else
			var employee = document.getElementById("employee").value;
		
					
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
			
		document.getElementById("display_details").innerHTML = '';
		document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('no_activity_report_data.php?employee='+employee+'&start_date='+start_date+'&end_date='+end_date,'display',0);
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
		a.download = 'No Activity Report' + postfix + '.xls';
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