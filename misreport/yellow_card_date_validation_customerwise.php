<?php
ob_start();
session_start();
require("adminUtils.php");
require ("attribute_selection.php");
if($_SESSION['admin_login']=="")  		header("product:index.php");
$mode = $_REQUEST['mode'];
if($mode == 'editcustomer')		    edit_record($_REQUEST['row_id']);
else {
disphtml("main();");
}

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
	$create_control = "<tr><td colspan=\"2\" align=\"center\">
From:<input type=\"date\" name=\"start_date\" id=\"start_date\" style=\"height:15px;\" />
To:<input type=\"date\" name=\"end_date\" id=\"end_date\" style=\"height:15px;\" />
</td></tr><tr><td align=\"right\">Validation Date:</td><td align=\"left\"><input type=\"date\" name=\"validation_date\" id=\"validation_date\" style=\"height:15px;\" /></td></tr>";
	echo "<center>";
	echo "<table width='100%'><tr><td align='left' valign='top' style='padding-left:10px;'><a href='adminMain.php' style='color:blue; font-weight:bold;'><< Back</a></td><td width='90%' align='center'>";
	echo "<table width=\"80%\" align=\"center\" border=\"0\" cellpadding=\"5\" cellspacing=\"1\" >
                    <tr> 
                        <td align=\"center\" class=\"ERR\">".stripslashes($GLOBALS['err_msg'])."</td>
                        <td align=\"right\" colspan=\"2\"></td>
                    </tr>
                </table><span style=\"font-weight:bold; font-size:14px;\">Yellow Card Date Validation Customer Wise</span><br><br>";
	attribute_selection($hidden,$create_control);
	echo "</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
	
	</table>";
	echo "<br>";
	?>
    <div id="display" style="max-height: 450px; max-width:1200px; overflow-y: scroll; overflow-x: scroll;" align="center"></div><br />
    <div id="display_details" style="max-height: 350px; width:95%; overflow-y: scroll;" align="center"></div><br />
    <!--div style="width:100%;" align="right" id="print_export" hidden><input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttocsv();" >
</div-->
	<input type="hidden" id="report_name" />
    <?php
	echo "</center>";
	?>
    <form name="frm_opts" action="customer_base_latt_longi_edit.php" method="post" >
        <input type="hidden" name="mode" value="<?=$_REQUEST['mode']?>">
        <input type="hidden" name="row_id" value="">
    </form>
    <script>
	function access_add_edit(ID)
	{
		//alert();
		document.frm_opts.mode.value='editcustomer';
		document.frm_opts.row_id.value=ID;
		document.frm_opts.submit();
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
		if(document.getElementById("validation_date").value.search(/\S/) == -1){
			alert('Please choose validation date');
			return false;
		}
		/*if(document.getElementById("sale_access"))
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
		}*/
		
		var zone = document.getElementById("zone").value;
		var state = document.getElementById("state").value;
		var branch = document.getElementById("branch").value;
		var start_date = document.getElementById("start_date").value;
		var end_date = document.getElementById("end_date").value;
		var validation_date = document.getElementById("validation_date").value;
		
		if(document.getElementById("start_date").value.search(/\S/) == -1 && document.getElementById("end_date").value.search(/\S/) == -1){
			alert("Please provide start date/end date");
			return false;
		}
		
		if(start_date>end_date){
			alert("Start date cannot be greater than end date");
			return false;
		}
		if(validation_date < end_date)
		{
			alert("validation date cannot be smaller than end date");
			return false;
		}
		
		//var employee = document.getElementById("employee").value;
		var partsstring = ",";
		//alert(department.indexOf(partsstring)!==-1 && employee.indexOf(partsstring)!==-1);
		/*if(department.indexOf(partsstring)!==-1 && employee.indexOf(partsstring)!==-1 )
		{
			alert("Please select one department then All employee");
			return false;
		}*/
		//string.indexOf(substring) !== -1;
		document.getElementById("display_details").innerHTML = '';
		document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('yellow_card_date_validation_customerwise_data.php?branch='+branch+'&start_date='+start_date+'&end_date='+end_date+'&validation_date='+validation_date+'&zone='+zone+'&state='+state,'display',0);
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
		var mywindow = window.open('', 'Actinable Report', 'height=400,width=600');
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
	
	</script>
    <?php
}

?>