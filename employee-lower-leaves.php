<?php		
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");
require("include/functions.php");

    
	$sqlempcodefetch="SELECT emp_code FROM employee_master ";
    $rsempcodefetch=mysqli_query($link,$sqlempcodefetch);
	$emp_code_no_appaccess='';
    while($rowempcodefetch=mysqli_fetch_assoc($rsempcodefetch))
    {
        echo $emp_code_fetched=$rowempcodefetch['emp_code'];
		$employee_hierarchy=return_employee_hierarchy($emp_code_fetched);
        
        echo $sqlupdateemp="UPDATE employee_master set lower_leaves='".addslashes($employee_hierarchy)."' WHERE emp_code='".$emp_code_fetched."'";
        $rsupdateemp=mysqli_query($link,$sqlupdateemp);
	}
	echo 'SUCCESS';