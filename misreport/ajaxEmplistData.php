<?php
ob_start();
session_start();
require("adminUtils.php");
if($_SESSION['admin_login']=="")  		header("location:index.php");

$emp_code = $_REQUEST['emp_id'];

if(!empty($emp_code)){
    //Fetch all state data
	echo "SELECT EM.emp_code, EM.emp_name FROM employee_master EM WHERE EM.reporting_to=FIND_IN_SET('".$emp_code."',EM.reporting_to) ORDER BY EM.emp_name ASC";
   $query = mysqli_query($link,"SELECT EM.emp_code, EM.emp_name FROM employee_master EM WHERE EM.reporting_to=FIND_IN_SET('".$emp_code."',EM.reporting_to) ORDER BY EM.emp_name ASC");
    
    //Count total number of rows
    $rowCount = mysqli_num_rows($query);
    
    //State option list
    if($rowCount > 0){
        echo '<option value="">Select Employee</option>';
        while($row =mysqli_fetch_assoc($query)){ 
            echo '<option value="'.$row['emp_code'].'">'.$row['emp_name'].'</option>';
        }
    }else{
        echo '<option value="">Employee not available</option>';
    }
}