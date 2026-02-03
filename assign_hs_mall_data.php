<?php
include "connection.php";

$emp_code = $_REQUEST['emp_code'];
$mall_hs_Array = $_REQUEST['mall_hs_Array'];
$emp_mall_status = $_REQUEST['emp_mall_status'];
$emp_Array = $_REQUEST['emp_Array'];
$designation = $_REQUEST['designation'];

if($designation == 'DCE'){
	
	foreach($mall_hs_Array as $mall_id){
		foreach($emp_Array as $emp_code){
			
			/*----> Using mall_emp_fs_relation table <-----*/
			$sql_check_mall_fs = "SELECT mall_id FROM mall_emp_fs_relation WHERE emp_code = '".$emp_code."' AND mall_id = '".$mall_id."'";
			$res_check_mall_fs = mysqli_query($link,$sql_check_mall_fs);
			$mall_fs_total_row = mysqli_num_rows($res_check_mall_fs);
			if($mall_fs_total_row>0)
				$condition = " mall_id = '".$mall_id."' AND emp_code = '".$emp_code."' AND status = 'done' ";
			else
				$condition = " mall_id = '".$mall_id."' AND status = 'done' ";

			$sql_update_mall_fs = "UPDATE mall_emp_fs_relation SET status = 'alloted' WHERE ".$condition;
			$res_update_mall_fs = mysqli_query($link,$sql_update_mall_fs);
			
			
			
			/*----> Using mall_hs_emp_relation table <-----*/
			$sql_insert_mall_emp_hs_rel = "INSERT INTO mall_hs_emp_relation SET mall_id = '".$mall_id."', emp_code = '".$emp_code."', designation = '".$designation."', status = '".$emp_mall_status."', enable_date = current_timestamp";
			$res_insert_mall_emp_hs_rel = mysqli_query($link,$sql_insert_mall_emp_hs_rel);
			
			/*----> Using mall_emp_relation table <-----*/
			$sql_insert_mall_emp_rel = "INSERT INTO mall_emp_relation SET mall_id = '".$mall_id."', emp_code = '".$emp_code."', status = '".$emp_mall_status."', enable_date = current_timestamp";
			$res_insert_mall_emp_rel = mysqli_query($link,$sql_insert_mall_emp_rel);
			
		}
		/*----> Update mall status mall_master table <-----*/
		$sql_update_mall_master = "UPDATE mall_master SET status = 'enable' WHERE mall_id = '".$mall_id."'";
		$res_update_mall_master = mysqli_query($link,$sql_update_mall_master);
		
		/*----> Update foot_soldier table download_time <-----*/
		$sql_foot_soldier = "UPDATE foot_soldier SET download_time = current_timestamp WHERE mall_id = '".$mall_id."' AND DCE_status = 'NOT DONE'";
		$res_foot_soldier = mysqli_query($link,$sql_foot_soldier);
	}
}
else if($designation == 'FS'){
	foreach($mall_hs_Array as $mall_id){
		/*---------> Insert Into mall_emp_fs_relation <---------*/
		$sql_insert_mall_emp_hs_rel = "INSERT INTO mall_emp_fs_relation SET mall_id = '".$mall_id."', emp_code = '".$emp_code."', status = '".$emp_mall_status."', enable_date = current_timestamp";
		$res_insert_mall_emp_hs_rel = mysqli_query($link,$sql_insert_mall_emp_hs_rel);
		
		/*----> Update mall status mall_master table <-----*/
		$sql_update_mall_master = "UPDATE mall_master SET status = 'enable' WHERE mall_id = '".$mall_id."'";
		$res_update_mall_master = mysqli_query($link,$sql_update_mall_master);
	}
}
else{
	$sql_desig = "SELECT designation FROM employee_master WHERE emp_code = '".$emp_code."'";
	$res_desig = mysqli_query($link,$sql_desig);
	$row_desig = mysqli_fetch_assoc($res_desig);
	$desig = $row_desig['designation'];
		
	foreach($mall_hs_Array as $mall_id){
		/*----> Using mall_hs_emp_relation table <-----*/
		$sql_insert_mall_emp_hs_rel = "INSERT INTO mall_hs_emp_relation SET mall_id = '".$mall_id."', emp_code = '".$emp_code."', designation = '".$desig."', status = '".$emp_mall_status."', enable_date = current_timestamp";
		$res_insert_mall_emp_hs_rel = mysqli_query($link,$sql_insert_mall_emp_hs_rel);
		
		$sql_update_mall_master = "UPDATE mall_master SET status = 'enable' WHERE mall_id = '".$mall_id."'";
		$res_update_mall_master = mysqli_query($link,$sql_update_mall_master);
		
		/*----> Using mall_emp_relation table <-----*/
		$sql_insert_mall_emp_rel = "INSERT INTO mall_emp_relation SET mall_id = '".$mall_id."', emp_code = '".$emp_code."', status = '".$emp_mall_status."'";
		$res_insert_mall_emp_rel = mysqli_query($link,$sql_insert_mall_emp_rel);
		
		$sql_update_mall_master = "UPDATE mall_master SET status = 'enable' WHERE mall_id = '".$mall_id."'";
		$res_update_mall_master = mysqli_query($link,$sql_update_mall_master);
	}
}
echo "Data updated successfully";
?>