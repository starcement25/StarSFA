<?php
ob_start();
session_start();
require("adminUtils.php");

$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];

$employee = $_REQUEST['employee'];
$employee_arg = str_replace("'","",$employee);
$emp_array = explode(",",$employee_arg);
$header ="PJP report for the period ".date('d-m-Y',strtotime($start_date))." to ".date('d-m-Y',strtotime($end_date))."\n";
$header .= "Employee Name"."\t"."Designation"."\t"."District"."\t"."Date"."\t"."Route Name";
	$no_records=0;
	foreach($emp_array as $emp_code){
			$sql_visit_date = "SELECT DISTINCT visit_date FROM route_plan WHERE (visit_date BETWEEN '".$start_date."' AND '".$end_date."') AND emp_code = '".$emp_code."' ORDER BY visit_date ASC";
			$res_visit_date = mysql_query($sql_visit_date);
			$visit_date_check = mysql_num_rows($res_visit_date);
			if($visit_date_check>0){
				
				$sql_emp_name = "SELECT emp_name,dns_emp_code,designation,district FROM employee_master WHERE emp_code = '".$emp_code."'";
				$res_emp_name = mysql_query($sql_emp_name);
				$row_emp_name = mysql_fetch_array($res_emp_name);
				$emp_name = $row_emp_name['emp_name'];
				$dns_emp_code = $row_emp_name['dns_emp_code'];
				$designation = $row_emp_name['designation'];
				$district = $row_emp_name['district'];
						
				$res_visit_date = mysql_query($sql_visit_date);
				while($row_visit_date = mysql_fetch_array($res_visit_date)){
					$visit_date = $row_visit_date['visit_date'];
					
					$sql_get_details = "SELECT route_code,create_date,(SELECT emp_name FROM employee_master WHERE emp_code=route_plan.working_with) AS working_with_name,remarks FROM route_plan WHERE visit_date = '".$visit_date."' 
										AND emp_code = '".$emp_code."' ORDER BY create_date ASC";
					$res_get_details = mysql_query($sql_get_details);
					$total_route_check = mysql_num_rows($res_get_details);
					if($total_route_check>0){
					$res_get_details = mysql_query($sql_get_details);
						${prev_create_date.$visit_date.$emp_code}='';
						while($row_get_details = mysql_fetch_array($res_get_details)){
							$route_code = $row_get_details['route_code'];
							$create_date = $row_get_details['create_date'];
							$working_with_name = $row_get_details['working_with_name'];
							$remarks = $row_get_details['remarks'];
							
							$sql_route_name = "SELECT route_name FROM route_master WHERE route_code = '".$route_code."'";
							$res_route_name = mysql_query($sql_route_name);
							$row_route_name = mysql_fetch_array($res_route_name);
							$route_name = $row_route_name['route_name'];
							if($working_with_name !='')
							{
							$route_name_final = $row_route_name['route_name'].' - <b>'.$working_with_name.'</b>';
							}
							else $route_name_final = $row_route_name['route_name'];
							
							
							if(strtoupper($_SESSION['nick_name'])=='SUPERSHAKTI'){
								
								if(${prev_create_date.$visit_date.$emp_code}!='' && (strtotime($create_date) > strtotime(${prev_create_date.$visit_date.$emp_code})))
								{
									$display_date=date('d/m/y',strtotime($create_date));
									$route_name_string .= 'ADDED '.$route_name." ON $display_date, ";
								}
								else
								{
								$route_name_string .= $route_name.", ";
								}
							}
							else if(strtoupper($_SESSION['nick_name'])=='ILS'){
								if($remarks!=''){
								$route_name_string .= $route_name_final.' - '.$remarks.', ';
								}
								else $route_name_string .= $route_name_final.", ";
							}
							else
							{
							$route_name_string .= $route_name_final.", ";
							}
							if(${prev_create_date.$visit_date.$emp_code}==''){
							${prev_create_date.$visit_date.$emp_code}=$create_date;
							}
						}
						$route_name_string = rtrim($route_name_string," ");
						$route_name_string = rtrim($route_name_string,",");
					}
		$table_data .=$emp_name."\t".$designation."\t".$district."\t".date('d-m-Y',strtotime($visit_date))."\t".$route_name_string."\n";
					$route_name_string = '';
					}
			}
		}
		if($table_data !=''){
			$datetime = gmdate('Y_m_d_H_m',strtotime('+330 minute'));
			$filename="pjp_report_".$datetime.'.xls';	
			header("Content-type: application/octet-stream"); 
			header("Content-Disposition: attachment; filename=$filename"); 
			header("Pragma: no-cache"); 
			header("Expires: 0"); //It will print all the Table row as Excel file row with selected column name as header. 
			echo ucwords($header)."\n".$table_data;
		}
		else
		{
			echo "<span style=\"font-weight:bold; color:red;\">No Records Found!</span>";
		}
mysql_close($link);
?>


