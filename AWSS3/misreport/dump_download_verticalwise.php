<?php
ob_start();
session_start();
if(strtoupper($_SESSION['admin_login'])=='ADMIN' ||strtoupper($_SESSION['admin_login'])=='SUPERVISOR' ||strtoupper($_SESSION['admin_login'])=='E0042' ||strtoupper($_SESSION['admin_login'])=='E0076'){
		require("adminUtils.php");
	}
	else
	{
		require("adminUtils_HBC_SFATS.php");
	}
require ("attribute_selection.php");
if($_SESSION['admin_login']=="")  		header("product:index.php");

disphtml("main();");

function main(){
	?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="ajax1.js"></script>
    <?php
	$hidden = "";
	$create_control = "";
	echo "<center>";
	echo "<span style=\"font-weight:bold; font-size:14px;\">Dump Dowload</span><br><br>";
	echo "<table width='100%'><tr><td align='left' valign='top' style='padding-left:10px;'><a href='adminMain.php' style='color:blue; font-weight:bold;'><< Back</a></td><td width='90%' align='center'>";

	$table_data = "<div id=\"display_data\"><table id=\"criteria_tab\" class=\"border\" width=\"45%\" style=\"border-collapse:collapse;border:1px solid #A92A61; padding:6px;\" ><tr class=\"TDHEAD\"><td colspan=\"2\" align=\"center\">Select Criteria</td></tr>";

		$table_data .="<tr><td align=\"right\">Choose attribute:</td><td><div id=\"attribute_div\">";
		$table_data .="<select name=\"attribute\" id=\"attribute\" >";
		$table_data .="<option value=\"\">Select</option>";
		$table_data .="<option value=\"branch\">Branch</option>";
		$table_data .="<option value=\"broker\">Broker</option>";
		$table_data .="<option value=\"customer\">Customer</option>";
		$table_data .="<option value=\"employee\">Employee</option>";
		if(strtoupper($_SESSION['vertical_value'])=='SPECIALTY FATS'){
		 $table_data .="<option value=\"mrp\">Mrp</option>";
		}
		$table_data .="<option value=\"packing\">Packing</option>";
		$table_data .="<option value=\"product\">Product</option>";
		$table_data .="<option value=\"freight\">Secondary Freight</option>";
		$table_data .="<option value=\"state\">State</option>";
		$table_data .="</select></div></td></tr>";

	echo $table_data."<tr><td colspan=\"2\" align=\"right\"><input type=\"submit\" name=\"submit\" value=\"Submit\" onclick=\"display_result();\"></td></tr></table></div><div id=\"loader\" style=\"display:none\"><br/><center><img src=\"ajax-loader.gif\" /></center></div>";

	echo "</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr></table>";
	echo "<br>";
	?>
    <div id="display" style="max-height: 350px; width:50%; overflow-y: scroll; " align="center"></div><br />
    <!--<div id="display_details" style="max-height: 350px; max-width:1000px; overflow-y: scroll; overflow-x: scroll;" align="center"></div><br />-->
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
		if(document.getElementById("attribute").value.search(/\S/) == -1){
			alert('Please Select one attribute to download dump');
			return false;
		}
		document.getElementById('loader').style.display='';
		var attribute=document.getElementById("attribute").value;
		//document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		//GenericAjaxFunction('dum_download_data.php?attribute='+attribute,'display',0);


		xmlHttp=GetXmlHttpObject()
		if (xmlHttp==null)
		{
			alert ("Browser does not support HTTP Request");
			return
		}

		var url="dump_download_data_verticalwise.php";
		var params = "attribute="+attribute;
		xmlHttp.open("POST", url, true);

	//Send the proper header information along with the request
		xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

		xmlHttp.onreadystatechange = function() {//Call a function when the state changes.
			if(xmlHttp.readyState == 4 && xmlHttp.status == 200) {
				var val=xmlHttp.responseText;
				if(val!="")
				 {
					 //alert("Success Download Started");
					 document.getElementById('loader').style.display='none';
					 window.location='http://salesmpower.acedns.in/misreport/dump/'+val;
				 }
			}
		}
		xmlHttp.send(params);
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
		mywindow.document.write('<html><head><title>PJP Report</title>');
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
			a.download = 'PJP Report' + postfix + '.xls';
			//triggering the function
			a.click();
			//just in case, prevent default behaviour
			e.preventDefault();
	}
	</script>
    <?php
}
?>
