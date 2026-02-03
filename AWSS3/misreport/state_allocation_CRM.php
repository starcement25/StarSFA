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
		$state_condition = " WHERE state_code != '' ";
		$route_condition = " WHERE route_name != '' ";
	}
	else{
		$emp_hierarchy_value=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_value_condition = " WHERE emp_code IN(".$emp_hierarchy_value.") AND acedns='Y'";
		$state_condition = " WHERE state_code != '' ";
		$route_condition = " AND route_name != '' ";
	}
	  $date=gmdate('d',strtotime('+330 minute'));
	  $month=gmdate('m',strtotime('+330 minute'));
	  $year=gmdate('Y',strtotime('+330 minute'));
	  $hour=gmdate('H',strtotime('+330 minute'));
	  $minute=gmdate('i',strtotime('+330 minute'));
	  $second=gmdate('s',strtotime('+330 minute'));
	  $contentsdate =$year.'-'.$month.'-'.$date;

	/*--------> Check If State Exists <--------*/	
	$sql_state = "SELECT DISTINCT state_code AS state FROM customer_master ".$state_condition." AND customer_code LIKE 'N%' AND state_code NOT IN(SELECT state_code FROM emp_datewise_state_allocation WHERE acedns='yes') ORDER BY state_code ASC";
	$res_state = mysql_query($sql_state);
	$state_total = mysql_num_rows($res_state);
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
	echo "<span style=\"font-weight:bold; font-size:14px;\">State Allocation</span>";
	echo "<div id=\"display\"><b></b></div>";
	echo "<table width='100%'><tr><td align='left' valign='top' style='padding-left:10px;'><a href='adminMain.php' style='color:blue; font-weight:bold;'><< Back</a></td><td width='90%' align='center'>
	<table id=\"criteria_tab\" class=\"border\" width=\"45%\" style=\"border-collapse:collapse;border:1px solid #A92A61; padding:6px;\" cellpadding=\"4px\">
					<tr class=\"TDHEAD\"><td colspan=\"2\" align=\"center\">Allocation Criteria</td></tr>";
	
	$table_data .= "<tr><td align=\"right\">Choose CRE:</td><td>";
	$emp_select_control = "<select name=\"employee\" id=\"employee\">";
	$emp_select_control .= "<option value=\"\">Select</option>";
	$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE acedns='Y'  AND designation='CRE' AND emp_code NOT IN
				(SELECT emp_code FROM menu_access WHERE not_accessible_menu='CRM') ORDER BY emp_name ASC";
	$res_emp = mysql_query($sql_emp);
	while($row_emp = mysql_fetch_array($res_emp)){
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
	//$onclick = "state_distributor(this.value);";
	$table_data .= "<td><table>";
	while($row_state = mysql_fetch_array($res_state)){
	$sqlcustomercount="SELECT count(customer_code) AS total_new_customer FROM customer_master WHERE customer_code LIKE 'N%' and acedns='N' 
						AND state_code='".$row_state['state']."'";
	$rscustomercount=mysql_query($sqlcustomercount);
	$rowcustomercount=mysql_fetch_array($rscustomercount);	
	$total_customer_state=$rowcustomercount['total_new_customer'];				
	$table_data.="<tr>";
	$table_data.="<td align='left'>";
	$table_data.="<input type='checkbox' name='state[]' value='".$row_state['state']."'  class='state_class_chk'/>".$row_state['state'].
				'<font color="#FF0000">('.$total_customer_state.')</font>';
	$table_data.="</td>";
	$table_data.= "</tr>";
	}
	/*$state_select_control = "<select name=\"state\" id=\"state\">";
	$state_select_control .= "<option value=\"\">Select</option>";
	$res_state = mysql_query($sql_state);
	while($row_state = mysql_fetch_array($res_state)){
		$state = $row_state['state'];
		$state_string .= "'".$state."',";
		$state_select_control_option .= "<option value=\"'".$state."'\">".$state."</option>";
	}
	$state_string = rtrim($state_string,",");
	//$state_select_control .= "<option value=\"".$state_string."\">All</option>";
	$state_select_control .= $state_select_control_option;
	$state_select_control .= "</select>";
	$table_data .= $state_select_control;*/
	$table_data .= "</table></td></tr><br />";
	/*$table_data.="<tr><td align=\"right\">Date:</td><td><input type=\"date\" name=\"allocation_date\" id=\"allocation_date\" style=\"height:15px;\" /></td></tr><br />";*/
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
		<?php //if($state_total>0){ ?>		
		/*if(document.getElementById("state").value.search(/\S/) == -1){
			alert('Please Select State');
			return false;
		}
		else 
			var state = document.getElementById("state").value;*/
		<?php //} ?>
		checkboxesstate = document.getElementsByName('state[]');
		var valsstate='';
		for(var i=0, n=checkboxesstate.length;i<n;i++) {
		  if (checkboxesstate[i].checked==true) 
		  {
			valsstate += ","+checkboxesstate[i].value;
		  }
		}
		valsstate=valsstate.substr(1);
		if(valsstate=='')
		{
			alert('Please select at least one State');
			return false;
		}
		var state_chk = new Array;
		$('.state_class_chk:checked').each(function() {
				var state_code = $(this).val();
			state_chk.push(state_code); //';' added to separate each checked value
		});
		var encode_state = escape(state_chk);
	
		/*var today = new Date();
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
		
		if(allocation_date < CUR_DATE){
			alert("Allocation date will be greater than equal to current date.");
			return false;
		}*/
		//document.getElementById("display_details").innerHTML = '';
		document.getElementById("display").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('state_allocation_CRM_submission.php?employee='+employee+'&state='+encode_state,'display',0);
		//document.window.refresh();
		alert("State allocation successfull.");
		document.location.reload(true);
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