<?php
require("include/config.php");
require("include/dbcon.php");

$deviceid=$_POST['deviceid'];
$emp_code=$_POST['emp_code'];
//$deviceid='911250100016392';
//$emp_code='E0012';

// If device id blank from device end
if($deviceid=='')
{
	echo '6';
}
else{
	//Query for fetching the device id for the particular employee
	$sqlquery="select CH.deviceid from employee_master EM,changepassword CH where EM.emp_code=CH.emp_code 
				and CH.emp_code='".$emp_code."'";
	$result = mysqli_query($link,$sqlquery);
	$count=mysqli_num_rows($result);
	$rowresult=mysqli_fetch_assoc($result);
	$device_id_database=$rowresult['deviceid'];
	
	if($count>0)
	{ 
	  if($deviceid==$device_id_database) //Checking the posted deviceid and the database existed deviceid  is same or not
	  {
		 echo '1'; 
	  }
	  else
	  {
		 if($device_id_database=='')     // Checking that the database existed deviceid is blank or not
		 {
			 $sqlchkdeviceid="SELECT EM.emp_name from employee_master EM,changepassword CH where EM.emp_code=CH.emp_code AND CH.deviceid='".$deviceid."'";
			 $rschkdeviceid=mysqli_query($link,$sqlchkdeviceid);
			 $cntchkdeviceid=mysqli_num_rows($rschkdeviceid);
			 if($cntchkdeviceid>0) // Checking that the POST data deviceid is already existed on the database for different employee code or not
			 {
				 $rowchkdeviceid=mysqli_fetch_assoc($rschkdeviceid);
				 $emp_name=$rowchkdeviceid['emp_name'];
				 echo '4'.'/'.$emp_name;
			 }
			 else
			 {
				echo '2';
			 }
		 }
		 else
		 {
			 echo '0';
		 }
	  }
	}
	else
	{
		echo '5';
	}
}
?>
