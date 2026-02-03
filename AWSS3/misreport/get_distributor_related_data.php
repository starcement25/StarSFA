<?php
ob_start();
session_start();
require("adminUtils.php");

/*--------> Employee Hierarchy Condition <--------*/

$cust_type = $_REQUEST['cust_type'];
$type = $_REQUEST['type'];
$emp_code = $_REQUEST['emp_code'];
$customer_code = $_REQUEST['customer_code'];

/*--------> Branch Data Populate <--------*/
if(rtrim($type) == 'fetchdist'){
	$url="http://salesmpower.acedns.in/customer-master-audit-txt-incremental-8.0.7.php?nick_name=MAGIK&emp_code=$emp_code&incremental_download=no";
	//set the url, number of POST vars, POST data
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_TIMEOUT, 20);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
	$result = curl_exec($ch);
	//print_r($result);
	
	curl_close($ch);
	$array=explode("\n",$result);
	$distributor_string_logic_array=array();
	foreach($array as $arrayval)
	{
		if(strpos($arrayval,'^')!==false){
			$explodearr=explode("^",$arrayval);
			//print_r($explodearr);
			//exit();
				if($cust_type=='recommended'){
				if(strpos($explodearr[44],'logic')!==false && $explodearr[47]=='no'){
					if(!in_array($explodearr[10],$distributor_string_logic_array))
					{
					  $distributor_string_logic=$distributor_string_logic."'".$explodearr[10]."'".',';
					  array_push($distributor_string_logic_array,$explodearr[10]);
					}
					${distributor_route_string_logic.$explodearr[10]}=${distributor_route_string_logic.$explodearr[10]}."'".$explodearr[2]."'".',';
					${distributor_route_retcount_string_logic.$explodearr[10].$explodearr[2]}=${distributor_route_retcount_string_logic.$explodearr[10].$explodearr[2]}."'".$explodearr[0]."'".',';
				}
			}
			if($cust_type=='additional'){
				if(strpos($explodearr[44],'logic')===false && $explodearr[47]=='no'){
					if(!in_array($explodearr[10],$distributor_string_logic_array))
					{
					  $distributor_string_logic=$distributor_string_logic."'".$explodearr[10]."'".',';
					  array_push($distributor_string_logic_array,$explodearr[10]);
					}
					${distributor_route_string_not_logic.$explodearr[10]}=$distributor_route_string_logic."'".$explodearr[2]."'".',';
					${distributor_route_retcount_string_not_logic.$explodearr[10].$explodearr[2]}=${distributor_route_retcount_string_not_logic.$explodearr[10].$explodearr[2]}."'".$explodearr[0]."'".',';
				}
			}
		}
	}
	$distributor_string_logic=substr($distributor_string_logic,0,-1);

	//print_r($array);
	$onclick = "distributor_route('".$cust_type."',this.value);";
	$select_control="<select name=\"distributor\" id=\"distributor\" onchange=\"".$onclick."\">";
	$select_control .= "<option value=\"\">Select</option>";
	//echo "<option value=\"all\">All</option>";
	$sql_branch = "SELECT customer_name,customer_code FROM customer_master WHERE customer_code IN (".$distributor_string_logic.") ORDER BY customer_name ASC";
	$res_branch = mysql_query($sql_branch);
	while($row_branch = mysql_fetch_array($res_branch)){
		$customer_code = $row_branch['customer_code'];
		$customer_name = $row_branch['customer_name'];
		$select_control_option .="<option value=\"".$customer_code."\">".$customer_name."</option>";
	}
		$select_control .= $select_control_option;
		$select_control .="</select>";
	echo $select_control;
}
/*--------> Sale Access Data Populate <--------*/
else if(rtrim($type) == 'fetchroute'){
	$distributor_code = $_REQUEST['distributor_code'];
	$url="http://salesmpower.acedns.in/customer-master-audit-txt-incremental-8.0.7.php?nick_name=MAGIK&emp_code=$emp_code&incremental_download=no";
	//set the url, number of POST vars, POST data
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_TIMEOUT, 20);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
	$result = curl_exec($ch);
	//print_r($result);
	
	curl_close($ch);
	$array=explode("\n",$result);
	$distributor_string_logic_array=array();
	foreach($array as $arrayval)
	{
		if(strpos($arrayval,'^')!==false){
			$explodearr=explode("^",$arrayval);
			//print_r($explodearr);
			//exit();
				if($cust_type=='recommended'){
				if(strpos($explodearr[44],'logic')!==false && $explodearr[47]=='no'){
					if($explodearr[10]==$distributor_code){
					$distributor_route_string_logic=$distributor_route_string_logic."'".$explodearr[2]."'".',';
					${distributor_route_retcount_string_logic.$distributor_code.$explodearr[2]}=${distributor_route_retcount_string_logic.$distributor_code.$explodearr[2]}."'".$explodearr[0]."'".',';
					}
				}
			}
			if($cust_type=='additional'){
				if(strpos($explodearr[44],'logic')===false && $explodearr[47]=='no'){
					if($explodearr[10]==$distributor_code){
					$distributor_route_string_not_logic=$distributor_route_string_not_logic."'".$explodearr[2]."'".',';
					${distributor_route_retcount_string_not_logic.$distributor_code.$explodearr[2]}=${distributor_route_retcount_string_not_logic.$distributor_code.$explodearr[2]}."'".$explodearr[0]."'".',';
					}
				}
			}
		}
	}
	$distributor_route_string_logic=substr($distributor_route_string_logic,0,-1);
	$distributor_route_string_not_logic=substr($distributor_route_string_not_logic,0,-1);
	//print_r($array);
	$onclick = "route_customer('".$cust_type."','".$distributor_code."',this.value);";
	echo "<select name=\"route\" id=\"route\" onchange=\"".$onclick."\">";
	echo "<option value=\"\">Select</option>";
	//echo "<option value=\"all\">All</option>";
	if($cust_type=='recommended'){
		$sql_route = "SELECT route_name,route_code FROM route_master WHERE route_code IN (".$distributor_route_string_logic.") ORDER BY route_name ASC";
	}
	if($cust_type=='additional'){
			$sql_route = "SELECT route_name,route_code FROM route_master WHERE route_code IN (".$distributor_route_string_not_logic.") ORDER BY route_name ASC";
	}
	$res_route = mysql_query($sql_route);
	while($row_route = mysql_fetch_array($res_route)){
		$route_code = $row_route['route_code'];
		$route_name = $row_route['route_name'];
		
		if($cust_type=='recommended'){
		${distributor_route_retcount_string_logic.$distributor_code.$route_code}=substr(${distributor_route_retcount_string_logic.$distributor_code.$route_code},0,-1);	
		${distributor_route_retcount_string_logic_array.$distributor_code.$route_code}=explode(",",${distributor_route_retcount_string_logic.$distributor_code.$route_code});
		echo "<option value=\"".$route_code."\">".$route_name."<font color='#FF0000'>(".count(${distributor_route_retcount_string_logic_array.$distributor_code.$route_code}).")</font></option>";
		}
		if($cust_type=='additional'){
		${distributor_route_retcount_string_not_logic.$distributor_code.$route_code}=substr(${distributor_route_retcount_string_not_logic.$distributor_code.$route_code},0,-1);	
		${distributor_route_retcount_string_not_logic_array.$distributor_code.$route_code}=explode(",",${distributor_route_retcount_string_not_logic.$distributor_code.$route_code});
		echo "<option value=\"".$route_code."\">".$route_name."<font color='#FF0000'>(".count(${distributor_route_retcount_string_not_logic_array.$distributor_code.$route_code}).")</font></option>";
		}
	}
		echo "</select>";
}
else if(rtrim($type) == 'fetchroutecust'){
	$distributor_code = $_REQUEST['distributor_code'];
	$route_code = $_REQUEST['route_code'];
	$url="http://salesmpower.acedns.in/customer-master-audit-txt-incremental-8.0.7.php?nick_name=MAGIK&emp_code=$emp_code&incremental_download=no";
	//set the url, number of POST vars, POST data
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_TIMEOUT, 20);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
	$result = curl_exec($ch);
	//print_r($result);
	
	curl_close($ch);
	$array=explode("\n",$result);
	$distributor_string_logic_array=array();
	foreach($array as $arrayval)
	{
		if(strpos($arrayval,'^')!==false){
			$explodearr=explode("^",$arrayval);
			//print_r($explodearr);
			//exit();
				if($cust_type=='recommended'){
				if(strpos($explodearr[44],'logic')!==false && $explodearr[47]=='no'){
					if($explodearr[10]==$distributor_code && $explodearr[2]==$route_code){
					$distributor_route_cust_string_logic=$distributor_route_cust_string_logic."'".$explodearr[0]."'".',';
					}
				}
			}
			if($cust_type=='additional'){
				if(strpos($explodearr[44],'logic')===false && $explodearr[47]=='no'){
					if($explodearr[10]==$distributor_code  && $explodearr[2]==$route_code){
					$distributor_route_cust_string_logic=$distributor_route_cust_string_logic."'".$explodearr[0]."'".',';
					}
				}
			}
		}
	}
	$distributor_route_cust_string_logic=substr($distributor_route_cust_string_logic,0,-1);
	//print_r($array);
	//$onclick = "route_customer('".$distributor_code."','this.value');";
	$sql_customer = "SELECT customer_name,customer_code FROM customer_master WHERE customer_code IN (".$distributor_route_cust_string_logic.") ORDER BY customer_name ASC";
	$select_control="<select name=\"customer_code\" id=\"customer_code\" >";
	$select_control .= "<option value=\"\">Select</option>";
	$sql_customer = "SELECT customer_name,customer_code FROM customer_master WHERE customer_code IN (".$distributor_route_cust_string_logic.") ORDER BY customer_name ASC";
	$res_customer = mysql_query($sql_customer);
	while($row_customer = mysql_fetch_array($res_customer)){
		$customer_code = $row_customer['customer_code'];
		$customer_name = $row_customer['customer_name'];
		$select_control_option .="<option value=\"".$customer_code."\">".$customer_name."</option>";
	}
		$select_control .= $select_control_option;
		$select_control .="</select><span class='error'>&nbsp;<img src='images/remarks.png' alt='remarks' onclick='javascript:show_remarks();' height='25' width='25'/></span>";
	echo $select_control;
}
/*--------> Headquarter Data Populate <--------*/
else if(rtrim($type) == 'fetchremarks'){
	
	$sql_remarks = "SELECT d_instruction,remarks,hint_remarks FROM prev_order_counting_master WHERE customer_code='".$customer_code."' ORDER BY visit_date DESC LIMIT 0,1";
	$res_remarks = mysql_query($sql_remarks);
	$row_remarks = mysql_fetch_array($res_remarks);
	$d_instruction = $row_remarks['d_instruction'];
	$remarks = $row_remarks['remarks'];
	$hint_remarks = $row_remarks['hint_remarks'];
	if($d_instruction==''){
		$d_instruction_disp='N/A';
	}
	else $d_instruction_disp=$d_instruction."\n\n".$remarks."\n\n".$hint_remarks ;
	echo $d_instruction_disp;
}
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
	//$onclick = "designation_emp(this.value);";

	echo "<select name=\"employee\" id=\"employee\" onchange=\"".$onclick."\">";
	echo "<option value=\"\">Select</option>";
	//$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE state IN (".$state.")".$emp_hierarchy_condition_one." ORDER BY emp_name ASC";
	$sql_emp = "SELECT emp_code, emp_name FROM employee_master WHERE ".$state_condition.$vertical_condition.$emp_hierarchy_condition_one." ORDER BY emp_name ASC";
	$res_emp = mysql_query($sql_emp);
	while($row_emp = mysql_fetch_array($res_emp)){
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
	$resempbragain = mysql_query($sqlempbargain);
	while($rowempbargain = mysql_fetch_array($resempbragain)){
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
	$resempbragain = mysql_query($sqlempbargain);
	while($rowempbargain = mysql_fetch_array($resempbragain)){
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
else if($type == 'customersaudalimit'){
	echo "<select name=\"emp_name\" id=\"emp_name\" onChange=\"show_customer_name(this.value);\">";
	echo "<option value=\"\">Select</option>";
	/*$sql_select_emp = "SELECT EM.emp_code, EM.emp_name FROM employee_master EM ".$emp_hierarchy_condition." 
					AND EM.emp_code NOT LIKE 'C%' AND EM.acedns='Y' AND EM.state IN(".$state.") AND EM.emp_code NOT IN(SELECT DISTINCT reporting_to FROM employee_master) ORDER BY EM.emp_name ASC";*/
	$sql_select_emp = "SELECT EM.emp_code, EM.emp_name FROM employee_master EM ".$emp_hierarchy_condition." 
					AND EM.emp_code NOT LIKE 'C%' AND EM.acedns='Y' AND EM.state IN(".$state.") ORDER BY EM.emp_name ASC";				
	$res_select_emp = mysql_query($sql_select_emp);
	while($row_select_emp = mysql_fetch_array($res_select_emp)){
		$emp_code = $row_select_emp['emp_code'];
		$emp_name = $row_select_emp['emp_name'];
		
		$sql_check_menu_access = "SELECT emp_code FROM menu_access WHERE emp_code='".$emp_code."' AND not_accessible_menu != 'sauda'";
		$res_check_menu_access = mysql_query($sql_check_menu_access);
		$menu_access_rows = mysql_num_rows($res_check_menu_access);
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
	$res_emp = mysql_query($sql_emp);
	while($row_emp = mysql_fetch_array($res_emp)){
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
    $resultcustomerroute = mysql_query($sqlquerycustomerroute);
	$countcustomerroute=mysql_num_rows($resultcustomerroute);
	if($countcustomerroute>0){
		while($rowscustomerroute = mysql_fetch_array($resultcustomerroute))
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
    $resultcustomerroute = mysql_query($sqlquerycustomerroute);
	$countcustomerroute=mysql_num_rows($resultcustomerroute);
	if($countcustomerroute>0){
		while($rowscustomerroute = mysql_fetch_array($resultcustomerroute))
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
	$rstransportmode=mysql_query($sqltransportmode);
	while($rowtransportmode=mysql_fetch_array($rstransportmode))
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
	$rstransportmode=mysql_query($sqltransportmode);
	while($rowtransportmode=mysql_fetch_array($rstransportmode))
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
		$res_customer = mysql_query($sql_customer);
		while($row_customer = mysql_fetch_array($res_customer)){
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
    $resultcustomer= mysql_query($sqlquerycustomer);
	$countcustomer=mysql_num_rows($resultcustomer);
	if($countcustomer>0){
		while($rowscustomer = mysql_fetch_array($resultcustomer))
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
	$res_product = mysql_query($sql_product);
	while($row_product = mysql_fetch_array($res_product)){
		$prod_code = $row_product['prod_code'];
		$prod_desc = $row_product['prod_desc'];
		echo "<option value=\"'".$prod_code."'\">".$prod_desc."</option>";
	}
	echo "<option value=\"all\">All</option>";
	echo "</select>";
}

mysql_close($link);
?>