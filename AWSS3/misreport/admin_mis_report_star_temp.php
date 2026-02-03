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
	echo "<center>";
	echo "<table width='100%'><tr><td align='left' valign='top' style='padding-left:10px;'><a href='adminMain.php' style='color:blue; font-weight:bold;'><< Back</a></td><td width='90%' align='center'>";
	attribute_selection($hidden);
	echo "</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr></table>";
	echo "<br>";
	?>
    <div id="display" style="max-height: 350px; width:100%; overflow-y: scroll; overflow-x: scroll;" align="center"></div><br />
    <div id="display_details" style="max-height: 350px; width:100%; overflow-y: scroll;" align="center"></div><br />
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
		document.getElementById("display_details").innerHTML = '';
		document.getElementById("display").innerHTML = '<img src="please_wait.gif" id="ajaxloader">';
		GenericAjaxFunction('admin_mis_report_data_star_test.php?employee='+employee,'display',0);
		document.getElementById("print_export").hidden = true;
		
	}
	
	function attendance(val,employee){
		var employee = encodeURIComponent(employee);
		var val = val;
		
		if(val == 'T')
			var url="showAttendanceActivity.php";
		else if(val == 'MTD' || val == 'YTD')
			var url="showAttendanceActivityMonthly.php";
		
		document.getElementById("display_details").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction(''+url+'?employee='+employee+'&val='+val+'&atd_report=true','display_details',0);
		document.getElementById("print_export").hidden = false;
		document.getElementById("report_name").value = 'Emp-Attendance-Report';
	}
	
	function customer_visit(val,employee){
		var employee = encodeURIComponent(employee);
		document.getElementById("display_details").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('admin_mis_report_customer_visit.php?employee='+employee+'&val='+val,'display_details',0);
		document.getElementById("print_export").hidden = false;
		document.getElementById("report_name").value = 'Customer-Visit-Report';
	}
	
	function order_received(val,employee){
		var employee = encodeURIComponent(employee);
		document.getElementById("display_details").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('admin_mis_report_order_received.php?employee='+employee+'&val='+val,'display_details',0);
		document.getElementById("print_export").hidden = false;
		document.getElementById("report_name").value = 'Order-Received-Report';
	}
	
	function collection_received(val,employee){
		var employee = encodeURIComponent(employee);
		document.getElementById("display_details").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('admin_mis_report_collection_received.php?employee='+employee+'&val='+val,'display_details',0);
		document.getElementById("print_export").hidden = false;
		document.getElementById("report_name").value = 'Collection-Received-Report';
	}
	
	function stock_audit(val,employee){
		var employee = encodeURIComponent(employee);
		document.getElementById("display_details").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('admin_mis_report_stock_audit.php?employee='+employee+'&val='+val,'display_details',0);
		document.getElementById("print_export").hidden = false;
		document.getElementById("report_name").value = 'Stock-Audit-Report';
	}
	
	function no_transaction(val,employee){
		var employee = encodeURIComponent(employee);
		document.getElementById("display_details").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('admin_mis_report_no_transaction.php?employee='+employee+'&val='+val,'display_details',0);
		document.getElementById("print_export").hidden = false;
		document.getElementById("report_name").value = 'No-Transaction-Report';
	}
	
	function show_mis_details(val,employee){
		var employee = encodeURIComponent(employee);
		document.getElementById("display_details").innerHTML = '<img src="please_wait.gif" id="ajaxloader">';
		GenericAjaxFunction('admin_mis_report_data_details_star_test.php?employee='+employee+'&val='+val,'display_details',0);
		//document.getElementById("print_export").hidden = false;
	}
	
	function sendDcrMail(val1,val2,val3)
	{
		xmlHttp=GetXmlHttpObject()
		if (xmlHttp==null)
		{
			alert ("Browser does not support HTTP Request");
			return
		} 
		var url="adminSendDcrMail.php?emp_code="+val1+"&nick_name="+val2+"&date="+val3;
		xmlHttp.onreadystatechange=sendMailDCR;
		xmlHttp.open("GET",url,true);
		xmlHttp.send(null);
	}
	
	function PrintElem(elem)
	{
		var displaydiv = document.getElementById("display_details").innerHTML;
		Popup(displaydiv);
	   //Popup($(elem).html());
	}

	function Popup(data) 
	{
		var get_report_name = document.getElementById("report_name").value
		var mywindow = window.open('', ''+get_report_name+'', 'height=400,width=600');
		mywindow.document.write('<html><head><title>'+get_report_name+'</title>');
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
			var table_div = document.getElementById('display_details');
			var table_html = table_div.outerHTML.replace(/ /g, '%20');
			a.href = data_type + ', ' + table_html;
			//setting the file name
			a.download = ''+get_report_name+'' + postfix + '.xls';
			//triggering the function
			a.click();
			//just in case, prevent default behaviour
			e.preventDefault();
	}
	</script>
    <?php
}
?>