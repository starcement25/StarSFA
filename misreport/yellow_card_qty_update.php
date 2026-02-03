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
		$previous_year = date('Y', strtotime('-1 year'));
		$previous_year_date = $previous_year."-04-01";
	}
	else{
		$previous_year_date = date('Y-04-01');
	}
	
	$create_control = "<tr><td align=\"right\">Month:</td><td align=\"left\"><select name=\"month_select\" id=\"month_select\" onchange=\"clear_display_div();\"><option value=\"\">Select</option>";
	$sql_month_selection = "SELECT DISTINCT SUBSTRING(challan_date,1,7) AS distinct_datetime FROM yellow_card_details ORDER BY challan_date ASC";
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
	echo "<span style=\"font-weight:bold; font-size:14px;\">YELLOW CARD</span><br><br>";
	echo "<table width='100%'><tr><td align='left' valign='top' style='padding-left:10px;'><a href='adminMain.php' style='color:blue; font-weight:bold;'><< Back</a></td><td width='90%' align='center'>";
	$get_control = "<tr>
						<td align=\"right\">Sub-Dealer:</td>
						<td><div id=\"subdealer_div\"></div></td>
					</tr>".$create_control;
	attribute_selection($hidden,$get_control);
	echo "</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr></table>";
	echo "<br>";
	?>
    <div id="display" style="max-height: 350px; width:65%; overflow-y: scroll;" align="center"></div><br />
    <div id="display_details" style="max-height: 350px; width:95%; overflow-y: scroll;" align="center"></div><br />
    <div id="show_update" style="max-height: 350px; width:95%; overflow-y: scroll;" align="center"></div><br />
    <div style="width:65%;" align="right" id="print_export" hidden><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
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
		document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('yellowcard_qty_update_data.php?employee='+employee+'&subdealer='+subdealer+'&month_data='+month_data+'&zone='+zone+'&state='+state+'&branch='+branch+'&department='+department,'display',0);
		
		if(document.getElementById("display").innerHTML != 'No Records Found')
			document.getElementById("print_export").hidden = false;
		
	}
	function edit_yellowcard_details(yellow_card_no){
		document.getElementById("show_update").innerHTML = '';
		document.getElementById("display_details").innerHTML = '<img src="please_wait.gif" id="ajaxloader">';
		GenericAjaxFunction('yellowcardqtyupdate.php?yellowcardno='+yellow_card_no,'display_details',0);
		document.getElementById("print_export").hidden = false;
	}
	
	function ajaxsub(){
	   var yellowcardnum =document.getElementById("yellowcardnum").value;
	   var qty=document.getElementById("qty").value;
	   GenericAjaxFunction('yellowcardupdate.php?yellowcardno='+yellowcardnum+'&qty='+qty,'show_update',0);
	   display_result();
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
	
	

	function Popup(data) 
	{
		var d = new Date();
		var date_time = d.toISOString();
		var mywindow = window.open('', 'Yellow Card Report', 'height=400,width=600');
		mywindow.document.write('<html><head><title></title>');
		/*optional stylesheet*/ //mywindow.document.write('<link rel="stylesheet" href="main.css" type="text/css" />');
		var star_mark = "<table width=\"90%\"><tr><td align=\"center\" colspan=\"2\">Sub Dealer Lifting</td></tr><tr><td align='left'><?php echo "Date:".date('d-m-Y'); ?><br><?php echo "Time:".date('h:i:s'); ?></td><td align='right'><img src=\"http://salesmpower.acedns.in/logo/STAR.png\"></td></tr></table><br>";
		var star_signature = "<br><br><table width=\"90%\"><tr><td>_____________________</td><td>____________________</td><td>___________________</td><td>_______________________</td><td>___________________________</td></tr><tr><td>Signature Of Sub<br>Dealer (Rubber Stamp)</td><td>Signature of Dealer<br>(Rubber Stamp)</td><td valign=\"top\">Signature Of SO/SE</td><td valign=\"top\">Signature Of ASM/Sr Ex.</td><td>Signature Of Sales Promoter<br>(Rubber Stamp)</td></tr></table><br>";
		mywindow.document.write('</head><body><center>'+star_mark+'');
		mywindow.document.write(data);
		mywindow.document.write(''+star_signature+'<p align=right><b>Powered By salesMpower</b></p></center></body></html>');
	
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
		
		var star_mark = "<table width=\"90%\"><tr><td align='left'><?php echo "Date:".date('d-m-Y'); ?><br><?php echo "Time:".date('h:i:s'); ?></td><td align='right'><img src=\"http://salesmpower.acedns.in/logo/STARLOGO.png\" alt=\"star_logo\"></td></tr></table><br>";
		var star_signature = "<br><br><table width=\"90%\"><tr><td>_____________________</td><td>____________________</td><td>___________________</td><td>_______________________</td><td>___________________________</td></tr><tr><td>Signature Of Sub<br>Dealer (Rubber Stamp)</td><td>Signature of Dealer<br>(Rubber Stamp)</td><td valign=\"top\">Signature Of SO/SE</td><td valign=\"top\">Signature Of ASM/Sr Ex.</td><td>Signature Of Sales Promoter<br>(Rubber Stamp)</td></tr></table><br>";
		
		var a = document.createElement('a');
		//getting data from our div that contains the HTML table
		var data_type = 'data:application/vnd.ms-excel';
		var table_div = document.getElementById('display_final');
		var table_html = table_div.outerHTML.replace(/ /g, '%20');
		a.href = data_type + ', ' + star_mark + table_html + star_signature;
		//setting the file name
		a.download = 'SubDealer Lifting' + postfix + '.xls';
		//triggering the function
		a.click();
		//just in case, prevent default behaviour
		//e.preventDefault();
	}
	
	function adhoc_function(emp_code){
		var employee = encodeURIComponent(emp_code);
		document.getElementById("subdealer_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_subdealer_data.php?employee='+employee,'subdealer_div',0);
		document.getElementById("display").innerHTML = '';
	}
	
	function clear_display_div(){
		document.getElementById("display").innerHTML = '';
	}
	</script>
    <?php
}
?>