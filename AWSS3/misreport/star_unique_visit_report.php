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
	
	$current_date = date('Y-m-d');
	$month_date = date('Y-m');
	$current_month = date('m');
	if($current_month == '01' || $current_month == '02' || $current_month == '03'){
		$previous_year = date('Y', strtotime('-1 year'));
		$previous_year_date = $previous_year."-04-01";
		$previous_year_date_format=$previous_year."0401";
	}
	else{
		$previous_year_date = date('Y-04-01');
		$previous_year_date_format=date('Y0401');
	}
	
	$create_control = "<tr><td align=\"right\">Month:</td><td align=\"left\"><select name=\"month_select\" id=\"month_select\"><option value=\"\">Select</option>";
	$sql_month_selection = "SELECT DISTINCT SUBSTRING(trans_id,-14,6) AS distinct_datetime FROM customer_visit_details WHERE 
						DATE_FORMAT(SUBSTRING(trans_id,-14,8),'%Y-%m-%d') > '".$previous_year_date."' ORDER BY SUBSTRING(trans_id,-14,8) ASC";
	$res_month_selection = mysql_query($sql_month_selection);
	while($row_month_selection = mysql_fetch_array($res_month_selection)){
		$distinct_date = $row_month_selection['distinct_datetime'];
		//$year_month_split = explode("-",$distinct_date);
		//$monthNum  = $year_month_split[1];
		$monthNum=substr($distinct_date,4,2);
		$monthName = date('M', mktime(0, 0, 0, $monthNum, 10));
		$create_control .= "<option value=\"".$distinct_date."\">".$monthName."-".substr($distinct_date,0,4)."</option>";
	}
	$create_control .= "</select></td></tr>";
	
	echo "<center>";
	echo "<table width='100%'><tr><td align='left' valign='top' style='padding-left:10px;'><a href='adminMain.php' style='color:blue; font-weight:bold;'><< Back</a></td><td width='90%' align='center'>";
	echo "<span style=\"font-weight:bold; font-size:14px;\">UNIQUE VISIT REPORT</span><br><br>";
	attribute_selection($hidden,$create_control);
	echo "</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr></table>";
	echo "<br>";
	?>
    <div id="display" style="max-height: 350px; max-width:1000px; overflow-y: scroll; overflow-x: scroll;" align="center"></div><br />
    <div id="display_details" style="max-height: 350px; width:95%; overflow-y: scroll;" align="center"></div><br />
    <div style="width:100%;" align="right" id="print_export" hidden><!--input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" -->
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
		if(document.getElementById("month_select").value.search(/\S/) == -1){
			alert('Please Select Month');
			return false;
		}
		
		var zone = document.getElementById("zone").value;
		var state = document.getElementById("state").value;
		var branch = document.getElementById("branch").value;
		var department = document.getElementById("sale_access").value;
		
		var month_data = document.getElementById("month_select").value;
		var employee = document.getElementById("employee").value;
		var partsstring = ",";
		//alert(department.indexOf(partsstring)!==-1 && employee.indexOf(partsstring)!==-1);
		if(department.indexOf(partsstring)!==-1 && employee.indexOf(partsstring)!==-1 )
		{
			alert("Please select one department then All employee");
			return false;
		}
		document.getElementById("display_details").innerHTML = '';
		document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('star_unique_visit_report_data.php?employee='+employee+'&month_data='+month_data+'&zone='+zone+'&state='+state+'&branch='+branch+'&department='+department,'display',0);
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
		var mywindow = window.open('', 'Unique Visit Report', 'height=400,width=600');
		mywindow.document.write('<html><head><title>Customer Visit Report</title>');
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
		//var get_report_name = document.getElementById("report_name").value
		var dt = new Date();
		var day = dt.getDate();
		var month = dt.getMonth() + 1;
		var year = dt.getFullYear();
		var hour = dt.getHours();
		var mins = dt.getMinutes();
		var postfix = day + "." + month + "." + year + "_" + hour + "." + mins;
		
		var a = document.createElement('a');
		var data_type = 'data:application/vnd.ms-excel';
		var table_div = document.getElementById('display');
		var table_html = table_div.outerHTML.replace(/ /g, '%20');
		a.href = data_type + ', ' + table_html;
		a.download = 'Unique Visit Report' + postfix + '.xls';
		a.click();
	}
function exporttocsv_part(part_one_data){
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
	
	a.href = 'data:application/vnd.ms-excel,' + part_one_data+ encodeURIComponent(tab_text);
	a.download = 'Unique Visit Report' + postfix + '.xls';
	document.body.appendChild(a);
	a.click();
	document.body.removeChild(a);
}
function visited_customer_details(val1,val2)
	{
		document.getElementById("display_details").innerHTML = '';
		//document.getElementById("display_table").style.display = 'none';
		if(document.getElementById("display_table_nonvisited"))
		{
		document.getElementById("display_table_nonvisited").innerHTML = '';
		}
		document.getElementById("display_details").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('visit_nonvisit_customer_data_empwise.php?employee='+val1+'&month_data='+val2+'&type=visited','display_details',0);
		document.getElementById("print_export").hidden = false;
		//document.getElementById("display_table").innerHTML = '';
	  }

function non_visited_customer_details(val1,val2)
	{
		document.getElementById("display_details").innerHTML = '';
		if(document.getElementById("display_table_visited"))
		{
		document.getElementById("display_table_visited").innerHTML = '';
		}
		document.getElementById("display_details").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('visit_nonvisit_customer_data_empwise.php?employee='+val1+'&month_data='+val2+'&type=nonvisited','display_details',0);
		document.getElementById("print_export").hidden = false;
		//document.getElementById("display_table").innerHTML = '';
	  }
	</script>
    <?php
}
?>