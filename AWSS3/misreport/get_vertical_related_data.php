<?php
ob_start();
session_start();
require("adminUtils.php");

/*--------> Employee Hierarchy Condition <--------*/
if($_SESSION['admin_login']=="admin" || $_SESSION['admin_login'] == 'emovesfa_do' || strtoupper($_SESSION['admin_login'])=='ACCOUNTS'){
	$emp_hierarchy = '';
	$emp_hierarchy_condition = '';
	$designation_condition = " WHERE designation != '' ";
	$emp_hierarchy_condition_one ='';
}
else{
	$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
	$emp_hierarchy_condition = " WHERE emp_code IN(".$emp_hierarchy.") ";
	$emp_hierarchy_condition_one = " AND emp_code IN(".$emp_hierarchy.") ";
	$designation_condition = " AND designation != '' ";
}

$state = $_REQUEST['state'];
$type = $_REQUEST['type'];
$vertical = $_REQUEST['vertical'];
$empcode	= $_REQUEST['empcode'];
$empcodeone	= $_REQUEST['empcodeone'];
$rsmcode=$_REQUEST['rsmcode'];
$asmsocode=$_REQUEST['asmsocode'];
$distributor=$_REQUEST['distributor'];
$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
$designation = $_REQUEST['designation'];
if($vertical != ''){
	if($vertical == 'MACROMAN'){
		$vertical_condition = " AND vertical_value LIKE 'M%' ";
		$vertical_condition_one=" AND EM.vertical_value LIKE 'M%'";
	}
	else if($vertical == 'all'){
		$vertical_condition = "";
		$vertical_condition_one="";
	}
	else{
		//$vertical_condition = " AND vertical_value = '".$vertical."'";
		$vertical_condition = " AND FIND_IN_SET(".$vertical.",vertical_value)";
		$vertical_condition_one=" AND FIND_IN_SET(".$vertical.",EM.vertical_value)";
	}
}
else{
	$vertical_condition = "";
}

if($state != ''){
	if($state == 'all')
		$state_condition = " state!=''";
	else
		if(strtoupper($_SESSION['nick_name']) == 'HALDIRAM'){
			$state_condition = " FIND_IN_SET(".$state.", state)";
		}
		else
		{
			$state_condition = " state IN(".$state.") ";
		}
}
if($empcode !='')
{
	$emp_condition_one=" reporting_to IN(".$empcode.")";
}
if($empcodeone !='')
{
	$emp_condition_two=" reporting_to IN(".$empcodeone.")";
}
$sql_emp = "SELECT emp_code FROM employee_master WHERE ".$state_condition." ORDER BY emp_name ASC";
	$res_emp = mysql_query($sql_emp);
	while($row_emp = mysql_fetch_array($res_emp)){
		$emp_code = $row_emp['emp_code'];
		$emp_code_string .= "'".$emp_code."',";
		//echo "<option value=\"'".$emp_code."'\">".$emp_name."</option>";
	}
$emp_code_string = rtrim($emp_code_string,",");
if($rsmcode!='')
{
	$rsmcode=str_replace("'","",$rsmcode);
	$emp_asmso_hierarchy=return_employee_hierarchy($rsmcode);
	$emp_asmso_hierarchy_condition=' emp_code IN('.$emp_asmso_hierarchy.')';
}
/*--------> Check If Branch Exists <--------*/
$sql_branch = "SELECT DISTINCT SUBSTRING_INDEX(branch_code, ',', 1) AS branch_code FROM employee_master".$emp_hierarchy_value_condition.$branch_condition." ORDER BY branch_code ASC";
$res_branch = mysql_query($sql_branch);
$branch_total = mysql_num_rows($res_branch);

/*--------> Branch Data Populate <--------*/
if($type == 'branch'){
	if($sale_access_total>0)
		$onclick = "branch_saleaccess(this.value);";
	else if($hq_total>0)
		$onclick = "branch_hq(this.value);";
	else if($designation_total>0)
		$onclick = "branch_designation(this.value);";
	else
		$onclick = "branch_emp(this.value);";
	
	echo "<select name=\"branch\" id=\"branch\" onchange=\"".$onclick."\">";
	echo "<option value=\"\">Select</option>";
	echo "<option value=\"all\">All</option>";
		
	$branch_code_array = array();
	$branch_name_array = array();
	$sql_branch = "SELECT DISTINCT branch_code FROM employee_master WHERE state IN (".$state.")".$emp_hierarchy_condition_one." AND branch_code != '' ".$zone_condition." ORDER BY branch_code ASC";
	$res_branch = mysql_query($sql_branch);
	while($row_branch = mysql_fetch_array($res_branch)){
		$branch_code = $row_branch['branch_code'];
		$sql_branch_name = "SELECT branch_code, branch_name FROM branch_master WHERE FIND_IN_SET(branch_code,'".$branch_code."')";
		$res_branch_name = mysql_query($sql_branch_name);
		while($row_branch_name = mysql_fetch_array($res_branch_name)){
			$branch_name = $row_branch_name['branch_name'];
			$branch_code_get = $row_branch_name['branch_code'];
			if(!in_array($branch_code_get,$branch_code_array)){
				//$branch_code_array[$branch_code_get] = $branch_name;
				array_push($branch_code_array,$branch_code_get);
				array_push($branch_name_array,$branch_name);
				$branch_string .= "'".$branch_code_get."',";
			}
		}
	}
	$branch_string = rtrim($branch_string,",");
	/*foreach($branch_code_array as $branch_index=>$branch_val){
		echo "<option value=\"'".$branch_index."'\">".$branch_val."</option>";
	}*/
	for($i=0;$i<count($branch_code_array);$i++)
	{
		echo "<option value=\"".$branch_code_array[$i]."\">".$branch_name_array[$i]."</option>";
	}
	
	echo "</select>";
}
/*--------> Sale Access Data Populate <--------*/
else if($type == 'sale_access'){
	if($hq_total>0)
		$onclick = "saleaccess_hq(this.value);";
	else if($designation_total>0)
		$onclick = "saleaccess_designation(this.value);";
	else
		$onclick = "saleaccess_emp(this.value);";
	
	echo "<select name=\"sale_access\" id=\"sale_access\" onchange=\"".$onclick."\">";
	echo "<option value=\"\">Select</option>";
	
	$sql_sale_access = "SELECT DISTINCT sale_access FROM employee_master WHERE state IN (".$state.")".$emp_hierarchy_condition_one." AND sale_access != '' ORDER BY sale_access ASC";
	$res_sale_access = mysql_query($sql_sale_access);
	while($row_sale_access = mysql_fetch_array($res_sale_access)){
		$sale_access = $row_sale_access['sale_access']; 
		$sale_access_string .= "'".$sale_access."',";
		echo "<option value=\"'".$sale_access."'\">".$sale_access."</option>";
	}
	$sale_access_string = rtrim($sale_access_string,",");
	echo "<option value=\"".$sale_access_string."\">All</option>";
	echo "</select>";
}
/*--------> Headquarter Data Populate <--------*/
else if($type == 'hq'){
	if($designation_total>0)
		$onclick = "hq_designation(this.value);";
	else
		$onclick = "hq_emp(this.value);";
	
	$hq_control = "<select name=\"hq\" id=\"hq\" onchange=\"".$onclick."\">";
	$hq_control .= "<option value=\"\">Select</option>";
	
	
	$sql_hq = "SELECT DISTINCT hq FROM employee_master WHERE state IN (".$state.")".$emp_hierarchy_condition_one." AND hq != '' ORDER BY hq ASC";
	$res_hq = mysql_query($sql_hq);
	while($row_hq = mysql_fetch_array($res_hq)){
		$hq = $row_hq['hq'];
		$hq_string .= "'".$hq."',";
		$hq_control_option .= "<option value=\"'".$hq."'\">".$hq."</option>";
	}
	$hq_string = rtrim($hq_string,",");
	$hq_control .= "<option value=\"".$hq_string."\">All</option>";
	$hq_control .= $hq_control_option;
	$hq_control .= "</select>";
	echo $hq_control;
}
/*--------> Designation Data Populate <--------*/
else if($type == 'designation'){
	$onclick = "designation_emp(this.value);";
	
	echo "<select name=\"designation\" id=\"designation\" onchange=\"".$onclick."\">";
	echo "<option value=\"\">Select</option>";
	
	
	$sql_designation = "SELECT DISTINCT designation FROM employee_master WHERE state IN (".$state.")".$emp_hierarchy_condition_one." AND designation != '' ORDER BY designation ASC";
	$res_designation = mysql_query($sql_designation);
	while($row_designation = mysql_fetch_array($res_designation)){
		$designation = $row_designation['designation'];
		$designation_string .= "'".$designation."',";
		echo "<option value=\"'".$designation."'\">".$designation."</option>";
	}
	$designation_string = rtrim($designation_string,",");
	echo "<option value=\"".$designation_string."\">All</option>";
	echo "</select>";
}
/*--------> Employee Data Populate <--------*/
else if($type == 'emp'){
	if(strtoupper($_SESSION['nick_name']) == 'RUPA'){
		$onclick = "emp_dealer(this.value);";
	}
	else $onclick = "";
	echo "<select name=\"employee\" id=\"employee\" onchange=\"".$onclick."\">";
	echo "<option value=\"\">Select</option>";
	//$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE state IN (".$state.")".$emp_hierarchy_condition_one." ORDER BY emp_name ASC";
	$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE ".$state_condition.$vertical_condition.$emp_hierarchy_condition_one." AND acedns='Y' ORDER BY emp_name ASC";
	$res_emp = mysql_query($sql_emp);
	$emp_code_string='';
	while($row_emp = mysql_fetch_array($res_emp)){
		$emp_code = $row_emp['emp_code'];
		$emp_name = $row_emp['emp_name'];
		$emp_code_string .= "'".$emp_code."',";
		echo "<option value=\"'".$emp_code."'\">".$emp_name."</option>";
	}
	$emp_code_string = rtrim($emp_code_string,",");
	echo "<option value=\"".$emp_code_string."\">All</option>";
	echo "</select>";
}
else if($type == 'ASM' || $type == 'SO' || $type == 'TSI' || $type == 'DSM'){
	//$onclick = "emp_lev_two(this.value);";
	
	echo "<select name=\"employee_lev_one\" id=\"employee_lev_one\" >";
	echo "<option value=\"\">Select</option>";
	//$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE state IN (".$state.")".$emp_hierarchy_condition_one." ORDER BY emp_name ASC";
	$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE ".$state_condition." AND designation='".$type."' AND acedns='Y' ORDER BY emp_name ASC";
	$res_emp = mysql_query($sql_emp);
	while($row_emp = mysql_fetch_array($res_emp)){
		$emp_code = $row_emp['emp_code'];
		$emp_name = $row_emp['emp_name'];
		$emp_code_string .= "'".$emp_code."',";
		echo "<option value=\"'".$emp_code."'\">".$emp_name."</option>";
	}
	$emp_code_string = rtrim($emp_code_string,",");
	echo "<option value=\"".$emp_code_string."\">All</option>";
	echo "</select>";
}
else if($type == 'stateemp'){
	//$onclick = "designation_emp(this.value);";
	echo "<select name=\"employee\" id=\"employee\" >";
	echo "<option value=\"\">Select</option>";
	//$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE state IN (".$state.")".$emp_hierarchy_condition_one." ORDER BY emp_name ASC";
	$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE ".$state_condition." AND acedns='Y' ORDER BY emp_name ASC";
	$res_emp = mysql_query($sql_emp);
	while($row_emp = mysql_fetch_array($res_emp)){
		$emp_code = $row_emp['emp_code'];
		$emp_name = $row_emp['emp_name'];
		$emp_code_string .= "'".$emp_code."',";
		echo "<option value=\"'".$emp_code."'\">".$emp_name."</option>";
	}
	$emp_code_string = rtrim($emp_code_string,",");
	echo "<option value=\"".$emp_code_string."\">All</option>";
	echo "</select>";
}
else if($type=='empasmso')
{
	/*echo $sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE ".$emp_asmso_hierarchy_condition." 
			AND designation IN('ASM','SO','SS') AND acedns='Y' ORDER BY emp_name ASC";*/
	echo "<select name=\"asm_so\" id=\"asm_so\" onchange=\"emp_vertical(this.value);\">";
	echo "<option value=\"\">Select</option>";
	$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE ".$emp_asmso_hierarchy_condition." 
			AND designation IN('ASM','SO','SS') AND acedns='Y' ORDER BY emp_name ASC";
	$res_emp = mysql_query($sql_emp);
	while($row_emp = mysql_fetch_array($res_emp)){
		$emp_code = $row_emp['emp_code'];
		$emp_name = $row_emp['emp_name'];
		$emp_code_string .= "'".$emp_code."',";
		echo "<option value=\"'".$emp_code."'\">".$emp_name."</option>";
	}
	$emp_code_string = rtrim($emp_code_string,",");
	echo "<option value=\"".$emp_code_string."\">All</option>";
	echo "</select>";
}
/*else if($type == 'stateemplevone'){
	$onclick = "state_route(this.value);";
	
	$sql_emp = "SELECT DISTINCT reporting_to FROM employee_master WHERE reporting_to!=''";
	$res_emp = mysql_query($sql_emp);
	while($row_emp = mysql_fetch_array($res_emp)){
		$reporting_to = $row_emp['reporting_to'];
		$emp_code_string .= "'".$reporting_to."',";
		//echo "<option value=\"'".$emp_code."'\">".$emp_name."</option>";
	}
	$emp_code_string = rtrim($emp_code_string,",");
	
	echo "<select name=\"employee_one\" id=\"employee_one\" onchange=\"".$onclick."\">";
	echo "<option value=\"\">Select</option>";
	
	//$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE state IN (".$state.")".$emp_hierarchy_condition_one." ORDER BY emp_name ASC";
	$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE ".$state_condition." AND acedns='Y' 
				AND emp_code NOT IN (".$emp_code_string.") ORDER BY emp_name ASC";
	$res_emp = mysql_query($sql_emp);
	while($row_emp = mysql_fetch_array($res_emp)){
		$emp_code = $row_emp['emp_code'];
		$emp_name = $row_emp['emp_name'];
		$emp_code_string .= "'".$emp_code."',";
		echo "<option value=\"'".$emp_code."'\">".$emp_name."</option>";
	}
	$emp_code_string = rtrim($emp_code_string,",");
	//echo "<option value=\"".$emp_code_string."\">All</option>";
	echo "</select>";
}*/
else if($type == 'statedist'){
	$onclick = "distributor_route(this.value);";
	
	echo "<select name=\"distributor\" id=\"distributor\" onchange=\"".$onclick."\">";
	echo "<option value=\"\">Select</option>";
	//$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE state IN (".$state.")".$emp_hierarchy_condition_one." ORDER BY emp_name ASC";
	$sql_distributor = "SELECT DISTINCT DRR.distributor_code,CM.customer_name FROM distributor_route_relation DRR,customer_master CM WHERE 
						DRR.distributor_code=CM.customer_code AND 
						DRR.emp_code IN(".$emp_code_string.") AND DRR.distributor_code IN(SELECT DISTINCT CMR.rds_tag FROM customer_master CMR,customer_route_emp_relation CRR WHERE CMR.customer_code=CRR.customer_code AND CRR.acedns='Y' AND CMR.cust_type='R') ORDER BY CM.customer_name ASC";
	$res_distributor = mysql_query($sql_distributor);
	while($row_distributor = mysql_fetch_array($res_distributor)){
		$distributor_code = $row_distributor['distributor_code'];
		$customer_name = $row_distributor['customer_name'];
		echo "<option value=\"'".$distributor_code."'\">".$customer_name."</option>";
	}
	echo "</select>";
}
else if($type == 'route'){
	//$onclick = "designation_emp(this.value);";
	//echo "<select name=\"route\" id=\"route\" >";
	//echo "<option value=\"\">Select</option>";
	/*$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE ".$state_condition." AND acedns='Y' ORDER BY emp_name ASC";
	$res_emp = mysql_query($sql_emp);
	while($row_emp = mysql_fetch_array($res_emp)){
		$emp_code = $row_emp['emp_code'];
		$emp_name = $row_emp['emp_name'];
		$emp_code_string .= "'".$emp_code."',";
		//echo "<option value=\"'".$emp_code."'\">".$emp_name."</option>";
	}
	$emp_code_string = rtrim($emp_code_string,",");*/
	$sqlquerycustomerroute="SELECT DISTINCT DRR.route_code,RM.route_name FROM distributor_route_relation DRR,route_master RM,
								customer_route_emp_relation CRR WHERE DRR.route_code=RM.route_code AND RM.route_name!='' 
								AND CRR.route_code=RM.route_code AND CRR.acedns='Y' AND 
								DRR.distributor_code IN(".$distributor.") AND DRR.emp_code IN(".$emp_code_string.")  ORDER BY RM.route_name ASC";
    $resultcustomerroute = mysql_query($sqlquerycustomerroute);
	$countcustomerroute=mysql_num_rows($resultcustomerroute);
	if($countcustomerroute>0){
		while($rowscustomerroute = mysql_fetch_array($resultcustomerroute))
		{
			$route_code=$rowscustomerroute['route_code'];
			$sqlquerycustomerroutedetails="SELECT DISTINCT CM.customer_code FROM customer_route_emp_relation CRR,customer_master CM 
										WHERE CM.customer_code=CRR.customer_code AND 
							CRR.route_code='".$route_code."' AND CM.cust_type='R' AND CRR.acedns='Y' AND CM.rds_tag IN(".$distributor.")";						
			$resultcustomerroutedetails = mysql_query($sqlquerycustomerroutedetails);
			$countcustomerroutedetails=mysql_num_rows($resultcustomerroutedetails);

			$route_name=$rowscustomerroute['route_name'];
			//echo "<option value=\"'".$route_code."'\">".$route_name."</option>";
			$content.="<tr>";
			$content.="<td align='left'>";
			$content.="<input type='checkbox' name='route[]' value='".$route_code."'  class='route_class_chk'/>".$route_name." - ".$countcustomerroutedetails."";
			$content.="</td>";
			$content.= "</tr>"; 
		}
	}
	echo $content;
	//echo "</select>";
}
else if($type == 'state'){
	$onclick = "state_emp(this.value);";
	$sql_state = "SELECT DISTINCT SUBSTRING_INDEX(state, ',', 1) AS state FROM employee_master WHERE state!='' ".$emp_hierarchy_condition_one.$vertical_condition." ORDER BY state ASC";
	$state_select_control = "<select name=\"state\" id=\"state\" onchange=\"".$onclick."\">";
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
	echo $table_data .= $state_select_control;
}
else if($type == 'empvertical')
{
	$sql_vertical = "SELECT vertical_value FROM employee_master WHERE emp_code IN(".$asmsocode.")  ORDER BY  SUBSTRING_INDEX(vertical_value, ',', -1) ASC";
	$res_vertical = mysql_query($sql_vertical);
	$vertical_select_control = "<select name=\"vertical\" id=\"vertical\">";
	$vertical_select_control .= "<option value=\"\">Select</option>";
	//$vertical_select_control .= "<option value=\"all\">All</option>";
	$dist_vertical_val_array=array();
	while($row_vertical = mysql_fetch_array($res_vertical)){
		$dist_vert_value_string = trim($row_vertical['vertical_value']);
		$dist_vert_val_arr=explode(',',$dist_vert_value_string);
		foreach($dist_vert_val_arr as $dist_vert_value){
			if(strtoupper($_SESSION['nick_name']) == 'RUPA'){
				$pos = substr($dist_vert_value,0,1);
				if($pos == 'M'){
					$dist_vert_value = 'MACROMAN';
				}
			}
			if(!in_array($dist_vert_value,$dist_vertical_val_array))
			{
				$vertical_select_control .= "<option value=\"'".$dist_vert_value."'\">".$dist_vert_value."</option>";
				$vertical_string .= "'".$dist_vert_value."',";
				array_push($dist_vertical_val_array,$dist_vert_value);
			}
		}
	}
	$vertical_string = rtrim($vertical_string,",");
	$vertical_select_control .= "<option value=\"".$vertical_string."\">All</option>";
	echo $vertical_select_control .= "</select>";
}
else if($type == 'datedistributor'){
	$onclick = "distributor_emp(this.value);";
	$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE ".$state_condition.$vertical_condition.$emp_hierarchy_condition_one." AND acedns='Y' ORDER BY emp_name ASC";
	$res_emp = mysql_query($sql_emp);
	while($row_emp = mysql_fetch_array($res_emp)){
		$emp_code = $row_emp['emp_code'];
		$emp_name = $row_emp['emp_name'];
		$emp_code_string .= "'".$emp_code."',";
		//echo "<option value=\"'".$emp_code."'\">".$emp_name."</option>";
	}
	$emp_code_string = rtrim($emp_code_string,",");
	echo "<select name=\"distributor\" id=\"distributor\" onchange=\"".$onclick."\">";
	echo "<option value=\"\">Select</option>";
	//$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE state IN (".$state.")".$emp_hierarchy_condition_one." ORDER BY emp_name ASC";
	$sql_distributor = "SELECT DISTINCT dns_customer_code,customer_name FROM customer_master WHERE 
						emp_code IN(".$emp_code_string.")  AND acedns='Y' AND cust_type='D' AND customer_name <>''
						AND customer_code IN(SELECT CMR.rds_tag FROM customer_master CMR,order_header OH WHERE 
						OH.customer_code=CMR.customer_code AND DATE_FORMAT(SUBSTRING(OH.order_no,-14,8),'%Y-%m-%d') 
						BETWEEN '".$start_date."' AND '".$end_date."') ORDER BY customer_name ASC";
	$res_distributor = mysql_query($sql_distributor);
	while($row_distributor = mysql_fetch_array($res_distributor)){
		$distributor_code = $row_distributor['dns_customer_code'];
		$customer_name = $row_distributor['customer_name'];
		$distributor_code_string .= "'".$distributor_code."',";
		echo "<option value=\"'".$distributor_code."'\">".$customer_name."</option>";
	}
	$distributor_code_string = rtrim($distributor_code_string,",");
	echo "<option value=\"".$distributor_code_string."\">All</option>";
	echo "</select>";
}
else if($type == 'distemp'){
	//$onclick = "designation_emp(this.value);";
	echo "<select name=\"employee\" id=\"employee\" >";
	echo "<option value=\"\">Select</option>";
	$sql_emp = "SELECT DISTINCT EM.emp_code, EM.emp_name FROM employee_master EM,customer_master CM 
			WHERE CM.dns_customer_code IN(".$distributor.") AND CM.acedns='Y' ".$vertical_condition_one." AND CM.emp_code=EM.emp_code AND EM.designation='SO' AND EM.acedns='Y' ORDER BY EM.emp_name ASC";
	$res_emp = mysql_query($sql_emp);
	while($row_emp = mysql_fetch_array($res_emp)){
		$emp_code = $row_emp['emp_code'];
		$emp_name = $row_emp['emp_name'];
		$emp_code_string_dist .= "'".$emp_code."',";
		echo "<option value=\"'".$emp_code."'\">".$emp_name."</option>";
	}
	$emp_code_string_dist = rtrim($emp_code_string_dist,",");
	echo "<option value=\"".$emp_code_string_dist."\">All</option>";
	echo "</select>";
}
else if($type == 'statehistorical'){
	$sql_state = "SELECT DISTINCT SUBSTRING_INDEX(state, ',', 1) AS state FROM employee_master WHERE state!='' ".$emp_hierarchy_condition_one.$vertical_condition." ORDER BY state ASC";
	$state_select_control = "<select name=\"state\" id=\"state\" >";
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
	echo $table_data .= $state_select_control;
}
else if($type=='verticalemp')
{
	if($designation=='RSM' || $designation=='ASM' || $designation=='SO' || $designation=='SS')
	{
		$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE vertical_value=".$vertical." AND acedns='Y' 
					".$emp_hierarchy_condition_one." ORDER BY emp_name ASC";
		$res_emp = mysql_query($sql_emp);
			echo "<select name=\"asm_so\" id=\"asm_so\" >";
			echo "<option value=\"\">Select</option>";
			while($row_emp = mysql_fetch_array($res_emp)){
				$emp_code = $row_emp['emp_code'];
				$emp_name = $row_emp['emp_name'];
				$emp_code_string .= "'".$emp_code."',";
				echo "<option value=\"'".$emp_code."'\">".$emp_name."</option>";
			}
			$emp_code_string = rtrim($emp_code_string,",");
			//echo "<option value=\"".$emp_code_string."\">All</option>";
			echo "</select>";

	}
	else
	{
		$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE vertical_value=".$vertical."
						AND designation='RSM' AND acedns='Y' ORDER BY emp_name ASC";
		$res_emp = mysql_query($sql_emp);
		$count_emp=mysql_num_rows($res_emp);
		if($count_emp==0)
		{
			$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE vertical_value=".$vertical."
						AND designation IN('ASM','SO','SS') AND acedns='Y' ORDER BY emp_name ASC";
		}
	echo "<select name=\"rsm_name\" id=\"rsm_name\" onchange=\"emp_select_asmso(this.value);\">";
	echo "<option value=\"\">Select</option>";
	while($row_emp = mysql_fetch_array($res_emp)){
		$emp_code = $row_emp['emp_code'];
		$emp_name = $row_emp['emp_name'];
		$emp_code_string .= "'".$emp_code."',";
		echo "<option value=\"'".$emp_code."'\">".$emp_name."</option>";
	}
	$emp_code_string = rtrim($emp_code_string,",");
	//echo "<option value=\"".$emp_code_string."\">All</option>";
	echo "</select>";
	}
}
else if($type=='verticalempall')
{
	$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE vertical_value=".$vertical." AND acedns='Y' ".$emp_hierarchy_condition_one." ORDER BY emp_name ASC";
	$res_emp = mysql_query($sql_emp);
	echo "<select name=\"employee\" id=\"employee\" >";
	echo "<option value=\"\">Select</option>";
	while($row_emp = mysql_fetch_array($res_emp)){
		$emp_code = $row_emp['emp_code'];
		$emp_name = $row_emp['emp_name'];
		$emp_code_string .= "'".$emp_code."',";
		echo "<option value=\"'".$emp_code."'\">".$emp_name."</option>";
	}
	$emp_code_string = rtrim($emp_code_string,",");
	echo "<option value=\"".$emp_code_string."\">All</option>";
	echo "</select>";
}
else if($type=='verticalempone')
{
	$onclick = "emp_lev_two(this.value);";
	if($vertical=='all')
	{
		$vertical_condition=' 1';
	}
	else
	{
		$vertical_condition=' FIND_IN_SET( '.$vertical.',vertical_value)';
	}
	echo "<select name=\"employee_lev_one\" id=\"employee_lev_one\" onchange=\"".$onclick."\">";
	echo "<option value=\"\">Select</option>";
	//$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE state IN (".$state.")".$emp_hierarchy_condition_one." ORDER BY emp_name ASC";
	$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE $vertical_condition AND designation='".$designation."' AND acedns='Y' 
			ORDER BY emp_name ASC";
	$res_emp = mysql_query($sql_emp);
	while($row_emp = mysql_fetch_array($res_emp)){
		$emp_code = $row_emp['emp_code'];
		$emp_name = $row_emp['emp_name'];
		$emp_code_string .= "'".$emp_code."',";
		echo "<option value=\"'".$emp_code."'\">".$emp_name."</option>";
	}
	$emp_code_string = rtrim($emp_code_string,",");
	echo "<option value=\"".$emp_code_string."\">All</option>";
	echo "</select>";
}
else if($type=='emp_lev_two')
{
	if($vertical=='all')
	{
		$vertical_condition=' 1';
	}
	else
	{
		$vertical_condition=' FIND_IN_SET( '.$vertical.',vertical_value)';
	}
	$onclick = "emp_lev_three(this.value);";
	echo "<select name=\"employee_lev_two\" id=\"employee_lev_two\" onchange=\"".$onclick."\">";
	echo "<option value=\"\">Select</option>";
	//$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE state IN (".$state.")".$emp_hierarchy_condition_one." ORDER BY emp_name ASC";
	$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE  $vertical_condition AND designation='".$designation."' 
				AND acedns='Y' AND reporting_to IN (".$empcode.") ORDER BY emp_name ASC";
	$res_emp = mysql_query($sql_emp);
	while($row_emp = mysql_fetch_array($res_emp)){
		$emp_code = $row_emp['emp_code'];
		$emp_name = $row_emp['emp_name'];
		$emp_code_string .= "'".$emp_code."',";
		echo "<option value=\"'".$emp_code."'\">".$emp_name."</option>";
	}
	$emp_code_string = rtrim($emp_code_string,",");
	echo "<option value=\"".$emp_code_string."\">All</option>";
	echo "</select>";
}
else if($type=='emp_lev_three')
{
	if($vertical=='all')
	{
		$vertical_condition=' 1';
	}
	else
	{
		$vertical_condition=' FIND_IN_SET( '.$vertical.',vertical_value)';
	}
	$onclick = "emp_lev_four(this.value);";
	echo "<select name=\"employee_lev_three\" id=\"employee_lev_three\" onchange=\"".$onclick."\">";
	echo "<option value=\"\">Select</option>";
	//$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE state IN (".$state.")".$emp_hierarchy_condition_one." ORDER BY emp_name ASC";
	$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE  $vertical_condition AND designation='".$designation."' 
				AND acedns='Y' AND reporting_to IN (".$empcode.") ORDER BY emp_name ASC";
	$res_emp = mysql_query($sql_emp);
	while($row_emp = mysql_fetch_array($res_emp)){
		$emp_code = $row_emp['emp_code'];
		$emp_name = $row_emp['emp_name'];
		$emp_code_string .= "'".$emp_code."',";
		echo "<option value=\"'".$emp_code."'\">".$emp_name."</option>";
	}
	$emp_code_string = rtrim($emp_code_string,",");
	echo "<option value=\"".$emp_code_string."\">All</option>";
	echo "</select>";
}
else if($type=='emp_lev_four')
{
	if($vertical=='all')
	{
		$vertical_condition=' 1';
	}
	else
	{
		$vertical_condition=' FIND_IN_SET( '.$vertical.',vertical_value)';
	}
	echo "<select name=\"employee_lev_four\" id=\"employee_lev_four\" >";
	echo "<option value=\"\">Select</option>";
	//$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE state IN (".$state.")".$emp_hierarchy_condition_one." ORDER BY emp_name ASC";
	$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE  $vertical_condition AND designation='".$designation."' 
				AND acedns='Y' AND reporting_to IN (".$empcode.") ORDER BY emp_name ASC";
	$res_emp = mysql_query($sql_emp);
	while($row_emp = mysql_fetch_array($res_emp)){
		$emp_code = $row_emp['emp_code'];
		$emp_name = $row_emp['emp_name'];
		$emp_code_string .= "'".$emp_code."',";
		echo "<option value=\"'".$emp_code."'\">".$emp_name."</option>";
	}
	$emp_code_string = rtrim($emp_code_string,",");
	echo "<option value=\"".$emp_code_string."\">All</option>";
	echo "</select>";
}

mysql_close($link);
?>