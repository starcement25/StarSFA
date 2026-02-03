<?php
/*ob_start();
session_start();
require("adminUtils.php");

attribute_selection();*/

function attribute_selection($hidden,$get_control){
	?>
    <script type="text/javascript" src="../ajax1.js"></script>
    <?php
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
	
	/*--------> Check If Zone Exists <--------*/
	$sql_zone = "SELECT DISTINCT SUBSTRING_INDEX(zone, ',', 1) AS zone FROM employee_master".$emp_hierarchy_value_condition.$zone_condition." ORDER BY zone ASC";
	$res_zone = mysqli_query($link,$sql_zone);
	$zone_total = mysqli_num_rows($res_zone);
	
	/*--------> Check If State Exists <--------*/	
	$sql_state = "SELECT DISTINCT SUBSTRING_INDEX(state, ',', 1) AS state FROM employee_master".$emp_hierarchy_value_condition.$state_condition." ORDER BY state ASC";
	$res_state = mysqli_query($link,$sql_state);
	$state_total = mysqli_num_rows($res_state);
	
	/*--------> Template Formation To Ask For Input <--------*/	
	$table_data = "<div id=\"display_data\"><table id=\"criteria_tab\" class=\"border\" width=\"45%\" style=\"border-collapse:collapse;border:1px solid #A92A61; padding:6px;\" >
					<tr class=\"TDHEAD\"><td colspan=\"2\" align=\"center\">Select Criteria</td></tr>";
	
	/*-----------------> Vertical Selection <-------------------*/
	$vertical_name_array=array();
	if(vertical_fields == 'yes'){
		$sql_attendance_verticalwise = "SELECT DISTINCT SUBSTRING_INDEX(EM.vertical_value, ',', -1) as distinct_vertical_value FROM employee_master 
		EM WHERE SUBSTRING_INDEX( EM.vertical_value, ',', -1 ) != ''";
		$res_attendance_verticalwise = mysqli_query($link,$sql_attendance_verticalwise);
		$total_rows = mysqli_num_rows($res_attendance_verticalwise);
		if($total_rows>0){
			$res_attendance_verticalwise = mysqli_query($link,$sql_attendance_verticalwise);
			while($row_attendance_verticalwise = mysqli_fetch_assoc($res_attendance_verticalwise)){
				$dist_vert_value = trim($row_attendance_verticalwise['distinct_vertical_value']);
				if(strtoupper($_SESSION['nick_name']) == 'RUPA'){
					$pos = substr($dist_vert_value,0,1);
					if($pos == 'M'){
						$dist_vert_value = 'MACROMAN';
					}
				}
				
				if($dist_vert_value != ''){
					if(!in_array($dist_vert_value,$vertical_name_array))
						array_push($vertical_name_array,$dist_vert_value);
				}
			}
		}
		
		$vertical_select_control = "<select name=\"vertical\" id=\"vertical\" >
										<option value=\"\">Select</option>";
		foreach($vertical_name_array as $vertical_name_val){
			$vertical_select_control .= "<option>".$vertical_name_val."</option>";
		}
		$vertical_select_control .= "</select>";
		
		$table_data .= "<tr><td align=\"right\">Vertical:</td>
							<td>".$vertical_select_control."</td></tr>";
	}
	
	
	/*--------> Zone Input Formation <--------*/
	if($zone_total>0){
		if($state_total>0)
			$onclick = "zone_state(this.value);";
		else
			$onclick = "zone_emp(this.value);";
		
			
		$table_data .= "<tr><td align=\"right\">Zone:</td><td>";
				
		$zone_select_control = "<select name=\"zone\" id=\"zone\" onchange=\"".$onclick."\">";
		$zone_select_control .= "<option value=\"\">Select</option>";
		$zone_select_control .= "<option value=\"all\">All</option>";
		$zone_total = mysqli_num_rows($res_zone);
		$res_zone = mysqli_query($link,$sql_zone);
		while($row_zone = mysqli_fetch_assoc($res_zone)){
			$zone = $row_zone['zone'];
			$zone_select_control .= "<option value=\"'".$zone."'\">".$zone."</option>";
			$zone_string .= "'".$zone."',";
		}
		$zone_string = rtrim($zone_string,",");
		$zone_select_control .= "</select>";
		$table_data .= $zone_select_control;
		$table_data .= "</td></tr>";
	}
	/*--------> State Input Formation <--------*/
	if($state_total>0){
		$table_data .= "<tr><td align=\"right\">State:</td>";
		if($zone_total>0){
			$table_data .= "<td><div id=\"state_select_div\"></div></td></tr>";
		}
		else{
			$onclick = "state_emp(this.value);";
			
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
			$state_select_control .= "<option value=\"".$state_string."\">All</option>";
			$state_select_control .= $state_select_control_option;
			$state_select_control .= "</select>";
			$table_data .= $state_select_control;
			$table_data .= "</td></tr>";
		}
	}
		
	/*--------> Employee Input Formation <--------*/
	if(vertical_field == 'yes' || $zone_total>0 || $state_total>0){
		$table_data .= "<tr><td align=\"right\">Employee:</td><td><div id=\"emp_select_div\"></div></td></tr>";
	}
	else{
		$table_data .= "<tr>
							<td align=\"right\">Employee:</td>
							<td>";
		$emp_select_control = "<select name=\"employee\" id=\"employee\">";
		$emp_select_control .= "<option value=\"\">Select</option>";
		
		$sql_emp = "SELECT emp_code, emp_name FROM employee_master".$emp_hierarchy_value_condition." ORDER BY emp_name ASC";
		$res_emp = mysqli_query($link,$sql_emp);
		while($row_emp = mysqli_fetch_assoc($res_emp)){
			$emp_code = $row_emp['emp_code'];
			$emp_name = $row_emp['emp_name'];
			$emp_code_string .= "'".$emp_code."',";
			$emp_select_control .= "<option value=\"'".$emp_code."'\">".$emp_name."</option>";
		}
		$emp_code_string = rtrim($emp_code_string,",");
		$emp_select_control .= "<option value=\"".$emp_code_string."\">All</option>";
		$emp_select_control .= "</select>";
		$table_data .= $emp_select_control;
		$table_data .= "</td>
					    </tr>";
	}
	$table_data .= $get_control;
	$table_data.="<tr><td colspan=\"2\" align=\"center\"><div id=\"date_div\" $hidden >
From:<input type=\"date\" name=\"start_date\" id=\"start_date\" style=\"height:15px;\" />
To:<input type=\"date\" name=\"end_date\" id=\"end_date\" style=\"height:15px;\" />
</div></td></tr>";
	echo $table_data."<tr><td colspan=\"2\" align=\"right\"><input type=\"submit\" name=\"submit\" value=\"Submit\" onclick=\"display_result();\"></td></tr></table></div>";
	?>
    
    <script>
	function zone_state(zone){
		if(document.getElementById("zone").value.search(/\S/) == -1)
			return false;
		var zone = encodeURIComponent(zone);
		document.getElementById("state_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('order_get_zone_related_data.php?zone='+zone+'&type=state','state_select_div',0);
	}
	
	function zone_branch(zone){
		if(document.getElementById("zone").value.search(/\S/) == -1)
			return false;
		var zone = encodeURIComponent(zone);
		document.getElementById("branch_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_zone_related_data.php?zone='+zone+'&type=branch','branch_select_div',0);
	}
	
	function zone_saleaccess(zone){
		if(document.getElementById("zone").value.search(/\S/) == -1)
			return false;
		var zone = encodeURIComponent(zone);
		document.getElementById("saleaccess_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_zone_related_data.php?zone='+zone+'&type=sale_access','saleaccess_select_div',0);
	}
	
	function zone_hq(zone){
		if(document.getElementById("zone").value.search(/\S/) == -1)
			return false;
		var zone = encodeURIComponent(zone);
		document.getElementById("hq_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_zone_related_data.php?zone='+zone+'&type=hq','hq_select_div',0);
	}
	
	function zone_designation(zone){
		if(document.getElementById("zone").value.search(/\S/) == -1)
			return false;
		var zone = encodeURIComponent(zone);
		document.getElementById("designation_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_zone_related_data.php?zone='+zone+'&type=designation','designation_select_div',0);
	}
	
	function zone_emp(zone){
		if(document.getElementById("zone").value.search(/\S/) == -1)
			return false;
		var zone = encodeURIComponent(zone);
		document.getElementById("emp_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_zone_related_data.php?zone='+zone+'&type=emp','emp_select_div',0);
	}
	
	
	
	function state_branch(state){
		if(document.getElementById("state").value.search(/\S/) == -1)
			return false;
			
		var state = encodeURIComponent(state);
		var zone = encodeURIComponent(document.getElementById("zone").value);
		
		document.getElementById("branch_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_state_related_data.php?state='+state+'&zone='+zone+'&type=branch','branch_select_div',0);
	}
	
	function sale_access(state){
		if(document.getElementById("state").value.search(/\S/) == -1)
			return false;
		var state = encodeURIComponent(state);
		document.getElementById("saleaccess_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_state_related_data.php?state='+state+'&type=sale_access','saleaccess_select_div',0);
	}
	
	function state_hq(state){
		if(document.getElementById("state").value.search(/\S/) == -1)
			return false;
		var state = encodeURIComponent(state);
		document.getElementById("hq_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_state_related_data.php?state='+state+'&type=hq','hq_select_div',0);
	}
	
	function state_designation(state){
		if(document.getElementById("state").value.search(/\S/) == -1)
			return false;
		var state = encodeURIComponent(state);
		document.getElementById("designation_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_state_related_data.php?state='+state+'&type=designation','designation_select_div',0);
	}
	
	function state_emp(state){
		if(document.getElementById("state").value.search(/\S/) == -1)
			return false;
		var state = encodeURIComponent(state);
		document.getElementById("emp_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_state_related_data.php?state='+state+'&type=emp','emp_select_div',0);
	}
	
	
	
	
	function branch_saleaccess(branch){
		if(document.getElementById("branch").value.search(/\S/) == -1)
			return false;
			
		var branch = encodeURIComponent(branch);
		var zone = encodeURIComponent(document.getElementById("zone").value);
		var state = encodeURIComponent(document.getElementById("state").value);
		
		//alert(branch+zone+state);
		
		document.getElementById("saleaccess_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_branch_related_data.php?branch='+branch+'&zone='+zone+'&state='+state+'&type=sale_access','saleaccess_select_div',0);
	}
	
	function branch_hq(branch){
		if(document.getElementById("branch").value.search(/\S/) == -1)
			return false;
		var branch = encodeURIComponent(branch);
		document.getElementById("hq_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_branch_related_data.php?branch='+branch+'&type=hq','hq_select_div',0);
	}
	
	function branch_designation(branch){
		if(document.getElementById("branch").value.search(/\S/) == -1)
			return false;
		var branch = encodeURIComponent(branch);
		document.getElementById("designation_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_branch_related_data.php?branch='+branch+'&type=designation','designation_select_div',0);
	}
	
	function branch_emp(branch){
		if(document.getElementById("branch").value.search(/\S/) == -1)
			return false;
		var branch = encodeURIComponent(branch);
		document.getElementById("emp_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_branch_related_data.php?branch='+branch+'&type=emp','emp_select_div',0);
	}
	
	
	
	
	function saleaccess_hq(sale_access){
		if(document.getElementById("sale_access").value.search(/\S/) == -1)
			return false;
		var sale_access = encodeURIComponent(sale_access);
		document.getElementById("hq_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_saleaccess_related_data.php?sale_access='+sale_access+'&type=hq','hq_select_div',0);
	}
	
	function saleaccess_designation(sale_access){
		if(document.getElementById("sale_access").value.search(/\S/) == -1)
			return false;
		var sale_access = encodeURIComponent(sale_access);
		document.getElementById("designation_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_saleaccess_related_data.php?sale_access='+sale_access+'&type=designation','designation_select_div',0);
	}
	
	function saleaccess_emp(sale_access){
		if(document.getElementById("sale_access").value.search(/\S/) == -1)
			return false;
			
		var sale_access = encodeURIComponent(sale_access);
		var branch = encodeURIComponent(document.getElementById("branch").value);
		var state = encodeURIComponent(document.getElementById("state").value);
		var zone = encodeURIComponent(document.getElementById("zone").value);
		
		document.getElementById("emp_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_saleaccess_related_data.php?sale_access='+sale_access+'&branch='+branch+'&state='+state+'&zone='+zone+'&type=emp','emp_select_div',0);
	}
	
	
	
	function hq_designation(hq){
		if(document.getElementById("hq").value.search(/\S/) == -1)
			return false;
			
		var hq_one = encodeURIComponent(hq);
		var zone = encodeURIComponent(document.getElementById("zone").value);
		var branch = encodeURIComponent(document.getElementById("branch").value);
		var state = encodeURIComponent(document.getElementById("state").value);
		
		document.getElementById("designation_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_hq_related_data.php?hq='+hq_one+'&zone='+zone+'&branch='+branch+'&state='+state+'&type=designation','designation_select_div',0);
	}
	
	function hq_emp(hq){
		if(document.getElementById("hq").value.search(/\S/) == -1)
			return false;
		var hq = encodeURIComponent(hq);
		document.getElementById("emp_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_hq_related_data.php?hq='+hq+'&type=emp','emp_select_div',0);
	}
	
	
	
	function designation_emp(designation){
		if(document.getElementById("designation").value.search(/\S/) == -1)
			return false;
		//var designation = encodeURIComponent(designation);		
		var zone = encodeURIComponent(document.getElementById("zone").value);
		var state = encodeURIComponent(document.getElementById("state").value);
		var hq = encodeURIComponent(document.getElementById("hq").value);
		var designation = encodeURIComponent(document.getElementById("designation").value);
		var branch = encodeURIComponent(document.getElementById("branch").value);
		
		document.getElementById("emp_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_designation_related_data.php?zone='+zone+'&state='+state+'&hq='+hq+'&designation='+designation+'&branch='+branch+'&type=emp','emp_select_div',0);
	}
	
	</script>
    <?php
}
?>