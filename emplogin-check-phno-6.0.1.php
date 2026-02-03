<?php

 ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL);
 
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");

$emp_code=$_REQUEST['emp_code'];

$deviceid=$_REQUEST['deviceid'];

$newpassword='';
	$sqlquery="select employee_master.emp_code,employee_master.emp_name,employee_master.sale_access,changepassword.newpassword,
		   changepassword.deviceid,employee_master.acedns, employee_master.phone_no as phonenumber from employee_master,changepassword where employee_master.emp_code=changepassword.emp_code and employee_master.phone_no='".$emp_code."'";
//echo $sqlquery;exit();
$result = mysqli_query($link,$sqlquery);
$count=mysqli_num_rows($result);

	$contents = "<?xml version='1.0' encoding='UTF-8'?><recordset>";
	if($count>0){
		while($rowsemp = mysqli_fetch_assoc($result))
		{
			$sqlselect="SELECT is_licensed FROM changepassword WHERE emp_code='".$emp_code."'";
			$rsselect=mysqli_query($link,$sqlselect);
			$rowselect=mysqli_fetch_assoc($rsselect);
			$is_licensed=$rowselect['is_licensed'];
			$device_id_database=$rowsemp['deviceid'];
			$acedns=$rowsemp['acedns'];
			
			//echo 'notdb'.$deviceid;
			//echo 'db'.$device_id_database;
			
			if(strtoupper($acedns)=='Y'){
				if($deviceid=='')
				{
					echo '6';
				}
				else
				{
					if($deviceid!=$device_id_database) //Checking the posted deviceid and the database existed deviceid  is same or not
	  				{
						if($device_id_database=='')     // Checking that the database existed deviceid is blank or not
		 				{
							$sqlchkdeviceid="SELECT EM.emp_name from employee_master EM,changepassword CH where 
							EM.emp_code=CH.emp_code AND CH.deviceid='".$deviceid."'";
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
														
									$i = 0; 
									$contents.="<data>";
									while ($i < mysqli_num_fields($result)) { 
										$meta = mysqli_fetch_field_direct($result,$i);
										$contents .='<'.$meta->name.'><![CDATA['.mb_convert_encoding($rowsemp[$meta->name], 'UTF-8', 'UTF-8').']]></'.$meta->name.'>';
										$i = $i + 1; 
										}
									$contents.="</data>";
									$contents .= "</recordset>";			
									echo $contents;
								
							 }
						}//End of IF for device id database blank
						else
						{
							echo '0';
						}
					}
					else
					{
						$i = 0; 
						
						$contents.="<data>";
						while ($i < mysqli_num_fields($result)) { 
							$meta = mysqli_fetch_field_direct($result, $i);
							$contents .='<'.$meta->name.'><![CDATA['.mb_convert_encoding($rowsemp[$meta->name], 'UTF-8', 'UTF-8').']]></'.$meta->name.'>';
							$i = $i + 1; 
							}
						$contents.="</data>";
						$contents .= "</recordset>";			
						echo $contents;
					}
				}
			} //End of Licensed User Checking IF
			else
			{
				echo 'NOT LICENSED USER';
			}
		  //}
		}// End of While
	}//End of Valid User Checking IF
	else
	{
	  echo 'NOT VALID USER';
	}
	$datetime = gmdate('Y-m-d H:m:s',strtotime('+330 minute'));
	$url = APICALLLOGURL."/emplogin-check-phno-6.0.1.php?nick_name=$nick_name&emp_code=$emp_code&deviceid=$deviceid&newpassword=$newpassword";
	insertapilog($datetime,$emp_code,$url,$nick_name);
mysqli_close($link);
?>
