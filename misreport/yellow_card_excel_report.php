<?php
//ob_start();
session_start();
if(strtoupper($_SESSION['admin_login']) == 'ACCOUNTS' && strtoupper($_SESSION['nick_name']) == 'STAR')
{
	require("adminUtils_accounts.php");
}
else
{
	require("adminUtils.php");
}
require("include/config.php");
require("include/config-setup.php");
require ("attribute_selection.php");
if($_SESSION['admin_login']=="")  		header("product:index.php");

disphtml("main();");

function main(){
	 require("include/dbcon.php");

   
	date_default_timezone_set("Asia/Kolkata");
	?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="ajax1.js"></script>
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
	
	$current_date = date('Y-m-d');
	$month_date = date('Y-m');
	$current_month = date('m');
	if($current_month == '01' || $current_month == '02' || $current_month == '03'){
		//$previous_year = date('Y', strtotime('-1 year'));
		$previous_year = date('Y', strtotime('-2 year'));
		$previous_year_date = $previous_year."-04-01";
	}
	else{
		//$previous_year_date = date('Y-04-01');
		$previous_year = date('Y', strtotime('-1 year'));
		$previous_year_date = $previous_year."-04-01";
	}
	
	$create_control = "<tr><td align=\"right\">Month:</td><td align=\"left\"><select name=\"month_select\" id=\"month_select\" onchange=\"clear_display_div();\"><option value=\"\">Select</option>";
	$sql_month_selection = "SELECT DISTINCT SUBSTRING(challan_date,1,7) AS distinct_datetime FROM yellow_card_details WHERE 
						challan_date >='".$previous_year_date."' AND challan_date <='".$current_date."' ORDER BY challan_date ASC ";
	$res_month_selection = mysqli_query($link,$sql_month_selection);
	while($row_month_selection = mysqli_fetch_assoc($res_month_selection)){
		$distinct_date = $row_month_selection['distinct_datetime'];
		$year_month_split = explode("-",$distinct_date);
		$monthNum  = $year_month_split[1];
		$monthName = date('M', mktime(0, 0, 0, $monthNum, 10));
		$create_control .= "<option value=\"".$distinct_date."\">".$monthName."-".$year_month_split[0]."</option>";
	}
	$create_control .= "</select></td></tr>";
	
	echo "<center>";
	echo "<span style=\"font-weight:bold; font-size:14px;\">YELLOW CARD SUMMARY</span><br><br>";
	echo "<table width='100%'><tr><td align='left' valign='top' style='padding-left:10px;'><a href='adminMain.php' style='color:blue; font-weight:bold;'><< Back</a></td><td width='90%' align='center'>";
	//$get_control = $create_control;
	$get_control = "<tr>
						<td align=\"right\">Dealer:</td>
						<td><div id=\"dealer_div\"></div></td>
					</tr>
					<tr>
						<td align=\"right\">Sub-Dealer:</td>
						<td><div id=\"subdealer_div\"></div></td>
					</tr>".$create_control;
	attribute_selection($hidden,$get_control);
	echo "</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr></table>";
	echo "<br>";
	?>
    <div id="loader" style="display:none"><br/><center><img src="ajax-loader.gif" /></center></div>
    <div id="display" style="max-height: 350px; width:95%; overflow-y: scroll;" align="center"></div><br />
    <div id="display_details" style="max-height: 350px; width:95%; overflow-y: scroll;" align="center"></div><br />
    <div style="width:100%;" align="right" id="print_export" hidden><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
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
		var subdealer_control = document.getElementById("subdealercontrol");
		if(subdealer_control){
			if(document.getElementById("subdealercontrol").value.search(/\S/) == -1){
				alert('Please Select Sub-Dealer');
				return false;
			}
		}
		else{
			alert('No Sub-Dealer. Cannot Proceed.');
			return false;
		}
		
		if(document.getElementById("month_select").value.search(/\S/) == -1){
			alert('Please Select Month');
			return false;
		}
		document.getElementById('loader').style.display='';
		var month_data = document.getElementById("month_select").value;
		
		var zone = document.getElementById("zone").value;
		var state = document.getElementById("state").value;
		var branch = document.getElementById("branch").value;
		var department = document.getElementById("sale_access").value;
				
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
		
		var employee = document.getElementById("employee").value;
		var subdealer = document.getElementById("subdealercontrol").value;
		document.getElementById("display_details").innerHTML = '';
		/*document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('yellow_card_excel_report_data.php?employee='+employee+'&subdealer='+subdealer+'&month_data='+month_data+'&zone='+zone+'&state='+state+'&branch='+branch+'&department='+department,'display',0);
		
		if(document.getElementById("display").innerHTML != 'No Records Found')
			document.getElementById("print_export").hidden = false;*/
			
		var url="yellow_card_excel_report_data.php";
		var params = "employee="+employee+'&subdealer='+subdealer+'&month_data='+month_data+'&zone='+zone+'&state='+state+'&branch='+branch+'&department='+department;
		xmlHttpdisplay=GetXmlHttpObject()
		if (xmlHttpdisplay==null)
		{
			alert ("Browser does not support HTTP Request");
			return
		} 
		xmlHttpdisplay.open("POST", url, true);
	
		//Send the proper header information along with the request
		xmlHttpdisplay.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		
		xmlHttpdisplay.onreadystatechange = function() {//Call a function when the state changes.
			if(xmlHttpdisplay.readyState == 4 && xmlHttpdisplay.status == 200) {
				var val=xmlHttpdisplay.responseText;
				if(val!="")
				 {
					 //alert("Success Download Started");
					 document.getElementById('loader').style.display='none';
					 document.getElementById('display').innerHTML=val;
				 }
			}
		}
		xmlHttpdisplay.send(params);
		document.getElementById("print_export").hidden = false;
	}
	
	function view_details(val,employee,cust_type){
		var employee = encodeURIComponent(employee);
		document.getElementById("display_details").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('admin_mis_order_register_details.php?val='+val+'&employee='+employee+'&cust_type='+cust_type,'display_details',0);
		document.getElementById("print_export").hidden = false;
	}
	function PrintElem(elem)
	{
		var displaydiv = document.getElementById("display_final").innerHTML;
		Popup(displaydiv);
	   //Popup($(elem).html());
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
		mywindow.document.write('<html><head><title>Yellow Card Report</title>');
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
			/*var dt = new Date();
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
			a.download = 'Yellow Card Summary Report' + postfix + '.xls';
			//triggering the function
			a.click();
			//just in case, prevent default behaviour
			e.preventDefault();*/
		document.getElementById('loader').style.display='';	
		var month_data = document.getElementById("month_select").value;
		var zone = document.getElementById("zone").value;
		var state = document.getElementById("state").value;
		var branch = document.getElementById("branch").value;
		var department = document.getElementById("sale_access").value;
		var employee = document.getElementById("employee").value;
		var subdealer = document.getElementById("subdealercontrol").value;
		//alert(department);
		var url="yellow_card_excel_report_data_export.php";
		var params = "employee="+employee+'&subdealer='+subdealer+'&month_data='+month_data+'&zone='+zone+'&state='+state+'&branch='+branch+'&department='+department;
		//alert(params);
		xmlHttpdisplay=GetXmlHttpObject()
		if (xmlHttpdisplay==null)
		{
			alert ("Browser does not support HTTP Request");
			return
		} 
		xmlHttpdisplay.open("POST", url, true);
	
		//Send the proper header information along with the request
		xmlHttpdisplay.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlHttpdisplay.onreadystatechange = function() {//Call a function when the state changes.
			if(xmlHttpdisplay.readyState == 4 && xmlHttpdisplay.status == 200) {
				var val=xmlHttpdisplay.responseText;
				if(val!="")
				 {
					 //alert("Success Download Started");
					 //alert(val);
					document.getElementById('loader').style.display='none';
				 	window.location='https://salesmpower.acedns.in/misreport/'+val+".xls";
				 }
			}
		}
		xmlHttpdisplay.send(params);
	}

	function dealer_subdealer(dealer_code){
		var dealer_code = encodeURIComponent(dealer_code);
		//var employee=document.getElementById("employee").value;
		var url="get_dealer_subdealer_data.php";
		var params = "dealer="+dealer_code;
		xmlHttp=GetXmlHttpObject()
		if (xmlHttp==null)
		{
			alert ("Browser does not support HTTP Request");
			return
		} 
		xmlHttp.open("POST", url, true);
	
		//Send the proper header information along with the request
		xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		
		xmlHttp.onreadystatechange = function() {//Call a function when the state changes.
			if(xmlHttp.readyState == 4 && xmlHttp.status == 200) {
				var val=xmlHttp.responseText;
				if(val!="")
				 {
					 //alert("Success Download Started");
					 document.getElementById('subdealer_div').innerHTML=val;
				 }
			}
		}
		xmlHttp.send(params);

		/*document.getElementById("subdealer_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_dealer_subdealer_data.php?dealer='+dealer_code,'subdealer_div',0);
		document.getElementById("display").innerHTML = '';*/
	}
	function emp_dealer(emp_code){
		var employee = encodeURIComponent(emp_code);
		//alert(employee);
		document.getElementById("dealer_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_dealer_data.php?employee='+employee,'dealer_div',0);
		//GenericAjaxFunction('get_dealer_data.php','dealer_div',0);
		document.getElementById("display").innerHTML = '';
	}

	function clear_display_div(){
		document.getElementById("display").innerHTML = '';
	}
	</script>
    <?php
}
?>