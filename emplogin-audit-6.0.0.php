<?php
require("include/config.php");
require("include/dbcon.php");

$emp_code=$_REQUEST['emp_code'];
$newpassword=$_REQUEST['newpassword'];

$sqlquery="select employee_master.emp_code,employee_master.emp_name,employee_master.sale_access,changepassword.newpassword,
		   changepassword.deviceid from employee_master,changepassword where employee_master.emp_code=changepassword.emp_code 
			and changepassword.newpassword='".$newpassword."' and changepassword.emp_code='".$emp_code."'";
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
			
			if($is_licensed==1){
				$i = 0; 
				$contents.="<data>";
				while ($i < mysqli_num_fields($result)) { 
					$meta = mysqli_fetch_field($result, $i);
					$contents .='<'.$meta->name.'><![CDATA['.mb_convert_encoding($rowsemp[$meta->name], 'UTF-8', 'UTF-8').']]></'.$meta->name.'>';
					$i = $i + 1; 
					}
				$contents.="</data>";
				$contents .= "</recordset>";			
				echo $contents;		
			}
			else
			{
				echo 'NOT LICENSED USER';
			}
		}
	}
	else
	{
		echo 'NOT VALID USER';
	}
?>
