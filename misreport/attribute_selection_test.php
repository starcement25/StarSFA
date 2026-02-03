<?php
ob_start();
session_start();
require("adminUtils.php");

attribute_selection();

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
	
	/*--------> Check If Branch Exists <--------*/
	$sql_branch = "SELECT DISTINCT SUBSTRING_INDEX(branch_code, ',', 1) AS branch_code FROM employee_master".$emp_hierarchy_value_condition.$branch_condition." ORDER BY branch_code ASC";
	$res_branch = mysqli_query($link,$sql_branch);
	$branch_total = mysqli_num_rows($res_branch);
	
	if(strtoupper($_SESSION['nick_name']) == 'STAR'){
	/*--------> Check If Sale Access Exists <--------*/
	$sql_sale_access = "SELECT DISTINCT sale_access FROM employee_master".$emp_hierarchy_value_condition.$sale_access_condition." ORDER BY sale_access ASC";
	$res_sale_access = mysqli_query($link,$sql_sale_access);
	$sale_access_total = mysqli_num_rows($res_sale_access);
	}
	
	if(strtoupper($_SESSION['nick_name']) != 'STAR'){
	/*--------> Check If Headquarter Exists <--------*/
	$sql_hq = "SELECT DISTINCT hq FROM employee_master".$emp_hierarchy_value_condition.$hq_condition." ORDER BY hq ASC";
	$res_hq = mysqli_query($link,$sql_hq);
	$hq_total = mysqli_num_rows($res_hq);
	
	/*--------> Check If Designation Exists <--------*/
	$sql_designation = "SELECT DISTINCT designation FROM employee_master".$emp_hierarchy_value_condition.$designation_condition." ORDER BY designation ASC";
	$res_designation = mysqli_query($link,$sql_designation);
	$designation_total = mysqli_num_rows($res_designation);
	}
	
	/*--------> Template Formation To Ask For Input <--------*/	
	$table_data = "<div id=\"display_data\"><table id=\"criteria_tab\" class=\"border\" width=\"45%\" style=\"border-collapse:collapse;border:1px solid #A92A61; padding:6px;\" >
					<tr class=\"TDHEAD\"><td colspan=\"2\" align=\"center\">Select Criteria</td></tr>";
	
	/*--------> Zone Input Formation <--------*/
	if($zone_total>0){
		if($state_total>0)
			$onclick = "zone_state(this.value);";
		else if($branch_total>0)
			$onclick = "zone_branch(this.value);";
		else if($sale_access_total>0)
			$onclick = "zone_saleaccess(this.value);";
		else if($hq_total>0)
			$onclick = "zone_hq(this.value);";
		else if($designation_total>0)
			$onclick = "zone_designation(this.value);";
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
			if($branch_total>0)
				$onclick = "state_branch(this.value);";
			else if($sale_access_total>0)
				$onclick = "state_saleaccess(this.value);";
			else if($hq_total>0)
				$onclick = "state_hq(this.value);";
			else if($designation_total>0)
				$onclick = "state_designation(this.value);";
			else
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
	/*--------> Branch Input Formation <--------*/
	if($branch_total>0){
		$table_data .= "<tr><td align=\"right\">Branch:</td>";
		if($zone_total>0 || $state_total>0){
			$table_data .= "<td><div id=\"branch_select_div\"></div></td></tr>";
		}
		else{
			if($sale_access_total>0)
				$onclick = "branch_saleaccess(this.value);";
			else if($hq_total>0)
				$onclick = "branch_hq(this.value);";
			else if($designation_total>0)
				$onclick = "branch_designation(this.value);";
			else
				$onclick = "branch_emp(this.value);";
			
			$table_data .= "<td>";
			$branch_select_control = "<select name=\"branch\" id=\"branch\" onchange=\"".$onclick."\">";
			$branch_select_control .= "<option value=\"\">Select</option>";
			$res_branch = mysqli_query($link,$sql_branch);
			while($row_branch = mysqli_fetch_assoc($res_branch)){
				$branch_code = $row_branch['branch_code'];
				$sql_branch_name = "SELECT branch_name FROM branch_master WHERE branch_code = '".$branch_code."'";
				$res_branch_name = mysqli_query($link,$sql_branch_name);
				$row_branch_name = mysqli_fetch_assoc($res_branch_name);
				$branch_name = $row_branch_name['branch_name'];
				$branch_string .= "'".$branch_code."',";
				$branch_select_control .= "<option value=\"'".$branch_code."'\">".$branch_name."</option>";
			}
			$branch_string = rtrim($branch_string,",");
			$branch_select_control .= "<option value=\"".$branch_string."\">All</option>";
			$branch_select_control .= "</select>";
			$table_data .= $branch_select_control;
			$table_data .= "</td></tr>";
		}
	}
	/*--------> Sale Access Input Formation <--------*/
	if($sale_access_total>0){
		$table_data .= "<tr><td align=\"right\">Department:</td>";
		if($zone_total>0 || $state_total>0 || $branch_total>0){
			$table_data .= "<td><div id=\"saleaccess_select_div\"></div></td></tr>";
		}
		else{
			if($hq_total>0)
				$onclick = "saleaccess_hq(this.value);";
			else if($designation_total>0)
				$onclick = "saleaccess_designation(this.value);";
			else
				$onclick = "saleaccess_emp(this.value);";
			
			$table_data .= "<td>";
			$saleaccess_select_control = "<select name=\"sale_access\" id=\"sale_access\" onchange=\"".$onclick."\">";
			$saleaccess_select_control .= "<option value=\"\">Select</option>";
			$res_sale_access = mysqli_query($link,$sql_sale_access);
			while($row_sale_access = mysqli_fetch_assoc($res_sale_access)){
				$sale_access = $row_sale_access['sale_access']; 
				$sale_access_string .= "'".$sale_access."',";
				$saleaccess_select_control .= "<option value=\"'".$sale_access."'\">".$sale_access."</option>";
			}
			$sale_access_string = rtrim($sale_access_string,",");
			$saleaccess_select_control .= "<option value=\"".$sale_access_string."\">All</option>";
			$saleaccess_select_control .= "</select>";
			$table_data .= $saleaccess_select_control;
			$table_data .= "</td></tr>";
		}
	}
	
	/*--------> Headquarter Input Formation <--------*/
	if($hq_total>0){
		$table_data .= "<tr><td align=\"right\">Headquarter:</td>";
		if($zone_total>0 || $state_total>0){
			$table_data .= "<td><div id=\"hq_select_div\"></div></td></tr>";
		}
		else{
			if($designation_total>0)
				$onclick = "hq_designation(this.value);";
			else
				$onclick = "hq_emp(this.value);";
			$table_data .= "<td>";
			$hq_select_control = "<select name=\"hq\" id=\"hq\" onchange=\"".$onclick."\">";
			$hq_select_control .= "<option value=\"\">Select</option>";
			
			$res_hq = mysqli_query($link,$sql_hq);
			while($row_hq = mysqli_fetch_assoc($res_hq)){
				$hq = $row_hq['hq'];
				$hq_string .= "'".$hq."',";
				$hq_select_control .= "<option value=\"'".$hq."'\">".$hq."</option>";
			}
			$hq_string = rtrim($hq_string,",");
			$hq_select_control .= "<option value=\"".$hq_string."\">All</option>";
			$hq_select_control .= "</select>";
			$table_data .= $hq_select_control;
			$table_data .= "</td></tr>";
		}
	}
	/*--------> Designation Input Formation<--------*/
	if($designation_total>0){
		$table_data .= "<tr><td align=\"right\">Designation:</td>";
		if($zone_total>0 || $state_total>0 || $hq_total>0){
			$table_data .= "<td><div id=\"designation_select_div\"></div></td></tr>";
		}
		else{
			$onclick = "designation_emp(this.value);";
			$table_data .= "<td>";
			$designation_select_control = "<select name=\"designation\" id=\"designation\" onchange=\"".$onclick."\">";
			$designation_select_control .= "<option value=\"\">Select</option>";
			
			$res_designation = mysqli_query($link,$sql_designation);
			while($row_designation = mysqli_fetch_assoc($res_designation)){
				$designation = $row_designation['designation'];
				$designation_string .= "'".$designation."',";
				$designation_select_control .= "<option value=\"'".$designation."'\">".$designation."</option>";
			}
			$designation_string = rtrim($designation_string,",");
			$designation_select_control .= "<option value=\"".$designation_string."\">All</option>";
			$designation_select_control .= "</select>";
			$table_data .= $designation_select_control;
			$table_data .= "</td></tr>";
		}
	}
	
	/*--------> Employee Input Formation <--------*/
	if($zone_total>0 || $state_total>0 || $hq_total>0 || $designation_total>0){
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
		GenericAjaxFunction('get_zone_related_data.php?zone='+zone+'&type=state','state_select_div',0);
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