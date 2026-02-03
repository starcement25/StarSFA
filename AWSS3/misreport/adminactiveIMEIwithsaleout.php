<?php
ob_start();
	session_start();
	require("adminUtils.php");
	if($_SESSION['admin_login']=="")  		header("location:index.php");
	disphtml("main();");
	function main(){
	$current_date=date('Y-m-d');
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
 <style>
.datatable{
  width:98%;
  table-layout: fixed;
  }
.tbl-header{
  background-color: rgba(255,255,255,0.3);
 }
.tbl-content{
  height:400px;
  overflow-x:auto;
  margin-top: 0px;
  border: 1px solid rgba(255,255,255,0.3);
}
.datatable th{
  padding: 20px 15px;
  text-align: left;
  font-weight: 500;
  font-size: 12px;
  color: #fff;
  text-transform: uppercase;
}
.datatable td{
  padding: 15px;
  text-align: left;
  vertical-align:middle;
  font-weight: 300;
  font-size: 12px;
  color: #000000;
  border-bottom: solid 1px rgba(255,255,255,0.1);
}
/* demo styles */
/* for custom scrollbar for webkit browser*/
::-webkit-scrollbar {
    width: 6px;
} 
::-webkit-scrollbar-track {
    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3); 
} 
::-webkit-scrollbar-thumb {
    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3); 
}
</style>

</head>

<body>
<center>
<br>
<div id="date_div" style="width:60%;" >
<table width="100%" align="center" cellpadding="2" cellspacing="2" border="0">
	<tr>
		<td width="100%" align="left" valign="middle" style="padding:10px;"><strong> Administrator >> Active IMEI with sale</strong></td>
	</tr>
    <tr>
		<td valign="top" >
			<table width="80%" align="center" border="0" cellpadding="5" cellspacing="2" class="border" id="todayAttDisplay">
				<tr class="TDHEAD" > 
					<td colspan="7" align="center"><strong>ATTRIBUTES SELECTION</strong></td>
				</tr>
                <tr>
                <td align="right" width="45%">FSO:</td>
                <td width="55%">
                    <select name="fso_id" id="fso_id" onChange="filter_display();">
                    <option value="">Select</option>
                    <?php
                    $sql_fso = "SELECT emp_code,emp_name FROM employee_master WHERE acedns='Y' AND emp_code 
                                    IN(SELECT DISTINCT reporting_to FROM employee_master WHERE acedns='Y') AND designation='FOS' ORDER BY emp_name ASC";
                    $res_fso = mysql_query($sql_fso);
                    while($row_fso = mysql_fetch_array($res_fso)){
                        $emp_code = $row_fso['emp_code'];
                        $emp_name = $row_fso['emp_name'];
                        //$emp_code_string .= "'".$emp_code."',";
                        $emp_code_string .= $emp_code.",";
                    ?>
                    <option value="<?php echo $emp_code;?>"><?php echo $emp_name;?></option>
                    <?php }
                    $emp_code_string = rtrim($emp_code_string,",");
                    ?>
                    <option value="<?php echo $emp_code_string;?>">All</option>
                    </select>
                    </td>
                 </tr>
                <tr>
                    <td align="right" width="45%">Filter:</td>
                    <td width="55%">
                        <input type="radio" name="filterval" id="customerwise" value="customerwise" onChange="javascript:filter_display();"/>Party Wise
                        <input type="radio" name="filterval" id="skuwise" value="skuwise" onChange="javascript:filter_display();"/>Model Wise
                    </td>
                </tr>
                <tr id="partydisplay" style="display:none;">
                    <td align="right" width="45%">Party:</td>
                    <td width="55%"><div id="customer_select_div"></div></td>
                </tr>
                 <tr id="modeldisplay" style="display:none;">
                    <td align="right" width="45%">Model:</td>
                    <td width="55%"><div id="sku_select_div"></div></td>
                </tr>
              <tr><td colspan="2" align="center">
            From:<input type="date" name="start_date" id="start_date" style="height:20px;" />
            To:<input type="date" name="end_date" id="end_date" style="height:20px;" />
            <input type="submit" name="submit" value="Submit" onClick="show_data();" />
            <td>
         </tr>
	</table>
    </td>
 </tr>
 </table>

</div>
<br>
<div id="display" style="max-height: 370px; width:90%;overflow-y: scroll;" align="center"></div><br />
<!--<img src="ajax-loader.gif" id="ajaxloader">-->
</div>
<br />
</center>
</body>
<script>
function filter_display(){
	//alert(document.getElementById("filterval").checked);
	if(document.getElementById("customerwise").checked)
	{
		document.getElementById("partydisplay").style.display="";
		document.getElementById("modeldisplay").style.display="none";
		if(document.getElementById("prod_code")){
			document.getElementById("prod_code").value="";
		}
		if(document.getElementById("fso_id").value.search(/\S/) == -1)
		return false;
		var fso_id = document.getElementById("fso_id").value;
		document.getElementById("customer_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_state_related_data.php?empcode='+fso_id+'&type=cust','customer_select_div',0);
	}
	if(document.getElementById("skuwise").checked)
	{
		document.getElementById("partydisplay").style.display="none";
		document.getElementById("modeldisplay").style.display="";
		document.getElementById("customer_code").value="";
		if(document.getElementById("customer_code")){
			document.getElementById("customer_code").value="";
		}
		document.getElementById("sku_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_state_related_data.php?type=model','sku_select_div',0);
	}
}
function select_customer(){
	if(document.getElementById("fso_id").value.search(/\S/) == -1)
		return false;
	var fso_id = document.getElementById("fso_id").value;
	document.getElementById("customer_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('get_state_related_data.php?empcode='+fso_id+'&type=cust','customer_select_div',0);
}

function show_data()
{
	var start_date = document.getElementById("start_date").value;
	var end_date = document.getElementById("end_date").value;
	var fso_id = document.getElementById("fso_id").value;
	var prod_code='';
	var customer_code='';
	var current_date="<?=$current_date;?>";
	if(document.getElementById("start_date").value.search(/\S/) == -1){
		alert('Provide start date');
		return false;
	}
	if(document.getElementById("end_date").value.search(/\S/) == -1){
		alert('Provide end date');
		return false;
	}
	if(start_date>end_date){
		alert("Start date cannot be greater than end date");
		return false;
	}
	if(start_date>current_date){
		alert("Start date cannot be greater than Today's date");
		return false;
	}
	if(document.getElementById("fso_id").value.search(/\S/) == -1){
		alert('Provide FSO');
		return false;
	}
	if(document.getElementById("customerwise").checked)
	{
		if(document.getElementById("customer_code").value.search(/\S/) == -1){
			alert('Provide customer');
			return false;
		}
		else
		{
			customer_code = document.getElementById("customer_code").value;
		}
	}
    if(document.getElementById("skuwise").checked)
	{
		if(document.getElementById("prod_code").value.search(/\S/) == -1){
			alert('Provide model');
			return false;
		}
		else
		{
			prod_code = document.getElementById("prod_code").value;
		}
	}
		
	document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('activeIMEIwithsaleoutdata.php?start_date='+start_date+'&end_date='+end_date+'&fso_id='+fso_id+'&customer_code='+customer_code+'&prod_code='+prod_code,'display',0);
}

function PrintElem(elem)
{
	var displaydiv = document.getElementById('display').innerHTML;	
	Popup(displaydiv);
}

function Popup(data) 
{
	var mywindow = window.open('', 'Active IMEI with sale', 'height=400,width=600');
	mywindow.document.write('<html><head><title>Active IMEI with sale</title>');
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

	function exporttocsv()
	{
		if(document.getElementById("start_date"))
		{
			var start_date = document.getElementById("start_date").value;
		}
		if(document.getElementById("end_date"))
		{
			var end_date = document.getElementById("end_date").value;
		}
		if(document.getElementById("fso_id"))
		{
			var fso_id = document.getElementById("fso_id").value;
		}
		if(document.getElementById("prod_code"))
		{
			var prod_code=document.getElementById("prod_code").value;
		}
		else
		{
			var prod_code='';
		}
		if(document.getElementById("customer_code"))
		{
			var customer_code=document.getElementById("customer_code").value;
		}
		else
		{
			var customer_code='';
		}

		window.open('activeIMEIwithsaleoutdataexport.php?start_date='+start_date+'&end_date='+end_date+'&fso_id='+fso_id+'&customer_code='+customer_code+'&prod_code='+prod_code,'mywindow')	;
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