<?php
ob_start();
session_start();
//require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");
if(strtoupper($_SESSION['admin_login'])=='ADMIN' || strtoupper($_SESSION['admin_login'])=='SUPERVISOR' 
	|| strtoupper($_SESSION['admin_login'])=='SYSTEM' || strtoupper($_SESSION['admin_login'])=='E0076' || strtoupper($_SESSION['admin_login'])=='GMSFATS' ||  strtoupper($_SESSION['admin_login'])=='E0042'){
		require("adminUtils.php");
	}
	else
	{
		require("adminUtils_HBC_SFATS.php");
	}

disphtml("main();");
function main(){	
	$current_date = date('Y-m-d');
	$current_date_array = explode("-",$current_date);
	$months = array (1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec');
	
	if($_SESSION['admin_login']=="admin")
	{
		//$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_condition=" 1 ";
	}
	else
	{
		$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_condition=" EM.emp_code IN (".$emp_hierarchy.")";
	}
	
?><head>
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
<body >
<center><br />
	<div>
    <table cellpadding="4">
          <tr class="TDHEAD">
          	<td colspan="2" align="center" style="font-weight:bold;">Search Criteria </td>
          </tr>
          <tr class="TDHEAD_SUB">
          	<td align="right">State:</td>
            <td>
				<?php
                $sql_state = "SELECT DISTINCT  state FROM employee_master WHERE acedns='Y' ORDER BY state ASC";
                $res_state = mysqli_query($link,$sql_state);
                $state_total = mysqli_num_rows($res_state);
				$state_select_control = "<select name=\"state\" id=\"state\" onchange=\"javascript:state_emp(this.value);\">";
				$state_select_control .= "<option value=\"\">Select</option>";
				$res_state = mysqli_query($link,$sql_state);
				while($row_state = mysqli_fetch_assoc($res_state)){
					$state = $row_state['state'];
					$state_select_control_option .= "<option value=\"'".$state."'\">".$state."</option>";
					$state_string .= "'".$state."',";
				}
				$state_string = rtrim($state_string,",");
				$state_select_control .= $state_select_control_option;
				$state_select_control .= "<option value=\"".$state_string."\">All</option>";
				echo $state_select_control .= "</select>";
                ?>
              </td>
             </tr>   

          <tr class="TDHEAD_SUB">
          	<td align="right">Employee:</td>
            <td><div id="emp_select_div"></div></td>
          </tr>
          <tr class="TDHEAD_SUB">
          	<td align="right">Customer:</td>
            <td>
            	<select name="cust_name" id="cust_name" >
         			<option value="" selected>Select</option>
                  </select>
             </td>
          </tr>            
         <tr class="TDHEAD_SUB">
          	<td></td>
            <td>               
        		<input name="submit" type="button" value="Submit" id="submit" onClick="submitdata();">
             </td>    
           </tr>
        </table>       
    </div>
    <br />
    <div id="display" style="max-height: 500px; max-width:1100px; overflow-y: scroll; overflow-x: scroll;" align="center"></div>
    <br />
    <input name="print" type="button" value="Print" id="print" onClick="PrintElem('#display');">&nbsp;
    <input name="export" type="button" value="Export" id="export" onClick="exporttocsv();">
</center>
</body>
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

function exporttocsv()
{
	var dt = new Date();
	var day = dt.getDate();
	var month = dt.getMonth() + 1;
	var year = dt.getFullYear();
	var hour = dt.getHours();
	var mins = dt.getMinutes();
	var postfix = day + "." + month + "." + year + "_" + hour + "." + mins;
	
	var a = document.createElement('a');
	//getting data from our div that contains the HTML table
	var data_type = 'data:application/vnd.ms-excel';
	var table_div = document.getElementById('display');
	var table_html = table_div.outerHTML.replace(/ /g, '%20');
	a.href = data_type + ', ' + table_html;
	//setting the file name
	a.download = 'Customer sauda limit' + postfix + '.xls';
	//triggering the function
	a.click();
	//just in case, prevent default behaviour
	e.preventDefault();
}

function PrintElem(elem)
{
   Popup($(elem).html());
}

function Popup(data) 
{
	var mywindow = window.open('', 'Stock Audit', 'height=400,width=600');
	mywindow.document.write('<html><head><title>Stock Audit</title>');
	/*optional stylesheet*/ //mywindow.document.write('<link rel="stylesheet" href="main.css" type="text/css" />');
	mywindow.document.write('</head><body >');
	mywindow.document.write(data);
	mywindow.document.write('</body></html>');

	mywindow.document.close(); // necessary for IE >= 10
	mywindow.focus(); // necessary for IE >= 10

	mywindow.print();
	mywindow.close();

    return true;
}

function show_customer_name(emp_code){
	//alert(emp_code);
	GenericAjaxFunction('customer_sauda_limit_getcustomer.php?emp_code='+emp_code,'cust_name',0);
}

function submitdata(){
	xmlHttp=GetXmlHttpObject()
		if (xmlHttp==null)
		{
			alert ("Browser does not support HTTP Request");
			return
		} 
	if(document.getElementById("emp_name").value.search(/\S/) == -1){
		alert('Provide Employee');
		return false;
	}
	if(document.getElementById("cust_name").value.search(/\S/) == -1){
		alert('Provide Customer');
		return false;
	}
	var emp_code = document.getElementById("emp_name").value;
	var cust_name = document.getElementById("cust_name").value;
	
	document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	var url="customer_wise_sauda_limit_data.php";
	xmlHttp.onreadystatechange=customersaudalimit;
	xmlHttp.open("POST",url,true);
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlHttp.send("emp_code="+emp_code+"&cust_code="+cust_name);

	//GenericAjaxFunction('customer_wise_sauda_limit_data.php?emp_code='+emp_code+'&cust_code='+cust_name,'display',0);
}
function customersaudalimit()
 {
	if(xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
	 {
		var val=xmlHttp.responseText;
		document.getElementById("display").innerHTML =val; 
	 }
 }
function state_emp(state){
		if(document.getElementById("state").value.search(/\S/) == -1)
			return false;
		var state = encodeURIComponent(state);
		document.getElementById("emp_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_state_related_data.php?state='+state+'&type=customersaudalimit','emp_select_div',0);
	}
</script>
<?php
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
</body>
</html>