<?php
ob_start();
session_start();
require("adminUtils_CRM_CRE.php");
if($_SESSION['admin_login']=="")  		header("product:index.php");

disphtml("main();");

function main(){
	if($_SESSION['admin_login']=="admin" ){
		$emp_hierarchy_value = '';
		$emp_hierarchy_value_condition = " WHERE acedns='Y'";
		$state_condition = " WHERE state != '' ";
		$route_condition = " WHERE route_name != '' ";
	}
	else{
		$emp_hierarchy_value=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_value_condition = " WHERE emp_code IN(".$emp_hierarchy_value.") AND acedns='Y'";
		$state_condition = " WHERE state != '' ";
		$route_condition = " AND route_name != '' ";
	}
	/*--------> Check If State Exists <--------*/	
	$sql_state = "SELECT DISTINCT SUBSTRING_INDEX(state, ',', 1) AS state FROM employee_master".$state_condition." ORDER BY state ASC";
	$res_state = mysqli_query($link,$sql_state);
	$state_total = mysqli_num_rows($res_state);
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
	$hidden = " ";
	
	echo "<center>";
	echo "<span style=\"font-weight:bold; font-size:14px;\">Route Allocation</span>";
	echo "<div id=\"display\"><b></b></div>";
	echo "<table width='100%'><tr><td align='left' valign='top' style='padding-left:10px;'><a href='adminMain.php' style='color:blue; font-weight:bold;'><< Back</a></td><td width='90%' align='center'>
	<table id=\"criteria_tab\" class=\"border\" width=\"45%\" style=\"border-collapse:collapse;border:1px solid #A92A61; padding:6px;\" cellpadding=\"4px\">
					<tr class=\"TDHEAD\"><td colspan=\"2\" align=\"center\">Allocation Criteria</td></tr>";
	
	$table_data .= "<tr><td align=\"right\">Choose CRE:</td><td>";
	$emp_select_control = "<select name=\"employee\" id=\"employee\">";
	$emp_select_control .= "<option value=\"\">Select</option>";
	$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE acedns='Y'  AND designation='CRE' AND emp_code NOT IN
				(SELECT emp_code FROM menu_access WHERE not_accessible_menu='CRM') ORDER BY emp_name ASC";
	$res_emp = mysqli_query($link,$sql_emp);
	while($row_emp = mysqli_fetch_assoc($res_emp)){
		$emp_code = $row_emp['emp_code'];
		$emp_name = $row_emp['emp_name'];
		$emp_code_string .= "'".$emp_code."',";
		$emp_select_control .= "<option value=\"'".$emp_code."'\">".$emp_name."</option>";
	}
	//$emp_code_string = rtrim($emp_code_string,",");
	//$emp_select_control .= "<option value=\"".$emp_code_string."\">All</option>";
	$emp_select_control .= "</select>";
	$table_data .= $emp_select_control;
	$table_data .= "</td></tr><br />";
	$table_data .= "<tr><td align=\"right\">State:</td>";
	//$onclick = "state_route(this.value);";
	$onclick = "state_distributor(this.value);";
	$table_data .= "<td>";
	$state_select_control = "<select name=\"state\" id=\"state\" onchange=\"".$onclick."\">";
	$state_select_control .= "<option value=\"\">Select</option>";
	
	$res_state = mysqli_query($link,$sql_state);
	while($row_state = mysqli_fetch_assoc($res_state)){
		$state = $row_state['state'];
		$state_string .= "'".$state."',";
		$state_select_control_option .= "<option value=\"'".$state."'\">".$state."</option>";
	}
	$state_string = rtrim($state_string,",");
	//$state_select_control .= "<option value=\"".$state_string."\">All</option>";
	$state_select_control .= $state_select_control_option;
	$state_select_control .= "</select>";
	$table_data .= $state_select_control;
	$table_data .= "</td></tr><br />";
	$table_data .= "<tr><td align=\"right\">Distributor:</td>";
	if($state_total>0){
		$table_data .= "<td><div id=\"distributor_div\"></div></td></tr><br />";
	}
	$table_data .= "<tr><td align=\"right\">Route:</td>";
	if($state_total>0){
		$table_data .= "<td><table id=\"route_select_div\"></table></td></tr><br />";
	}
	$table_data.="<tr><td align=\"right\">Date:</td><td><input type=\"date\" name=\"allocation_date\" id=\"allocation_date\" style=\"height:15px;\" /></td></tr><br />";
	echo $table_data."<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" name=\"submit\" value=\"Submit\" onclick=\"display_result();\"></td></tr></table>";
	echo "</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr></table>";
	echo "<br>";
	?>
    <script>
	function display_result(){
		if(document.getElementById("employee").value.search(/\S/) == -1){
			alert('Please Select CRE');
			return false;
		}
		else 
			var employee = document.getElementById("employee").value;
		<?php if($state_total>0){ ?>		
		if(document.getElementById("state").value.search(/\S/) == -1){
			alert('Please Select State');
			return false;
		}
		else 
			var state = document.getElementById("state").value;
		<?php } ?>
		if(document.getElementById("distributor").value.search(/\S/) == -1){
			alert('Please Select Distributor');
			return false;
		}
		else
			var distributor = document.getElementById("distributor").value;
			
		 checkboxesroute = document.getElementsByName('route[]');
		var valsroute='';
		for(var i=0, n=checkboxesroute.length;i<n;i++) {
		  if (checkboxesroute[i].checked==true) 
		  {
			valsroute += ","+checkboxesroute[i].value;
		  }
		}
		valsroute=valsroute.substr(1);
		if(valsroute=='')
		{
			alert('Please select at least one Route');
			return false;
		}	
		var today = new Date();
		var dd = today.getDate();
		var mm = today.getMonth()+1; //January is 0!
		var yyyy = today.getFullYear();
		
		if(dd<10) {
			dd = '0'+dd
		} 
		
		if(mm<10) {
			mm = '0'+mm
		} 
		var CUR_DATE = yyyy + '-' + mm + '-' + dd;	
		if(document.getElementById("allocation_date").value.search(/\S/) == -1){
			alert("Please choose date");
			return false;
		}
		else
			var allocation_date = document.getElementById("allocation_date").value;
		
		var route_chk = new Array;
		$('.route_class_chk:checked').each(function() {
				var route_code = $(this).val();
			route_chk.push(route_code); //';' added to separate each checked value
		});
		var encode_route = escape(route_chk);
		if(allocation_date < CUR_DATE){
			alert("Allocation date will be greater than equal to current date.");
			return false;
		}
		
		//document.getElementById("display_details").innerHTML = '';
		document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('customer_allocation_CRM_submission.php?route='+encode_route+'&distributor='+distributor+'&employee='+employee+'&allocation_date='+allocation_date+'&state='+state,'display',0);
	}
	function distributor_route(distributor){
		if(document.getElementById("state").value.search(/\S/) == -1)
			return false;
		var state = document.getElementById("state").value;
		if(document.getElementById("distributor").value.search(/\S/) == -1)
			return false;
		var distributor = encodeURIComponent(distributor);
		document.getElementById("route_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_state_related_data.php?state='+state+'&distributor='+distributor+'&type=route','route_select_div',0);
	}
	/*function state_emp_lev_one(state){
		if(document.getElementById("state").value.search(/\S/) == -1)
			return false;
		var state = encodeURIComponent(state);
		document.getElementById("emp_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_state_related_data.php?state='+state+'&type=stateemplevone','emp_div',0);
	}*/
	function state_distributor(state){
		if(document.getElementById("state").value.search(/\S/) == -1)
			return false;
		var state = encodeURIComponent(state);
		document.getElementById("distributor_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_state_related_data.php?state='+state+'&type=statedist','distributor_div',0);
	}
	</script>
    <?php
}
?>