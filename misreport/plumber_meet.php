<?php
	ob_start();
	session_start();
	require("adminUtils.php");
	require ("attribute_selection.php");
	if($_SESSION['admin_login']=="")  		header("product:index.php");

	disphtml("main();");

	function main(){
		if($_SESSION['admin_login']=="admin"){
			$emp_hierarchy_value = '';
			$emp_hierarchy_value_condition = '';
			$zone_condition = " WHERE zone != '' ";
			$state_condition = " WHERE state != '' ";
			$branch_condition = " WHERE branch_code != '' ";
			$sale_access_condition = " WHERE sale_access != '' ";
			$hq_condition = " WHERE hq != '' ";
			$designation_condition = " WHERE designation != '' ";
		}
		else{
			$emp_hierarchy_value=return_employee_hierarchy($_SESSION['admin_login']);
			$emp_hierarchy_value_condition = " WHERE emp_code IN(".$emp_hierarchy_value.") ";
			$zone_condition = " AND zone != '' ";
			$state_condition = " AND state != '' ";
			$branch_condition = " AND branch_code != '' ";
			$sale_access_condition = " AND sale_access != '' ";
			$hq_condition = " AND hq != '' ";
			$designation_condition = " AND designation != '' ";
	}	
?>

<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title> Welcome To ACEDNS PRODUCT Administrator Control Panel </title>

<link href="https://salesmpower.acedns.in/css/adminStyle.css" rel="stylesheet" type="text/css" />


<script type ="text/javascript" src="date.js"></script>

<!--
<script type="text/JavaScript">

function timedRefresh(timeoutPeriod) {

	setTimeout("location.reload(true);",timeoutPeriod);

}


</script>

<script type="text/javascript" src="jquery.freezeheader.js">
    
</script>

<script type="text/javascript">

$(document).ready(function(){

    $("table").freezeHeader({ top: true, left: true });

}); -->

</script>


<style>
    .disp{
display: none;
}
    </style>  

</head>

<!-- <form name="frm_logout"	action="login.php" method="post">

<input name="mode" type="hidden" value="logout">

</form> -->

<body  >

<script language="JavaScript" type="text/javascript" src="adminEssential.js"></script>

<script language="JavaScript" type="text/javascript" src="mm_menu.js"></script>



<!--script type="text/javascript" src="popcalendar.js"></script!-->



 <!--<script language="JavaScript" src="calendar3.js"></script>



<script language="JavaScript1.2" type="text/javascript">mmLoadMenus();</script> -->


<table width="100%" border="0" cellpadding="0" cellspacing="0" class="maintable">
<tr>
<td valign="top" ><head>
	<!--script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script-->
    <script type="text/javascript" src="ajax1.js"></script>
    <script src="http://cdn.jsdelivr.net/webshim/1.12.4/extras/modernizr-custom.js"></script>
    <!-- polyfiller file to detect and load polyfills -->
    <script src="http://cdn.jsdelivr.net/webshim/1.12.4/polyfiller.js"></script>
    <script>
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
	function showMe(ans)
{
   console.log(ans.value);
   if(ans.value > 1){
    document.getElementById('branch').classList.remove('disp');
   }else{
    document.getElementById('branch').classList.add('disp');
   }
   
};

function showMee(ans)
{
   console.log(ans.value);
   if(ans.value > 1){
    document.getElementById('route').classList.remove('disp');
   }else{
    document.getElementById('route').classList.add('disp');
   }
   
};
</script>
<!-- <script>
function PrintElem(elem)
{
	var displaydiv = document.getElementById("display").innerHTML;
	Popup(displaydiv);
   //Popup($(elem).html());
}

function Popup(data) 
{
	var mywindow = window.open('', 'Plumber Meet', 'height=400,width=600');
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

function exporttocsv(){
	var dt = new Date();
	var day = dt.getDate();
	var month = dt.getMonth() + 1;
	var year = dt.getFullYear();
	var hour = dt.getHours();
	var mins = dt.getMinutes();
	var postfix = day + "." + month + "." + year + "_" + hour + "." + mins;
	
	var tab_text="<table border='2px'><tr bgcolor='#87AFC6'>";
    var textRange; var j=0;
    tab = document.getElementById('display_table'); // id of table

    for(j = 0 ; j < tab.rows.length ; j++) 
    {     
        tab_text=tab_text+tab.rows[j].innerHTML+"</tr>";
    }

    tab_text=tab_text+"</table>";
	tab_text= tab_text.replace(/<A[^>]*>|<\/A>/g, "");//remove if u want links in your table
    tab_text= tab_text.replace(/<img[^>]*>/gi,""); // remove if u want images in your table
    tab_text= tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // reomves input params
		
	var a = document.createElement('a');
	
	a.href = 'data:application/vnd.ms-excel,' + encodeURIComponent(tab_text);
	a.download = 'Sale Register' + postfix + '.xls';
	document.body.appendChild(a);
	a.click();
	document.body.removeChild(a);
} 

function export_to_csv()
{
	var start_date = document.getElementById("start_date").value;
	var end_date = document.getElementById("end_date").value;
	var cust_type = document.getElementById("cust_type").value;
	var state = document.getElementById("state").value;
	var employee = document.getElementById("employee").value;
	
	window.open('sale_register_data_export.php?cust_type='+cust_type+'&start_date='+start_date+'&end_date='+end_date+'&state='+state+'&employee='+employee,'mywindow')	;
	
}
function state_emp(state){
	if(document.getElementById("state").value.search(/\S/) == -1)
		return false;
	var state = encodeURIComponent(state);
	document.getElementById("emp_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('get_state_related_data.php?state='+state+'&type=emp','emp_select_div',0);
}
</script> -->
<body>
<center>
<br>
<table width="45%" class="border" style="border-collapse:collapse;" border="1" cellpadding="2">
  <tr class="TDHEAD">
  	<td colspan="2" align="center">Select Criteria</td>
  </tr>
  <tr>
  	<td align="right">Employee:</td>
    <td align="left"><select name="emp_name" id="emp_name" onchange="showMe(this)">
    <option value="1">Select</option>
    <option value="2">All</option></select>
	<div id="emp_select_div"></div></td>
  </tr>

  <tr>
  	<td align="right">Branch:</td>
    <td align="left">
	<select id="branch" class="disp" onchange="showMee(this)">
    <option value="1">Select</option>
    <option value="2">Kolkata</option>
	<option value="3">Howrah</option></select>
    </td>
  </tr>
  <tr>
<td align="right">Route:</td>
<td align="left"><select name="route" id="route" class="disp">
<option value="1">Select</option>
<option value="2">All</option></select></td>
</tr>
<tr>
<td align="right">Date Range:</td>
<td align="left">
From:<input type="date" name="start_date" id="start_date" value="" style="height:20px;" />
To:<input type="date" name="end_date" id="end_date" value="" style="height:20px;" />
</td>
</tr>
<tr>
<td colspan="2" align="center">
<input type="submit" name="submit" value="Submit" onclick="show_data()">
</td>
  </tr></table>
<br />
<div id="display" style="max-height: 440px; width:95%; overflow-y: scroll;" align="center">
</div>
</center>
</body>
</td>
</tr>

<tr>
<td>&nbsp;</td>
</tr>

</table>
</td>
</tr>
</table>
</body>

<script>

function show_data()
{
	var empSelect=document.getElementById("emp_name");
	if(empSelect.value === "1"){
		alert("Please Select Employee");
		return false;
	}

	var branchSelect = document.getElementById("branch");
	if(branchSelect.value === "1"){
		alert("Please Select Branch");
		return false;
	}
		
	var routeSelect = document.getElementById("route");
	//alert(employee);
	if(routeSelect.value === "1"){
		alert("Please Select Route");
		return false;
	}

	var start_date = document.getElementById("start_date").value;
	var end_date = document.getElementById("end_date").value;
	
	//if(document.getElementById("start_date").value.search(/\S/) == -1 && document.getElementById("end_date").value.search(/\S/) == -1)
     //   {
		//	alert("Start date/End date cannot be empty");
		//	return false;
	//	}

		if(start_date>end_date)
        {
			alert("Start date cannot be greater than end date");
			return false;
		}

	if(document.getElementById("start_date").value.search(/\S/)== -1 || document.getElementById("end_date").value.search(/\S/)== -1)
	{
		alert("Start date/End date cannot be empty");
		return false;
	}

	return true;

	document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	GenericAjaxFunction('sale_register_data.php?start_date='+start_date+'&end_date='+end_date+'&employee='+employee,'display',0);

	// var cust_type = document.getElementById("cust_type").value;
	
	
	
	// document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
	// GenericAjaxFunction('sale_register_data.php?cust_type='+cust_type+'&start_date='+start_date+'&end_date='+end_date+'&state='+state+'&employee='+employee,'display',0);
}
</script>
<?php } ?>
