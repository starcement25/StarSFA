<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");

disphtml("main();");	
function main()
{
?>
<head>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script type="text/javascript" src="ajax1.js"></script>
    <script src="http://cdn.jsdelivr.net/webshim/1.12.4/extras/modernizr-custom.js"></script>
    <!-- polyfiller file to detect and load polyfills -->
    <script src="http://cdn.jsdelivr.net/webshim/1.12.4/polyfiller.js"></script>
    <script>
      webshims.setOptions('waitReady', false);
      webshims.setOptions('forms-ext', {types: 'date'});
      webshims.polyfill('forms forms-ext');
    </script>
    <!--<script src="tableToExcel.js"></script>-->
    <link rel="stylesheet" href="table.css" type="text/css"/>
</head>
<script>
function validation()
{
	var start_date = document.getElementById("start_date").value;
	var end_date = document.getElementById("end_date").value;
	var emp_code = document.getElementById("employee").value;
	
	if(document.getElementById("employee").value.search(/\S/) == -1)
	{
		alert('Select Employee');
		return false;
	}
		
	if(document.getElementById("start_date").value.search(/\S/) == -1)
	{
		alert('Provide start date');
		return false;
	}
	
	if(document.getElementById("end_date").value.search(/\S/) == -1)
	{
		alert('Provide end date');
		return false;
	}
	
	if(start_date>end_date)
	{
		alert('Start date cannot be greater than end date');
		return false;
	}
	
	document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('tourtravelexpensedata.php?start_date='+start_date+'&end_date='+end_date+'&emp_code='+emp_code,'display',0);
}

function showtour()
{
	//alert('Hi');
	document.getElementById("tour_expenses").hidden = false;
	document.getElementById("food_expenses").hidden = true;
	document.getElementById("lodge_expenses").hidden = true;
	
	document.getElementById("tdtour").style.background = '#999999';
	document.getElementById("tdfood").style.background = '#CCCCCC';
	document.getElementById("tdlodge").style.background = '#CCCCCC';
	
	var toprint = 'tour_expenses';
	document.getElementById("print").setAttribute("onClick","PrintElem('#"+toprint+"')");
	
	var toexport = 'touring_expenses';
	document.getElementById("btnExport").setAttribute("onClick","exporttocsv('"+toexport+"')");
	
}

function showfood()
{
	//alert('Hi');
	document.getElementById("tour_expenses").hidden = true;
	document.getElementById("food_expenses").hidden = false;
	document.getElementById("lodge_expenses").hidden = true;
	
	document.getElementById("tdtour").style.background = '#CCCCCC';
	document.getElementById("tdfood").style.background = '#999999';
	document.getElementById("tdlodge").style.background = '#CCCCCC';
	
	var toprint = 'food_expenses';
	document.getElementById("print").setAttribute("onClick","PrintElem('#"+toprint+"')");
	
	var toexport = 'fooding_expenses';
	document.getElementById("btnExport").setAttribute("onClick","exporttocsv('"+toexport+"')");
}

function showlodge()
{
	//alert('Hi');
	document.getElementById("tour_expenses").hidden = true;
	document.getElementById("food_expenses").hidden = true;
	document.getElementById("lodge_expenses").hidden = false;
	
	document.getElementById("tdtour").style.background = '#CCCCCC';
	document.getElementById("tdfood").style.background = '#CCCCCC';
	document.getElementById("tdlodge").style.background = '#999999';
	
	var toprint = 'lodge_expenses';
	document.getElementById("print").setAttribute("onClick","PrintElem('#"+toprint+"')");
	
	var toexport = 'lodging_expenses';
	document.getElementById("btnExport").setAttribute("onClick","exporttocsv('"+toexport+"')");
}

function PrintElem(elem)
{
   Popup($(elem).html());
}

function Popup(data) 
{
	var mywindow = window.open('', 'Market Feedback Details', 'height=400,width=600');
	mywindow.document.write('<html><head><title>Tour Details</title>');
	/*optional stylesheet*/ //mywindow.document.write('<link rel="stylesheet" href="main.css" type="text/css" />');
	mywindow.document.write('</head><body ><center>');
	mywindow.document.write(data);
	mywindow.document.write('<br><br><div align=\'right\' style=\'font-weight:bold; font-size:18px;\'>Powered By SalesMpower</div></center></body></html>');

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
        //creating a temporary HTML link element (they support setting file names)
        var a = document.createElement('a');
        //getting data from our div that contains the HTML table
        var data_type = 'data:application/vnd.ms-excel';
        var table_div = document.getElementById(''+divid+'');
        var table_html = table_div.outerHTML.replace(/ /g, '%20');
        a.href = data_type + ', ' + table_html;
        //setting the file name
        a.download = 'Travelling Expenses' + postfix + '.xls';
        //triggering the function
        a.click();
        //just in case, prevent default behaviour
        e.preventDefault();
}

function print_final(){
	var start_date = document.getElementById("start_date").value;
	var end_date = document.getElementById("end_date").value;
	var emp_code = document.getElementById("employee").value;
	
	//var print_data = "tourtravelexpensedata_print.php?start_date="+start_date+"&end_date="+end_date+"&emp_code="+emp_code;
	
	$.post("tourtravelexpensedata_print.php",
    {
		start_date: start_date,
		end_date: end_date,
		emp_code: emp_code
    },
    function(data, status){
        		
		var mywindow = window.open('', 'Tour Expenses', 'height=400,width=600');
		mywindow.document.write('<html><head><title>Tour Details</title>');
		/*optional stylesheet*/ //mywindow.document.write('<link rel="stylesheet" href="main.css" type="text/css" />');
		mywindow.document.write('</head><body ><center>');
		mywindow.document.write(data);
		mywindow.document.write('<br><br><div align=\'right\' style=\'font-weight:bold; font-size:18px;\'>Powered By SalesMpower</div></center></body></html>');
	
		mywindow.document.close(); // necessary for IE >= 10
		mywindow.focus(); // necessary for IE >= 10
	
		mywindow.print();
		mywindow.close();
	
		return true;
	})
	
	
	//window.open(''+print_data+'','_blank');
}
</script>


<center>
<table cellpadding="4px" width="90%" class="border">
	<tr class="TDHEAD_SUB">
    	<td align="center">TOURS &amp; TRAVEL EXPENSES</td>
    </tr>
    <tr><td>
<table cellpadding="4px" width="100%">
	<tr>
    	<td align="left">Select Employee:
        <select name="employee" id="employee">
        	<option value="" selected>Select</option>
            <option value="all" >All</option>
            <?php
			$sql_empl = "SELECT emp_code, emp_name FROM employee_master ORDER BY emp_name ASC";
			$res_empl = mysql_query($sql_empl);
			while($row_empl = mysql_fetch_array($res_empl))
			{
				echo "<option value=\"$row_empl[emp_code]\">$row_empl[emp_name]</option>";
			}
			?>
        </select><font color="#FF0000">*</font>&nbsp;&nbsp;
        </td>
        <td align="right">From:<input type="date" name="start_date" id="start_date" style="height:20px;" /><font color="#FF0000">*</font>&nbsp;
&nbsp;To:<input type="date" name="end_date" id="end_date" style="height:20px;" /><font color="#FF0000">*</font></td>
    </tr>
    <tr>
    	<td align="center" colspan="2"><input name="submit" type="button" value="Submit" onclick="return validation();"/></td>
    </tr>
    
</table>
</td></tr>
<tr><td align="center">
<div id="display" style="max-height: 500px; width:100%; overflow-y: scroll;" align="center"></div>
</td></tr>
<tr><td align="right" colspan="2"><input name="print" type="button" value="Print" id="print" onClick="print_final();">&nbsp;
    <!--input name="export" type="button" value="Export" id="btnExport" onclick="exporttocsv('touring_expenses');" -->
</td></tr>
</table>
</center>
<?php
}
?>