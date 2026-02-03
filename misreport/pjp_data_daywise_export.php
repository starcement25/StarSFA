<?php
ob_start();
session_start();
require("adminUtils.php");
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");

$start_date = $_REQUEST['start_date'];
$end_date = $_REQUEST['end_date'];
$survey_type = $_REQUEST['survey_type'];

$employee = $_REQUEST['employee'];
$employee_arg = str_replace("'","",$employee);
$emp_array = explode(",",$employee_arg);
$header = "Employee Code"."\t"."Employee Name"."\t"."Date"."\t"."Route Name";
	$no_records=0;
	foreach($emp_array as $emp_code){
			$sql_visit_date = "SELECT DISTINCT visit_date FROM route_plan WHERE (visit_date BETWEEN '".$start_date."' AND '".$end_date."') AND emp_code = '".$emp_code."' ORDER BY visit_date ASC";
			$res_visit_date = mysqli_query($link,$sql_visit_date);
			$visit_date_check = mysqli_num_rows($res_visit_date);
			if($visit_date_check>0){
				$sql_emp_name = "SELECT emp_name,dns_emp_code FROM employee_master WHERE emp_code = '".$emp_code."'";
				$res_emp_name = mysqli_query($link,$sql_emp_name);
				$row_emp_name = mysqli_fetch_assoc($res_emp_name);
				$emp_name = $row_emp_name['emp_name'];
				$dns_emp_code = $row_emp_name['dns_emp_code'];
				$res_visit_date = mysqli_query($link,$sql_visit_date);
				if(strtoupper($_SESSION['nick_name'])=='RUPA')
				{
					$disp_emp_code = $emp_code;
				}
				else
				{
					$disp_emp_code = $dns_emp_code;
				}
				while($row_visit_date = mysqli_fetch_assoc($res_visit_date)){
					$visit_date = $row_visit_date['visit_date'];
					
					$sql_get_details = "SELECT route_code,(SELECT emp_name FROM employee_master WHERE emp_code=route_plan.working_with) AS working_with_name FROM route_plan WHERE visit_date = '".$visit_date."' AND emp_code = '".$emp_code."'";
					$res_get_details = mysqli_query($link,$sql_get_details);
					$total_route_check = mysqli_num_rows($res_get_details);
					if($total_route_check>0){
						
						$res_get_details = mysqli_query($link,$sql_get_details);
						while($row_get_details = mysqli_fetch_assoc($res_get_details)){
							$route_code = $row_get_details['route_code'];
							$working_with_name = $row_get_details['working_with_name'];
							
							$sql_route_name = "SELECT route_name FROM route_master WHERE route_code = '".$route_code."'";
							$res_route_name = mysqli_query($link,$sql_route_name);
							$row_route_name = mysqli_fetch_assoc($res_route_name);
							$route_name = $row_route_name['route_name'];
							if($working_with_name !='')
							{
							$route_name_final = $row_route_name['route_name'].' - '.$working_with_name;
							}
							else $route_name_final = $row_route_name['route_name'];
							
							$route_name_string .= $route_name_final.", ";
						}
						$route_name_string = rtrim($route_name_string," ");
						$route_name_string = rtrim($route_name_string,",");
					}
		$table_data .= $disp_emp_code."\t".$emp_name."\t".date('d-m-Y',strtotime($visit_date))."\t".$route_name_string."\n";
					$route_name_string = '';
					}
			}
		}
		if($table_data !=''){	
			header("Content-type: application/octet-stream"); 
			header("Content-Disposition: attachment; filename=pjp_report.xls"); 
			header("Pragma: no-cache"); 
			header("Expires: 0"); //It will print all the Table row as Excel file row with selected column name as header. 
			echo ucwords($header)."\n".$table_data;
		}
		else
		{
			echo "<span style=\"font-weight:bold; color:red;\">No Records Found!</span>";
		}
mysqli_close($link);
?>


