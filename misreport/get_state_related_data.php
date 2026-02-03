<?php
//ob_start();
/*error_reporting(E_ALL);
ini_set('display_errors', '1');
*/
session_start();
require("adminUtils.php");
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");

/*--------> Employee Hierarchy Condition <--------*/
if($_SESSION['admin_login']=="admin" || $_SESSION['admin_login'] == 'emovesfa_do' || $_SESSION['admin_login']=='emovesfa_hr' || strtoupper($_SESSION['admin_login'])=='ACCOUNTS'){
	$emp_hierarchy = '';
	$emp_hierarchy_condition = '';
	$designation_condition = " WHERE designation != '' ";
}
else{
	$emp_hierarchy=return_employee_hierarchy($_SESSION['admin_login']);
	$emp_hierarchy_condition = " WHERE emp_code IN(".$emp_hierarchy.") ";
	$emp_hierarchy_condition_one = " AND emp_code IN(".$emp_hierarchy.") ";
	$designation_condition = " AND designation != '' ";
}

$state = $_REQUEST['state'];
$type = $_REQUEST['type'];
$zone = $_REQUEST['zone'];
$vertical = $_REQUEST['vertical'];
$empcode	= $_REQUEST['empcode'];
$empcodeone	= $_REQUEST['empcodeone'];
$distributor=$_REQUEST['distributor'];
$region = $_REQUEST['region'];

if($vertical != ''){
	if($vertical == 'MACROMAN')
		$vertical_condition = " AND vertical_value LIKE 'M%' ";
	else if($vertical == 'all')
		$vertical_condition = "";
	else
		//$vertical_condition = " AND vertical_value = '".$vertical."'";
		$vertical_condition = " AND FIND_IN_SET('".$vertical."',vertical_value)";
}
else{
	$vertical_condition = "";
}

if($zone != ''){
	if($zone == 'all')
		$zone_condition = "";
	else
		$zone_condition = " AND zone IN(".$zone.") ";
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
if($state_condition==""){
    $sql_emp = "SELECT emp_code FROM employee_master ORDER BY emp_name ASC";
}

	$res_emp = mysqli_query($link,$sql_emp);
	while($row_emp = mysqli_fetch_assoc($res_emp)){
		$emp_code = $row_emp['emp_code'];
		$emp_code_string .= "'".$emp_code."',";
		//echo "<option value=\"'".$emp_code."'\">".$emp_name."</option>";
	}
	$emp_code_string = rtrim($emp_code_string,",");

/*--------> Check If Branch Exists <--------*/
$sql_branch = "SELECT DISTINCT SUBSTRING_INDEX(branch_code, ',', 1) AS branch_code FROM employee_master".$emp_hierarchy_value_condition.$branch_condition." ORDER BY branch_code ASC";
$res_branch = mysqli_query($link,$sql_branch);
$branch_total = mysqli_num_rows($res_branch);

if(strtoupper($_SESSION['nick_name']) == 'STAR' || strtoupper($_SESSION['nick_name']) == 'START' || strtoupper($_SESSION['nick_name']) == 'GOLDSTONET'){
/*--------> Check If Sale Access Exists <--------*/
$sql_sale_access = "SELECT DISTINCT sale_access FROM employee_master".$emp_hierarchy_value_condition.$sale_access_condition." ORDER BY sale_access ASC";
$res_sale_access = mysqli_query($link,$sql_sale_access);
$sale_access_total = mysqli_num_rows($res_sale_access);
}

if(strtoupper($_SESSION['nick_name']) != 'STAR' && strtoupper($_SESSION['nick_name']) != 'START' && strtoupper($_SESSION['nick_name']) != 'GOLDSTONET'){
/*--------> Check If Headquarter Exists <--------*/
$sql_hq = "SELECT DISTINCT hq FROM employee_master".$emp_hierarchy_condition.$hq_condition." ORDER BY hq ASC";
$res_hq = mysqli_query($link,$sql_hq);
$hq_total = mysqli_num_rows($res_hq);

/*--------> Check If Designation Exists <--------*/
$sql_designation = "SELECT DISTINCT designation FROM employee_master".$emp_hierarchy_condition.$designation_condition." ORDER BY designation ASC";
$res_designation = mysqli_query($link,$sql_designation);
$designation_total = mysqli_num_rows($res_designation);
}

/*--------> Branch Data Populate <--------*/
if($type == 'branch'){
	if(strpos($_SERVER['HTTP_REFERER'],'branding_verification_report.php') > 0
	|| strpos($_SERVER['HTTP_REFERER'],'branding_verification_with_location.php') >0
	|| strpos($_SERVER['HTTP_REFERER'],'branding_verification_with_location_modified.php') >0
	|| strpos($_SERVER['HTTP_REFERER'],'logistics_checkin_checkout.php') > 0){
		$onclick = "branch_emp(this.value);";
	}
	else if(strpos($_SERVER['HTTP_REFERER'],'admin_customer_tagging.php') > 0)
	{
		$onclick = "branch_cluster(this.value);";
	}
	else
	{
	if($sale_access_total>0)
		$onclick = "branch_saleaccess(this.value);";
	else if($hq_total>0)
		$onclick = "branch_hq(this.value);";
	else if($designation_total>0)
		$onclick = "branch_designation(this.value);";
	else
		$onclick = "branch_emp(this.value);";
	}
	if(strpos($_SERVER['HTTP_REFERER'],'branchwise_schemes_PDF.php') > 0 || strpos($_SERVER['HTTP_REFERER'],'distributorwise_yellowcard_count.php') > 0 || strpos($_SERVER['HTTP_REFERER'],'customer_base_latt_longi_edit.php')
	|| strpos($_SERVER['HTTP_REFERER'],'yellow_card_date_validation_customerwise.php') > 0 || strpos($_SERVER['HTTP_REFERER'],'admin_customer_tagging.php') > 0){
	echo "<select name=\"branch\" id=\"branch\" onchange=\"".$onclick."\">";
	echo "<option value=\"\">Select</option>";
	$branch_code_array = array();
	$branch_name_array = array();
	$sql_branch = "SELECT DISTINCT branch_code FROM employee_master WHERE state IN (".$state.")".$emp_hierarchy_condition_one." AND branch_code != '' ".$zone_condition." ORDER BY branch_code ASC";
	$res_branch = mysqli_query($link,$sql_branch);
	while($row_branch = mysqli_fetch_assoc($res_branch)){
		$branch_code = $row_branch['branch_code'];
		$sql_branch_name = "SELECT branch_code, branch_name FROM branch_master WHERE FIND_IN_SET(branch_code,'".$branch_code."')";
		$res_branch_name = mysqli_query($link,$sql_branch_name);
		while($row_branch_name = mysqli_fetch_assoc($res_branch_name)){
			$branch_name = $row_branch_name['branch_name'];
			$branch_code_get = $row_branch_name['branch_code'];
			if(!in_array($branch_code_get,$branch_code_array)){
				//$branch_code_array[$branch_code_get] = $branch_name;
				array_push($branch_code_array,$branch_code_get);
				array_push($branch_name_array,$branch_name);

				if(strpos($_SERVER['HTTP_REFERER'],'yellow_card_date_validation_customerwise.php') > 0 || strpos($_SERVER['HTTP_REFERER'],'admin_customer_tagging.php') > 0)
				{
					$branch_string .= "'".$branch_code_get."'".",";
				}
				else
				{
					$branch_string .= "".$branch_code_get.",";
				}
			}
		}
	}
	$branch_string = rtrim($branch_string,",");
	/*foreach($branch_code_array as $branch_index=>$branch_val){
		echo "<option value=\"'".$branch_index."'\">".$branch_val."</option>";
	}*/
	echo "<option value=\"".$branch_string."\">All</option>";
	for($i=0;$i<count($branch_code_array);$i++)
	{
		if(strpos($_SERVER['HTTP_REFERER'],'yellow_card_date_validation_customerwise.php') > 0 || strpos($_SERVER['HTTP_REFERER'],'admin_customer_tagging.php') > 0)
				{
					echo "<option value=\"'".$branch_code_array[$i]."'\">".$branch_name_array[$i]."</option>";
				}
			else
			{
		echo "<option value=\"".$branch_code_array[$i]."\">".$branch_name_array[$i]."</option>";
			}
	}
	echo "</select>";
  }
 else if(strpos($_SERVER['HTTP_REFERER'],'branchwise_geo_fencing_employee.php') > 0){
	 echo "<select name=\"branch\" id=\"branch\" >";
	echo "<option value=\"\">Select</option>";
	$branch_code_array = array();
	$branch_name_array = array();
	$sql_branch = "SELECT branch_code,branch_name FROM branch_master WHERE branch_code IN(SELECT DISTINCT branch_code FROM employee_master WHERE state IN (".$state.")".$emp_hierarchy_condition_one."
					AND branch_code != '' ".$zone_condition." AND branch_code
					IN(SELECT DISTINCT branch_code FROM branchwise_geo_fencing WHERE geo_fencing='yes')) ORDER BY branch_name ASC";
	$res_branch = mysqli_query($link,$sql_branch);
	while($row_branch = mysqli_fetch_assoc($res_branch)){

			$branch_name = $row_branch['branch_name'];
			$branch_code_get = $row_branch['branch_code'];
			if(!in_array($branch_code_get,$branch_code_array)){
				//$branch_code_array[$branch_code_get] = $branch_name;
				array_push($branch_code_array,$branch_code_get);
				array_push($branch_name_array,$branch_name);
				$branch_string .= "'".$branch_code_get."'".",";
			}
	}
	$branch_string = rtrim($branch_string,",");
	/*foreach($branch_code_array as $branch_index=>$branch_val){
		echo "<option value=\"'".$branch_index."'\">".$branch_val."</option>";
	}*/
	echo "<option value=\"".$branch_string."\">All</option>";
	for($i=0;$i<count($branch_code_array);$i++)
	{
		echo "<option value=\"'".$branch_code_array[$i]."'\">".$branch_name_array[$i]."</option>";
	}
	echo "</select>";
 }

  else
  {
	if(strpos($_SERVER['HTTP_REFERER'],'SIS_report_ROE.php') > 0 || strpos($_SERVER['HTTP_REFERER'],'SIS_report_NE.php') > 0)
	{
		$region_condition=" AND region=".$region."";
	}
	else $region_condition='';

	echo "<select name=\"branch\" id=\"branch\" onchange=\"".$onclick."\">";
	echo "<option value=\"\">Select</option>";
	if(strpos($_SERVER['HTTP_REFERER'],'admin_mis_market_pricing_rsp.php') <= 0){
	echo "<option value=\"all\">All</option>";
	}

	$branch_code_array = array();
	$branch_name_array = array();
	echo $sql_branch = "SELECT DISTINCT branch_code FROM employee_master WHERE state IN (".$state.")".$emp_hierarchy_condition_one.$region_condition." AND branch_code != '' ".$zone_condition." ORDER BY branch_code ASC";
	$res_branch = mysqli_query($link,$sql_branch);
	while($row_branch = mysqli_fetch_assoc($res_branch)){
		$branch_code = $row_branch['branch_code'];
		$sql_branch_name = "SELECT branch_code, branch_name FROM branch_master WHERE FIND_IN_SET(branch_code,'".$branch_code."')";
		$res_branch_name = mysqli_query($link,$sql_branch_name);
		while($row_branch_name = mysqli_fetch_assoc($res_branch_name)){
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
	$res_sale_access = mysqli_query($link,$sql_sale_access);
	while($row_sale_access = mysqli_fetch_assoc($res_sale_access)){
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
	$res_hq = mysqli_query($link,$sql_hq);
	while($row_hq = mysqli_fetch_assoc($res_hq)){
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
	$res_designation = mysqli_query($link,$sql_designation);
	while($row_designation = mysqli_fetch_assoc($res_designation)){
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
	//$onclick = "designation_emp(this.value);";

	echo "<select name=\"employee\" id=\"employee\" onchange=\"".$onclick."\">";
	echo "<option value=\"\">Select</option>";
	//$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE state IN (".$state.")".$emp_hierarchy_condition_one." ORDER BY emp_name ASC";
	$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE ".$state_condition.$vertical_condition.$emp_hierarchy_condition_one." and acedns='Y' ORDER BY emp_name ASC";
	$res_emp = mysqli_query($link,$sql_emp);
	while($row_emp = mysqli_fetch_assoc($res_emp)){
		$emp_code = $row_emp['emp_code'];
		$emp_name = $row_emp['emp_name'];
		$emp_code_string_val .= "'".$emp_code."',";
		echo "<option value=\"'".$emp_code."'\">".$emp_name."</option>";
	}
	$emp_code_string_val = rtrim($emp_code_string_val,",");
	echo "<option value=\"".$emp_code_string_val."\">All</option>";
	echo "</select>";
}
else if($type == 'empbargain'){
	//$onclick = "designation_emp(this.value);";
	/*echo $sqlemp="SELECT DISTINCT EM.emp_code,EM.emp_name FROM customer_master CM,customer_route_emp_relation CRR,employee_master EM WHERE
				CRR.emp_code=EM.emp_code AND CRR.customer_code=CM.customer_code AND CRR.acedns='Y'
				AND CM.state_code IN(".$state.") ORDER BY EM.emp_name ASC";*/
	$emp_select_control = "<select name=\"employee\" id=\"employee\" onchange=\"".$onclick."\">";
	$emp_select_control .="<option value=\"\">Select</option>";
	//$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE state IN (".$state.")".$emp_hierarchy_condition_one." ORDER BY emp_name ASC";
	/*$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE ".$state_condition.$vertical_condition.$emp_hierarchy_condition_one."
				AND emp_code IN(SELECT SUBSTRING(sauda_no,3,5) FROM sauda_header) ORDER BY emp_name ASC";*/
	$sqlempbargain="SELECT DISTINCT EM.emp_code,EM.emp_name FROM customer_master CM,employee_master EM,DO_master SH WHERE
				SUBSTRING(SH.sauda_no,3,5)=EM.emp_code AND SH.customer_code=CM.customer_code
				AND CM.state_code IN(".$state.") ORDER BY EM.emp_name ASC";
	$resempbragain = mysqli_query($link,$sqlempbargain);
	while($rowempbargain = mysqli_fetch_assoc($resempbragain)){
		$emp_code = $rowempbargain['emp_code'];
		$emp_name = $rowempbargain['emp_name'];
		$emp_code_string_val .= "'".$emp_code."',";
		$emp_select_control_options .="<option value=\"'".$emp_code."'\">".$emp_name."</option>";
	}
	$emp_code_string_val = rtrim($emp_code_string_val,",");
	$emp_select_control .="<option value=\"".$emp_code_string_val."\">All</option>";
	$emp_select_control .=$emp_select_control_options;
	echo $emp_select_control .="</select>";
}
else if($type == 'empdo'){
	//$onclick = "designation_emp(this.value);";
	/*echo $sqlemp="SELECT DISTINCT EM.emp_code,EM.emp_name FROM customer_master CM,customer_route_emp_relation CRR,employee_master EM WHERE
				CRR.emp_code=EM.emp_code AND CRR.customer_code=CM.customer_code AND CRR.acedns='Y'
				AND CM.state_code IN(".$state.") ORDER BY EM.emp_name ASC";*/
	$emp_select_control = "<select name=\"employee\" id=\"employee\" onchange=\"".$onclick."\">";
	$emp_select_control .="<option value=\"\">Select</option>";
	//$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE state IN (".$state.")".$emp_hierarchy_condition_one." ORDER BY emp_name ASC";
	/*$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE ".$state_condition.$vertical_condition.$emp_hierarchy_condition_one."
				AND emp_code IN(SELECT SUBSTRING(sauda_no,3,5) FROM sauda_header) ORDER BY emp_name ASC";*/
	$sqlempbargain="SELECT DISTINCT EM.emp_code,EM.emp_name FROM customer_master CM,employee_master EM,DO_transaction DT WHERE
				SUBSTRING(DT.DO_no,3,5)=EM.emp_code AND DT.customer_code=CM.customer_code
				AND CM.state_code IN(".$state.") ORDER BY EM.emp_name ASC";
	$resempbragain = mysqli_query($link,$sqlempbargain);
	while($rowempbargain = mysqli_fetch_assoc($resempbragain)){
		$emp_code = $rowempbargain['emp_code'];
		$emp_name = $rowempbargain['emp_name'];
		$emp_code_string_val .= "'".$emp_code."',";
		$emp_select_control_options .="<option value=\"'".$emp_code."'\">".$emp_name."</option>";
	}
	$emp_code_string_val = rtrim($emp_code_string_val,",");
	$emp_select_control .="<option value=\"".$emp_code_string_val."\">All</option>";
	$emp_select_control .=$emp_select_control_options;
	echo $emp_select_control .="</select>";
}
else if($type == 'ASM' || $type == 'SO' || $type == 'TSI' || $type == 'DSM'){
	//$onclick = "emp_lev_two(this.value);";

	echo "<select name=\"employee_lev_one\" id=\"employee_lev_one\" >";
	echo "<option value=\"\">Select</option>";
	//$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE state IN (".$state.")".$emp_hierarchy_condition_one." ORDER BY emp_name ASC";
	$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE ".$state_condition." AND designation='".$type."' AND acedns='Y' ORDER BY emp_name ASC";
	$res_emp = mysqli_query($link,$sql_emp);
	while($row_emp = mysqli_fetch_assoc($res_emp)){
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
	$res_emp = mysqli_query($link,$sql_emp);
	while($row_emp = mysqli_fetch_assoc($res_emp)){
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
	$res_emp = mysqli_query($link,$sql_emp);
	while($row_emp = mysqli_fetch_assoc($res_emp)){
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
	$res_emp = mysqli_query($link,$sql_emp);
	while($row_emp = mysqli_fetch_assoc($res_emp)){
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
	$res_distributor = mysqli_query($link,$sql_distributor);
	while($row_distributor = mysqli_fetch_assoc($res_distributor)){
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
	$res_emp = mysqli_query($link,$sql_emp);
	while($row_emp = mysqli_fetch_assoc($res_emp)){
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
    $resultcustomerroute = mysqli_query($link,$sqlquerycustomerroute);
	$countcustomerroute=mysqli_num_rows($resultcustomerroute);
	if($countcustomerroute>0){
		while($rowscustomerroute = mysqli_fetch_assoc($resultcustomerroute))
		{
			$route_code=$rowscustomerroute['route_code'];
			$sqlquerycustomerroutedetails="SELECT DISTINCT CM.customer_code FROM customer_route_emp_relation CRR,customer_master CM
										WHERE CM.customer_code=CRR.customer_code AND
							CRR.route_code='".$route_code."' AND CM.cust_type='R' AND CRR.acedns='Y' AND CM.rds_tag IN(".$distributor.")";
			$resultcustomerroutedetails = mysqli_query($link,$sqlquerycustomerroutedetails);
			$countcustomerroutedetails=mysqli_num_rows($resultcustomerroutedetails);

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
else if($type == 'customersaudalimit'){
	echo "<select name=\"emp_name\" id=\"emp_name\" onChange=\"show_customer_name(this.value);\">";
	echo "<option value=\"\">Select</option>";
	/*$sql_select_emp = "SELECT EM.emp_code, EM.emp_name FROM employee_master EM ".$emp_hierarchy_condition."
					AND EM.emp_code NOT LIKE 'C%' AND EM.acedns='Y' AND EM.state IN(".$state.") AND EM.emp_code NOT IN(SELECT DISTINCT reporting_to FROM employee_master) ORDER BY EM.emp_name ASC";*/
	$sql_select_emp = "SELECT EM.emp_code, EM.emp_name FROM employee_master EM ".$emp_hierarchy_condition."
					AND EM.emp_code NOT LIKE 'C%' AND EM.acedns='Y' AND EM.state IN(".$state.") ORDER BY EM.emp_name ASC";
	$res_select_emp = mysqli_query($link,$sql_select_emp);
	while($row_select_emp = mysqli_fetch_assoc($res_select_emp)){
		$emp_code = $row_select_emp['emp_code'];
		$emp_name = $row_select_emp['emp_name'];

		$sql_check_menu_access = "SELECT emp_code FROM menu_access WHERE emp_code='".$emp_code."' AND not_accessible_menu != 'sauda'";
		$res_check_menu_access = mysqli_query($link,$sql_check_menu_access);
		$menu_access_rows = mysqli_num_rows($res_check_menu_access);
		if($menu_access_rows >1)
			echo "<option value=\"'".$emp_code."'\">".$emp_name."</option>";
			$emp_code_string .= "'".$emp_code."',";
	}
	$emp_code_string = rtrim($emp_code_string,",");
	echo "<option value=\"".$emp_code_string."\">All</option>";
	echo "</select>";
}
else if($type == 'stateroute'){
	//$onclick = "designation_emp(this.value);";
	//echo "<select name=\"route\" id=\"route\" >";
	//echo "<option value=\"\">Select</option>";
	/*$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE ".$state_condition." AND acedns='Y' ORDER BY emp_name ASC";
	$res_emp = mysqli_query($link,$sql_emp);
	while($row_emp = mysqli_fetch_assoc($res_emp)){
		$emp_code = $row_emp['emp_code'];
		$emp_name = $row_emp['emp_name'];
		$emp_code_string .= "'".$emp_code."',";
		//echo "<option value=\"'".$emp_code."'\">".$emp_name."</option>";
	}
	$emp_code_string = rtrim($emp_code_string,",");*/
	echo "<select name=\"route\" id=\"route\" onChange=\"javascript:show_transportmode(this.value);\">";
	echo "<option value=\"\">Choose Route</option>";
	$sqlquerycustomerroute="SELECT DISTINCT CM.route_code,RM.route_name FROM customer_master CM,route_master RM
							WHERE CM.route_code=RM.route_code AND RM.route_name!='' AND CM.acedns='Y' AND
								CM.state_code IN(".$state.") AND CM.cust_type='D'  ORDER BY RM.route_name ASC";
    $resultcustomerroute = mysqli_query($link,$sqlquerycustomerroute);
	$countcustomerroute=mysqli_num_rows($resultcustomerroute);
	if($countcustomerroute>0){
		while($rowscustomerroute = mysqli_fetch_assoc($resultcustomerroute))
		{
			$route_name=$rowscustomerroute['route_name'];
			$route_code=$rowscustomerroute['route_code'];
			$option_value_string.="<option value=\"'".$route_code."'\">".strtoupper($route_name)."</option>";
			$route_code_string .= "'".$route_code."',";
		}
	}
	$route_code_string = rtrim($route_code_string,",");
	echo "<option value=\"".$route_code_string."\">All</option>";
	echo $option_value_string;
	echo "</select>";
}
else if($type == 'stateroutecust'){
	echo "<select name=\"route\" id=\"route\" onChange=\"javascript:show_transportmode(this.value);\">";
	echo "<option value=\"\">Choose Route</option>";
	$sqlquerycustomerroute="SELECT DISTINCT CM.route_code,RM.route_name FROM customer_master CM,route_master RM
							WHERE CM.route_code=RM.route_code AND RM.route_name!='' AND CM.acedns='Y' AND
								CM.state_code IN(".$state.") AND CM.cust_type='D'  ORDER BY RM.route_name ASC";
    $resultcustomerroute = mysqli_query($link,$sqlquerycustomerroute);
	$countcustomerroute=mysqli_num_rows($resultcustomerroute);
	if($countcustomerroute>0){
		while($rowscustomerroute = mysqli_fetch_assoc($resultcustomerroute))
		{
			$route_name=$rowscustomerroute['route_name'];
			$route_code=$rowscustomerroute['route_code'];
			$option_value_string.="<option value=\"'".$route_code."'\">".strtoupper($route_name)."</option>";
			$route_code_string .= "'".$route_code."',";
		}
	}
	$route_code_string = rtrim($route_code_string,",");
	//echo "<option value=\"".$route_code_string."\">All</option>";
	echo $option_value_string;
	echo "</select>";
}
if($type=="statetransport"){
	$state=$_REQUEST['state'];
	$content='<select name="transport_mode" id="transport_mode" onChange="javascript:load_capacity();">';
	$content.='<option value="">Choose Trasport Mode </option>';
	$sqltransportmode="SELECT DISTINCT transport_mode FROM customer_master
						WHERE  acedns='Y' AND
						 state_code IN(".$state.")  AND transport_mode <> '' ORDER BY transport_mode ASC";
	$rstransportmode=mysqli_query($link,$sqltransportmode);
	while($rowtransportmode=mysqli_fetch_assoc($rstransportmode))
	{
		$content.="<option value='".$rowtransportmode['transport_mode']."'>".$rowtransportmode['transport_mode']."</option>";
	}
	echo $content.='</select>';
}
if($type=="stateroutetransport"){
	$state=$_REQUEST['state'];
	$route=$_REQUEST['route'];
	$content='<select name="transport_mode" id="transport_mode" onChange="javascript:load_capacity();">';
	$content.='<option value="">Trasport Mode</option>';
	$sqltransportmode="SELECT DISTINCT transport_mode FROM customer_master
						WHERE  acedns='Y' AND cust_type='D' AND
						 state_code IN(".$state.") AND route_code IN(".$route.") AND transport_mode <> '' ORDER BY transport_mode ASC";
	$rstransportmode=mysqli_query($link,$sqltransportmode);
	while($rowtransportmode=mysqli_fetch_assoc($rstransportmode))
	{
		$content.="<option value='".$rowtransportmode['transport_mode']."'>".strtoupper($rowtransportmode['transport_mode'])."</option>";
	}
	echo $content.='</select>';
}
else if($type == 'cust'){
	$empcodearray=explode(",",$empcode);
	$stringval="<select name=\"customer_code\" id=\"customer_code\" >";
	$stringval.="<option value=\"\">Select</option>";
	foreach ($empcodearray as $empcodeval)
	{
		//$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE state IN (".$state.")".$emp_hierarchy_condition_one." ORDER BY emp_name ASC";
		$sql_customer = "SELECT CRR.customer_code,CM.customer_name FROM customer_route_emp_relation CRR,customer_master CM,employee_master EM WHERE
						CRR.emp_code=EM.emp_code AND CM.customer_code=CRR.customer_code AND CRR.emp_code
						IN(SELECT emp_code FROM employee_master WHERE   FIND_IN_SET( '".$empcodeval."',reporting_to))
						ORDER BY CM.customer_name ASC";
		$res_customer = mysqli_query($link,$sql_customer);
		while($row_customer = mysqli_fetch_assoc($res_customer)){
			$customer_code = $row_customer['customer_code'];
			$customer_name = $row_customer['customer_name'];
			$stringval.="<option value=\"'".$customer_code."'\">".$customer_name."</option>";
		}
	}
	$stringval.="<option value=\"all\">All</option>";
	$stringval.="</select>";
	echo $stringval;
}
else if($type == 'custvalidity'){
		echo "<select name=\"customer_code\" id=\"customer_code\" >";
	echo "<option value=\"\">Select</option>";

	$sqlquerycustomer="SELECT customer_code,customer_name FROM customer_master
							WHERE state_code IN(".$state.") AND  customer_code IN(SELECT DISTINCT customer_code FROM DO_master WHERE is_approved='yes'
							AND qty >0) ORDER BY customer_name ASC";
    $resultcustomer= mysqli_query($link,$sqlquerycustomer);
	$countcustomer=mysqli_num_rows($resultcustomer);
	if($countcustomer>0){
		while($rowscustomer = mysqli_fetch_assoc($resultcustomer))
		{
			$customer_code=$rowscustomer['customer_code'];
			$customer_name=$rowscustomer['customer_name'];
			$option_value_string.="<option value=\"'".$customer_code."'\">".$customer_name."</option>";
			$customer_code_string .= "'".$customer_code."',";
		}
	}
	$customer_code_string = rtrim($customer_code_string,",");
	echo "<option value=\"".$customer_code_string."\">All</option>";
	echo $option_value_string;
	echo "</select>";
}
else if($type == 'model'){
	echo "<select name=\"prod_code\" id=\"prod_code\" >";
	echo "<option value=\"\">Select</option>";
	//$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE state IN (".$state.")".$emp_hierarchy_condition_one." ORDER BY emp_name ASC";
	$sql_product = "SELECT prod_code,prod_desc FROM product_master WHERE acedns='Y' ORDER BY prod_desc ASC";
	$res_product = mysqli_query($link,$sql_product);
	while($row_product = mysqli_fetch_assoc($res_product)){
		$prod_code = $row_product['prod_code'];
		$prod_desc = $row_product['prod_desc'];
		echo "<option value=\"'".$prod_code."'\">".$prod_desc."</option>";
	}
	echo "<option value=\"all\">All</option>";
	echo "</select>";
}
else if($type == 'emproute'){
	$employeeval	= $_REQUEST['employee'];
	//$onclick = "designation_emp(this.value);";
	//echo "<select name=\"route\" id=\"route\" >";
	//echo "<option value=\"\">Select</option>";
	/*$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE ".$state_condition." AND acedns='Y' ORDER BY emp_name ASC";
	$res_emp = mysqli_query($link,$sql_emp);
	while($row_emp = mysqli_fetch_assoc($res_emp)){
		$emp_code = $row_emp['emp_code'];
		$emp_name = $row_emp['emp_name'];
		$emp_code_string .= "'".$emp_code."',";
		//echo "<option value=\"'".$emp_code."'\">".$emp_name."</option>";
	}
	$emp_code_string = rtrim($emp_code_string,",");*/
	echo "<select name=\"route\" id=\"route\" >";
	echo "<option value=\"\">Route</option>";
	$sqlqueryemproute="SELECT DISTINCT RM.route_code,RM.route_name FROM customer_route_emp_relation CM,route_master RM
							WHERE CM.route_code=RM.route_code AND RM.route_name!='' AND CM.acedns='Y' AND
								CM.emp_code IN(".$employeeval.") ORDER BY RM.route_name ASC";
    $resultqueryemproute = mysqli_query($link,$sqlqueryemproute);
	$countqueryemproute=mysqli_num_rows($resultqueryemproute);
	if($countqueryemproute>0){
		while($rowqueryemproute = mysqli_fetch_assoc($resultqueryemproute))
		{
			$route_name=$rowqueryemproute['route_name'];
			$route_code=$rowqueryemproute['route_code'];
			$option_value_string.="<option value=\"'".$route_code."'\">".$route_name."</option>";
			$route_code_string .= "'".$route_code."',";
		}
	}
	$route_code_string = rtrim($route_code_string,",");
	echo "<option value='all'>All</option>";
	echo $option_value_string;
	echo "</select>";
}

mysqli_close($link);
?>
