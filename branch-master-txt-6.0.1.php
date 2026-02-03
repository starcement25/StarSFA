<?php
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");

$emp_code=$_REQUEST['emp_code'];

$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);
$incremental_download=$_REQUEST['incremental_download'];
$data_download_time=$_REQUEST['data_download_time'];
$data_download_time=str_replace('€',' ',$data_download_time);

if(survey=='yes')
{
	$sqlquery="SELECT BM.branch_code,BM.branch_name,BM.comp_code,BM.HQ,BM.plant_name FROM 
				branch_master BM,employee_master EM WHERE FIND_IN_SET(BM.branch_code,EM.branch_code) AND  EM.emp_code='".$emp_code."' 
				ORDER BY BM.branch_name ASC";
}
else
{
	$sqlquery="SELECT branch_code,branch_name,comp_code,HQ,plant_name FROM branch_master WHERE 1 ORDER BY branch_name ASC";
}
	$result = mysqli_query($link,$sqlquery);
	$count=mysqli_num_rows($result);
	$cnt=1;
	$contentsrowcolumn  =$count.'¥'.'5';
	if($count>0){
		while($rowbranch = mysqli_fetch_assoc($result))
		{
			$contents  = (($rowbranch['comp_code']!='')?$rowbranch['comp_code']: ' ')."^";
			$contents  .= (($rowbranch['branch_code']!='')?$rowbranch['branch_code']: ' ')."^";
			$contents  .= (($rowbranch['branch_name']!='')?$rowbranch['branch_name']: ' ')."^";
			$contents  .= (($rowbranch['HQ']!='')?$rowbranch['HQ']: ' ')."^";
			$contents  .= (($rowbranch['plant_name']!='')?$rowbranch['plant_name']: ' ');
			
			$linecontents  .= $contents."\n";
		}
		$datacontents = $contentsrowcolumn."\n".str_replace("\r","",$linecontents);
	}
	else
	{
		$datacontents = '0'.'¥'.'0';
	}


	header("Content-type: application/text"); 
	header("Content-Disposition: attachment; filename=branch_master.txt");
	print "$datacontents"; 		
?>
