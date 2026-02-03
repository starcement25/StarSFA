<?php
//ob_start();
//  error_reporting(E_ALL);
session_start();

require("adminUtils.php");
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
	
	$current_month_year = date('M')."-".date('Y');
$current_month = date('m');
if($current_month == '01' || $current_month == '02' || $current_month == '03'){
	$previous_year = date('Y', strtotime('-1 year'));
	$current_year = date('Y');
	$months = array ('Apr-'.$previous_year.'','May-'.$previous_year.'','Jun-'.$previous_year.'','Jul-'.$previous_year.'','Aug-'.$previous_year.'','Sep-'.$previous_year.'','Oct-'.$previous_year.'','Nov-'.$previous_year.'','Dec-'.$previous_year.'','Jan-'.$current_year.'','Feb-'.$current_year.'','Mar-'.$current_year.'');
}
else{
	$previous_year = date('Y');
	
	$current_year = date('Y', strtotime('+1 year'));
	$months = array ('Apr-'.$previous_year.'','May-'.$previous_year.'','Jun-'.$previous_year.'','Jul-'.$previous_year.'','Aug-'.$previous_year.'','Sep-'.$previous_year.'','Oct-'.$previous_year.'','Nov-'.$previous_year.'','Dec-'.$previous_year.'','Jan-'.$current_year.'','Feb-'.$current_year.'','Mar-'.$current_year.'');
	
	if(strtoupper($_SESSION['nick_name'])=='SKIPPER' || strtoupper($_SESSION['nick_name'])=='HALDIRAM' || strtoupper($_SESSION['nick_name'])=='RUPA' || strtoupper($_SESSION['nick_name'])=='PARLE')
	{
			$months = array ('Jan-'.$previous_year.'','Feb-'.$previous_year.'','Mar-'.$previous_year.'','Apr-'.$previous_year.'','May-'.$previous_year.'','Jun-'.$previous_year.'','Jul-'.$previous_year.'','Aug-'.$previous_year.'','Sep-'.$previous_year.'','Oct-'.$previous_year.'','Nov-'.$previous_year.'','Dec-'.$previous_year.'','Jan-'.$current_year.'','Feb-'.$current_year.'','Mar-'.$current_year.'');

	}
}

	
	/*$create_control = "<tr><td align=\"right\">Month:</td><td align=\"left\"><select name=\"month_select\" id=\"month_select\"><option value=\"\">Select</option>";
	/*$sql_month_selection = "SELECT DISTINCT SUBSTRING(date_time,1,7) AS distinct_datetime FROM competitor_pricing WHERE 
						date_time > '".$previous_year_date."' ORDER BY date_time ASC";
	$res_month_selection = mysqli_query($link,$sql_month_selection);
	while($row_month_selection = mysqli_fetch_assoc($res_month_selection)){
		$distinct_date = $row_month_selection['distinct_datetime'];
		$year_month_split = explode("-",$distinct_date);
		$monthNum  = $year_month_split[1];
		$monthName = date('M', mktime(0, 0, 0, $monthNum, 10));
		$create_control .= "<option value=\"".$distinct_date."\">".$monthName."-".$year_month_split[0]."</option>";
	}*/
	/*foreach($months as $monthvalue){
			$create_control .= "<option>".$monthvalue."</option>";
			if($monthvalue == $current_month_year)
				break;
		}
	$create_control .= "</select></td></tr>";*/
	$create_control ='';
	$hidden = "";
	echo "<center>";
	echo "<table width='100%'><tr><td align='left' valign='top' style='padding-left:10px;'><a href='adminMain.php' style='color:blue; font-weight:bold;'><< Back</a></td><td width='90%' align='center'>";
	echo "<span style=\"font-weight:bold; font-size:14px;\">ATTENDANCE DOWNLOAD</span><br><br>";
	attribute_selection($hidden,$create_control);
	echo "</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr></table>";
	echo "<br>";
	?>
    <div id="display" style="max-height: 350px; max-width:1000px; overflow-y: scroll; overflow-x: scroll;" align="center"></div><br />
    <div id="display_details" style="max-height: 350px; width:95%; overflow-y: scroll;" align="center"></div><br />
	<div id="loader" style="display:none">
    <br/>
   <center><img src="ajax-loader.gif" /></center>
   </div>
	<input type="hidden" id="report_name" />
    <?php
	echo "</center>";
	?>
    <script>
	function GetXmlHttpObject()
{
	var xmlHttp=null;
	try
	{
		// Firefox, Opera 8.0+, Safari
		xmlHttp=new XMLHttpRequest();
	}

	catch (e)
	{
		// Internet Explorer
		try
		{
			xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e)
		{
			xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
	}
	return xmlHttp;
}

	function display_result(){
		const BASE_URL = "<?php echo ROOT_BASE_URL; ?>";
		//alert(BASE_URL)
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
		/*if(document.getElementById("month_select").value.search(/\S/) == -1){
			alert('Please Select Month');
			return false;
		}*/
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
		
		var zone = document.getElementById("zone").value;
		var state = document.getElementById("state").value;
		var branch = document.getElementById("branch").value;
		var department = document.getElementById("sale_access").value;
		
		//var month_data = document.getElementById("month_select").value;
		var employee = document.getElementById("employee").value;
		var partsstring = ",";
		//alert(department.indexOf(partsstring)!==-1 && employee.indexOf(partsstring)!==-1);
		document.getElementById('loader').style.display='';
	xmlHttp=GetXmlHttpObject()
	if (xmlHttp==null)
	{
		alert ("Browser does not support HTTP Request");
		return
	} 
	
	var url="download_attendance_xls_data_modified.php";
	var params = "start_date="+start_date+"&end_date="+end_date+"&employee="+employee;
	xmlHttp.open("POST", url, true);

	//Send the proper header information along with the request
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	
	xmlHttp.onreadystatechange = function() {//Call a function when the state changes.
		if(xmlHttp.readyState == 4 && xmlHttp.status == 200) {
			var val=xmlHttp.responseText;
			//alert(val);
			//document.write(val);
			// if(val!="")
			//  {
			// 	 //alert("Success Download Started");
			// 	 document.getElementById('loader').style.display='none';
			// 	 window.location='https://sfa.starcement.co.in/misreport/'+val+".xls";
			//  }

			if (val !== "" && val.indexOf("No Records Found") === -1) {
    document.getElementById('loader').style.display = 'none';
    window.location = 'https://sfa.starcement.co.in/misreport/' + val + ".xls";
} else {
    document.getElementById('loader').style.display = 'none';
    alert("No records found for the selected criteria.");
}
		}
	}
	xmlHttp.send(params);
	}
	function PrintElem(elem)
	{
		var displaydiv = document.getElementById("display").innerHTML;
		Popup(displaydiv);
	   //Popup($(elem).html());
	}
	function Popup(data) 
	{
		var mywindow = window.open('', 'Customer Visit Report', 'height=400,width=600');
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
	/*function exporttocsv(divid)
	{
		var get_report_name = document.getElementById("report_name").value
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
		a.download = 'Customer Visit Report' + postfix + '.xls';
		a.click();
	}*/
function exporttocsv(){
	/*var dt = new Date();
	var day = dt.getDate();
	var month = dt.getMonth() + 1;
	var year = dt.getFullYear();
	var hour = dt.getHours();
	var mins = dt.getMinutes();
	var postfix = day + "." + month + "." + year + "_" + hour + "." + mins;
	
	var tab_text="<table border='2px'><tr bgcolor='#87AFC6'>";
	var textRange; var j=0;
	//alert(document.getElementById('display_table_val').value);
	if(document.getElementById('display_table_val').value=='visited'){
	tab = document.getElementById('display_table_visited'); // id of table
	}
	if(document.getElementById('display_table_val').value=='nonvisited'){
	tab = document.getElementById('display_table_nonvisited'); // id of table
	//alert(tab);
	}
	
	for(j = 0 ; j < tab.rows.length ; j++) 
	{     
		tab_text=tab_text+tab.rows[j].innerHTML+"</tr>";
	}
	
	tab_text=tab_text+"</table>";
	tab_text= tab_text.replace(/<A[^>]*>|<\/A>/g, "");//remove if u want links in your table
	tab_text= tab_text.replace(/<img[^>]*>/gi,""); // remove if u want images in your table
	tab_text= tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // reomves input params
		
	var a = document.createElement('a');*/
	
	/*a.href = 'data:application/vnd.ms-excel,' + encodeURIComponent(tab_text);
	a.download = 'Customer Visit Report-Part I' + postfix + '.xls';
	document.body.appendChild(a);
	a.click();
	document.body.removeChild(a);*/
	
	//exporttocsv_part(encodeURIComponent(tab_text));
	var month_data = document.getElementById("month_select").value;
    var employee = document.getElementById("employee_val").value;
	var type=document.getElementById('display_table_val').value;
	window.open('visit_nonvisit_customer_data_empwise_export.php?employee='+employee+'&month_data='+month_data+'&type='+type,'mywindow')	;
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
	a.download = 'Customer Visit Report' + postfix + '.xls';
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