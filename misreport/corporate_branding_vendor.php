<?php
ob_start();
session_start();
if(strpos(strtolower($_SESSION['sale_access']),'vendor')!=false && (strtoupper($_SESSION['nick_name'])== 'STAR' || strtoupper($_SESSION['nick_name'])== 'START'))
	{
		require("adminUtils_branding.php");
	}
else if((strtoupper($_SESSION['admin_login'])=='ACCOUNTS' || $_SESSION['admin_login']=='E1697') && strtoupper($_SESSION['nick_name']) == 'STAR')
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
	$hidden = "";
	echo "<center>";
	echo "<table width='100%'><tr><td align='left' valign='top' style='padding-left:10px;'><a href='adminMain.php' style='color:blue; font-weight:bold;'><< Back</a></td><td width='90%' align='center'>";
	echo "<span style=\"font-weight:bold; font-size:14px;\">Corporate Branding</span><br><br>";
	attribute_selection($hidden,$create_control='');
	echo "</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr></table>";
	echo "<br>";
	?>
    <div id="display" style="max-height: 450px; max-width:1400px; overflow-y: scroll; overflow-x: scroll;" align="center"></div><br />
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
			var department = document.getElementById("sale_access").value;

		}
		else var department='';
		if(document.getElementById("employee").value.search(/\S/) == -1){
			alert('Please Select Employee');
			return false;
		}
		
		var zone = document.getElementById("zone").value;
		var state = document.getElementById("state").value;
		var branch = document.getElementById("branch").value;
		
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
		
		var employee = document.getElementById("employee").value;
		var partsstring = ",";
		//alert(department.indexOf(partsstring)!==-1 && employee.indexOf(partsstring)!==-1);
		if(department.indexOf(partsstring)!==-1 && employee.indexOf(partsstring)!==-1 )
		{
			alert("Please select one department then All employee");
			return false;
		}
		//string.indexOf(substring) !== -1;
		document.getElementById("display_details").innerHTML = '';
		document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('corporate_branding_vendor_data.php?employee='+employee+'&start_date='+start_date+'&end_date='+end_date+'&zone='+zone+'&state='+state+'&branch='+branch+'&department='+department,'display',0);
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
		var mywindow = window.open('', 'Branding Verification Report', 'height=400,width=600');
		mywindow.document.write('<html><head><title>Actinable Report</title>');
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
	
	function exporttocsv(){
			var display_div = 'display';
			var report_name = 'Corporate Branding Vendor'
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
			//var table_html = table_div.outerHTML.replace(/Customer Code/gi, '');
			a.href = data_type + ', ' + table_html;
			//setting the file name
			a.download = ''+report_name+'' + postfix + '.xls';
			//triggering the function
			a.click();
			//just in case, prevent default behaviour
			e.preventDefault();
}
	</script>
    <?php
	if($_REQUEST['mode']=='PO_no_update')
	{
	$survey_id=$_REQUEST['survey_id'];
	//print_r($dono);
	for($i=0;$i<=count($survey_id);$i++)
	{
	  $survey_id_val=$survey_id[$i];
	  $PO_val=$_REQUEST["PO_".$survey_id_val];
	  if($PO_val!=''){
		$upd_sql="UPDATE survey_header SET PO_no ='$PO_val',PO_submitted_by ='".$_SESSION['admin_login']."',PO_submitted_date =CURRENT_TIMESTAMP() 
					WHERE survey_id = '".$survey_id_val."'";
		mysqli_query($link,$upd_sql) or die(mysqli_error()." Error in att status update.");
	  }
	}
		?>
         <table width="50%" align="center" border="0" cellpadding="5" cellspacing="1">
				<tr> 
					<td align="center" class="ERR">PO updated successfully</td>
					<td align="right">&nbsp;</td>
					<td align="right" width="3%">&nbsp;</td>
				</tr>
				</table>
        <?php
	//$GLOBALS['err_msg']="DO ".strtoupper($status)." SUCCESSFUL.";
	//disphtml("main();");
  }// end main

}
?>