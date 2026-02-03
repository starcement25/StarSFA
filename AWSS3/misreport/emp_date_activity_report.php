<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");
disphtml("main();");
function main(){	
	$current_date = date('Y-m-d');
	if(strtoupper($_SESSION['admin_login'])=="ADMIN")
	{
		$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_condition=" 1 AND EM.acedns!='N' ";
		$customer_condition=" 1 ";
		
	}
	else
	{
		$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_condition=" EM.emp_code IN (".$emp_hierarchy.") AND EM.acedns!='N' ";
		$customer_condition = " CM.emp_code IN (".$emp_hierarchy.") ";
	}
	
	/*------------------------------> Select of month (financial year)<-------------------------------*/
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
	/*$months = array ('Apr-'.$previous_year.'','May-'.$previous_year.'','Jun-'.$previous_year.'','Jul-'.$previous_year.'','Aug-'.$previous_year.'','Sep-'.$previous_year.'','Oct-'.$previous_year.'','Nov-'.$previous_year.'','Dec-'.$previous_year.'','Jan-'.$current_year.'','Feb-'.$current_year.'','Mar-'.$current_year.'');*/
	$months = array ('Jan-'.$previous_year.'','Feb-'.$previous_year.'','Mar-'.$previous_year.'','Apr-'.$previous_year.'','May-'.$previous_year.'','Jun-'.$previous_year.'','Jul-'.$previous_year.'','Aug-'.$previous_year.'','Sep-'.$previous_year.'','Oct-'.$previous_year.'','Nov-'.$previous_year.'','Dec-'.$previous_year.'','Jan-'.$current_year.'','Feb-'.$current_year.'','Mar-'.$current_year.'');

}
?>
<head>
	<!--script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script-->
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
function show_date_div()
{
	document.getElementById("date_div").hidden = false;
}

function hide_date_div()
{
	document.getElementById("date_div").hidden = true;
}

function PrintElem(elem)
{
	var displaydiv = document.getElementById("display").innerHTML;
	Popup(displaydiv);
   //Popup($(elem).html());
}

function Popup(data) 
{
	var mywindow = window.open('', 'Monthly Activity Report', 'height=400,width=600');
	mywindow.document.write('<html><head>');
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
	//var table_html = encodeURIComponent(table_div.outerHTML.replace(/ /g, '%20'));
	//alert(table_html);return false;
	a.href = data_type + ', ' + table_html;
	//setting the file name
	a.download = 'Monthly Activity Report' + postfix + '.xls';
	//triggering the function
	document.body.appendChild(a);
	a.click();
	document.body.removeChild(a);
	//just in case, prevent default behaviour
	//e.preventDefault();
}
</script>
<body>
<center>
<br>
<table width="40%" class="border" style="border-collapse:collapse;" border="1" cellpadding="4">
  <tr  class="TDHEAD">
  	<td colspan="2" align="center">Monthly Activity Report</td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td align="right">Employee:</td>
    <td align="left">
    	<select name="employee" id="reportingto_employee" onChange="show_emplist()">
			<option value="">Select</option>
            <?php
			$sql_user="SELECT DISTINCT `reporting_to` FROM `employee_master` where designation='ASM'";
			$res_user=mysql_query($sql_user);
			while($row_user=mysql_fetch_array($res_user)){
				$userlists.=$row_user['reporting_to']."','";
			}
			$userlist=substr($userlists,0,-3);
			$sql_empl = "SELECT EM.emp_code, EM.emp_name FROM employee_master EM WHERE EM.emp_code IN('$userlists') ORDER BY EM.emp_name ASC";
			$res_empl = mysql_query($sql_empl);
			while($row_empl = mysql_fetch_array($res_empl)){
				echo "<option value=\"".$row_empl['emp_code']."\">".$row_empl['emp_name']."</option>";
			}
			?>
		 </select>
    </td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td align="right">Employee:</td>
    <td align="left">
    	<select name="employee" id="employee">
			<option value="">Select</option>
		 </select>
    </td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td align="right">Month:</td>
    <td align="left">
    	<select name="month_select" id="month_select">
        <option value="">Select</option>
    	<?php
		foreach($months as $monthvalue){
			echo "<option>".$monthvalue."</option>";
			if($monthvalue == $current_month_year)
				break;
		}
		?>
        </select>
    </td>
  </tr>
  <tr class="TDHEAD_SUB">
  	<td></td>
    <td align="left"><input name="submit" type="button" value="Submit" id="submitdata" onClick="get_data();" ></td>
  </tr>
</table>
<br />
<div id="display" style="max-height: 440px; width:80%; overflow-y: scroll; margin-left:10px;" align="center">
</div>

</center>
</body>
<script>
function show_saudawise()
{
	var emp_code = document.getElementById("employee").value;
	if(document.getElementById("employee").value.search(/\S/) == -1){
		alert('Select employee first');
		return false;
	}
		
	if(document.getElementById("today").checked == true)
	{
		var type = 'today';
	}
	else if(document.getElementById("mtd").checked == true)
	{
		var type = 'mtd';
	}
	else if(document.getElementById("custom").checked == true)
	{
		var type = 'custom';
		var start_date = document.getElementById("start_date").value;
		var end_date = document.getElementById("end_date").value;
		
		if(start_date>end_date)
		{
			alert("Start date cannot be greater than end date");
			var response1 = 0;
		}
	
	if(document.getElementById("start_date").value.search(/\S/)==-1 || document.getElementById("end_date").value.search(/\S/)==-1)
		{
			alert("Start date/End date cannot be empty");
			var response2 = 0;
		}
	}
	
	document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	if(type != 'custom')
		GenericAjaxFunction('minu_order_data.php?type='+type+'&emp_code='+emp_code,'display',0);
	else 
	{
		if(response1 != 0 && response2 != 0)
			GenericAjaxFunction('minu_order_data.php?type='+type+'&start_date='+start_date+'&end_date='+end_date+'&emp_code='+emp_code,'display',0);
	}
}

function remove_selection(){
	//document.getElementById("today").checked = false;
	//document.getElementById("mtd").checked = false;
	//document.getElementById("custom").checked = false;
	document.getElementById("date_div").hidden = true;
	document.getElementById("display").innerHTML = '';
}
function show_emplist(){
	 var empID = document.getElementById("reportingto_employee").value;
        if(empID){
			GenericAjaxFunction('ajaxEmplistData.php?emp_id='+empID,'employee',0);
        }
}

function get_data(){
	var emp_code = document.getElementById("employee").value;
	if(document.getElementById("employee").value.search(/\S/) == -1){
		alert('Provide employee name');
		return false;
	}
	
	var month = document.getElementById("month_select").value;
	if(document.getElementById("month_select").value.search(/\S/) == -1){
		alert('Provide month');
		return false;
	}
	
	//alert(emp_code+" "+month);
	document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader"><br><br><center><div align="center" style="color:green; font-weight:bold;">Please Wait For Few Minutes...</div></center>';
	GenericAjaxFunction('emp_date_activity_report_data.php?emp_code='+emp_code+'&month='+month,'display',0);
}
</script>
<?php } ?>