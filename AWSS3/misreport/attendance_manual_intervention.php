<?php
ob_start();
session_start();
require("adminUtils.php");
require ("attribute_selection.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");
disphtml("main();");
function main(){	
	$current_date = date('Y-m-d');
	/*------------------------------> Select of month (financial year)<-------------------------------*/
	$previous_month=date('m')-6;
	$previous_month_date= date('Y').'-'.$previous_month.'-'.'01';
$months = array();
for ($x = $previous_month; $x < $previous_month + 12; $x++) {
	$year=substr($previous_month_date,0,4);
	$key=$year.'-'.date('m', mktime(0, 0, 0, $x, 1));
	$months[$key] = date('F', mktime(0, 0, 0, $x, 1)).'-'.$year;
	$previous_month_date = date("Y-m-d", strtotime("+1 month", strtotime($previous_month_date)));
}
?>
<head>
	<!--script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script-->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="ajax1.js"></script>
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
</script>
<body>
<center>
<br>
<!--table width="40%" class="border" style="border-collapse:collapse;" border="1" cellpadding="4"-->
  <!--tr  class="TDHEAD">
  	<td colspan="2" align="center">Yellow Card Date Validation Special</td>
  </tr-->
  <?php

	$create_control .= "</select></td></tr><tr ><td  align=\"right\">Date:</td><td align=\"left\"><input type=\"date\" name=\"validation_date\" id=\"validation_date\" style=\"height:15px;\" />
</td></tr>";
	echo "<center>";
	echo "<span style=\"font-weight:bold; font-size:14px;\">Manual Attendance</span><br><br>";
	echo "<table width='100%'><tr><td align='left' valign='top' style='padding-left:10px;'><a href='adminMain.php' style='color:blue; font-weight:bold;'><< Back</a></td><td width='90%' align='center'>";
	//$get_control = $create_control;
	attribute_selection($hidden,$create_control);
	echo "</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr></table>";
	echo "<br>";
	?>
    <!--td align="left"><input name="submit" type="button" value="Submit" id="submitdata" onClick="get_data();" ></td-->
  <!--/tr>
</table-->
<br />
    <div id="loader" style="display:none"><br/><center><img src="ajax-loader.gif" /></center></div>

<div id="display" style="max-height: 440px; width:80%; overflow-y: scroll; margin-left:10px;" align="center">
</div>
</center>
</body>
<script>
function remove_selection(){
	//document.getElementById("today").checked = false;
	//document.getElementById("mtd").checked = false;
	//document.getElementById("custom").checked = false;
	document.getElementById("date_div").hidden = true;
	document.getElementById("display").innerHTML = '';
}

function get_data(){
	var month_select = document.getElementById("month_select").value;
	var month_select_validate=month_select+'-'+'01';
	if(document.getElementById("month_select").value.search(/\S/) == -1){
		alert('Please choose validation month');
		return false;
	}
	if(document.getElementById("validation_date").value.search(/\S/) == -1){
		alert('Please choose validation date');
		return false;
	}
	//alert(document.getElementById("validation_date").value);
	
	var dateval = encodeURIComponent(document.getElementById("validation_date").value);
	var datearray = dateval.split("-");
	var newdate = datearray[0] + '-' + datearray[1] + '-' + datearray[2];
	if(newdate < month_select_validate)
	{
		alert('Please choose proper validation date');
		return false;
	}
	document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader"><br><br><center><div align="center" style="color:green; font-weight:bold;">Please Wait For Few Minutes...</div></center>';
	GenericAjaxFunction('yellow_card_date_submission.php?month_select='+month_select+'&newdate='+newdate,'display',0);
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
		if(document.getElementById("validation_date").value.search(/\S/) == -1){
			alert('Please choose validation date');
			return false;
		}

		
		var zone = document.getElementById("zone").value;
		var state = document.getElementById("state").value;
		var branch = document.getElementById("branch").value;
		var department = document.getElementById("sale_access").value;
		var employee = document.getElementById("employee").value;
		/*var multipledropdown = document.getElementsByName('employee[]');
		var valsemployee='';
		alert(multipledropdown);
		for(var i=0, n=multipledropdown.length;i<n;i++) {
			
		  if (multipledropdown[i].selected==true) 
		  {
			alert(multipledropdown[i].selected.value);
			valsemployee += ","+multipledropdown[i].selected.value;
		  }
		}
		alert(valsemployee);*/
		var dateval = encodeURIComponent(document.getElementById("validation_date").value);
		var datearray = dateval.split("-");
		var newdate = datearray[0] + '-' + datearray[1] + '-' + datearray[2];
		document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader"><br><br><center><div align="center" style="color:green; font-weight:bold;">Please Wait For Few Minutes...</div></center>';
		GenericAjaxFunction('attendance_manual_intervention_data.php?employee='+employee+'&newdate='+newdate,'display',0);
	}
	function show_input_text(dns_emp_code){
		document.getElementById("showflag_"+dns_emp_code).style.display='none';
		document.getElementById("updateflag_"+dns_emp_code).style.display='';
	}
	function show_input_text_checkout(dns_emp_code){
		document.getElementById("showcheckoutflag_"+dns_emp_code).style.display='none';
		document.getElementById("updatecheckoutflag_"+dns_emp_code).style.display='';
	}
	function text_blank(dns_emp_code)
	{
		document.getElementById("attendance_time_"+dns_emp_code).value='';
	}
	function text_blank_checkout(dns_emp_code)
	{
		document.getElementById("checkout_time_"+dns_emp_code).value='';
	}
   function update_att_time(emp_code){
		var employee = document.getElementById("employee").value;
		var dateval = encodeURIComponent(document.getElementById("validation_date").value);
		var datearray = dateval.split("-");
		var newdate = datearray[0] + '-' + datearray[1] + '-' + datearray[2];
		var attendance_time = document.getElementById("attendance_time_"+emp_code).value;
		if(attendance_time.length < 8)
		{
			attendance_time +=':00';
		}
		if(attendance_time.length!=8)
		{
			alert('Attendance Time format will be hh:mm:ss');
		}
		else
		{
			var url = 'attendance_manual_intervention_data.php';
			document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
			GenericAjaxFunction(''+url+'?employee='+employee+'&emp_code='+emp_code+'&newdate='+newdate+'&attendance_time='+attendance_time+'&opt_type=atttimeupdate','display',0);
		}
		/*if(survey_type == 'KYC' )
		document.getElementById("print_export").hidden = false;*/
	}
	function update_checkout_time(emp_code){
		var employee = document.getElementById("employee").value;
		var dateval = encodeURIComponent(document.getElementById("validation_date").value);
		var datearray = dateval.split("-");
		var newdate = datearray[0] + '-' + datearray[1] + '-' + datearray[2];
		var checkout_time = document.getElementById("checkout_time_"+emp_code).value;
		if(checkout_time.length < 8)
		{
			checkout_time +=':00';
		}
		if(checkout_time.length!=8)
		{
			alert('Checkout Time format will be hh:mm:ss');
		}
		else
		{		
			var url = 'attendance_manual_intervention_data.php';
			document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
			GenericAjaxFunction(''+url+'?employee='+employee+'&emp_code='+emp_code+'&newdate='+newdate+'&checkout_time='+checkout_time+'&opt_type=checkouttimeupdate','display',0);
		}
		/*if(survey_type == 'KYC' )
		document.getElementById("print_export").hidden = false;*/
	}
</script>
<?php } ?>