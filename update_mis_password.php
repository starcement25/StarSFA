<?php
require("include/config.php");
require("include/dbcon.php");

$sqladminlogin="select AM.admin_login,AM.admin_pwd,EM.emp_name  FROM admin_master AM,employee_master EM WHERE 
			EM.emp_code=AM.admin_login AND AM.admin_login!='admin'";
$rsadminlogin=mysqli_query($link,$sqladminlogin);
while($rowcustomer=mysqli_fetch_assoc($rsadminlogin))
{
		$admin_login=$rowcustomer['admin_login'];
		$emp_name=$rowcustomer['emp_name'];
	
	$rand_password = rand(1,9).rand(0,9).rand(0,9).rand(1,9).rand(0,9);
	$updated_password=strtoupper(substr($emp_name,0,2)).'@'.$rand_password;

	
	echo $sqlUpdate="UPDATE admin_master SET admin_pwd='".$updated_password."'
				WHERE admin_login='".$admin_login."'";
	//mysqli_query($link,$sqlUpdate);			
	/*if(mysqli_query($link,$sqlUpdate))
	{
		echo "1";
	}
	else
	{
		echo "0";
	}*/
}
		
?>
