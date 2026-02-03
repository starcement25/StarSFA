<?php
$sql_emp_designation = "SELECT designation FROM employee_master WHERE emp_code = '$_SESSION[admin_login]'";
$res_emp_designation = mysql_query($sql_emp_designation);
$row_emp_designation = mysql_fetch_array($res_emp_designation);
$designation = $row_emp_designation['designation'];

$sql_sauda_allocation_access = "SELECT flag FROM sauda_allocation_access WHERE designation = '$designation'";
$res_sauda_allocation_access = mysql_query($sql_sauda_allocation_access);
$row_sauda_allocation_access = mysql_fetch_array($res_sauda_allocation_access);

$_SESSION['flag'] = $row_sauda_allocation_access['flag'];
?>