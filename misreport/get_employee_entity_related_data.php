<?php
	ob_start();
session_start();
require("adminUtils.php");
	require("include/config.php");
	require("include/config-setup.php");
	require("include/dbcon.php");

	//For zone
	$zone=$_REQUEST['zone'];
	$zone_array=explode(',',$zone);
	$zone="'".implode("','", $zone_array)."'";
	//For branch
	$branch_code=$_REQUEST['branch_code'];
	$branch_code_array=explode(',',$branch_code);
	$branch_code="'".implode("','", $branch_code_array)."'";
	
	$data_type=$_REQUEST['data_type'];
	if($data_type=='fetch_branch')
	{
		$sql_branch = "SELECT DISTINCT  branch_code FROM employee_master 
					WHERE zone IN (".$zone.") AND branch_code != '' ORDER BY branch_code ASC";
		$res_branch = mysqli_query($link,$sql_branch);
		while($row_branch = mysqli_fetch_assoc($res_branch)){
			$branch_code = $row_branch['branch_code'];
			$sql_branch_name = "SELECT branch_name,branch_code FROM branch_master WHERE branch_code = '".$branch_code."'";
			$res_branch_name = mysqli_query($link,$sql_branch_name);
			$row_branch_name = mysqli_fetch_assoc($res_branch_name);
			$branch_name = $row_branch_name['branch_name'];
			  $content.="<tr>";
			  $content.="<td align='left'>";
			  $content.="<input type='checkbox' name='branch_code[]' value='".$row_branch['branch_code']."' onchange='javascript:select_emp_details();'/>".$branch_name."";
			  $content.="</td>";
			  $content.= "</tr>"; 
		}
		echo $content;
	}
	if($data_type=='fetch_emp')
	{
	    $emp_access=$_REQUEST['emp_access'];
		if($emp_access!='') $emp_access_condition=" AND acedns='".$emp_access."'";
		else 				$emp_access_condition='';
		$sql_emp = "SELECT emp_code,emp_name FROM employee_master WHERE branch_code IN (".$branch_code.") AND branch_code!='' $emp_access_condition ORDER BY emp_name ASC";
		$res_emp = mysqli_query($link,$sql_emp);
		while($row_emp = mysqli_fetch_assoc($res_emp)){
			  $content.="<tr>";
			  $content.="<td align='left'>";
			  $content.="<input type='checkbox' name='emp_code[]' value='".$row_emp['emp_code']."' onchange='javascript:select_entity();'/>".$row_emp['emp_name']."";
			  $content.="</td>";
			  $content.= "</tr>"; 
		}
		echo $content;
	}
	mysqli_close($link);
?>