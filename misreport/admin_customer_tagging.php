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
	$create_control = "<tr><td align=\"right\">Route:</td>
						<td><div id=\"route_select_div\"></div></td></tr>";
	echo "<center>";
	echo "<table width='100%'><tr><td align='left' valign='top' style='padding-left:10px;'><a href='adminMain.php' style='color:blue; font-weight:bold;'><< Back</a></td><td width='90%' align='center'>";
	echo "<table width=\"80%\" align=\"center\" border=\"0\" cellpadding=\"5\" cellspacing=\"1\" >
                    <tr> 
                        <td align=\"center\" class=\"ERR\">".stripslashes($GLOBALS['err_msg'])."</td>
                        <td align=\"right\" colspan=\"2\"></td>
                    </tr>
                </table><span style=\"font-weight:bold; font-size:14px;\">Market Survey Customer Tagging</span><br><br>";
	attribute_selection($hidden,$create_control);
	echo "</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>
	</table>";
	echo "<br>";
	?>
    <div id="display" style="max-height: 450px; max-width:1200px; overflow-y: scroll; overflow-x: scroll;" align="center"></div><br />
    <div id="display_details" style="max-height: 350px; width:95%; overflow-y: scroll;" align="center"></div><br />
    <div style="width:100%;" align="center" id="print_export" hidden>&nbsp;
    <input name="export" type="button" value="Export" id="btnExport" onClick="exporttoxls();" >&nbsp;&nbsp;
    <input name="create_map" type="button" value="Create Map Link" id="btncreatemap" onClick="createmap();" >
    <input name="delete_map" type="button" value="Delete Map Link" id="btndeletmap" onClick="deletemap();" >
	</div>
	<input type="hidden" id="report_name" />
    <br />
     <div id="display_msg" style="max-height: 350px; width:50%; overflow-y: scroll;" align="center"></div><br />

 <table class="border" width="40%" style="border-collapse:collapse;" cellpadding="6px"   hidden id="table_tag_employee">
      <tr class="TDHEAD_SUB">
      	<td align="right">Tag Employee</td>
        <td align="left">
        <!--select id="employee" >
        	<option value="">Select</option>
            <option value="all">All</option>
        <?php
		$sqljemp="SELECT data FROM tagged_employee where emp_code='".$res_emp."'";
		$rsjemp=mysqli_query($link,$sqljemp);
		$rowjemp=mysqli_fetch_assoc($rsjemp);
		/*$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE acedns = 'Y' AND sale_access='Primary' ".$emp_hierarchy_value_condition." ORDER BY emp_name ASC";
		$res_emp = mysqli_query($link,$sql_emp);
		while($row_emp = mysqli_fetch_assoc($res_emp)){
			$emp_code = $row_emp['emp_code'];
			$emp_name = $row_emp['emp_name'];
			echo "<option value=\"".$emp_code."\">".$emp_name."</option>";
		}*/
		?>
        </select-->
        <input type="text" name="tag_employee" id="tag_employee" value="" />
        </td>
        </tr>
          <tr class="TDHEAD_SUB">
              <td colspan="2" align="right">
              <input type="button" name="Submit1" value="Create Tagging" onClick="tagged_employee();" />
               <input type="button" name="Submit2" value="Delete Tagging" onClick="delete_all_tagging();" />
              </td>
          </tr>
    </table>

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
		if(document.getElementById("route").value.search(/\S/) == -1){
			alert('Please Select Route');
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
		var route = document.getElementById("route").value;
		var cluster = document.getElementById("cluster").value;
		
		/*if(document.getElementById("start_date").value.search(/\S/) == -1 && document.getElementById("end_date").value.search(/\S/) == -1){
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
		GenericAjaxFunction('customer_tagging_data.php?branch='+branch+'&route='+route+'&zone='+zone+'&state='+state+'&cluster='+cluster,'display',0);
		document.getElementById("print_export").hidden = false;
		document.getElementById("table_tag_employee").hidden = false;
	}
	function tagged_employee(){
		if(document.getElementById("tag_employee").value.search(/\S/) == -1){
			alert('Please Enter Tag Employee');
			return false;
		}
		var tag_employee = document.getElementById("tag_employee").value;
		
		var customer_Array = new Array;
		$('.tagging_customer_chk:checked').each(function() {
        customer_Array.push($(this).val());
   		 });
	
		//alert(JSON.stringify(customer_Array));
	
		if(customer_Array.length == 0){
			alert('Please Check Customer');
			return false;
		}
		
		//var emp_mall_status = 'assigned';
		
		$.post("update_customer_tagging.php",
		{
			emp_code: tag_employee,
			customer_Array: customer_Array
		},
		function(data, status){
			//alert(data);
			document.getElementById('display_msg').innerHTML=data;
			//window.location.href = "misreport.php";
		})
		
	}
	function createmap(){
		var customer_Array = new Array;
		$('.tagging_customer_chk:checked').each(function() {
        customer_Array.push($(this).val());
   		 });
	
		//alert(JSON.stringify(customer_Array));
	
		if(customer_Array.length == 0){
			alert('Please Check Customer');
			return false;
		}
		
		//var emp_mall_status = 'assigned';
		$.post("create_map_link.php",
		{
			customer_Array: customer_Array
		},
		function(data, status){
			//alert(data);
			document.getElementById('display_details').innerHTML=data;
			//window.location.href = "misreport.php";
		})
		
	}
	function delete_all_tagging(){
		if(document.getElementById("tag_employee").value.search(/\S/) == -1){
			alert('Please Enter Tag Employee');
			return false;
		}
		var tag_employee = document.getElementById("tag_employee").value;
		
		if (confirm("Are Yor Sure to delete all tagging for this Employee") == true) {
			$.post("delete_customer_tagging.php",
			{
				emp_code: tag_employee
			},
			function(data, status){
				alert(data);
				//window.location.href = "misreport.php";
			})
		}
		
	}
	function deletemap(){
		window.open('http://salesmpower.acedns.in/misreport/listing_map_data.php', '_blank');
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
	function exporttocsv()
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
		a.href = data_type + ', ' + table_html;
		//setting the file name
		a.download = 'Counter Info' + postfix + '.xls';
		//triggering the function
		document.body.appendChild(a);
		a.click();
		document.body.removeChild(a);
		//just in case, prevent default behaviour
		//e.preventDefault();
	}
	function exporttoxls(){
		var customer_Array = new Array;
		$('.tagging_customer_chk:checked').each(function() {
        customer_Array.push($(this).val());
   		 });
	
		//alert(JSON.stringify(customer_Array));
	
		if(customer_Array.length == 0){
			alert('Please Check Customer');
			return false;
		}
		
		//var emp_mall_status = 'assigned';
		
		$.post("export_customer_tagging.php",
		{
			customer_Array: customer_Array
		},
		function(data, status){
			//alert(data);
			//document.getElementById('display_msg').innerHTML=data;
			window.location='http://salesmpower.acedns.in/warehouse/'+data;
			//window.location.href = "misreport.php";
		})
		
	}
	</script>
    <?php
}

?>