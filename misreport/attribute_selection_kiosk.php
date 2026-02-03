<?php
/*ob_start();
session_start();
require("adminUtils.php");

attribute_selection();*/

function attribute_selection($hidden,$get_control,$type){
	?>
    <script type="text/javascript" src="../ajax1.js"></script>
    <?php
	if($_SESSION['admin_login']=="admin"){
		$emp_hierarchy_value = '';
		$emp_hierarchy_value_condition = '';
	}
	else{
		$emp_hierarchy_value=return_employee_hierarchy($_SESSION['admin_login']);
		$emp_hierarchy_value_condition = " WHERE emp_code IN(".$emp_hierarchy_value.") ";
	}
	$zone_condition = " WHERE zone != '' ";
	$division_condition = " WHERE division_id != '' ";
	$region_condition = " WHERE region != '' ";
	$CCC_condition = " WHERE kiosk_id != '' ";
	
	$sql_zone = "SELECT DISTINCT SUBSTRING_INDEX(zone, ',', 1) AS zone FROM employee_master".$emp_hierarchy_value_condition.$zone_condition." ORDER BY zone ASC";
	$res_zone = mysqli_query($link,$sql_zone);
	$zone_total = mysqli_num_rows($res_zone);
	
	/*--------> Template Formation To Ask For Input <--------*/	
	/*$table_data = "<div id=\"display_data\"><table id=\"criteria_tab\" class=\"border\" width=\"45%\" style=\"border-collapse:collapse;border:1px solid #A92A61; padding:6px;\" >
	<tr class=\"TDHEAD\"><td colspan=\"2\" align=\"center\">Select Criteria</td></tr>";
	$table_data.="<tr><td colspan=\"2\" align=\"center\"><div id=\"date_div\">
From:<input type=\"date\" name=\"start_date\" id=\"start_date\" style=\"height:15px;\" onchange=\"zone_division('".$type."');division_region('".$type."');region_CCC('".$type."');\" />
To:<input type=\"date\" name=\"end_date\" id=\"end_date\" style=\"height:15px;\" onchange=\"zone_division('".$type."');division_region('".$type."');region_CCC('".$type."');\" />
</div></td></tr>";*/
?>
<div id="display_data">
<form name ="frmSearch" method="post" action="<?=$_SERVER['PHP_SELF']?>" >
<table id="criteria_tab" class="border" width="54%" style="border-collapse:collapse;border:1px solid #A92A61; padding:6px;" >
	<tr class="TDHEAD"><td colspan="4" align="center">Select Criteria</td></tr>
    <tr>
    <td align="left" width="15%">From Date:</td>
    <td align="left" width="30%" style="vertical-align:top;">
    <?php $from_date=$_REQUEST['start_date'];
	
	?>
          <input id="start_date" type="text" value="<?php echo str_replace('/','-',$from_date);?>" name="start_date" onchange="zone_division('<?=$type;?>');division_region('<?=$type;?>');region_CCC('<?=$type;?>');"></input>&nbsp;
            <a href="javascript:cal5.popup();"><img style="cursor:hand;position:absolute;border:0;" border="0" src="images/cal.gif" width="20" height="18" ></a>
        </label>
        <script language="JavaScript" type="text/javascript">
            <!-- // create calendar object(s) just after form tag closed
             // specify form element as the only parameter (document.forms['formname'].elements['inputname']);
             // note: you can have as many calendar objects as you need for your application
            var cal5 = new calendar3(document.forms['frmSearch'].elements['start_date']);
            cal5.year_scroll = true;
            cal5.time_comp = false;
            //-->
        </script>
    </td>
    <td width="15%" align="left" style="padding-left:10px;">To Date:</td>
    <td width="" style="vertical-align:top;">
        <?php $to_date=$_REQUEST['end_date'];?>
         <input id="end_date" type="text" value="<?php echo str_replace('/','-',$to_date);?>" name="end_date"  onchange="zone_division('<?=$type;?>');division_region('<?=$type;?>');region_CCC('<?=$type;?>');"></input>&nbsp;
            <a href="javascript:cal6.popup();"><img style="cursor:hand;position:absolute;border:0;" border="0" src="images/cal.gif" width="20" height="18" ></a>
        </label>
        <script language="JavaScript" type="text/javascript">
            <!-- // create calendar object(s) just after form tag closed
             // specify form element as the only parameter (document.forms['formname'].elements['inputname']);
             // note: you can have as many calendar objects as you need for your application
            var cal6 = new calendar3(document.forms['frmSearch'].elements['end_date']);
            cal6.year_scroll = true;
            cal6.time_comp = false;
            //-->
        </script>
    </td>
    </tr>
	<?php			
	/*--------> Zone Input Formation <--------*/
	if($zone_total>0){
			$onclick = "zone_division('".$type."');division_region('".$type."');region_CCC('".$type."');";
		$table_data .= "<tr><td align=\"right\" colspan=\"2\">Zone:</td><td colspan=\"2\">";
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
		$table_data .= "<tr><td align=\"right\" colspan=\"2\">Division:</td>";
		$table_data .= "<td colspan=\"2\"><div id=\"division_select_div\"></div></td></tr>";
		$table_data .= "<tr><td align=\"right\" colspan=\"2\">Region:</td>";
		$table_data .= "<td colspan=\"2\"><div id=\"region_select_div\"></div></td></tr>";
		$table_data .= "<tr><td align=\"right\" colspan=\"2\">CCC:</td>";
		$table_data .= "<td colspan=\"2\"><div id=\"CCC_select_div\"></div></td></tr>";
	}
	/*--------> State Input Formation <--------*/
	/*if($state_total>0){
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
	}*/
	/*--------> Branch Input Formation <--------*/
	/*if($branch_total>0){
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
	}*/
	/*--------> Sale Access Input Formation <--------*/
	/*if($sale_access_total>0){
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
	}*/
	
	/*--------> Headquarter Input Formation <--------*/
	/*if($hq_total>0){
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
	}*/
	/*--------> Designation Input Formation<--------*/
	/*if($designation_total>0){
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
	}*/
	
	/*--------> Employee Input Formation <--------*/
	/*if($zone_total>0 || $state_total>0 || $hq_total>0 || $designation_total>0){
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
	}*/
	$table_data .= $get_control;
	echo $table_data."<tr><td colspan=\"4\" align=\"right\"><input type=\"button\" name=\"submit\" value=\"Submit\" onclick=\"display_result('".$type."');\"></td></tr></table></form></div>";
	?>
    
    <script>
	function zone_division(type){
		if(document.getElementById("zone").value.search(/\S/) == -1)
			return false;
		if(document.getElementById("start_date").value.search(/\S/) == -1)
			return false;
		if(document.getElementById("end_date").value.search(/\S/) == -1)
			return false;		
		var zone=document.getElementById("zone").value;	
		zone = encodeURIComponent(zone);
		var start_date=document.getElementById("start_date").value;
		var end_date=document.getElementById("end_date").value;
		document.getElementById("division_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_division_data.php?zone='+zone+'&start_date='+start_date+'&end_date='+end_date+'&type='+type,'division_select_div',0);
	}
	
	function division_region(type){
		if(document.getElementById("zone").value.search(/\S/) == -1)
			return false;
		if(document.getElementById("start_date").value.search(/\S/) == -1)
			return false;
		if(document.getElementById("end_date").value.search(/\S/) == -1)
			return false;
		if(document.getElementById("division_id"))
		{	
			if(document.getElementById("division_id").value.search(/\S/) == -1)
			return false;
		}
		var zone=document.getElementById("zone").value;				
		zone = encodeURIComponent(zone);
		var start_date=document.getElementById("start_date").value;
		var end_date=document.getElementById("end_date").value;
		if(document.getElementById("division_id"))
		{
			var division_id=document.getElementById("division_id").value;
		}
		
		document.getElementById("region_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_region_related_data.php?zone='+zone+'&start_date='+start_date+'&end_date='+end_date+'&division_id='+division_id+'&type='+type,'region_select_div',0);
	}
	function region_CCC(type){
		if(document.getElementById("zone").value.search(/\S/) == -1)
			return false;
		if(document.getElementById("start_date").value.search(/\S/) == -1)
			return false;
		if(document.getElementById("end_date").value.search(/\S/) == -1)
			return false;
		if(document.getElementById("division_id"))
		{	
			if(document.getElementById("division_id").value.search(/\S/) == -1)
			return false;
		}
		if(document.getElementById("region"))
		{
			if(document.getElementById("region").value.search(/\S/) == -1)
			return false;
		}
		var zone=document.getElementById("zone").value;				
		zone = encodeURIComponent(zone);
		var start_date=document.getElementById("start_date").value;
		var end_date=document.getElementById("end_date").value;
		if(document.getElementById("division_id"))
		{
			var division_id=document.getElementById("division_id").value;
		}
		if(document.getElementById("region"))
		{
			var region=document.getElementById("region").value;
		}
		
		//alert(region);
		document.getElementById("CCC_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_CCC_related_data.php?zone='+zone+'&start_date='+start_date+'&end_date='+end_date+'&division_id='+division_id+'&region='+region+'&type='+type,'CCC_select_div',0);
	}
	function state_branch(state){
		if(document.getElementById("state").value.search(/\S/) == -1)
			return false;
			
		var state = encodeURIComponent(state);
		var zone = encodeURIComponent(document.getElementById("zone").value);
		
		document.getElementById("branch_select_div").innerHTML = '<img src="ajax-loader.gif" id="ajaxloader">';
		GenericAjaxFunction('get_state_related_data.php?state='+state+'&zone='+zone+'&type=branch','branch_select_div',0);
	}
	</script>
    <?php
}
?>